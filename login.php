<?php 
	require_once './boot.php';
?>

<!DOCTYPE html>
<html>
<head>
	<meta charset="UTF-8">
	<title>Full Layout - jQuery EasyUI Demo</title>
	<link rel="stylesheet" type="text/css" href="static/css/easyui.css">
	<script type="text/javascript" src="static/js/jquery.min.js"></script>
	<script type="text/javascript" src="static/js/jquery.easyui.min.js"></script>
	<style type="text/css">
		html {
	     height: 100%;
	   	 width: 100%;
		}
		.login {
		    height: 100%;
		    width: 100%;
		    display: table;
		    background-image:url('../static/images/wallpaper.jpg');
		    background-size: 100%;
		    margin: 0;
		    padding: 0;
		}

		.login-wrapper {
		    display: table-cell;
		    vertical-align: middle;
		    text-align: center;
		}

		.login-content {
		    display: inline-block;
		    background: #FFF;
		    padding: 10px 10px 10px 0;
		    width: 450px;
		}
		.mb20 {
			margin-bottom: 5px !important; 
		}
	</style>
</head>
	<body class="login">

		<div class="login-wrapper">
	
			<div class="login-content">
				<div class="row">

	                <div class="small-3 columns">
	                   <img src="static/images/gembok.png" style="background:transparent;">
	                </div>
					<div class="small-9 columns">
						<div class="row mb20">
		                <div class="small-3 columns">
		                    <label for="username" class="left inline">Username</label>
		                </div>
		                <div class="small-9 columns">
		                    <input type="text" id="username" name="username" class="form-control easyui-textbox">
		                </div>
						</div>
			            <div class="row mb20">
			                <div class="small-3 columns">
			                    <label for="password" class="left inline">Password</label>
			                </div>
			                <div class="small-9 columns">
			                    <input type="password" id="password" name="password" class="form-control easyui-textbox">
			                </div>
			            </div>
			            <div class="row">
			                <div class="small-3 columns">
			                    <label for="tahun" class="left inline">Tahun</label>
			                </div>
			                <div class="small-6 columns">
		                		 <input id="tahun" name="tahun" class="form-control easyui-combobox">
			                </div>
			                <div class="small-3 columns">
		                		<button type="submit" class="tiny right"><i class="fa fa-lock"></i> Login</button>
			                </div>
			            </div>
					</div>
            	</div>
			</div>
	</div>

	<script type="text/javascript">
		$('#tahun').combobox({
			url:'data.json',
			valueField: 'thn',  
        	textField: 'thn',
	       	validType:'inList["#tahun"]'
		});
	</script>
</body>
</html>