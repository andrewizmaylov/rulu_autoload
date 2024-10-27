<?php

namespace App\Services;

class Database {
	protected static array $config = [];
	protected static array $credentials = [];

	public static function init(): void
	{
		self::$credentials = [
			'user' => EnvReader::getKey('DB_USERNAME'),
			'pass' => EnvReader::getKey('DB_PASSWORD'),
		];
		self::$config = [
			'host' => EnvReader::getKey('DB_HOST'),
			'port' => EnvReader::getKey('DB_PORT'),
			'dbname' => EnvReader::getKey('DB_DATABASE'),
			'charset' => 'utf8mb4',
		];
	}

	protected static array $options = [
		\PDO::ATTR_ERRMODE            => \PDO::ERRMODE_EXCEPTION, // Throw exceptions on errors
		\PDO::ATTR_DEFAULT_FETCH_MODE => \PDO::FETCH_ASSOC,       // Fetch results as associative arrays
		\PDO::ATTR_EMULATE_PREPARES   => false,                  // Use native prepared statements
	];

	public static \PDO $pdo;
	protected static function initConnection(): void
	{
		self::init();
		$dsn = 'mysql:' . http_build_query(self::$config, '', ';');

		try {
			self::$pdo = new \PDO($dsn, self::$credentials['user'], self::$credentials['pass'], self::$options);
		} catch (\PDOException $e) {
			echo "Connection failed: " . $e->getMessage();
		}
	}

	public static function getConnection(): \PDO
	{
		self::initConnection();

		return self::$pdo;
	}

	public static function getQueryResults(string $query): false|\PDOStatement
	{
		self::initConnection();
		$statement = self::$pdo->prepare($query);
		$statement->execute();

		return $statement;
	}
}