<?php

# TODO:
# Make this script go through the groups tables to render the creators and viewers.

function make_stat($query,$func) {
	$res=my_mysql_query_one($query);
	if($func!=null) {
		$res=$func($res);
	}
	return $query.' = '.$res.'<br/>';
}

function create_works($type) {
	$res='';

	// collecting data from other tables...
	$types=my_mysql_query_hash('SELECT * FROM TbWkWorkType','id');
	$locations=my_mysql_query_hash('SELECT * FROM TbLcNamed','id');
	$devices=my_mysql_query_hash('SELECT * FROM TbDevice','id');
	$persons=my_mysql_query_hash('SELECT * FROM TbIdPerson','id');
	$external=my_mysql_query_hash('SELECT * FROM TbWkWorkExternal','id');
	#$works=my_mysql_query_hash('SELECT * FROM TbWkWork','id');

	// sending query
	if($type=='audio') {
		$add='TbWkWorkType.isAudio=1';
	} else {
		$add='TbWkWorkType.isVideo=1';
	}
	$query=sprintf('SELECT TbWkWork.id,TbWkWork.externalCode,TbWkWork.externalId,TbWkWork.name,TbWkWork.length,TbWkWork.size,TbWkWork.chapters,TbWkWork.typeId,TbWkWorkView.startViewDate,TbWkWorkView.endViewDate,TbWkWorkView.viewerId,TbWkWorkView.locationId,TbWkWorkView.deviceId,TbWkWorkReview.rating,TbWkWorkReview.review,TbWkWorkReview.reviewDate FROM TbWkWork,TbWkWorkType,TbWkWorkReview,TbWkWorkView where TbWkWork.typeId=TbWkWorkType.id and TbWkWorkReview.workId=TbWkWork.id and TbWkWorkView.workId=TbWkWork.id and %s order by TbWkWorkView.endViewDate',$add);
	//$query=sprintf('SELECT * FROM TbWkWork');
	$result=my_mysql_query($query);

	$fields_num=mysql_num_fields($result);

	// analyzing positions of ids in the data
	for($i=0; $i<$fields_num; $i++) {
		$field=mysql_fetch_field($result,$i);
		if($field->name=='name') {
			$nameid=$i;
		}
		if($field->name=='typeId') {
			$typeid=$i;
		}
		if($field->name=='length') {
			$lengthid=$i;
		}
		if($field->name=='size') {
			$sizeid=$i;
		}
		if($field->name=='chapters') {
			$chaptersid=$i;
		}
		if($field->name=='externalCode') {
			$externalcodeid=$i;
		}
		if($field->name=='externalId') {
			$externalidid=$i;
		}
		# view table
		if($field->name=='startViewDate') {
			$startviewdateid=$i;
		}
		if($field->name=='endViewDate') {
			$endviewdateid=$i;
		}
		if($field->name=='viewerId') {
			$viewerid=$i;
		}
		if($field->name=='locationId') {
			$locationid=$i;
		}
		if($field->name=='deviceId') {
			$deviceid=$i;
		}
		# review table
		if($field->name=='rating') {
			$ratingid=$i;
		}
		if($field->name=='review') {
			$reviewid=$i;
		}
		if($field->name=='reviewDate') {
			$reviewdateid=$i;
		}
	}
	$res.=multi_accordion_start();
	// printing table rows
	while($row=mysql_fetch_row($result))
	{
		if($row[$typeid]!=NULL) {
			$s_type=$types[$row[$typeid]]['name'];
		} else {
			$s_type=get_na_string();
		}
		if($row[$locationid]!=NULL) {
			$s_location=$locations[$row[$locationid]]['name'];
		} else {
			$s_location=get_na_string();
		}
		if($row[$deviceid]!=NULL) {
			$s_device=$devices[$row[$deviceid]]['name'];
		} else {
			$s_device=get_na_string();
		}
		if($row[$viewerid]!=NULL) {
			$s_viewer=get_full_name($persons[$row[$viewerid]]);
		} else {
			$s_viewer=get_na_string();
		}
		if($row[$sizeid]!=NULL) {
			$s_size=formatSize($row[$sizeid]);
		}
		if($row[$lengthid]!=NULL) {
			$s_length=formatTimeperiod($row[$lengthid]);
		}
		if($row[$nameid]!=NULL) {
			$header=$row[$nameid];
		} else {
			$header='No Name';
		}

		$body='';
		$body.='<ul>';
		if($row[$nameid]!=NULL) {
			$body.='<li>name: '.$row[$nameid].'</li>';
		}
		if($row[$lengthid]!=NULL) {
			$body.='<li>length: '.$s_length.'</li>';
		}
		if($row[$sizeid]!=NULL) {
			$body.='<li>size: '.$s_size.'</li>';
		}
		if($row[$chaptersid]!=NULL) {
			$body.='<li>chapters: '.$row[$chaptersid].'</li>';
		}
		if($row[$typeid]!=NULL) {
			$body.='<li>type: '.$s_type.'</li>';
		}
		# external stuff
		if($row[$externalcodeid]!=NULL && $row[$externalidid]!=NULL) {
			$externalcode=$row[$externalcodeid];
			$externalid=$row[$externalidid];
			$externalname=$external[$externalid]['name'];
			$externalidname=$external[$externalid]['idname'];
			$link=get_external_href($externalname,$externalcode);
			$link='<a href=\''.$link.'\'>'.$externalidname.': '.$externalcode.'</a>';
			$body.='<li>'.$link.'</li>';
		}
		# view stuff
		if($row[$startviewdateid]!=NULL) {
			$body.='<li>start view date: '.$row[$startviewdateid].'</li>';
		}
		if($row[$endviewdateid]!=NULL) {
			$body.='<li>end view date: '.$row[$endviewdateid].'</li>';
		}
		if($row[$viewerid]!=NULL) {
			$body.='<li>viewer: '.$s_viewer.'</li>';
		}
		if($row[$locationid]!=NULL) {
			$body.='<li>location: '.$s_location.'</li>';
		}
		if($row[$deviceid]!=NULL) {
			$body.='<li>device: '.$s_device.'</li>';
		}
		# review stuff
		if($row[$ratingid]!=NULL) {
			$body.='<li>rating: '.$row[$ratingid].'</li>';
		}
		if($row[$reviewid]!=NULL) {
			$body.='<li>review: '.$row[$reviewid].'</li>';
		}
		if($row[$reviewdateid]!=NULL) {
			$body.='<li>review date: '.$row[$reviewdateid].'</li>';
		}
		$body.='</ul>';
		$res.=multi_accordion_entry($header,$body);
	}
	assert(mysql_free_result($result));
	$res.=multi_accordion_end();
	return $res;
}

function create_stats() {
	$res='';

	# work stats
	$res.=make_stat('SELECT sum(length) from TbWkWork',formatTimeperiod);
	$res.=make_stat('SELECT sum(size) from TbWkWork',formatSize);
	$res.=make_stat('SELECT count(distinct typeId) from TbWkWork',null);

	# view stats
	$res.=make_stat('SELECT count(*) FROM TbWkWorkView',null);
	$res.=make_stat('SELECT count(distinct viewerId) FROM TbWkWorkView',null);
	$res.=make_stat('SELECT count(distinct locationId) FROM TbWkWorkView',null);
	$res.=make_stat('SELECT count(distinct deviceId) FROM TbWkWorkView',null);

	# review stats
	$res.=make_stat('SELECT avg(rating) FROM TbWkWorkReview',null);
	$res.=make_stat('SELECT count(distinct rating) FROM TbWkWorkReview',null);
	return $res;
}

?>
