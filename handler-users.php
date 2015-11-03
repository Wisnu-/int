<?php

require_once './boot.php';

$req = isset($_REQUEST["op"]) ? $_REQUEST["op"] : "";

if ($req == "showUser") {

	$qry = DB::prepare("SELECT
		user_group.group_name,
		users.user_id,
		users.user_name,
		users.real_name,
		users.is_active,
		users.last_login
		FROM
		users
		INNER JOIN user_group ON user_group.group_id = users.group_id");

	$qry->execute();

	$res['total'] = $qry->rowCount();
	$res['rows'] = $qry->fetchAll();

	echo json_encode($res);

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
