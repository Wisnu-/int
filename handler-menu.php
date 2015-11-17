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

if ($req == "showGroup") {

	$qry = DB::prepare("SELECT
		user_group.*
		FROM
		user_group");

	$qry->execute();

	$res['total'] = $qry->rowCount();
	$res['rows'] = $qry->fetchAll();

	echo json_encode($res);

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
            <input type="text" id="titleMenu" name="titleMenu" class="form-control easyui-textbox">
        </div>
        <div class="small-2 columns">
        </div>
	</div>
	<div class="row mb20">
        <div class="small-3 columns">
            <label for="fileHandler" class="left inline">Handler</label>
        </div>
        <div class="small-7 columns">
            <input type="text" id="fileHandler" name="fileHandler" class="form-control easyui-textbox">
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
	    	$("#fileHandler").textbox('setValue', dt);
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


if($req == "") {
	R::fail();
}
