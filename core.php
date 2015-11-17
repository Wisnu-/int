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
                $element['checked'] = true;
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

/**
 * Build MS SQL Query untuk input Usulan Program Rutin
 * @param  array() $arr 
 * @return string $query
 * @todo  bikin transaksi -- sudah
 */
function createQueryUsulanProgramRutin($arr) {
    
    $query = "";
    $i = 0;
    foreach ($arr as $v) {
        $i++;
        $dt = pecahSkpd($v["skpdTujuan"]);
        $ds = target($v["volumeOK2"]);

        $query .= " begin tran if exists ( select * from [tests].[dbo].[program] with (updlock,serializable)";
        $query .= " where [tahun] = " . $v["Thn"] ;
        $query .= " and [kd_urusan] = " . $dt["urusan"];
        $query .= " and [kd_bidang] = " . $dt["bidang"];
        $query .= " and [kd_unit] = " . $dt["unit"];
        $query .= " and [kd_sub] = " . $dt['sub'];
        $query .= " and [id_prog] = " . $dt["id_prog"];
        $query .= " and [kd_prog] = " . intval($v["program"]);
        $query .= " and [kd_urusan1] = " . $dt["urusan"];
        $query .= " and [kd_bidang1] = " . $dt["bidang"] . ")";

        $query .= " begin update [tests].[dbo].[program]";

        $query .= " set [ket_program] = " . "'" . removeQuote($v["namaProgram"]) ."',";
        $query .= " [tolak_ukur] = " . "'" . removeQuote($v["indikatorOutcome"]) ."',";
        $query .= " [target_angka] = " . "'" . $ds["target"] ."',";
        $query .= " [target_uraian] = " . "'" . $ds["satuan"] ."'";

        $query .= " where [tahun] = " . $v["Thn"] ;
        $query .= " and [kd_urusan] = " . $dt["urusan"];
        $query .= " and [kd_bidang] = " . $dt["bidang"];
        $query .= " and [kd_unit] = " . $dt["unit"];
        $query .= " and [kd_sub] = " . $dt['sub'];
        $query .= " and [id_prog] = " . $dt["id_prog"];
        $query .= " and [kd_prog] = " . intval($v["program"]);
        $query .= " and [kd_urusan1] = " . $dt["urusan"];
        $query .= " and [kd_bidang1] = " . $dt["bidang"] . ";";
        $query .= " end else begin ";


        $query .= "INSERT INTO [tests].[dbo].[program] 
            ([tahun], [kd_urusan], [kd_bidang], [kd_unit], [kd_sub], 
                [kd_prog], [id_prog], [ket_program], [tolak_ukur], [target_angka], 
                [target_uraian], [kd_urusan1], [kd_bidang1]) VALUES";
        $query .= "('" . $v["Thn"] ."',";        
        $query .= "'" . $dt["urusan"] ."',";        
        $query .= "'" . $dt["bidang"] ."',";      
        $query .= "'" . $dt["unit"] ."',";        
        $query .= "'" . $dt["sub"] ."',";        
        $query .= "'" . intval($v["program"]) ."',";        
        $query .= "'" . $dt["id_prog"] ."',";        
        $query .= "'" . removeQuote($v["namaProgram"]) ."',";        
        $query .= "'" . removeQuote($v["indikatorOutcome"]) ."',";        
        $query .= "'" . $ds["target"] ."',";        
        $query .= "'" . $ds["satuan"] ."',";        
        $query .= "'" . $dt["urusan"] ."',";        
        $query .= "'" . $dt["bidang"] ."');" ;                
        $query .= " end commit tran";

        // if ($i == 52) break;
    }    

    return $query;
}

/**
 * Build MS SQL Query untuk input Usulan Program Prioritas
 * @param  array() $arr 
 * @return string $query
 * @todo  bikin transaksi -- sudah
 */
function createQueryUsulanProgramPrioritas($arr) {
    
    $query = "";
    $i = 0;
    foreach ($arr as $v) {
        $i++;
        $dt = pecahSkpd($v["skpdTujuan"], $v["urusan"], $v["bidang"]);
        $ds = target($v["volumeOK2"]);
        $v["bid"] = intval(substr($v["bidang"], 1,2));

        $query .= " begin tran if exists ( select * from [tests].[dbo].[program] with (updlock,serializable)";
        $query .= " where [tahun] = " . $v["Thn"] ;
        $query .= " and [kd_urusan] = " . $dt["urusan"];
        $query .= " and [kd_bidang] = " . $dt["bidang"];
        $query .= " and [kd_unit] = " . $dt["unit"];
        $query .= " and [kd_sub] = " . $dt['sub'];
        $query .= " and [id_prog] = " . $dt["id_prog"];
        $query .= " and [kd_prog] = " . intval($v["program"]);
        $query .= " and [kd_urusan1] = " . $v["urusan"];
        $query .= " and [kd_bidang1] = " . $v["bid"] . ")";

        $query .= " begin update [tests].[dbo].[program]";

        $query .= " set [ket_program] = " . "'" . removeQuote($v["namaProgram"]) ."',";
        $query .= " [tolak_ukur] = " . "'" . removeQuote($v["indikatorOutcome"]) ."',";
        $query .= " [target_angka] = " . "'" . $ds["target"] ."',";
        $query .= " [target_uraian] = " . "'" . $ds["satuan"] ."'";

        $query .= " where [tahun] = " . $v["Thn"] ;
        $query .= " and [kd_urusan] = " . $dt["urusan"];
        $query .= " and [kd_bidang] = " . $dt["bidang"];
        $query .= " and [kd_unit] = " . $dt["unit"];
        $query .= " and [kd_sub] = " . $dt['sub'];
        $query .= " and [id_prog] = " . $dt["id_prog"];
        $query .= " and [kd_prog] = " . intval($v["program"]);
        $query .= " and [kd_urusan1] = " . $v["urusan"];
        $query .= " and [kd_bidang1] = " . $v["bid"] . ";";
        $query .= " end else begin ";


        $query .= "INSERT INTO [tests].[dbo].[program] 
            ([tahun], [kd_urusan], [kd_bidang], [kd_unit], [kd_sub], 
                [kd_prog], [id_prog], [ket_program], [tolak_ukur], [target_angka], 
                [target_uraian], [kd_urusan1], [kd_bidang1]) VALUES";
        $query .= "('" . $v["Thn"] ."',";        
        $query .= "'" . $dt["urusan"] ."',";        
        $query .= "'" . $dt["bidang"] ."',";      
        $query .= "'" . $dt["unit"] ."',";        
        $query .= "'" . $dt["sub"] ."',";        
        $query .= "'" . intval($v["program"]) ."',";        
        $query .= "'" . $dt["id_prog"] ."',";        
        $query .= "'" . removeQuote($v["namaProgram"]) ."',";        
        $query .= "'" . $v["indikatorOutcome"] ."',";        
        $query .= "'" . $ds["target"] ."',";        
        $query .= "'" . $ds["satuan"] ."',";        
        $query .= "'" . $v["urusan"] ."',";        
        $query .= "'" . $v["bid"] ."');" ;                
        $query .= " end commit tran";

        // if ($i == 52) break;
    }    

    return $query;
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

/**
 * Build MS SQL Query untuk input Usulan Program Rutin
 * @param  array() $arr 
 * @return string $query
 * @todo  - kelompok sasaran
 *        - status kegiatan
 *        - waktu pelaksanaan
 *        - sumber masuh DAU
 */
function createQueryUsulanKegiatanRutin($arr)
{
    $query = "";
    $i = 0;
    foreach ($arr as $v) {
        $i++;
        $dt = pecahSkpd($v["skpdTujuan"]);
        $ds = target($v["volume2"]);
        $duit = $v["apbdKota2"] + $v["apbdProp2"] + $v["apbn2"] + $v["danaLain2"];

        $query .= " begin tran if exists ( select * from [tests].[dbo].[kegiatan] with (updlock,serializable)";
        $query .= " where [tahun] = " . $v["Thn"] ;
        $query .= " and [kd_urusan] = " . $dt["urusan"];
        $query .= " and [kd_bidang] = " . $dt["bidang"];
        $query .= " and [kd_unit] = " . $dt["unit"];
        $query .= " and [kd_sub] = " . $dt['sub'];
        $query .= " and [id_prog] = " . $dt["id_prog"];
        $query .= " and [kd_prog] = " . intval($v["program"]);
        $query .= " and [kd_keg] = " . intval($v["kegiatan"])  . ")";

        $query .= " begin update [tests].[dbo].[kegiatan]";

        $query .= " set [ket_kegiatan] = " . "'" . removeQuote($v["namaKegiatan"]) ."',";
        $query .= " [lokasi] = " . "'" . removeQuote($v["lokasi"]) ."',";
        $query .= " [pagu_anggaran] = " . "'" . $duit ."'";

        $query .= " where [tahun] = " . $v["Thn"] ;
        $query .= " and [kd_urusan] = " . $dt["urusan"];
        $query .= " and [kd_bidang] = " . $dt["bidang"];
        $query .= " and [kd_unit] = " . $dt["unit"];
        $query .= " and [kd_sub] = " . $dt['sub'];
        $query .= " and [id_prog] = " . $dt["id_prog"];
        $query .= " and [kd_prog] = " . intval($v["program"]);
        $query .= " and [kd_keg] = " . intval($v["kegiatan"])  . ";";
        $query .= " end else begin ";


        $query .= "INSERT INTO [tests].[dbo].[kegiatan] ([tahun], [kd_urusan], [kd_bidang],
                 [kd_unit], [kd_sub], [kd_prog], [id_prog], [kd_keg], [ket_kegiatan], [lokasi], 
                 [kelompok_sasaran], [status_kegiatan], [pagu_anggaran], 
                 [waktu_pelaksanaan], [kd_sumber]) VALUES";
        $query .= "('" . $v["Thn"] ."',";        
        $query .= "'" . $dt["urusan"] ."',";        
        $query .= "'" . $dt["bidang"] ."',";      
        $query .= "'" . $dt["unit"] ."',";        
        $query .= "'" . $dt["sub"] ."',";        
        $query .= "'" . intval($v["program"]) ."',";        
        $query .= "'" . $dt["id_prog"] ."',";        
        $query .= "'" . intval($v["kegiatan"]) ."',";        
        $query .= "'" . removeQuote($v["namaKegiatan"]) ."',";        
        $query .= "'" . removeQuote($v["lokasi"]) ."',";        
        $query .= "' ',"; //kelompok sasaran        
        $query .= "'B',"; //status kegiatan        
        $query .= "'" . $duit ."',";   //pagu anggaran     
        $query .= "' ',";    // waktu pelaksanaan           
        $query .= "'3');" ; // sumber DAU           
        $query .= " end commit tran";

        // if ($i == 52) break;
    }    

    return $query;
}

/**
 * Build MS SQL Query untuk input Usulan Program Rutin
 * @param  array() $arr 
 * @return string $query
 * @todo  - kelompok sasaran
 *        - status kegiatan
 *        - waktu pelaksanaan
 *        - sumber masuh DAU
 */
function createQueryUsulanKegiatanPrioritas($arr)
{
    $query = "";
    $i = 0;
    foreach ($arr as $v) {
        $i++;
        $dt = pecahSkpd($v["skpdTujuan"],$v["urusan"],$v["bidang"]);
        $ds = target($v["volume2"]);
        $duit = $v["apbdKota2"] + $v["apbdProp2"] + $v["apbn2"] + $v["danaLain2"];

        $query .= " begin tran if exists ( select * from [tests].[dbo].[kegiatan] with (updlock,serializable)";
        $query .= " where [tahun] = " . $v["Thn"] ;
        $query .= " and [kd_urusan] = " . $dt["urusan"];
        $query .= " and [kd_bidang] = " . $dt["bidang"];
        $query .= " and [kd_unit] = " . $dt["unit"];
        $query .= " and [kd_sub] = " . $dt['sub'];
        $query .= " and [id_prog] = " . $dt["id_prog"];
        $query .= " and [kd_prog] = " . intval($v["program"]);
        $query .= " and [kd_keg] = " . intval($v["kegiatan"])  . ")";

        $query .= " begin update [tests].[dbo].[kegiatan]";

        $query .= " set [ket_kegiatan] = " . "'" .  removeQuote($v["namaKegiatan"]) ."',";
        $query .= " [lokasi] = " . "'" . removeQuote($v["lokasi"]) ."',";
        $query .= " [pagu_anggaran] = " . "'" . $duit ."'";

        $query .= " where [tahun] = " . $v["Thn"] ;
        $query .= " and [kd_urusan] = " . $dt["urusan"];
        $query .= " and [kd_bidang] = " . $dt["bidang"];
        $query .= " and [kd_unit] = " . $dt["unit"];
        $query .= " and [kd_sub] = " . $dt['sub'];
        $query .= " and [id_prog] = " . $dt["id_prog"];
        $query .= " and [kd_prog] = " . intval($v["program"]);
        $query .= " and [kd_keg] = " . intval($v["kegiatan"])  . ";";
        $query .= " end else begin ";


        $query .= "INSERT INTO [tests].[dbo].[kegiatan] ([tahun], [kd_urusan], [kd_bidang],
                 [kd_unit], [kd_sub], [kd_prog], [id_prog], [kd_keg], [ket_kegiatan], [lokasi], 
                 [kelompok_sasaran], [status_kegiatan], [pagu_anggaran], 
                 [waktu_pelaksanaan], [kd_sumber]) VALUES";
        $query .= "('" . $v["Thn"] ."',";        
        $query .= "'" . $dt["urusan"] ."',";        
        $query .= "'" . $dt["bidang"] ."',";      
        $query .= "'" . $dt["unit"] ."',";        
        $query .= "'" . $dt["sub"] ."',";        
        $query .= "'" . intval($v["program"]) ."',";        
        $query .= "'" . $dt["id_prog"] ."',";        
        $query .= "'" . intval($v["kegiatan"]) ."',";        
        $query .= "'" . removeQuote($v["namaKegiatan"]) ."',";        
        $query .= "'" . removeQuote($v["lokasi"]) ."',";        
        $query .= "' ',"; //kelompok sasaran        
        $query .= "'B',"; //status kegiatan        
        $query .= "'" . $duit ."',";   //pagu anggaran     
        $query .= "' ',";    // waktu pelaksanaan           
        $query .= "'3');" ; // sumber DAU           
        $query .= " end commit tran";

        // if ($i == 52) break;
    }    

    return $query;
}
