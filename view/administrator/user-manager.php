<?php 
	require_once '../../boot.php';
?>

<div id="konten">
	<div data-options="region:'east',split:true" title="Menu Manager" style="width:350px">
   		<div id="mLayout">
   			<div data-options="
   					region:'south',
   					collapsible:false,
   					border:false" 
				title="Event Manager"
				style="
					height:256px;">

				<div id="eventGrid"></div>        		
        	</div>
        	<div data-options="region:'center',border:false">
        		<div id="menuGrid"></div>
        	</div>
   		</div>
    </div>

	<div data-options="region:'center'">
		<div id="cLayout">
			
			<div  data-options="
	   					region:'north',
	   					collapsible:false,
	   					border:false" 
					title="Group Manager"
					style="
						height:40%;">
				
				<div id="groupGrid"></div>
			</div>
			<div  data-options="
	   					region:'center',
	   					border:false" 
					title="User Manager">
				<div id="userGrid"></div>
			</div>
		</div>
		
	</div>

   <div id="toolbar"> 
	   <a id="eSave">Simpan</a>
	</div> 

   <div id="groupToolbar"> 
	   <a id="gAdd">Tambah</a>
	   <a id="gEdit">Rubah</a>
	   <a id="gDelete">Hapus</a>
	</div> 

   <div id="userToolbar"> 
	   <a id="uAdd">Tambah</a>
	   <a id="uEdit">Rubah</a>
	   <a id="uDelete">Hapus</a>
	</div> 

</div>

<script type="text/javascript">
	
	$('#konten').layout({
		fit:true
	});		

	$('#cLayout').layout({
		fit:true
	});	

	$('#mLayout').layout({
		fit:true
	});

	$("#x-content").panel({border:0,noheader:true,doSize:true});

	$('#eventGrid').propertygrid({
	    url: 'get_data.php',
	    showGroup: false,
	    scrollbarSize: 0,
	    toolbar: '#toolbar',
	    fit:true
	});


	$('#groupGrid').datagrid({
	    url: 'handler-users.php',
	    queryParams: {
			op: 'showGroup'
		},
		rownumbers:true,
		toolbar: '#groupToolbar',
		columns:[[
	        {field:'group_id',title:'Nama Group',width:250,hidden:true},
	        {field:'group_name',title:'Nama Group',width:'38%'},
	        {field:'group_description',title:'Keterangan',width:'58%'}
	    ]],
	    singleSelect:true,
	    pagination:true,
	    remoteSort:false,
	    fit:true
	});

	$('#userGrid').datagrid({
	    url: 'handler-users.php',
	    queryParams: {
			op: 'showUser'
		},
		toolbar: '#userToolbar',
		rownumbers:true,
		columns:[[
	        {field:'user_id',title:'Nama Group',width:250,hidden:true},
	        {field:'user_name',title:'User Login',width:200},
	        {field:'group_name',title:'Nama Group',width:250},
	        {field:'real_name',title:'Nama Pengguna',width:220},
	        {field:'last_login',title:'Terakhir Login',width:175},
	        {field:'is_active',title:'Aktif',width:100,
	        	formatter: function(value,row,index){
					if (row.is_active){
						return 'Aktif';
					} else {
						return 'Non Aktif';
				}
				}
	    	}
	    ]],
	    singleSelect:true,
	    pagination:true,
	    remoteSort:false,
	    fit:true
	});

/**
 * *****************************************************************************
 * Button
 * *****************************************************************************
 */

	$('#eSave').linkbutton({
	    iconCls: 'icon-save',
		plain:true,
	});

	$('#gDelete').linkbutton({
	    iconCls: 'icon-cancel',
		plain:true,
	});

	$('#gAdd').linkbutton({
	    iconCls: 'icon-add',
		plain:true,
	});

	$('#gEdit').linkbutton({
	    iconCls: 'icon-edit',
		plain:true,
	});

	$('#uDelete').linkbutton({
	    iconCls: 'icon-cancel',
		plain:true,
	});

	$('#uAdd').linkbutton({
	    iconCls: 'icon-add',
		plain:true,
	});

	$('#uEdit').linkbutton({
	    iconCls: 'icon-edit',
		plain:true,
	});

	$('#eSave').bind('click', function(){
		var rows = $('#eventGrid').propertygrid('getData');
        $.post('testpost.php', {data:rows.rows}, function(data) {
        	console.log(data);
        });
    });




</script>