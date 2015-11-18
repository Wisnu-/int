<?php 
	require_once '../../boot.php';
?>

<div id="konten">
	<div data-options="region:'east',split:true" style="width:350px">
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
	        	<div id="groupMenuLayout">
		        	<div data-options="region:'north',collapsible:false,border:false" title="Menu Manager" style="height:54px;padding:2px 5px;background:#F4F4F4;">
		        		<a id="checkAllMenu" class="easyui-linkbutton">Check All</a>
						<a id="unCheckAll" class="easyui-linkbutton">Check None</a>
						<a id="saveGroupMenu" class="easyui-linkbutton">Save Menu</a>
		        	</div>
		        	<div data-options="region:'center',border:false" style="padding:0 10px;">
		        		<ul id="groupMenuTree"></ul>
		        	</div>
		        </div>
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
	   <a id="gSave">Simpan</a>
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

	$('#groupMenuLayout').layout({
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

	$("#groupMenuTree").tree({
		url : 'handler-users.php',
		queryParams: {
			op:'getMenu'
		}
	})

	$('#groupGrid').edatagrid({
	    url: 'handler-users.php',
	    queryParams: {
			op: 'showGroup'
		},
		saveUrl: 'handler-users.php?op=groupSave',
	    destroyUrl: 'handler-users.php?op=groupRemove',
	    updateUrl: 'handler-users.php?op=groupUpdate',
		idField:'group_id',
		rownumbers:true,
		toolbar: '#groupToolbar',
		columns:[[
	        {field:'group_id',title:'Nama Group',width:250,hidden:true},
	        {field:'group_name',title:'Nama Group',width:'38%',
	        	editor:{
	        		type:'text',
	        		options: {
	        			required:true
	        		}
	    		}
	    	},
	        {field:'group_description',title:'Keterangan',width:'58%',
	        	editor:{
	        		type:'text',
	        		options: {
	        			required:true
	        		}
	    		}
	    	}
	    ]],
	    singleSelect:true,
	    pagination:true,
	    remoteSort:false,
	    fit:true,
	    onClickRow:function(index,row) {
			$("#groupMenuTree").tree({
				url : 'handler-users.php',
				queryParams: {
					op:'getMenu',
					group_id : row.group_id
				},
				lines:true,
				checkbox:true,
				cascadeCheck:false,
			})
	    }
	});

	$('#userGrid').edatagrid({
	    url: 'handler-users.php',
	    queryParams: {
			op: 'showUser'
		},
		saveUrl: 'handler-users.php?op=userSave',
	    destroyUrl: 'handler-users.php?op=userRemove',
	    updateUrl: 'handler-users.php?op=userUpdate',
		toolbar: '#userToolbar',
		rownumbers:true,
		idField:'user_id',
		columns:[[
	        {field:'user_id',title:'Nama Group',width:250,hidden:true},
	        {field:'user_name',title:'User Login',width:200,editor:'text'},
	        {field:'group_id',title:'Nama Group',width:250},
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

	$('#gSave').linkbutton({
	    iconCls: 'icon-save',
		plain:true,
	});

	$('#gAdd').linkbutton({
	    iconCls: 'icon-add',
		plain:true,
	});

	$('#checkAllMenu').linkbutton({
		plain:true
	});

	$('#unCheckAll').linkbutton({
		plain:true
	});

	
	$('#saveGroupMenu').linkbutton({
		plain:true
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

	$("#gSave").bind('click', function() {
		$('#groupGrid').edatagrid('saveRow');
	});

	$("#gAdd").bind('click', function() {
		$('#groupGrid').edatagrid('addRow');
	});

	$("#gDelete").bind('click', function() {
		$('#groupGrid').edatagrid('destroyRow');
	});



</script>