<?php 

define('DB_HOST', 'localhost');
define('DB_USER', 'root');
define('DB_PASS', '');
define('DB_NAME', 'integrasi_sipp');

/**
 * define database connection configuration
 */

define('DBH_HOST', 'CODING-PRIMA\SQLEXPRESS');
define('DBH_USER', 'sa');
define('DBH_PASS', '1195100038');
define('DBH_NAME', 'tests');

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

function bacaRangeTahun() {
	$q = DB::prepare("select * from tahun");
	$q->execute();

	$rowTahun = $q->fetchAll(PDO::FETCH_ASSOC);

	$result = '';
	foreach ($rowTahun as $v) {
		$result .= '<option value="'. $v["Thn"] .'">' . $v["Thn"] . '</option>';
	}

	return $result;
}

/**
 * ----------------------------------------------------------------------------------
 * END FUNCTION
 *
 * START an Actions
 * ----------------------------------------------------------------------------------
 */
$url = strtok($_SERVER["REQUEST_URI"],'?');
$act = isset($_REQUEST["act"]) && $_REQUEST["act"] != '' ? $_REQUEST['act'] : '';
$thn = isset($_REQUEST["thn"]) && intval($_REQUEST["thn"]) > 1990 && intval($_REQUEST["thn"]) < 2100  ? intval($_REQUEST['thn']) : '';

/**
 * jika aksi kosong dan tahun kosong
 * @return html View
 */

if ($act == '' && $thn == '') {

	$rangeTahun = bacaRangeTahun();

	$res = <<<EOD
<!DOCTYPE html>
<html>
<head>
	<meta charset="UTF-8">
	<title>Full Layout - jQuery EasyUI Demo</title>
	<link rel="stylesheet" type="text/css" href="static/css/easyui.css">
	<script type="text/javascript" src="static/js/jquery.min.js"></script>
	<script type="text/javascript" src="static/js/jquery.easyui.min.js"></script>
	<script type="text/javascript" src="app.js"></script>
</head>
<body>
<div id="panelIntegrasi" style="padding:10px;">
	<table border=0>
		<tr>
			<td>Pilih Tahun Anggaran</td>
			<td>:</td>
			<td>
				<select id="alThn" class="easyui-combobox" name="alThn" style="width:200px;">
    				{$rangeTahun}
				</select>
				</td>
		</tr>
		<tr>
			<td>Pilih Jenis Aksi</td>
			<td>:</td>
			<td>
				<select id="alAksi" class="easyui-combobox" name="alAksi" style="width:200px;">
    				<option value="updateProgram">Posting Data Program</option>
    				<option value="updateKegiatan">Posting Data Kegiatan</option>
				</select>
			</td>
		</tr>
		<tr>
			<td></td>
			<td></td>
			<td></td>
		</tr>
		<tr>
			<td></td>
			<td></td>
			<td>
				<a id="btnKirim" class="easyui-linkbutton" data-options="iconCls:'icon-add'">Kirim</a> 
			</td>
		</tr>
		<tr>
			<td></td>
			<td></td>
			<td></td>
		</tr>
	</table>
	<div id="panelHasilIntegrasi" style="padding:10px;">
		<div id="resIntegrasi">
		</div>
	</div>
</div>
<script>
	$("#panelIntegrasi").panel({
		title : 'Posting Data Ke DPPKA',
		fit: true 
	});

	$("#panelHasilIntegrasi").panel({
		title : 'Hasil Posting Data Ke DPPKA',
		width: 500,
		height: 300,
		loadingMessage : 'Silahkan Menunggu Sebentar...................' 
	});

	 $('#btnKirim').bind('click', function(e) {
		e.preventDefault();
		var act = $('#alAksi').combobox('getValue');
		var thn = $("#alThn").combobox('getValue');
		var url = "$url?act=" + act + "&thn="+thn;

		$("#panelHasilIntegrasi").panel({
			href: url 
		});

		// $.get(url,
		// 	function (data) {
		// 		console.log(data);
		// 		$("#resIntegrasi").empty(); 

		// 		$("#resIntegrasi").append(data);

		// });
	});
</script>
</body>
</html>
EOD;

	echo $res;
}
else if  ($act == 'updateProgram' && $thn != '') {

	/**
	 * ------------------------------------------------------------------------------------
	 * Ambil Usulan Program Rutin semua SKPD
	 * ------------------------------------------------------------------------------------
	 */
	$queryUsulanRutin = "SELECT
				usulanrutin.Thn,
				usulanrutin.skpdTujuan,
				usulanrutin.program,
				usulanrutin.kegiatan,
				usulanrutin.volumeOK2,
				usulanrutin.volume2,
				usulanrutin.volumeHK2,
				usulanrutin.apbdKota2,
				usulanrutin.apbdProp2,
				usulanrutin.apbn2,
				program.namaProgram,
				kegiatan.namaKegiatan,
				usulanrutin.indikatorHK,
				usulanrutin.indikatorOutput,
				usulanrutin.indikatorOutcome
				FROM
					usulanrutin
				INNER JOIN 
					program ON 
						program.kode = usulanrutin.program 
						AND program.Thn = usulanrutin.Thn
				INNER JOIN 
					kegiatan ON 
						kegiatan.id_Program = usulanrutin.program 
						AND kegiatan.kode = usulanrutin.kegiatan 
						AND kegiatan.Thn = usulanrutin.Thn 
				WHERE usulanrutin.Thn=$thn and status=1 and status2=1
				GROUP BY usulanrutin.program, usulanrutin.skpdTujuan";

	$usulanRutin = DB::prepare($queryUsulanRutin);
	$usulanRutin->execute();


	$rowProgramRutin = $usulanRutin->fetchAll(PDO::FETCH_ASSOC);

	/**
	 * ------------------------------------------------------------------------------------
	 * Insert Program Rutin ke MS SQL Server
	 * ------------------------------------------------------------------------------------
	 */

	if (!empty($rowProgramRutin)) {
		try {
			$queryInsert = createQueryUsulanProgramRutin($rowProgramRutin);
			DBH::exec( $queryInsert);

			echo "<li>Posting Usulan Program Rutin Sukses!</li>";
		} catch (PDOException $e) {
			echo $e;
		}
	} else {
		echo "<li style='color:red;'>Posting Usulan Program Rutin Gagal! Data Kosong!!</li>";
	}

	/**
	 * Ambil Progran Non Rutin Di RKPD
	 * 	- tahun belum dinamis
	 */

	$queryUsulanPrioritas = "SELECT
					usulanprioritas.Thn,
					usulanprioritas.urusan,
					usulanprioritas.bidang,
					usulanprioritas.skpdTujuan,
					usulanprioritas.program,
					usulanprioritas.kegiatan,
					program.namaProgram,
					kegiatan.namaKegiatan,
					usulanprioritas.indikatorOutput,
					usulanprioritas.indikatorOutcome,
					usulanprioritas.indikatorHK,
					usulanprioritas.lokasi,
					usulanprioritas.volume2,
					usulanprioritas.volumeOK2,
					usulanprioritas.volumeHK2,
					usulanprioritas.apbdKota2,
					usulanprioritas.apbdProp2,
					usulanprioritas.apbn2,
					usulanprioritas.danaLain2
					FROM
					usulanprioritas
					INNER JOIN program ON program.id_Urusan = usulanprioritas.urusan AND 
							program.id_Bidang = usulanprioritas.bidang AND 
							program.kode = usulanprioritas.program AND 
							program.Thn = usulanprioritas.Thn
					INNER JOIN kegiatan ON kegiatan.kode_urusan = usulanprioritas.urusan AND 
							kegiatan.Thn = usulanprioritas.Thn AND 
							kegiatan.kode = usulanprioritas.kegiatan AND 
							kegiatan.kode_bidang = usulanprioritas.bidang AND 
							kegiatan.id_Program = usulanprioritas.program
					WHERE usulanprioritas.Thn=$thn and 
							usulanprioritas.status=1 and 
							usulanprioritas.status2=1
					GROUP BY usulanprioritas.program, 
							usulanprioritas.skpdTujuan, 
							usulanprioritas.urusan, 
							usulanprioritas.bidang";

	$usulanPrioritas = DB::prepare($queryUsulanPrioritas);
	$usulanPrioritas->execute();


	$rowProgramPrioritas = $usulanPrioritas->fetchAll(PDO::FETCH_ASSOC);

	/**
	 * ------------------------------------------------------------------------------------
	 * Insert Program Rutin ke MS SQL Server
	 * ------------------------------------------------------------------------------------
	 */
	if (!empty($rowProgramPrioritas)) {
		try {
			$queryInsert = createQueryUsulanProgramPrioritas($rowProgramPrioritas);
			DBH::exec( $queryInsert);

			echo "<li>Posting Usulan Program Prioritas Sukses!</li>";
		} catch (PDOException $e) {
			echo $e;
		}
	} else {
		echo "<li style='color:red;'>Posting Usulan Program Prioritas Gagal! Data Kosong!!</li>";
	}

} 

elseif  ($act == 'updateKegiatan' && $thn != '') {

	/**
	 * Ambil kegiatan Rutin Di RKPD
	 * 	
	 */

	$queryKegiatanRutin = "SELECT
				usulanrutin.Thn,
				usulanrutin.skpdTujuan,
				usulanrutin.program,
				usulanrutin.kegiatan,
				usulanrutin.volume2,
				usulanrutin.volumeOK2,
				usulanrutin.volumeHK2,
				usulanrutin.apbdKota2,
				usulanrutin.apbdProp2,
				usulanrutin.apbn2,
				usulanrutin.lokasi,
				usulanrutin.danaLain2,
				program.namaProgram,
				kegiatan.namaKegiatan,
				usulanrutin.indikatorHK,
				usulanrutin.indikatorOutcome,
				usulanrutin.indikatorOutput
				FROM
					usulanrutin
				INNER JOIN 
					program ON 
						program.kode = usulanrutin.program 
						AND program.Thn = usulanrutin.Thn
				INNER JOIN 
					kegiatan ON 
						kegiatan.id_Program = usulanrutin.program 
						AND kegiatan.kode = usulanrutin.kegiatan 
						AND kegiatan.Thn = usulanrutin.Thn 
				WHERE usulanrutin.Thn=$thn and status=1 and status2=1";
	$kegiatanRutin = DB::prepare($queryKegiatanRutin);
	$kegiatanRutin->execute();

	$rowKegiatanRutin = $kegiatanRutin->fetchAll(PDO::FETCH_ASSOC);

	/**
	 * ------------------------------------------------------------------------------------
	 * Insert Kegiatan Rutin ke MS SQL Server
	 * ------------------------------------------------------------------------------------
	 */
	
	if(! empty($rowKegiatanRutin)) {

		try {
			// $conn = DBH::conn();
			$queryInsert = createQueryUsulanKegiatanRutin($rowKegiatanRutin);
			DBH::exec( $queryInsert);
			echo "<li>Posting Usulan Kegiatan Rutin Sukses!</li>";
		} catch (PDOException $e) {
			echo $e;
		}
	} else {
		echo "<li style='color:red;'>Posting Usulan Kegiatan Rutin Gagal! data Kosong !!</li>";
	}

	/**
	 * Ambil kegiatan Rutin Di RKPD
	 * 	- tahun belum dinamis
	 */

	$queryKegiatanPrioritas = "SELECT
					usulanprioritas.Thn,
					usulanprioritas.urusan,
					usulanprioritas.bidang,
					usulanprioritas.skpdTujuan,
					usulanprioritas.program,
					usulanprioritas.kegiatan,
					program.namaProgram,
					kegiatan.namaKegiatan,
					usulanprioritas.indikatorOutput,
					usulanprioritas.indikatorOutcome,
					usulanprioritas.indikatorHK,
					usulanprioritas.lokasi,
					usulanprioritas.volume2,
					usulanprioritas.volumeOK2,
					usulanprioritas.volumeHK2,
					usulanprioritas.apbdKota2,
					usulanprioritas.apbdProp2,
					usulanprioritas.apbn2,
					usulanprioritas.danaLain2
					FROM
					usulanprioritas
					INNER JOIN program ON program.id_Urusan = usulanprioritas.urusan AND 
							program.id_Bidang = usulanprioritas.bidang AND 
							program.kode = usulanprioritas.program AND 
							program.Thn = usulanprioritas.Thn
					INNER JOIN kegiatan ON kegiatan.kode_urusan = usulanprioritas.urusan AND 
							kegiatan.Thn = usulanprioritas.Thn AND 
							kegiatan.kode = usulanprioritas.kegiatan AND 
							kegiatan.kode_bidang = usulanprioritas.bidang AND 
							kegiatan.id_Program = usulanprioritas.program
					WHERE usulanprioritas.Thn=$thn and 
							usulanprioritas.status=1 and 
							usulanprioritas.status2=1";

	$kegiatanPrioritas = DB::prepare($queryKegiatanPrioritas);
	$kegiatanPrioritas->execute();

	$rowKegiatanPrioritas = $kegiatanPrioritas->fetchAll(PDO::FETCH_ASSOC);


	/**
	 * ------------------------------------------------------------------------------------
	 * Insert Kegiatan Prioritas ke MS SQL Server
	 * ------------------------------------------------------------------------------------
	 */

	if (!empty($rowKegiatanPrioritas)) {

		try {
			// $conn = DBH::conn();
			$queryInsertKegitanPrioritas = createQueryUsulanKegiatanPrioritas($rowKegiatanPrioritas);
			DBH::exec($queryInsertKegitanPrioritas);
			echo "<li>Posting Usulan Kegiatan Prioritas Sukses!</li>";
		} catch (PDOException $e) {
			echo $e;
		}
	} else {
		echo "<li style='color:red;'>Posting Usulan Kegiatan Prioritas Gagal! Data Kosong!!</li>";
	}
}

else {
	echo "Status Perintah salah !!!";
}








/**
 * end of file
 * @author Izzul Afif <maz.izzul@gmail.com>
 * @copyright MIT
 * 
 */