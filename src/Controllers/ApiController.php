<?php

namespace App\Controllers;

use App\Enums\PathEnums;
use App\Services\Database;

class ApiController extends FetchController {
	public static function create() {
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

			return self::respond([
				"success" => true,
				"result"  => ["id" => $userId]
			]);
		} catch (\Exception $e) {
			return self::respond([
				"success" => false,
				"result"  => ["error" => $e->getMessage()]
			]);
		}
	}


	public static function get()
	{
		if (self::isApiRequest()) {
			header('Content-Type: application/json');
		}

		if (
			parse_url($_SERVER['REQUEST_URI'])['path'] === PathEnums::USER->value
			&& empty($_GET)
		) {
			return self::respond([
				"success" => true,
				"result"  => null
			]);
		}
		try {
			$queryParams = [];

			// Базовый запрос
			$query = "SELECT * FROM `users` WHERE 1=1";

			if (isset($_GET['id'])) {
				$query .= " AND id = :id";
				$queryParams['id'] = $_GET['id'];
			}

			if (isset($_GET['full_name'])) {
				$query .= " AND full_name = :full_name";
				$queryParams['full_name'] = $_GET['full_name'];
			}
			if (isset($_GET['role'])) {
				$query .= " AND role = :role";
				$queryParams['role'] = $_GET['role'];
			}
			if (isset($_GET['efficiency'])) {
				$query .= " AND efficiency = :efficiency";
				$queryParams['efficiency'] = $_GET['efficiency'];
			}

			$stmt = Database::getConnection()->prepare($query);
			$stmt->execute($queryParams);
			$user = $stmt->fetchAll();

			if ($user) {
				return self::respond([
					"success" => true,
					"result"  => $user
				]);
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

		$userId = $_GET['id'] ?? null;
		if (!$userId) {
			return self::respond([
				"success" => false,
				"result" => ["error" => 'No user id provided']
			]);
		}
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

			return self::respond([
				"success" => true,
				"result" => $user
			]);
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

		$userId = $_GET['id'] ?? null;
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

				return self::respond([
					"success" => true,
					"result" => $user
				]);
			} else {
				Database::getConnection()->exec("DELETE FROM `users`");
				return self::respond(["success" => true]);
			}
		} catch (\Exception $e) {
			return self::respond([
				"success" => false,
				"result" => ["error" => $e->getMessage()]
			]);
		}
	}
}