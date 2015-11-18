<?php

require_once './boot.php';

$req = isset($_REQUEST["op"]) ? $_REQUEST["op"] : "";

if ($req == "showList") {

	$qry = DB::prepare("SELECT
			menu.id,
			menu.parent_id,
			menu.text,
			menu.`handler`,
			menu.published
		FROM
		menu
		ORDER BY sort_id ASC");

	$qry->execute();

	
	$res = $qry->fetchAll();

	$menu = buildTree($res);

	echo json_encode($menu);

	exit();
}

if ($req == "showEvent") {
	$id = isset($_REQUEST['id']) ? (int) $_REQUEST['id'] : 0;

	$qry = DB::prepare("SELECT
		menu_event.id,
		menu_event.event_name
		FROM
		menu_event where menu_id=:id");
	$qry->bindParam(':id', $id, PDO::PARAM_INT);
	$qry->execute();
	$res['rows'] = $qry->fetchAll();

	echo json_encode($res);

	exit();
}

if ($req == "formAddMenu") {
	
?>
<div style="padding:15px;">
	<div class="row mb20">
        <div class="small-3 columns">
            <label for="titleMenu" class="left inline">Title</label>
        </div>
        <div class="small-7 columns">
            <input type="text" id="titleMenu" name="titleMenu" class="form-control">
        </div>
        <div class="small-2 columns">
        </div>
	</div>
	<div class="row mb20">
        <div class="small-3 columns">
            <label for="fileHandler" class="left inline">Handler</label>
        </div>
        <div class="small-7 columns">
            <input type="text" id="fileHandler" name="fileHandler" class="form-control">
        </div>
        <div class="small-2 columns">
           <button id="pilihFile" type="chose" class="tiny right"><i class="fa fa-lock"></i> Pilih</button>
        </div>
	</div>
</div>
<script type="text/javascript">
	$("#pilihFile").on('click', function(e) {
		$("#x-dialog2").dialog({
			title:'Pilih File Handler',
			width: 400,
			height: 300,
			href: 'handler-menu.php?op=gridChooseFile',
			modal:true
		}).show();

	});
</script>

<?php

	exit();
}
if ($req == "formEditMenu") {
	$id = isset($_REQUEST['id']) ? (int) $_REQUEST['id'] : 0;

	$qry = DB::prepare("SELECT `text`, handler from menu where id=:id");
	$qry->bindParam(':id', $id, PDO::PARAM_INT);
	$qry->execute();

	$jml = $qry->rowCount();

	if ($jml != 1) {
		echo "gagal";
		exit;
	}

	$dt = $qry->fetch();
	$text = $dt["text"];
	$handler = $dt["handler"];
?>
<div style="padding:15px;">
	<div class="row mb20">
        <div class="small-3 columns">
            <label for="titleMenu" class="left inline">Title</label>
        </div>
        <div class="small-7 columns">
            <input value="<?php echo $text; ?>" type="text" id="titleMenu" name="titleMenu" class="form-control">
        </div>
        <div class="small-2 columns">
        </div>
	</div>
	<div class="row mb20">
        <div class="small-3 columns">
            <label for="fileHandler" class="left inline">Handler</label>
        </div>
        <div class="small-7 columns">
            <input value="<?php echo $handler; ?>" type="text" id="fileHandler" name="fileHandler" class="form-control">
        </div>
        <div class="small-2 columns">
           <button id="pilihFile" type="chose" class="tiny right"><i class="fa fa-lock"></i> Pilih</button>
        </div>
	</div>
</div>
<script type="text/javascript">

	$("#pilihFile").on('click', function(e) {
		$("#x-dialog2").dialog({
			title:'Pilih File Handler',
			width: 400,
			height: 300,
			href: 'handler-menu.php?op=gridChooseFile',
			modal:true
		}).show();
	});
</script>

<?php

	exit();
}

if ($req == "gridChooseFile") {
	
?>
<div id="gridChooseFile">
</div>
<script type="text/javascript">
	$("#gridChooseFile").datagrid({
		// title:'None',
		fit:true,
		url:'handler-menu.php',
		queryParams : {
			op:'populateHandler'
		},
		columns:[[
	        {field:'name',title:'Handler',width:200}
	    ]],
	    singleSelect:true,
	    onDblClickRow: function(i,row){
	    	var dt = row.name;
	    	$("#fileHandler").val(dt);
	    	$("#x-dialog2").dialog('close');

	    }
	});
</script>

<?php

	exit();
}

if($req == "delete") {
	
}

if($req == "populateHandler") {
	
	$path = $ROOT . '/view/*.php';
	
	$file = glob($path);
	$res = [];
	foreach ( $file as $filename)
	{ 
		$data['name'] = basename($filename);
		array_push($res, $data); 
   	} 

   	echo json_encode($res);
}

if($req == "addMenu") {
	
	$title = isset($_POST['title']) ? $_POST['title'] : '';
	$handler = isset($_POST['handler']) ? $_POST['handler'] : '';

	$qry = DB::prepare("insert into menu(sort_id,text, parent_id,handler)
		SELECT max(sort_id)+1, :title, 0, :handler from menu where parent_id=0");
	$qry->bindParam(':title', $title, PDO::PARAM_STR);
	$qry->bindParam(':handler', $handler, PDO::PARAM_STR);

	try {
		$qry->execute();
		echo "sukses";
	} catch (PDOException $e) {
		echo $e->getMessage();
	}
}

if($req == "publishMenu") {
	
	$data = isset($_POST['data']) ? $_POST['data'] : [];

	if(empty($data)) {
		echo "Gagal";
		exit();
	}
	$arrData = json_decode($data,true);
	
	$i = 0;

	foreach ($arrData as $d) {
		$i++;
		$d["sort_id"] = $i;
		$d["parent"] = 0;
		$d["_checked"] = (isset($d["_checked"])) ? $d["_checked"] : 0;
		$e = publishMenu($d);
		

	}
	echo "sukses";

}

if($req == "deleteMenu") {
	
	$id = isset($_POST['id']) ? (int) $_POST['id'] : 0;

	if($id != 0 || $id > 0) {

		$qry = DB::prepare("DELETE from menu where id=:id or parent_id = :id");
		$qry->bindParam(':id', $id, PDO::PARAM_INT);

		try {
			$qry->execute();
			echo "sukses";
		} catch (PDOException $e) {
			echo "Gagal " . $e->getMessage();
		}
	}
	

}

if($req == "editMenu") {
	
	$id = isset($_POST['id']) ? (int) $_POST['id'] : 0;
	$title = isset($_POST['title']) ? $_POST['title'] : '';
	$handler = isset($_POST['handler']) ? $_POST['handler'] : '';

	if($id != 0 || $id > 0) {

		$qry = DB::prepare("UPDATE menu SET `text` =:title, handler=:handler where id=:id");
		$qry->bindParam(':id', $id, PDO::PARAM_INT);
		$qry->bindParam(':title', $title, PDO::PARAM_STR);
		$qry->bindParam(':handler', $handler, PDO::PARAM_STR);

		try {
			$qry->execute();
			echo "sukses";
		} catch (PDOException $e) {
			echo "Gagal " . $e->getMessage();
		}
	}
	

}

if($req == "eventSave") {
	
	$id = isset($_REQUEST['id']) ? (int) $_REQUEST['id'] : 0;
	$menu_id = isset($_REQUEST['menu_id']) ? (int) $_REQUEST['menu_id'] : 0;
	$event_name = isset($_REQUEST['event_name']) ? $_REQUEST['event_name'] : '';
	$isNewRecord = isset($_REQUEST['isNewRecord']) ? (bool) $_REQUEST['isNewRecord'] : false;

	if($isNewRecord) {
		$qry = DB::prepare("INSERT into menu_event (menu_id,event_name) values(:menu_id,:event_name)");
		$qry->bindParam(':menu_id', $menu_id, PDO::PARAM_INT);
		$qry->bindParam(':event_name', $event_name, PDO::PARAM_STR);

		try {
			$qry->execute();
			$data["id"] = DB::lastInsertId();
			echo json_encode($data);
		} catch (PDOException $e) {
			echo json_encode(array(
				'isError' => true,
				'msg' => 'Erorr'
			));
		}
	}

}

if($req == "eventUpdate") {
	
	$id = isset($_REQUEST['id']) ? (int) $_REQUEST['id'] : 0;
	$event_name = isset($_REQUEST['event_name']) ? $_REQUEST['event_name'] : '';


	$qry = DB::prepare("UPDATE menu_event set event_name=:event_name where id=:id");
	$qry->bindParam(':id', $id, PDO::PARAM_INT);
	$qry->bindParam(':event_name', $event_name, PDO::PARAM_STR);

	try {
		$qry->execute();

		echo json_encode(["success"=>true]);
	} catch (PDOException $e) {
		echo json_encode(array(
			'isError' => true,
			'msg' => 'Erorr'
		));
	}


}

if($req == "eventRemove") {
	
	$id = isset($_REQUEST['id']) ? (int) $_REQUEST['id'] : 0;
	$menu_id = isset($_REQUEST['menu_id']) ? (int) $_REQUEST['menu_id'] : 0;

	$qry = DB::prepare("DELETE FROM menu_event where id=:id");
	$qry->bindParam(':id', $id, PDO::PARAM_INT);

	try {
		$qry->execute();

	} catch (PDOException $e) {
		echo json_encode(array(
			'isError' => true,
			'msg' => 'Erorr'
		));
		exit();
	}

	echo json_encode(["success"=>true]);
}


if($req == "") {
	R::fail();
}
