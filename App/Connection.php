<?php

namespace App;

class Connection {

	public static function getDb() {
		try {

			$conn = new \PDO(
				"mysql:host={hostname};dbname={database name};charset=utf8",
				"{user name}",
				"{password}"
			);

			return $conn;
		} catch (\PDOException $e) {
			//.. tratar de alguma forma ..//
		}
	}
}
