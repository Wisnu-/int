<?php 
	require_once '../boot.php';
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
   
   </div>

   <div id="toolbar"> 
	   <a id="eSave">Simpan</a>
	</div> 

</div>

<script type="text/javascript">
	
	$('#konten').layout({
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


	$('#eSave').linkbutton({
	    iconCls: 'icon-save',
		plain:true,
	});

	$('#eSave').bind('click', function(){
		var s = [];
		var rows = $('#eventGrid').propertygrid('getData');
		for(var i=0; i<rows.total; i++){
				 s[i].id = '"' + rows.rows[i].id + '"';
				 s[i].value = rows.rows[i].value;
            }

        // $.post('testpost.php', {data:s}, function(data) {
        // 	console.log(data);
        // });
        console.log(s);
    });


</script>