<?php
require('utils.php');
utils_init();
$type=my_get_get('type');
$handled=0;
if($type=='video_devices') {
	$query=sprintf('select id,name as label,name as value from TbDevice where isVideo=1');
	$result=my_mysql_query($query);
	$response=my_json_encode($result);
	$handled=1;
}
if($type=='TbIdPerson') {
	//error("fake error");
	$rows=get_person_data();
	$response=json_encode($rows);
	$handled=1;
}
if($type=='TbLcNamed' || $type=='TbRating') {
	$query=sprintf('select id,name as label,name as value from %s',
		mysql_real_escape_string($type)
	);
	$result=my_mysql_query($query);
	$response=my_json_encode($result);
	$handled=1;
}
if($handled) {
	echo $response;
} else {
	error('unknown type: '.$type);
}
utils_finish();
?>
