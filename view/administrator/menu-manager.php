<?php
require '../../boot.php';
?>

<div id="konten">
   <div data-options="region:'east',split:true" title="Menu Event Manager" style="width:250px">
   		<div id="menuEventGrid">
   			
   		</div>
   		<div id="toolbarMenuEventGrid" style="padding:2px 5px;background:#F4F4F4;">
   			<a id="addEventMenu" class="easyui-linkbutton">Add</a>
   			<a id="saveEventMenu" class="easyui-linkbutton">Save</a>
			<a id="removeEventMenu" class="easyui-linkbutton">Delete</a>
   		</div>
   </div>
    <div data-options="region:'center'">
        
        <div id="mLayout">
        	<div data-options="region:'north',collapsible:false,border:false" title="Menu Manager" style="height:54px;padding:2px 5px;background:#F4F4F4;">
        		<a id="addMenu" class="easyui-linkbutton">Add Menu</a>
				<a id="editMenu" class="easyui-linkbutton">Edit Menu</a>
				<a id="removeMenu" class="easyui-linkbutton">Remove Menu</a>
				<a id="saveMenu" class="easyui-linkbutton">Publish Checked Menu</a>
        	</div>
        	<div data-options="region:'center',border:false" style="padding:0 10px;">
        		<ul id="mTree"></ul>
        	</div>
        </div>
    </div>
</div>


<div id="eTolbar">
	
</div>

<script type="text/javascript">

	$("#konten").layout({fit:true});
	$("#mLayout").layout({fit:true});
	$("#x-content").panel({border:0,noheader:true,doSize:true});

	$('#menuEventGrid').edatagrid({
		toolbar:'#toolbarMenuEventGrid',
		idField:'id',
		columns:[[
	        {field:'id',title:'id',width:250,hidden:true},
	        {field:'event_name',title:'Nama Event',width:200,editor:'text'}
	    ]],
	    singleSelect:true,
	    pagination:false,
	    remoteSort:false,
	    fit:true,
	    url:'handler-menu.php',
	    queryParams : {
	    	op : 'showEvent'
	    }
	});

	$('#addEventMenu').linkbutton({plain:true});
	$('#removeEventMenu').linkbutton({plain:true});
	$('#saveEventMenu').linkbutton({plain:true});
	$('#addMenu').linkbutton({plain:true});
	$('#editMenu').linkbutton({plain:true});
	$('#removeMenu').linkbutton({plain:true});
	$('#saveMenu').linkbutton({plain:true});




	$("#mTree").tree({
		url:'handler-menu.php',
		queryParams : {
			op: 'showList'
		},
		lines:true,
		dnd:true,
		checkbox:true,
		cascadeCheck:false,
		onClick: function(node) {
			$("#menuEventGrid").edatagrid({
				queryParams : {
					op:'showEvent',
					id:node.id
				},
			  //   onSuccess: function () {
			  //   	$("#menuEventGrid").edatagrid({
					// 	queryParams : {
					// 		op:'showEvent',
					// 		id:node.id
					// 	}
					// });
			  //   },
				saveUrl: 'handler-menu.php?op=eventSave&menu_id=' + node.id,
			    destroyUrl: 'handler-menu.php?op=eventRemove&menu_id=' + + node.id,
			    updateUrl: 'handler-menu.php?op=eventUpdate&menu_id=' + node.id
			});
		}

	});

	$("#addEventMenu").bind('click', function() {
		var idTree = $('#mTree').tree('getSelected');
		if(idTree) {
			$('#menuEventGrid').edatagrid('addRow');
		}
	});

	$("#saveEventMenu").bind('click', function() {
		var idTree = $('#mTree').tree('getSelected');
		if(idTree) {
			$('#menuEventGrid').edatagrid('saveRow');
		}
	});

	$("#removeEventMenu").bind('click', function() {
		var idTree = $('#mTree').tree('getSelected');
		if(idTree) {
			$('#menuEventGrid').edatagrid('destroyRow');
		}
	});

	$("#removeMenu").bind('click', function() {
		var rows = $('#mTree').tree('getSelected');
		if(rows) {
			$.post('handler-menu.php', {op: 'deleteMenu', id:rows.id}, function(data) {
				if(data =='sukses') {
					$("#mTree").tree({queryParams:{op: 'showList'}})
				}
			});
		}
	});

	$('#saveMenu').bind('click', function(){
		var rows = $('#mTree').tree('getRoots');
		var dataJson = JSON.stringify(rows);
		$.post('handler-menu.php', {op:'publishMenu',data:dataJson}, function(data) {
        	if (data == 'sukses') {
				$("#mTree").tree({queryParams:{op: 'showList'}})
			}
        });
    });


	$('#addMenu').bind('click', function(){
		$("#x-dialog").dialog({
			width: 500,
			href: 'handler-menu.php?op=formAddMenu',
			height:170,
			title:'Add Menu',
			modal: true,
			buttons: [{
                text:'Simpan',
                iconCls:'icon-add',
                handler:function(){
                    var handlerData = $("#fileHandler").val();
                    var titleData = $("#titleMenu").val();

                    if (titleData !='') {
                    	$.post('handler-menu.php', {op: 'addMenu', 
                    			handler:handlerData,title:titleData}, 
                    			function(data) {
                    				if (data == 'sukses') {
                    					$("#x-dialog").dialog('close');
                    					$("#mTree").tree({queryParams:{op: 'showList'}})
                    				}
                    	});
                    }
                }
            },{
                text:'Cancel',
                handler:function(){
                    $("#x-dialog").dialog('close');
                }
            }]
		}).show();
    });

	$('#editMenu').bind('click', function(){
		var rows = $('#mTree').tree('getSelected');
		if(rows) {
			$("#x-dialog").dialog({
				width: 500,
				href: 'handler-menu.php?op=formEditMenu&id=' + rows.id,
				height:170,
				title:'Edit Menu',
				modal: true,
				buttons: [{
	                text:'Simpan',
	                iconCls:'icon-save',
	                handler:function(){
	                    var handlerData = $("#fileHandler").val();
	                    var titleData = $("#titleMenu").val();

	                    if (titleData !='') {
	                    	$.post('handler-menu.php', {op: 'editMenu', 
	                    			handler:handlerData,title:titleData,id:rows.id}, 
	                    			function(data) {
	                    				if (data == 'sukses') {
	                    					$("#x-dialog").dialog('close');
	                    					$("#mTree").tree({queryParams:{op: 'showList'}})
	                    				}
	                    	});
	                    }
	                }
	            },{
	                text:'Cancel',
	                handler:function(){
	                    $("#x-dialog").dialog('close');
	                }
	            }]
			}).show();
		}
    });

</script>