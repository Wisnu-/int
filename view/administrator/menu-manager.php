<?php
require '../../boot.php';
?>

<div id="konten">
   <div data-options="region:'east',split:true" title="Menu Event Manager" style="width:250px">
   		<div id="menuEventGrid">
   			
   		</div>
   		<div id="toolbarMenuEventGrid" style="padding:2px 5px;background:#F4F4F4;">
   			<a id="addEventMenu" class="easyui-linkbutton">Add Event</a>
			<a id="editEventMenu" class="easyui-linkbutton">Edit Event</a>
   		</div>
   </div>
    <div data-options="region:'center'">
        
        <div id="mLayout">
        	<div data-options="region:'north',collapsible:false,border:false" title="Menu Manager" style="height:54px;padding:2px 5px;background:#F4F4F4;">
        		<a id="addMenu" class="easyui-linkbutton">Add Menu</a>
				<a id="editMenu" class="easyui-linkbutton">Edit Menu</a>
				<a id="removeMenu" class="easyui-linkbutton">Remove Menu</a>
				<a id="saveMenu" class="easyui-linkbutton">Save Menu</a>
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

	$('#menuEventGrid').datagrid({
		toolbar:'#toolbarMenuEventGrid',
		columns:[[
	        {field:'id',title:'Nama Group',width:250,hidden:true},
	        {field:'event_name',title:'Nama Event',width:200}
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
	$('#editEventMenu').linkbutton({plain:true});
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
			// console.log(JSON.stringify(node.id));
			$("#menuEventGrid").datagrid({
				queryParams : {
					op:'showEvent',
					id:node.id
				}
			})
		}

	});


	$('#saveMenu').bind('click', function(){
		var rows = $('#mTree').tree('getRoots');
		var dataJson = JSON.stringify(rows);
		$.post('ok.php', {data:dataJson}, function(data) {
        	console.log(data);
        	// console.log(JSON.stringify(rows));
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
                    alert('ok');
                }
            },{
                text:'Cancel',
                handler:function(){
                    $("#x-dialog").dialog('close');
                }
            }]
		}).show();
    });

</script>