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
$order=create_order_by($p_sort);
$limit=$p_start.','.$p_limit;
# form the queries
$query_data=sprintf('SELECT TbWkWork.id,TbWkWork.name,TbWkWork.length,TbWkWork.size,TbWkWork.chapters,TbWkWork.typeId,TbWkWork.languageId,UNIX_TIMESTAMP(TbWkWorkView.startViewDate) as startViewDate,UNIX_TIMESTAMP(TbWkWorkView.endViewDate) as endViewDate,TbWkWorkViewPerson.viewerId,TbWkWorkView.locationId,TbWkWorkView.deviceId,TbWkWorkView.langId,TbWkWorkReview.ratingId,TbWkWorkReview.review,UNIX_TIMESTAMP(TbWkWorkReview.reviewDate) as reviewDate FROM TbWkWorkViewPerson,TbWkWork,TbWkWorkType,TbWkWorkReview,TbWkWorkView WHERE TbWkWork.typeId=TbWkWorkType.id AND TbWkWorkViewPerson.viewId=TbWkWorkView.id AND TbWkWorkReview.workId=TbWkWork.id AND TbWkWorkView.workId=TbWkWork.id AND TbWkWorkType.name=\'video movie\' %s LIMIT %s',$order,$limit);
$query_count=sprintf('SELECT COUNT(*) FROM TbWkWorkViewPerson,TbWkWork,TbWkWorkType,TbWkWorkReview,TbWkWorkView WHERE TbWkWork.typeId=TbWkWorkType.id AND TbWkWorkViewPerson.viewId=TbWkWorkView.id AND TbWkWorkReview.workId=TbWkWork.id AND TbWkWorkView.workId=TbWkWork.id AND TbWkWorkType.name=\'video movie\'');
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
