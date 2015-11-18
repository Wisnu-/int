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

class DBH
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
                self::$conn = new PDO( "sqlsrv:Server=CODING-PRIMA\SQLEXPRESS ; Database = tests ", "sa", "1195100038", array(PDO::SQLSRV_ATTR_DIRECT_QUERY => true));
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
            if($element['published'] == 1) {
                $element['checked'] = isset($element['active']) ? (bool) $element['active'] : true;
            }
            $branch[] = $element;
        }
    }
    return $branch;
}

function removeQuote($str) {
    $a = str_replace('"', "", $str);
    $b = str_replace("'", "", $a);

    return $b;
}

function target($a)
{
    $i= array();
    if (strlen($a) < 3) {
        $i["target"] = '0'; 
        $i["satuan"] = ' '; 
    } else {
        $dt = explode(" ", $a);

        $i["target"] = $dt[0];

        unset($dt[0]);
        $i["satuan"] = implode(" ", $dt);

    }

    return $i;
}



function pecahSkpd ($skpd, $urusan ='', $bidang ='') {

    if ($urusan == '' || $bidang == '') {

        $r['urusan'] = substr($skpd, 0, 1); 
        $r['bidang'] = intval(substr($skpd, 1, 2)); 
        $r['unit'] = intval(substr($skpd, 3, 2)); 
        $r['sub'] = intval(substr($skpd, 5, 2)); 

        $r['id_prog'] = intval(substr($skpd, 0, 3)); 
    } else {
        $r['urusan'] = substr($skpd, 0, 1); 
        $r['bidang'] = intval(substr($skpd, 1, 2)); 
        // $r['urusan'] = intval($urusan); 
        // $r['bidang'] = intval(substr($bidang, 1, 2));
        $r['unit'] = intval(substr($skpd, 3, 2)); 
        $r['sub'] = intval(substr($skpd, 5, 2)); 

        $r['id_prog'] = $bidang; 
    }


    return $r;
}

function publishMenu(array $data)
{
    if (!empty($data)) {
        $i = '';
        $qry = DB::prepare("UPDATE menu SET 
                    `parent_id` =:parent_id,
                    `sort_id`   =:sort_id,
                    `text`      =:texts,
                    `handler`   =:handler,
                    `published` =:checked 
                WHERE id=:id");
        $qry->bindParam(':parent_id', $data['parent'], PDO::PARAM_INT);
        $qry->bindParam(':sort_id', $data['sort_id'], PDO::PARAM_INT);
        $qry->bindParam(':texts', $data['text'], PDO::PARAM_STR);
        $qry->bindParam(':handler', $data['handler'], PDO::PARAM_STR);
        $qry->bindParam(':checked', $data['_checked'], PDO::PARAM_INT);
        $qry->bindParam(':id', $data['id'], PDO::PARAM_INT);

        try {
            $qry->execute();
        } catch (PDOException $e) {
            $i .= $e;
        }

        if (!empty($data["children"])) {
            $ci = 0;
            foreach ($data["children"] as $d) {
                $ci++;
                $d["parent"] = $data["id"];
                $d["sort_id"] = $ci;
                publishMenu($d);
            }
        }

        return $i;
    }
}