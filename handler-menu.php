<?php

require_once './boot.php';

$req = isset($_REQUEST["op"]) ? $_REQUEST["op"] : "";

if ($req == "showList") {

	$qry = DB::prepare("SELECT
		*
		FROM
		menu
		WHERE published = 1
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

if($req == "delete") {
	
}


if($req == "") {
	R::fail();
}
