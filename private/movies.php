<?php
# initialisation
require('utils.php');
utils_init();
# get the parameters
$p_page=my_get_get('page');
$p_start=my_get_get('start');
$p_limit=my_get_get('limit');
$p_sort=json_decode(my_get_get('sort'));
# prepare to create the queries...
$sql_order=create_order_by($p_sort);
$sql_limit='LIMIT '.$p_start.','.$p_limit;
# form the queries
$sql_select=<<<EOT
SELECT
	TbWkWork.id,
	TbWkWork.name,
	TbWkWork.length,
	TbWkWork.size,
	TbWkWork.chapters,
	TbWkWorkType.name as typeName,
	UNIX_TIMESTAMP(TbWkWorkView.startViewDate) as startViewDate,
	UNIX_TIMESTAMP(TbWkWorkView.endViewDate) as endViewDate,
	TbLocation.name as locationName,
	TbDevice.name as deviceName,
	TbRating.name as ratingName,
	TbWkWorkReview.review,
	UNIX_TIMESTAMP(TbWkWorkReview.reviewDate) as reviewDate,
	TbWkWorkExternal.externalCode
EOT;
$sql_frame=<<<EOT
FROM
	TbIdPerson,
	TbWkWorkViewPerson,
	TbWkWorkView,
	TbWkWork,
	TbWkWorkType,
	TbLocation,
	TbRating,
	TbDevice,
	TbWkWorkExternal,
	TbExternalType,
	TbWkWorkReview
WHERE
	TbIdPerson.id=1 AND
	TbIdPerson.id=TbWkWorkViewPerson.viewerId AND
	TbWkWorkViewPerson.viewId=TbWkWorkView.id AND
	TbWkWorkView.locationId=TbLocation.id AND
	TbWkWorkView.deviceId=TbDevice.id AND
	TbWkWorkView.endViewDate IS NOT NULL AND
	TbWkWorkView.workId=TbWkWork.id AND
	TbWkWork.typeId=TbWkWorkType.id AND
	TbWkWork.id=TbWkWorkExternal.workId AND
	TbWkWork.id=TbWkWorkReview.workId AND
	TbWkWorkType.name='video movie' AND
	TbWkWorkExternal.externalId=TbExternalType.id AND
	TbExternalType.name='imdb_title' AND
	TbWkWorkReview.ratingId=TbRating.id
EOT;
$query_data=sprintf('%s %s %s %s',$sql_select,$sql_frame,$sql_order,$sql_limit);
$query_count=sprintf('%s %s','SELECT COUNT(*)',$sql_frame);
# get the data...
$result_obj=my_mysql_query($query_data);
$result_rows=my_get_rows($result_obj);
$total=my_mysql_query_one($query_count);
# form the response
$response=array('total'=>$total,'views'=>$result_rows);
# send the response
echo json_encode($response);
# finish up
utils_finish();
?>
