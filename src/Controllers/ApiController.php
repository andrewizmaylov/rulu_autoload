<?php

namespace App\Controllers;

use App\Enums\ApiPathEnum;
use App\Enums\PathEnums;
use App\Services\Database;

class ApiController extends FetchController {
	public static function create()
	{
		if (self::isApiRequest()) {
			header('Content-Type: application/json');
		}

		try {
			$data = self::sanitize();

			if (empty($data['full_name'])) {
				throw new \Exception('Invalid payload: full_name is required.');
			}
			$connection = Database::getConnection();
			$stmt = $connection->prepare("INSERT INTO `users` (full_name, role, efficiency) VALUES (:full_name, :role, :efficiency)");
			$stmt->execute($data);

			$userId = $connection->lastInsertId();

			if ($_SERVER['REQUEST_URI'] !== PathEnums::HOME->value && !self::isApiRequest()) {
				header('Location: ' . PathEnums::HOME->value, true, 301);
			} else {
				return self::respond([
					"success" => true,
					"result"  => ["id" => $userId]
				]);
			}
		} catch (\Exception $e) {
			return self::respond([
				"success" => false,
				"result"  => ["error" => $e->getMessage()]
			]);
		}
	}

	public static function defineGetQuery()
	{
		$where_query = '';
		$query_params = [];
		if (!empty($_GET)) {
			$available_keys = ['full_name', 'role', 'efficiency', 'id'];
			foreach ($available_keys as $key) {
				if (isset($_GET[$key])) {
					$where_query .= " AND `" . $key . "` = :" . $key;
					$query_params[$key] = $_GET[$key];
				}
			}
		}

		return [
			$where_query,
			$query_params
		];
	}

	public static function get()
	{
		$path = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
		$method = $_SERVER['REQUEST_METHOD'];
		if ($method === 'PATCH') return self::update();
		if ($method === 'DELETE') return self::delete();

		if (self::isApiRequest()) {
			header('Content-Type: application/json');
			if (empty($_GET)) {
				// ищем и возвращаем всех пользователей если $_SERVER['REQUEST_URI']['path'] заканчивается на get
				// если $_SERVER['REQUEST_URI']['path'] заканчивается на цифру - ищем пользователя по id
				$destructed_path = explode('/', $path);

				if ($path === ApiPathEnum::GET->value) {
					return self::getQueryConstructor();
				} elseif (ctype_digit(end($destructed_path))) {
					return self::getQueryConstructor((int) end($destructed_path));
				} else {
					return self::respond([
						"success" => false,
						"result"  => ["error" => 'Wrong path for get URL']
					]);
				}
			}
		}

		// Условие для возврата чистой формы
		if ($path === PathEnums::USER->value && empty($_GET) ) {
			return self::respond([
				"success" => true,
				"result"  => null
			]);
		}

		return self::getQueryConstructor();
	}


	/**
	 * @return false|string|null
	 */
	public static function getQueryConstructor(int $id = null)
	{
		try {
			list($where_query, $query_params) = self::defineGetQuery();
			// Базовый запрос
			$query = "SELECT * FROM `users` WHERE 1=1".$where_query;
			if (isset($id)) {
				$query_params["id"] = $id;
				$query .= " AND id = :id";
			}

			$stmt = Database::getConnection()->prepare($query);
			$stmt->execute($query_params);
			$user = $stmt->fetchAll();

			if ($user) {

				if ($_SERVER['REQUEST_METHOD'] === 'POST' && $_SERVER['REQUEST_URI'] !== PathEnums::HOME->value && !self::isApiRequest()) {
					header('Location: ' . PathEnums::HOME->value, true, 301);
				} else {
					return self::respond([
						"success" => true,
						"result"  => [
							'users' => $user,
						]
					]);
				}
			} else {
				throw new \Exception("User not found");
			}
		} catch (\Exception $e) {
			return self::respond([
				"success" => false,
				"result"  => ["error" => $e->getMessage()]
			]);
		}
	}

	public static function update()
	{
		if (self::isApiRequest()) {
			header('Content-Type: application/json');
		}

		$userId = self::getUserIdFromGetOrUrl();

		try {
			$data = self::sanitize();
			$fieldsToUpdate = [];
			$params = [];

			foreach (['full_name', 'role', 'efficiency'] as $field) {
				if (isset($data[$field])) {
					$fieldsToUpdate[] = "$field = :$field";
					$params[$field] = $data[$field];
				}
			}

			if (empty($fieldsToUpdate)) {
				throw new \Exception("No fields to update provided");
			}

			$params['id'] = $userId;
			$sql = "UPDATE `users` SET " . implode(", ", $fieldsToUpdate) . " WHERE id = :id";
			$stmt = Database::getConnection()->prepare($sql);
			$stmt->execute($params);

			$updatedUser = Database::getConnection()->prepare("SELECT * FROM `users` WHERE id = :id");
			$updatedUser->execute(['id' => $userId]);
			$user = $updatedUser->fetch();

			if ($_SERVER['REQUEST_URI'] !== PathEnums::HOME->value && !self::isApiRequest()) {
				header('Location: ' . PathEnums::HOME->value, true, 301);
			} else {
				return self::respond([
					"success" => !empty($user),
					"result"  => !empty($user) ? $user : ['error' => 'User not found'],
				]);
			}
		} catch (\Exception $e) {
			return self::respond([
				"success" => false,
				"result" => ["error" => $e->getMessage()]
			]);
		}
	}

	public static function delete()
	{
		if (self::isApiRequest()) {
			header('Content-Type: application/json');
		}

		if ($_SERVER['REQUEST_URI'] === ApiPathEnum::DELETE->value) {
			Database::getConnection()->exec("DELETE FROM `users`");
			return self::respond([
				"success" => true
			]);
		}

		$userId = self::getUserIdFromGetOrUrl();
		try {
			if ($userId) {
				$stmt = Database::getConnection()->prepare("SELECT * FROM `users` WHERE id = :id");
				$stmt->execute(['id' => $userId]);
				$user = $stmt->fetch();

				if (!$user) {
					throw new \Exception("User not found");
				}

				$deleteStmt = Database::getConnection()->prepare("DELETE FROM `users` WHERE id = :id");
				$deleteStmt->execute(['id' => $userId]);

				if ($_SERVER['REQUEST_URI'] !== PathEnums::HOME->value && !self::isApiRequest()) {
					header('Location: ' . PathEnums::HOME->value, true, 301);
				} else {
					return self::respond([
						"success" => true,
						"result"  => $user,
					]);
				}
			}
		} catch (\Exception $e) {
			return self::respond([
				"success" => false,
				"result" => ["error" => $e->getMessage()]
			]);
		}
	}

	private static function getUserIdFromGetOrUrl(): int
	{
		$user_id = -1;
		// Check $_GET for id
		if (isset($_GET['id'])) {
			$user_id = $_GET['id'];
		}

		// check Path is number
		if (empty($_GET)) {
			$path = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
			$parsed_url = explode('/', $path);

			$user_id = ctype_digit(end($parsed_url)) ? (int) end($parsed_url) : -1;
		}

		return $user_id;
	}
}