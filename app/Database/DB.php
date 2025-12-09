<?php
// app/Database/DB.php → GANTI SEMUA DENGAN INI

class DB
{
    private static $pdo = null;

    public static function connect()
    {
        if (self::$pdo === null) {
            try {
                self::$pdo = new PDO(
                    "mysql:host=localhost;port=3306;dbname=smartwarehouse;charset=utf8mb4",
                    "root",
                    "",
                    [
                        PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
                        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                    ]
                );
            } catch (Exception $e) {
                die("Database error: " . $e->getMessage());
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


