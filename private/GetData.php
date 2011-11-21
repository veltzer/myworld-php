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
	$query=sprintf('SELECT id,name AS label,name AS value FROM TbDevice WHERE isVideo=1 ORDER BY name');
	$result=my_mysql_query($query);
	$response=my_json_encode($result);
	$handled=1;
}
if($type=='video_places') {
	$query=sprintf('SELECT id,name AS label,name AS value FROM TbLocation WHERE isVideo=1 ORDER BY name');
	$result=my_mysql_query($query);
	$response=my_json_encode($result);
	$handled=1;
}
if($type=='video_viewing_month') {
	$query=sprintf('SELECT DATE_FORMAT(TbWkWorkView.endViewDate,"%%m-%%y") AS id,count(*) AS value FROM TbWkWorkViewPerson, TbWkWorkView, TbWkWork, TbWkWorkType WHERE TbWkWorkView.workId=TbWkWork.id AND TbWkWorkViewPerson.viewerId=1 AND TbWkWorkViewPerson.viewId=TbWkWorkView.id AND TbWkWorkType.name="video movie" AND TbWkWorkType.id=TbWkWork.typeId GROUP BY DATE_FORMAT(TbWkWorkView.endViewDate,"%%m-%%y") ORDER BY TbWkWorkView.endViewDate');
	$result=my_mysql_query($query);
	# the wrapping is for dojo charting to work...
	$response='{ label: "id", identifier: "id", items: '.my_json_encode($result).'}';
	$handled=1;
}
if($type=='video_viewing_year') {
	$query=sprintf('SELECT DATE_FORMAT(TbWkWorkView.endViewDate,"%%y") AS id,count(*) AS value FROM TbWkWorkViewPerson, TbWkWorkView, TbWkWork, TbWkWorkType WHERE TbWkWorkView.workId=TbWkWork.id AND TbWkWorkViewPerson.viewerId=1 AND TbWkWorkViewPerson.viewId=TbWkWorkView.id AND TbWkWorkType.name="video movie" AND TbWkWorkType.id=TbWkWork.typeId GROUP BY DATE_FORMAT(TbWkWorkView.endViewDate,"%%y") ORDER BY TbWkWorkView.endViewDate');
	$result=my_mysql_query($query);
	# the wrapping is for dojo charting to work...
	$response='{ label: "id", identifier: "id", items: '.my_json_encode($result).'}';
	$handled=1;
}
if($type=='openbook_progress') {
	$query=sprintf('SELECT DATE_FORMAT(TbGraphData.dt,"%%d%%m%%y") AS id,TbGraphData.value AS value FROM TbGraph, TbGraphData WHERE TbGraph.name=\'openbook_progress\' AND TbGraph.id=TbGraphData.graphId ORDER BY TbGraphData.dt');
	$result=my_mysql_query($query);
	# the wrapping is for dojo charting to work...
	$response='{ label: "id", identifier: "id", items: '.my_json_encode($result).'}';
	$handled=1;
}
if($type=='TbIdPerson') {
	$rows=get_person_data();
	$response=json_encode($rows);
	$handled=1;
}
if($type=='TbLocation' || $type=='TbRating' || $type=='TbClCalendar' || $type=='TbBsCourses' || $type=='TbExternalType' || $type=='TbWkWorkType' || $type=='TbWkWork' || $type=='TbWkWorkContribType' || $type=='TbDevice' || $type=='TbOrganization' || $type=='TbTdActivity' || $type=='TbLanguage' || $type=='TbIdHonorific') {
	$query=sprintf('SELECT id,name AS label,name AS value FROM %s ORDER BY name',
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
