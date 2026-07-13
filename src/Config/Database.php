<?php
namespace App\Config;

use PDO;
use PDOException;

class Database {
    private static ?PDO $conn = null;

    public static function getConnection(): PDO {
        if (self::$conn === null) {
            $host = $_ENV['DB_HOST'];
            $db   = $_ENV['DB_NAME'];
            $user = $_ENV['DB_USER'];
            $pass = $_ENV['DB_PASS'];
            $charset = $_ENV['DB_CHARSET'] ?? 'utf8mb4';

            $dsn = "mysql:host=$host;dbname=$db;charset=$charset";

            try {
                self::$conn = new PDO($dsn, $user, $pass, [
                    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                    PDO::ATTR_EMULATE_PREPARES => true,   // ← changed from false
                    PDO::ATTR_PERSISTENT => true,          // ← added
                ]);
            } catch(PDOException $e) {
                error_log("Database connection error: " . $e->getMessage());
                die("የዳታቤዝ ግንኙነት አልተሳካም። እባክዎ ቆይተው ይሞክሩ።".$e->getMessage());
            }
        }

        return self::$conn;
    }
}