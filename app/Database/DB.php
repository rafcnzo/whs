<?php
// app/Database/DB.php → GANTI SEMUA DENGAN INI

class DB
{
    private static $pdo = null;

    public static function connect()
    {
        if (self::$pdo === null) {
            try {

                $host = getenv('DB_HOST') ? getenv('DB_HOST') : 'localhost';
                $port = getenv('DB_PORT') ? getenv('DB_PORT') : '3306';
                $db   = getenv('DB_DATABASE') ? getenv('DB_DATABASE') : 'smartwarehouse';
                $user = getenv('DB_USERNAME') ? getenv('DB_USERNAME') : 'root';
                $pass = getenv('DB_PASSWORD') ? getenv('DB_PASSWORD') : '';
                $dsn = "mysql:host={$host};port={$port};dbname={$db};charset=utf8mb4";

                self::$pdo = new PDO(
                    $dsn,
                    $user,
                    $pass,
                    [
                        PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
                        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                        PDO::ATTR_EMULATE_PREPARES   => false,
                    ]
                );

            } catch (Exception $e) {
                die("Database Connection Error: " . $e->getMessage());
            }
        }
        return self::$pdo;
    }

    // Laravel-style helpers
    public static function table($table)
    {
        return self::connect()->prepare("SELECT * FROM `$table`");
    }

    public static function select($query, $params = [])
    {
        $stmt = self::connect()->prepare($query);
        $stmt->execute($params);
        return $stmt->fetchAll(PDO::FETCH_OBJ);
    }

    public static function insert($query, $params = [])
    {
        $stmt = self::connect()->prepare($query);
        return $stmt->execute($params);
    }

    public static function update($query, $params = [])
    {
        $stmt = self::connect()->prepare($query);
        return $stmt->execute($params);
    }

    public static function delete($query, $params = [])
    {
        $stmt = self::connect()->prepare($query);
        return $stmt->execute($params);
    }

    public static function raw($query, $params = [])
    {
        $stmt = self::connect()->prepare($query);
        $stmt->execute($params);
        return $stmt;
    }

    public static function transaction($callback)
    {
        $pdo = self::connect();
        try {
            $pdo->beginTransaction();
            $callback();
            $pdo->commit();
        } catch (Exception $e) {
            $pdo->rollBack();
            throw $e;
        }
    }
}
