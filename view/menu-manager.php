<?php
require '../boot.php';
?>

<div id="konten">
   <div data-options="region:'east',split:true" title="Menu Event Manager" style="width:250px">
   	
   </div>
    <div data-options="region:'center'">
        
        <div id="mLayout">
        	<div data-options="region:'north',collapsible:false,border:false" title="Menu Manager" style="height:54px;padding:2px 5px;background:#F4F4F4;">
        		<a id="addMenu" class="easyui-linkbutton">Add Menu</a>
				<a id="editMenu" class="easyui-linkbutton">Edit Menu</a>
				<a id="removeMenu" class="easyui-linkbutton">Remove Menu</a>
				<a id="saveMenu" class="easyui-linkbutton">Save Menu</a>
        	</div>
        	<div data-options="region:'center',border:false"></div>
        </div>
    </div>
</div>


<div id="eTolbar">
	
</div>

<script type="text/javascript">

	$("#konten").layout({fit:true});
	$("#mLayout").layout({fit:true});
	$("#x-content").panel({border:0,noheader:true,doSize:true});

	$('menuGrid').datagrid({
		title:'Menu Manager'
	});

	$('#addMenu').linkbutton({plain:true});
	$('#editMenu').linkbutton({plain:true});
	$('#removeMenu').linkbutton({plain:true});
	$('#saveMenu').linkbutton({plain:true});
</script>