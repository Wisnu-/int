<?php

class DB
{
    protected static $conn = null;
    final private function __construct() {}
    final private function __clone() {}
    /**
     * @return PDO
     */
    public static function conn() {
        if (self::$conn === null) {
            try {
                self::$conn = new PDO(
                    'mysql:host=' . DB_HOST . ';dbname=' . DB_NAME,
                    DB_USER,
                    DB_PASS
                );
                self::$conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
                self::$conn->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
            } catch (PDOException $e) {
                die('Database connection could not be established.');
            }
        }
        return self::$conn;
    }
    
    public static function __callStatic($method, $args) {
        return call_user_func_array(array(self::conn(), $method), $args);
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
        header("HTTP/1.1 501 Not Implemented");
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
function buildTree(array &$elements, $parentId = 0) {
    $branch = array();

    foreach ($elements as $element) {
        if ($element['parent_id'] == $parentId) {
            $children = buildTree($elements, $element['id']);
            if ($children) {
                $element['children'] = $children;
            }
            $branch[] = $element;
            // $branch[$element['id']] = $element;
            unset($elements[$element['id']]);
        }
    }
    return $branch;
}
