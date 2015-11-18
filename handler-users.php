<?php

require_once './boot.php';

$req = isset($_REQUEST["op"]) ? $_REQUEST["op"] : "";

if ($req == "showUser") {

	$qry = DB::prepare("SELECT
		user_group.group_id,
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

	$page = isset ($_REQUEST['page']) ? (int)$_REQUEST['page'] : 0;
	$size = isset ($_REQUEST['rows']) ? (int)$_REQUEST['rows'] : 0;

	$start = (int)($page * $size) - $size;

	$qry = DB::prepare("SELECT
		user_group.*
		FROM
		user_group LIMIT :start,:size");

	$qry->bindParam(':start', $start, PDO::PARAM_INT);
	$qry->bindParam(':size', $size, PDO::PARAM_INT);
	$qry->execute();

	$qtot = DB::query("SELECT * FROM user_group");
	$res['total'] = $qtot->rowCount();
	$res['rows'] = $qry->fetchAll();

	echo json_encode($res);
	// echo json_encode($qry->fetchAll());

	exit();
}

if ($req == "groupSave") {

	$isNewRecord = isset($_REQUEST['isNewRecord']) ? (bool) $_REQUEST['isNewRecord'] : false;
	$group_name = isset($_REQUEST['group_name']) ?  $_REQUEST['group_name'] : '';
	$group_description = isset($_REQUEST['group_description']) ?  $_REQUEST['group_description'] : '';

	$qry = DB::prepare("INSERT into user_group (group_name,group_description) VALUES (:group_name,:group_description)");
	$qry->bindParam(':group_name', $group_name, PDO::PARAM_STR);
	$qry->bindParam(':group_description', $group_description, PDO::PARAM_STR);

	try {
		$qry->execute();
		$data["id"] = DB::lastInsertId();
		echo json_encode($data);

	} catch (PDOException $e) {
		echo json_encode(array(
			'isError' => true,
			'msg' => 'Error Tidak Dapat Menyimpan'
		));
	}
	exit();
}

if ($req == "groupUpdate") {

	$group_id = isset($_REQUEST['group_id']) ?  $_REQUEST['group_id'] : '';
	$group_name = isset($_REQUEST['group_name']) ?  $_REQUEST['group_name'] : '';
	$group_description = isset($_REQUEST['group_description']) ?  $_REQUEST['group_description'] : '';

	$qry = DB::prepare("UPDATE user_group SET group_name=:group_name,group_description=:group_description WHERE group_id=:group_id");
	$qry->bindParam(':group_id', $group_id, PDO::PARAM_INT);
	$qry->bindParam(':group_name', $group_name, PDO::PARAM_STR);
	$qry->bindParam(':group_description', $group_description, PDO::PARAM_STR);

	try {
		$qry->execute();
		$data["group_id"] = $group_id;
		$data["group_name"] = $group_name;
		$data["group_description"] = $group_description;
		echo json_encode($data);

	} catch (PDOException $e) {
		echo json_encode(array(
			'isError' => true,
			'msg' => 'Error Tidak Dapat Menyimpan'
		));
	}
	exit();
}

if ($req == "groupRemove") {

	$group_id = isset($_REQUEST['id']) ?  $_REQUEST['id'] : '';

	$qry = DB::prepare("DELETE FROM user_group WHERE group_id=:group_id");
	$qry->bindParam(':group_id', $group_id, PDO::PARAM_INT);
	
	try {
		$qry->execute();
		$data["success"] = true;
		echo json_encode($data);

	} catch (PDOException $e) {
		echo json_encode(array(
			'isError' => true,
			'msg' => 'Error Tidak Dapat Menyimpan'
		));
	}
	exit();
}

if ($req == "getMenu") {

	$qry = DB::prepare("SELECT
			menu.id,
			menu.parent_id,
			menu.text,
			menu.`handler`,
			menu.published,
			menu.sort_id,
			role_menu_group.role_menu_id,
			role_menu_group.group_id,
			IFNULL(role_menu_group.is_active, 0) as active
			FROM
			menu
			LEFT JOIN role_menu_group ON role_menu_group.menu_id = menu.id
			where group_id =:group_id or ISNULL(group_id)  and menu.published =1 ");
	$qry->bindParam(':group_id', $group_id, PDO::PARAM_INT);
	$qry->execute();

	
	$res = $qry->fetchAll();

	$menu = buildTree($res);

	echo json_encode($menu);

	exit();
}

if($req == "delete") {
	
}


if($req == "") {
	R::fail();
}
