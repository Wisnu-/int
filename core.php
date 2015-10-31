<?php

class DB
{
    protected static $instance = null;
    final private function __construct() {}
    final private function __clone() {}
    /**
     * @return PDO
     */
    public static function instance() {
        if (self::$instance === null) {
            try {
                self::$instance = new PDO(
                    'mysql:host=' . DB_HOST . ';dbname=' . DB_NAME,
                    DB_USER,
                    DB_PASS
                );
                self::$instance->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            } catch (PDOException $e) {
                die('Database connection could not be established.');
            }
        }
        return self::$instance;
    }
    
    public static function __callStatic($method, $args) {
        return call_user_func_array(array(self::instance(), $method), $args);
    }
}

/**
 * class Redirect / Route
 */
class R {

    public static function to($loc = 'maintenance')
    {

        $site = $_SERVER['HTTP_HOST'];
        header("Location: http://$site/$loc.php");
    }

    public static function fail($message = '')
    {
        header("Status: 501 Not Implemented");
            $data = [
                'message' => $message 
            ];
            echo json_encode($data);
        exit;
    }

    public static function base()
    {
        return 'http://' . $_SERVER['HTTP_HOST'] . '/';
    }

    public static function modul($name = '')
    {
        return 'http://' . $_SERVER['HTTP_HOST'] . '/modul/' . $name . '.php';
    }

}

