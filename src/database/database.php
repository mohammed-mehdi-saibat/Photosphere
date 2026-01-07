<?php

declare(strict_types=1);

namespace PHOTOSPHERE\database;

use PDO;
use PDOException;

class Database
{
    private static $instance = null;

    private function __construct() {}

    public static function getInstance(): self
    {
        if (self::$instance === null) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    public function getConnection(): PDO
    {
        static $pdo = null;
        if ($pdo === null) {
            try {
                $dsn = "mysql:host=" . \DB_HOST . ";dbname=" . \DB_NAME . ";charset=utf8mb4";
                $pdo = new PDO($dsn, \DB_USER, \DB_PASS);
                $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
                $pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
            } catch (PDOException $e) {
                die("Connection failed: " . $e->getMessage());
            }
        }
        return $pdo;
    }
}
