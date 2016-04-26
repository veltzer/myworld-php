<?php
require('utils.php');
utils_init();
$type=my_get_get('type');
$types=array();
$types['jsondata']=1;
$types['video_devices']=1;
$types['video_places']=1;
$types['video_viewing_year']=1;
$types['video_viewing_month']=1;
$types['ob_year']=1;
$types['ob_month']=1;
$types['bt_year']=1;
$types['bt_month']=1;
$types['study_year']=1;
$types['study_month']=1;
$types['pw_year']=1;
$types['pw_month']=1;
$types['et_year']=1;
$types['et_month']=1;
$types['dr_year']=1;
$types['dr_month']=1;
$types['bg_year']=1;
$types['bg_month']=1;
$types['upgrade_laptop_year']=1;
$types['upgrade_laptop_month']=1;
$types['upgrade_desktop_year']=1;
$types['upgrade_desktop_month']=1;
$types['upgrade_blog_year']=1;
$types['upgrade_blog_month']=1;
$types['TbIdPerson']=1;
$types['TbLocation']=1;
$types['TbRating']=1;
$types['TbClCalendar']=1;
$types['TbBsCourses']=1;
$types['TbExternalType']=1;
$types['TbWkWorkType']=1;
$types['TbWkWork']=1;
$types['TbWkWorkContribType']=1;
$types['TbDevice']=1;
$types['TbOrganization']=1;
$types['TbTdActivity']=1;
$types['TbLanguage']=1;
$types['TbIdHonorific']=1;
if(!array_key_exists($type,$types)) {
	echo 'unknown type ['.$type.']<br/>';
	echo 'allowed types are:<br/>';
	foreach($types as $k => $v) {
		echo '&nbsp;&nbsp;&nbsp;&nbsp;'.$k.'<br/>';
	}
	error('unknown type: '.$type);
}
if($type=='jsondata') {
	$response='[ { "id": "Nycticorax nycticorax", "label": "Black-crowned Night Heron", "value": "Black-crowned Night Heron" }, { "id": "Ardea purpurea", "label": "Purple Heron", "value": "Purple Heron" }, { "id": "Ardea cinerea", "label": "Grey Heron", "value": "Grey Heron" }, { "id": "Anser albifrons", "label": "Greater White-fronted Goose", "value": "Greater White-fronted Goose" }, { "id": "Anser erythropus", "label": "Lesser White-fronted Goose", "value": "Lesser White-fronted Goose" }, { "id": "Ardeola ralloides", "label": "Squacco Heron", "value": "Squacco Heron" }, { "id": "Serinus pusillus", "label": "Red-Fronted Serin", "value": "Red-Fronted Serin" }, { "id": "Butorides virescens", "label": "Green Heron", "value": "Green Heron" } ]';
}
if($type=='video_devices') {
	$query=sprintf('SELECT id,name AS label,name AS value FROM TbDevice WHERE isVideo=1 ORDER BY name');
	$result=my_mysql_query($query);
	$response=my_json_encode($result);
}
if($type=='video_places') {
	$query=sprintf('SELECT id,name AS label,name AS value FROM TbLocation WHERE isVideo=1 ORDER BY name');
	$result=my_mysql_query($query);
	$response=my_json_encode($result);
}
if($type=='video_viewing_year') {
	$query=sprintf('SELECT DATE_FORMAT(TbWkWorkView.endViewDate,"%%Y") AS year,COUNT(*) AS views FROM TbWkWorkViewPerson, TbWkWorkView, TbWkWork, TbWkWorkType WHERE TbWkWorkView.endViewDate is not NULL AND TbWkWorkView.workId=TbWkWork.id AND TbWkWorkViewPerson.viewerId=1 AND TbWkWorkViewPerson.viewId=TbWkWorkView.id AND TbWkWorkType.name="video movie" AND TbWkWorkType.id=TbWkWork.typeId GROUP BY DATE_FORMAT(TbWkWorkView.endViewDate,"%%Y") ORDER BY DATE_FORMAT(TbWkWorkView.endViewDate,"%%Y")');
	$result=my_mysql_query($query);
	$response='{ items: '.my_json_encode($result).'}';
}
if($type=='video_viewing_month') {
	$query=sprintf('SELECT DATE_FORMAT(TbWkWorkView.endViewDate,"%%m/%%y") AS month,COUNT(*) AS views FROM TbWkWorkViewPerson, TbWkWorkView, TbWkWork, TbWkWorkType WHERE TbWkWorkView.endViewDate is not NULL AND TbWkWorkView.workId=TbWkWork.id AND TbWkWorkViewPerson.viewerId=1 AND TbWkWorkViewPerson.viewId=TbWkWorkView.id AND TbWkWorkType.name="video movie" AND TbWkWorkType.id=TbWkWork.typeId GROUP BY DATE_FORMAT(TbWkWorkView.endViewDate,"%%m/%%y") ORDER BY DATE_FORMAT(TbWkWorkView.endViewDate,"%%m/%%y")');
	$result=my_mysql_query($query);
	$response='{ items: '.my_json_encode($result).'}';
}
if($type=='ob_year') {
	$query=sprintf('SELECT DATE_FORMAT(TbGraphData.dt,"%%Y") AS year,value AS views FROM TbGraphData, TbGraph WHERE TbGraphData.graphId=TbGraph.id AND TbGraph.name="openbook_progress" GROUP BY DATE_FORMAT(TbGraphData.dt,"%%Y") ORDER BY TbGraphData.dt');
	$result=my_mysql_query($query);
	$response='{ items: '.my_json_encode($result).'}';
}
if($type=='ob_month') {
	$query=sprintf('SELECT DATE_FORMAT(TbGraphData.dt,"%%m/%%Y") AS month,value AS views FROM TbGraphData, TbGraph WHERE TbGraphData.graphId=TbGraph.id AND TbGraph.name="openbook_progress" GROUP BY DATE_FORMAT(TbGraphData.dt,"%%m/%%Y") ORDER BY TbGraphData.dt');
	$result=my_mysql_query($query);
	$response='{ items: '.my_json_encode($result).'}';
}
if($type=='bt_year') {
	$query=sprintf('SELECT DATE_FORMAT(TbTdDone.end,"%%Y") AS year,COUNT(*) AS views FROM TbTdDone, TbTdActivity WHERE TbTdDone.end is not NULL AND TbTdDone.activityId=TbTdActivity.id AND TbTdDone.personId=1 AND TbTdActivity.name="blind typing training" GROUP BY DATE_FORMAT(TbTdDone.end,"%%Y") ORDER BY TbTdDone.end');
	$result=my_mysql_query($query);
	$response='{ items: '.my_json_encode($result).'}';
}
if($type=='bt_month') {
	$query=sprintf('SELECT DATE_FORMAT(TbTdDone.end,"%%m/%%Y") AS month,COUNT(*) AS views FROM TbTdDone, TbTdActivity WHERE TbTdDone.end is not NULL AND TbTdDone.activityId=TbTdActivity.id AND TbTdDone.personId=1 AND TbTdActivity.name="blind typing training" GROUP BY DATE_FORMAT(TbTdDone.end,"%%m/%%Y") ORDER BY TbTdDone.end');
	$result=my_mysql_query($query);
	$response='{ items: '.my_json_encode($result).'}';
}
if($type=='upgrade_laptop_year') {
	$query=sprintf('SELECT DATE_FORMAT(TbTdDone.end,"%%Y") AS year,COUNT(*) AS views FROM TbTdDone, TbTdActivity WHERE TbTdDone.end is not NULL AND TbTdDone.activityId=TbTdActivity.id AND TbTdDone.personId=1 AND TbTdActivity.name="upgrade laptop machine" GROUP BY DATE_FORMAT(TbTdDone.end,"%%Y") ORDER BY TbTdDone.end');
	$result=my_mysql_query($query);
	$response='{ items: '.my_json_encode($result).'}';
}
if($type=='upgrade_laptop_month') {
	$query=sprintf('SELECT DATE_FORMAT(TbTdDone.end,"%%m/%%Y") AS month,COUNT(*) AS views FROM TbTdDone, TbTdActivity WHERE TbTdDone.end is not NULL AND TbTdDone.activityId=TbTdActivity.id AND TbTdDone.personId=1 AND TbTdActivity.name="upgrade laptop machine" GROUP BY DATE_FORMAT(TbTdDone.end,"%%m/%%Y") ORDER BY TbTdDone.end');
	$result=my_mysql_query($query);
	$response='{ items: '.my_json_encode($result).'}';
}
if($type=='upgrade_desktop_year') {
	$query=sprintf('SELECT DATE_FORMAT(TbTdDone.end,"%%Y") AS year,COUNT(*) AS views FROM TbTdDone, TbTdActivity WHERE TbTdDone.end is not NULL AND TbTdDone.activityId=TbTdActivity.id AND TbTdDone.personId=1 AND TbTdActivity.name="upgrade desktop machine" GROUP BY DATE_FORMAT(TbTdDone.end,"%%Y") ORDER BY TbTdDone.end');
	$result=my_mysql_query($query);
	$response='{ items: '.my_json_encode($result).'}';
}
if($type=='upgrade_desktop_month') {
	$query=sprintf('SELECT DATE_FORMAT(TbTdDone.end,"%%m/%%Y") AS month,COUNT(*) AS views FROM TbTdDone, TbTdActivity WHERE TbTdDone.end is not NULL AND TbTdDone.activityId=TbTdActivity.id AND TbTdDone.personId=1 AND TbTdActivity.name="upgrade desktop machine" GROUP BY DATE_FORMAT(TbTdDone.end,"%%m/%%Y") ORDER BY TbTdDone.end');
	$result=my_mysql_query($query);
	$response='{ items: '.my_json_encode($result).'}';
}
if($type=='upgrade_blog_year') {
	$query=sprintf('SELECT DATE_FORMAT(TbTdDone.end,"%%Y") AS year,COUNT(*) AS views FROM TbTdDone, TbTdActivity WHERE TbTdDone.end is not NULL AND TbTdDone.activityId=TbTdActivity.id AND TbTdDone.personId=1 AND TbTdActivity.name="upgrade blog" GROUP BY DATE_FORMAT(TbTdDone.end,"%%Y") ORDER BY TbTdDone.end');
	$result=my_mysql_query($query);
	$response='{ items: '.my_json_encode($result).'}';
}
if($type=='upgrade_blog_month') {
	$query=sprintf('SELECT DATE_FORMAT(TbTdDone.end,"%%m/%%Y") AS month,COUNT(*) AS views FROM TbTdDone, TbTdActivity WHERE TbTdDone.end is not NULL AND TbTdDone.activityId=TbTdActivity.id AND TbTdDone.personId=1 AND TbTdActivity.name="upgrade blog" GROUP BY DATE_FORMAT(TbTdDone.end,"%%m/%%Y") ORDER BY TbTdDone.end');
	$result=my_mysql_query($query);
	$response='{ items: '.my_json_encode($result).'}';
}
if($type=='study_year') {
	$query=sprintf('SELECT DATE_FORMAT(TbWkWorkView.endViewDate,"%%Y") as year,COUNT(*) AS views FROM TbWkWorkViewPerson,TbWkWorkView,TbWkWork,TbWkWorkType WHERE TbWkWorkViewPerson.viewerId=1 AND TbWkWorkViewPerson.viewId=TbWkWorkView.id AND TbWkWorkView.workId=TbWkWork.id AND TbWkWork.typeId=TbWkWorkType.id AND TbWkWorkType.isStudy=1 GROUP BY DATE_FORMAT(TbWkWorkView.endViewDate,"%%Y") ORDER BY TbWkWorkView.endViewDate');
	$result=my_mysql_query($query);
	$response='{ items: '.my_json_encode($result).'}';
}
if($type=='study_month') {
	$query=sprintf('SELECT DATE_FORMAT(TbWkWorkView.endViewDate,"%%m/%%Y") AS month,COUNT(*) AS views FROM TbWkWorkViewPerson,TbWkWorkView,TbWkWork,TbWkWorkType WHERE TbWkWorkViewPerson.viewerId=1 AND TbWkWorkViewPerson.viewId=TbWkWorkView.id AND TbWkWorkView.workId=TbWkWork.id AND TbWkWork.typeId=TbWkWorkType.id AND TbWkWorkType.isStudy=1 GROUP BY DATE_FORMAT(TbWkWorkView.endViewDate,"%%m/%%Y") ORDER BY TbWkWorkView.endViewDate');
	$result=my_mysql_query($query);
	$response='{ items: '.my_json_encode($result).'}';
}
if($type=='pw_year') {
	$query=sprintf('SELECT DATE_FORMAT(TbTdDone.end,"%%Y") AS year,COUNT(*) AS views FROM TbTdDone, TbTdActivity WHERE TbTdDone.end is not NULL AND TbTdDone.activityId=TbTdActivity.id AND TbTdDone.personId=1 AND TbTdActivity.name="piano workout" GROUP BY DATE_FORMAT(TbTdDone.end,"%%Y") ORDER BY TbTdDone.end');
	$result=my_mysql_query($query);
	$response='{ items: '.my_json_encode($result).'}';
}
if($type=='pw_month') {
	$query=sprintf('SELECT DATE_FORMAT(TbTdDone.end,"%%m/%%Y") AS month,COUNT(*) AS views FROM TbTdDone, TbTdActivity WHERE TbTdDone.end is not NULL AND TbTdDone.activityId=TbTdActivity.id AND TbTdDone.personId=1 AND TbTdActivity.name="piano workout" GROUP BY DATE_FORMAT(TbTdDone.end,"%%m/%%Y") ORDER BY TbTdDone.end');
	$result=my_mysql_query($query);
	$response='{ items: '.my_json_encode($result).'}';
}
if($type=='et_year') {
	$query=sprintf('SELECT DATE_FORMAT(TbTdDone.end,"%%Y") AS year,COUNT(*) AS views FROM TbTdDone, TbTdActivity WHERE TbTdDone.end is not NULL AND TbTdDone.activityId=TbTdActivity.id AND TbTdDone.personId=1 AND TbTdActivity.name="ear training" GROUP BY DATE_FORMAT(TbTdDone.end,"%%Y") ORDER BY TbTdDone.end');
	$result=my_mysql_query($query);
	$response='{ items: '.my_json_encode($result).'}';
}
if($type=='et_month') {
	$query=sprintf('SELECT DATE_FORMAT(TbTdDone.end,"%%m/%%Y") AS month,COUNT(*) AS views FROM TbTdDone, TbTdActivity WHERE TbTdDone.end is not NULL AND TbTdDone.activityId=TbTdActivity.id AND TbTdDone.personId=1 AND TbTdActivity.name="ear training" GROUP BY DATE_FORMAT(TbTdDone.end,"%%m/%%Y") ORDER BY TbTdDone.end');
	$result=my_mysql_query($query);
	$response='{ items: '.my_json_encode($result).'}';
}
if($type=='dr_year') {
	$query=sprintf('SELECT DATE_FORMAT(TbTdDone.end,"%%Y") AS year,COUNT(*) AS views FROM TbTdDone, TbTdActivity WHERE TbTdDone.end is not NULL AND TbTdDone.activityId=TbTdActivity.id AND TbTdDone.personId=1 AND TbTdActivity.name="drum training" GROUP BY DATE_FORMAT(TbTdDone.end,"%%Y") ORDER BY TbTdDone.end');
	$result=my_mysql_query($query);
	$response='{ items: '.my_json_encode($result).'}';
}
if($type=='dr_month') {
	$query=sprintf('SELECT DATE_FORMAT(TbTdDone.end,"%%m/%%Y") AS month,COUNT(*) AS views FROM TbTdDone, TbTdActivity WHERE TbTdDone.end is not NULL AND TbTdDone.activityId=TbTdActivity.id AND TbTdDone.personId=1 AND TbTdActivity.name="drum training" GROUP BY DATE_FORMAT(TbTdDone.end,"%%m/%%Y") ORDER BY TbTdDone.end');
	$result=my_mysql_query($query);
	$response='{ items: '.my_json_encode($result).'}';
}
if($type=='bg_year') {
	$query=sprintf('SELECT DATE_FORMAT(TbTdDone.end,"%%Y") AS year,COUNT(*) AS views FROM TbTdDone, TbTdActivity WHERE TbTdDone.end is not NULL AND TbTdDone.activityId=TbTdActivity.id AND TbTdDone.personId=1 AND TbTdActivity.name="bass guitar workout" GROUP BY DATE_FORMAT(TbTdDone.end,"%%Y") ORDER BY TbTdDone.end');
	$result=my_mysql_query($query);
	$response='{ items: '.my_json_encode($result).'}';
}
if($type=='bg_month') {
	$query=sprintf('SELECT DATE_FORMAT(TbTdDone.end,"%%m/%%Y") AS month,COUNT(*) AS views FROM TbTdDone, TbTdActivity WHERE TbTdDone.end is not NULL AND TbTdDone.activityId=TbTdActivity.id AND TbTdDone.personId=1 AND TbTdActivity.name="bass guitar workout" GROUP BY DATE_FORMAT(TbTdDone.end,"%%m/%%Y") ORDER BY TbTdDone.end');
	$result=my_mysql_query($query);
	$response='{ items: '.my_json_encode($result).'}';
}
if($type=='TbIdPerson') {
	$rows=get_person_data();
	$response=json_encode($rows);
}
if($type=='TbLocation' || $type=='TbRating' || $type=='TbClCalendar' || $type=='TbBsCourses' || $type=='TbExternalType' || $type=='TbWkWorkType' || $type=='TbWkWork' || $type=='TbWkWorkContribType' || $type=='TbDevice' || $type=='TbOrganization' || $type=='TbTdActivity' || $type=='TbLanguage' || $type=='TbIdHonorific') {
	$query=sprintf('SELECT id,name AS label,name AS value FROM %s WHERE name IS NOT NULL ORDER BY name',
		$type
	);
	$result=my_mysql_query($query);
	$response=my_json_encode($result);
}
echo $response;
utils_finish();
?>
