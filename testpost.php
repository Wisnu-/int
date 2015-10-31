<?php 
$data = $_REQUEST['data'];

$dt = json_decode($data);
exit($dt);
foreach ($dt as $k => $v) {
	echo $key . " => " . $v . "<br />";
}
?>