<?php 
	require './boot.php';
	
	if (! isset($_SESSION["auth"]) || $_SESSION["auth"] == '') {
		//R::to('login');
	}

?>

<!DOCTYPE html>
<html>
<head>
	<meta charset="UTF-8">
	<title>Full Layout - jQuery EasyUI Demo</title>
	<link rel="stylesheet" type="text/css" href="static/css/easyui.css">
	<script type="text/javascript" src="static/js/jquery.min.js"></script>
	<script type="text/javascript" src="static/js/jquery.easyui.min.js"></script>
</head>
<body class="easyui-layout">
	<div data-options="region:'north',border:false" style="height:60px;background:#B3DFDA;padding:10px">north region</div>
<div data-options="region:'west',split:true,border:false"  style="width:230px;padding:0px;">
			<div class="easyui-layout" data-options="fit:true">
                <div id="x-menu"data-options="region:'center',border:true,collapsible:false,iconCls:'icon-app-menu'" title="Main Menu">
				 <ul id="x-menu-tree">
				</ul>
				
				</div>
                
                <div id="x-status" data-options="region:'south',border:true" style="overflow:hidden;height:25px;padding:3px;">
					<span id="x-status-left" class="left online">
						<a style='text-decoration:none' href="javascript:logout()">Log Out</a>
					</span>
					<span id="x-status-right" class="right">
						
					</span>
				</div>
            </div>
		</div>
        <div id="x-content" class="bg-kotak" data-options="region:'center'" title="Selamat Datang di SIMONEVA - Sistem Informasi Monitoring & Evaluasi">

        </div>
</body>
</html>