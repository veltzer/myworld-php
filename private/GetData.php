<?php
require('utils.php');
utils_init();
$type=my_get_get('type');
$handled=0;
if($type=='jsondata') {
	$response='[ { "id": "Nycticorax nycticorax", "label": "Black-crowned Night Heron", "value": "Black-crowned Night Heron" }, { "id": "Ardea purpurea", "label": "Purple Heron", "value": "Purple Heron" }, { "id": "Ardea cinerea", "label": "Grey Heron", "value": "Grey Heron" }, { "id": "Anser albifrons", "label": "Greater White-fronted Goose", "value": "Greater White-fronted Goose" }, { "id": "Anser erythropus", "label": "Lesser White-fronted Goose", "value": "Lesser White-fronted Goose" }, { "id": "Ardeola ralloides", "label": "Squacco Heron", "value": "Squacco Heron" }, { "id": "Serinus pusillus", "label": "Red-Fronted Serin", "value": "Red-Fronted Serin" }, { "id": "Butorides virescens", "label": "Green Heron", "value": "Green Heron" } ]';
	$handled=1;
}
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
if($type=='TbLcNamed' || $type=='TbRating' || $type=='TbClCalendar' || $type=='TbBsCompanies' || $type=='TbBsCourses') {
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
