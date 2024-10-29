<?php

namespace App\Controllers;

use App\Contracts\CrudInterface;
use App\Enums\PathEnums;

abstract class FetchController  implements CrudInterface
{
	/**
	 * @return bool
	 */
	public static function isApiRequest(): bool
	{
		return strrpos(parse_url($_SERVER['REQUEST_URI'])['path'], PathEnums::API->value) === 0;
	}

	protected static function respond($data)
	{
		$jsonData = json_encode($data);

		if (self::isApiRequest()) {
			echo $jsonData;
			return null;
		} else {
			return $jsonData;
		}
	}

	protected static function sanitize(): array {
		$content = file_get_contents('php://input');
		if (self::isApiRequest()) {
			$data = json_decode($content, true);
		} else {
			parse_str($content, $data);
		}

		return [
			'full_name' => htmlspecialchars(trim($data['full_name'])) ?? null,
			'role' => htmlspecialchars(trim($data['role'])) ?? null,
			'efficiency' => htmlspecialchars(trim($data['efficiency'])) ?? null,
		];
	}
}