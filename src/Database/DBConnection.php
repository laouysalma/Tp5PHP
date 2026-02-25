<?php
declare(strict_types=1);

namespace App\Database;

use PDO;
use PDOException;
use App\Log\Logger;

class DBConnection
{
    private static $pdo = null;    // ------- PDO -------------
    private static $logger;        // -------- logger -------------

    public static function init(Logger $logger): void
    {
        self::$logger = $logger; // ------------- set logger  ---------------
    }

    public static function get(): PDO
    {
        if (self::$pdo instanceof PDO) return self::$pdo; // ---------- déjà connecté ----------

        $config = require __DIR__ . '/../../config/db.php'; // --------- load config ----------
        $dsn  = $config['dsn'];
        $user = $config['user'];
        $pass = $config['pass'];
        $opts = $config['options'] ?? [];

        try {
            self::$pdo = new PDO($dsn, $user, $pass, $opts); // ---------- connect  -------------
            self::$pdo->exec('SET NAMES utf8mb4');  
            return self::$pdo;

        } catch (PDOException $e) {
            if (self::$logger) {
                self::$logger->error(
                    'Erreur PDO', 
                    ['method'=>__METHOD__, 'dsn'=>$dsn, 'err'=>$e->getMessage()]
                );
            }
            throw $e; // ------------  relance   ------------
        }
    }
}