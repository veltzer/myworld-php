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
	TbWkWorkView.id as viewId,
	TbWkWork.id as workId,
	TbWkWork.name,
	TbWkWork.length,
	TbWkWork.size,
	TbWkWork.chapters,
	TbWkWorkType.name as typeName,
	UNIX_TIMESTAMP(TbWkWorkView.endViewDate) as endViewDate,
	UNIX_TIMESTAMP(TbWkWorkView.startViewDate) as startViewDate,
	TbLocation.name as locationName,
	TbDevice.name as deviceName,
	TbWkWorkExternal.externalCode as imdbId
EOT;
$sql_frame=<<<EOT
FROM
	TbIdPerson,
	TbWkWorkViewPerson,
	TbWkWorkView,
	TbWkWork,
	TbWkWorkType,
	TbLocation,
	TbDevice,
	TbWkWorkExternal,
	TbExternalType
WHERE
	TbIdPerson.id=1 AND
	TbIdPerson.id=TbWkWorkViewPerson.viewerId AND
	TbWkWorkViewPerson.viewId=TbWkWorkView.id AND
	TbWkWorkView.locationId=TbLocation.id AND
	TbWkWorkView.deviceId=TbDevice.id AND
	TbWkWorkView.workId=TbWkWork.id AND
	TbWkWork.typeId=TbWkWorkType.id AND
	TbWkWorkType.name='video movie' AND
	TbWkWork.id=TbWkWorkExternal.workId AND
	TbWkWorkExternal.externalId=TbExternalType.id AND
	TbExternalType.name='imdb_title_id'
EOT;
/*
 * If you want to only show movies that have dates add the following
 * predicate to the SQL above:
 * TbWkWorkView.endViewDate IS NOT NULL AND
 */
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
