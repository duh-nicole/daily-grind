<?php
class Database {
    private static $dsn = 'mysql:host=localhost;dbname=inventory_grounds';
    private static $username = 'root';
    private static $password = 'ch1cken';
    private static $db;

    private function __construct() {}

    public static function getDB () {
        if (!isset(self::$db)) {
            try {
                self::$db = new PDO(self::$dsn,
                                     self::$username,
                                     self::$password);
            } catch (PDOException $e) {
                $error_message = $e->getMessage();
                self::displayError($error_message);
            }
        }
        return self::$db;
    }

    public static function displayError($error_message) {
        include(__DIR__ . '/../errors/db_error.php');
        exit();
    }
}
?>

