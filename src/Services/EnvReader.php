<?php

namespace App\Services;

class EnvReader
{
	private static array $arrayKeys = [];

	protected static function loadEnv($filePath = __DIR__ . '/../../.env'): void
	{
		if (!file_exists($filePath)) {
			throw new \Exception("Environment file not found at $filePath");
		}

		$lines = file($filePath, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);

		foreach ($lines as $line) {
			if (str_starts_with(trim($line), '#')) {
				continue;
			}

			list($key, $value) = explode('=', $line, 2);
			self::$arrayKeys[trim($key)] = trim($value);
		}
	}

	/**
	 * @param  string  $key
	 * @return string|null
	 * @throws \Exception
	 */
	public static function getKey(string $key): string|null
	{
		self::loadEnv();

		return array_key_exists($key, self::$arrayKeys)
			? self::$arrayKeys[$key]
			: null;
	}
}