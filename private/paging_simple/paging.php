<?php
# initialisation
require('utils.php');
utils_init();

# get the parameters
$p_page=my_get_get('page');
$p_start=my_get_get('start');
$p_limit=my_get_get('limit');

# lets go...
$type='video movie';
switch($type) {
	case 'audio':
		$add='TbWkWorkType.isAudio=1';
		break;
	case 'video':
		$add='TbWkWorkType.isVideo=1';
		break;
	case 'text':
		$add='TbWkWorkType.isText=1';
		break;
	default:
		$add='TbWkWorkType.name=\''.$type.'\'';
		break;
}
$order='DESC';
$limit=$p_start.','.$p_limit;

$query_data=sprintf('SELECT TbWkWork.id,TbWkWork.name,TbWkWork.length,TbWkWork.size,TbWkWork.chapters,TbWkWork.typeId,TbWkWork.languageId,TbWkWorkView.startViewDate,TbWkWorkView.endViewDate,TbWkWorkViewPerson.viewerId,TbWkWorkView.locationId,TbWkWorkView.deviceId,TbWkWorkView.langId,TbWkWorkReview.ratingId,TbWkWorkReview.review,TbWkWorkReview.reviewDate FROM TbWkWorkViewPerson,TbWkWork,TbWkWorkType,TbWkWorkReview,TbWkWorkView WHERE TbWkWork.typeId=TbWkWorkType.id AND TbWkWorkViewPerson.viewId=TbWkWorkView.id AND TbWkWorkReview.workId=TbWkWork.id AND TbWkWorkView.workId=TbWkWork.id AND %s ORDER BY TbWkWorkView.endViewDate %s LIMIT %s',$add,$order,$limit);
$query_count=sprintf('SELECT COUNT(*) FROM TbWkWorkViewPerson,TbWkWork,TbWkWorkType,TbWkWorkReview,TbWkWorkView WHERE TbWkWork.typeId=TbWkWorkType.id AND TbWkWorkViewPerson.viewId=TbWkWorkView.id AND TbWkWorkReview.workId=TbWkWork.id AND TbWkWorkView.workId=TbWkWork.id AND %s',$add);
$result=my_mysql_query($query_data);
$total=my_mysql_query_one($query_count);
$response='{ total: '.$total.', views: '.my_json_encode($result).'}';

# send the response
echo $response;

# finish up
utils_finish();
?>
