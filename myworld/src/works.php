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
	$contrib=my_mysql_query_hash('SELECT * FROM TbWkWorkContrib','id');
	$types=my_mysql_query_hash('SELECT * FROM TbWkWorkType','id');
	$locations=my_mysql_query_hash('SELECT * FROM TbLcNamed','id');
	$devices=my_mysql_query_hash('SELECT * FROM TbDevice','id');
	$persons=my_mysql_query_hash('SELECT * FROM TbIdPerson','id');
	$organizations=my_mysql_query_hash('SELECT * FROM TbOrganization','id');
	$external=my_mysql_query_hash('SELECT * FROM TbExternalType','id');
	$workexternal=my_mysql_query_hash('SELECT * FROM TbWkWorkExternal','id');
	$contribtype=my_mysql_query_hash('SELECT * FROM TbWkWorkContribType','id');
	#$works=my_mysql_query_hash('SELECT * FROM TbWkWork','id');

	# create a hash table of lists of contributors
	$work_contrib=array();
	$role_contrib=array();
	$work_contrib_org=array();
	$role_contrib_org=array();
	foreach($contrib as $id => $row) {
		$workId=$row['workId'];
		$personId=$row['personId'];
		$organizationId=$row['organizationId'];
		$typeId=$row['typeId'];
		if($personId!=NULL) {
			if(!isset($work_contrib[$workId])) {
				$work_contrib[$workId]=array();
			}
			$work_contrib[$workId][]=$personId;
			$role_contrib[$workId][]=$typeId;
		}
		if($organizationId!=NULL) {
			if(!isset($work_contrib_org[$workId])) {
				$work_contrib_org[$workId]=array();
			}
			$work_contrib_org[$workId][]=$organizationId;
			$role_contrib_org[$workId][]=$typeId;
		}
	}

	// sending query
	if($type=='audio') {
		$add='TbWkWorkType.isAudio=1';
		$order='asc';
		$limit=300;
	} else {
		$add='TbWkWorkType.isVideo=1';
		$order='desc';
		$limit=10;
	}
	$query=sprintf('SELECT TbWkWork.id,TbWkWork.name,TbWkWork.length,TbWkWork.size,TbWkWork.chapters,TbWkWork.typeId,TbWkWorkView.startViewDate,TbWkWorkView.endViewDate,TbWkWorkView.viewerId,TbWkWorkView.locationId,TbWkWorkView.deviceId,TbWkWorkReview.rating,TbWkWorkReview.review,TbWkWorkReview.reviewDate FROM TbWkWork,TbWkWorkType,TbWkWorkReview,TbWkWorkView where TbWkWork.typeId=TbWkWorkType.id and TbWkWorkReview.workId=TbWkWork.id and TbWkWorkView.workId=TbWkWork.id and %s order by TbWkWorkView.endViewDate %s limit %s',$add,$order,$limit);
	//$query=sprintf('SELECT * FROM TbWkWork');
	$result=my_mysql_query($query);

	$fields_num=mysql_num_fields($result);

	// analyzing positions of ids in the data
	for($i=0; $i<$fields_num; $i++) {
		$field=mysql_fetch_field($result,$i);
		if($field->name=='id') {
			$idid=$i;
		}
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
		# append contributors to the header...(do not include organizations)
		$cont_array=array();
		foreach($work_contrib[$row[$idid]] as $personId) {
			$cont_array[]=get_full_name($persons[$personId]);
		}
		if(count($cont_array)>0) {
			$header.=' / '.join($cont_array,' ');
		}

		$body='';
		$body.='<ul>';
		if($row[$idid]!=NULL) {
			$body.='<li>id: '.$row[$idid].'</li>';
		}
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
		/*
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
		 */
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
		# contributor stuff
		$j=0;
		foreach($work_contrib[$row[$idid]] as $personId) {
			$name=get_full_name($persons[$personId]);
			$roleid=$role_contrib[$row[$idid]][$j];
			$role_name=$contribtype[$roleid]['name'];
			$body.='<li>'.$role_name.': '.$name.'</li>';
			$j++;
		}
		$j=0;
		foreach($work_contrib_org[$row[$idid]] as $organizationId) {
			$name=$organizations[$organizationId]['name'];
			$url=$organizations[$organizationId]['url'];
			$roleid=$role_contrib_org[$row[$idid]][$j];
			$role_name=$contribtype[$roleid]['name'];
			$body.='<li>'.$role_name.': '.'<a href=\''.$url.'\'>'.$name.'</a></li>';
			$j++;
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
