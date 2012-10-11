<?php

function create_works($params) {
	// debugging aid that was removed...
	//error_log(var_export($params,true),0);
	// TODO: throw error if type param does not exist
	$type=$params['type'];
	if(array_key_exists('limit',$params)) {
		$limit=$params['limit'];
	} else {
		$limit=100000; // limitless
	}
	if(array_key_exists('order',$params)) {
		$limit=$params['order'];
	} else {
		$order='desc';
	}
	$res='';

	// collecting other table data ...
	$honorifics=my_mysql_query_hash('SELECT * FROM TbIdHonorific','id');
	$contrib=my_mysql_query_hash('SELECT * FROM TbWkWorkContrib','id');
	$types=my_mysql_query_hash('SELECT * FROM TbWkWorkType','id');
	$locations=my_mysql_query_hash('SELECT * FROM TbLocation','id');
	$devices=my_mysql_query_hash('SELECT * FROM TbDevice','id');
	$languages=my_mysql_query_hash('SELECT * FROM TbLanguage','id');
	$persons=my_mysql_query_hash('SELECT * FROM TbIdPerson','id');
	$personexternal=my_mysql_query_hash('SELECT * FROM TbIdPersonExternal','id');
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
				$role_contrib[$workId]=array();
			}
			$work_contrib[$workId][]=$personId;
			$role_contrib[$workId][]=$typeId;
		}
		if($organizationId!=NULL) {
			if(!isset($work_contrib_org[$workId])) {
				$work_contrib_org[$workId]=array();
				$role_contrib_org[$workId]=array();
			}
			$work_contrib_org[$workId][]=$organizationId;
			$role_contrib_org[$workId][]=$typeId;
		}
	}
	# create a hash table of external ids for works
	$workexternal_externalid=array();
	$workexternal_externalcode=array();
	foreach($workexternal as $id => $row) {
		$workId=$row['workId'];
		$externalId=$row['externalId'];
		$externalCode=$row['externalCode'];
		$workexternal_externalid[$workId][]=$externalId;
		$workexternal_externalcode[$workId][]=$externalCode;
	}
	# create a hash table of external ids for people
	$personexternal_externalid=array();
	$personexternal_externalcode=array();
	# create an empty entry for every person
	foreach($persons as $id => $row) {
		$personexternal_externalid[$id]=array();
		$personexternal_externalcode[$id]=array();
	}
	# fill with external ids
	foreach($personexternal as $id => $row) {
		$personId=$row['personId'];
		$externalId=$row['externalId'];
		$externalCode=$row['externalCode'];
		$personexternal_externalid[$personId][]=$externalId;
		$personexternal_externalcode[$personId][]=$externalCode;
	}

	// sending query
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
	$query=sprintf('SELECT TbWkWork.id,TbWkWork.name,TbWkWork.length,TbWkWork.size,TbWkWork.chapters,TbWkWork.typeId,TbWkWork.languageId,TbWkWorkView.startViewDate,TbWkWorkView.endViewDate,TbWkWorkViewPerson.viewerId,TbWkWorkView.locationId,TbWkWorkView.deviceId,TbWkWorkView.langId,TbWkWorkReview.ratingId,TbWkWorkReview.review,TbWkWorkReview.reviewDate FROM TbWkWorkViewPerson,TbWkWork,TbWkWorkType,TbWkWorkReview,TbWkWorkView WHERE TbWkWork.typeId=TbWkWorkType.id AND TbWkWorkViewPerson.viewId=TbWkWorkView.id AND TbWkWorkReview.workId=TbWkWork.id AND TbWkWorkView.workId=TbWkWork.id AND %s order by TbWkWorkView.endViewDate %s LIMIT %s',$add,$order,$limit);
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
		if($field->name=='languageId') {
			$languageid=$i;
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
		if($field->name=='langId') {
			$langid=$i;
		}
		# review table
		if($field->name=='ratingId') {
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
		if($row[$languageid]!=NULL) {
			$s_language=$languages[$row[$languageid]]['name'];
		} else {
			$s_language=get_na_string();
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
		if($row[$langid]!=NULL) {
			$s_lang=$languages[$row[$langid]]['name'];
		} else {
			$s_lang=get_na_string();
		}
		if($row[$viewerid]!=NULL) {
			$s_viewer=get_full_name($persons[$row[$viewerid]],$honorifics);
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
		if(isset($work_contrib[$row[$idid]])) {
			$cont_array=array();
			foreach($work_contrib[$row[$idid]] as $personId) {
				$cont_array[]=get_full_name($persons[$personId],$honorifics);
			}
			if(count($cont_array)>0) {
				$header.=' / '.join($cont_array,', ');
			}
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
		if($row[$languageid]!=NULL) {
			$body.='<li>language: '.$s_language.'</li>';
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
		if($row[$langid]!=NULL) {
			$body.='<li>lang: '.$s_lang.'</li>';
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
		if(isset($work_contrib[$row[$idid]])) {
			$j=0;
			foreach($work_contrib[$row[$idid]] as $personId) {
				$name=get_full_name($persons[$personId],$honorifics);
				$roleid=$role_contrib[$row[$idid]][$j];
				$role_name=$contribtype[$roleid]['name'];
				$body.='<li>'.$role_name.': '.$name;
				$j++;
				$e=0;
				foreach($personexternal_externalid[$personId] as $externalid) {
					$externalcode=$personexternal_externalcode[$personId][$e];
					$externalname=$external[$externalid]['name'];
					$externalidname=$external[$externalid]['idname'];
					$link=get_external_href($externalname,$externalcode);
					$link='<a href=\''.$link.'\'>'.$externalidname.': '.$externalcode.'</a>';
					$body.=' '.$link;
					$e++;
				}
				$body.='</li>';
			}
		}
		if(isset($work_contrib_org[$row[$idid]])) {
			$j=0;
			foreach($work_contrib_org[$row[$idid]] as $organizationId) {
				$name=$organizations[$organizationId]['name'];
				$url=$organizations[$organizationId]['url'];
				$roleid=$role_contrib_org[$row[$idid]][$j];
				$role_name=$contribtype[$roleid]['name'];
				$body.='<li>'.$role_name.': '.'<a href=\''.$url.'\'>'.$name.'</a></li>';
				$j++;
			}
		}
		# external stuff
		$j=0;
		if(isset($workexternal_externalid[$row[$idid]])) {
			foreach($workexternal_externalid[$row[$idid]] as $externalid) {
				$externalcode=$workexternal_externalcode[$row[$idid]][$j];
				$externalname=$external[$externalid]['name'];
				$externalidname=$external[$externalid]['idname'];
				$link=get_external_href($externalname,$externalcode);
				$link='<a href=\''.$link.'\'>'.$externalidname.': '.$externalcode.'</a>';
				$body.='<li>'.$link.'</li>';
				$j++;
			}
		}
		$body.='</ul>';
		$res.=multi_accordion_entry($header,$body);
	}
	my_mysql_free_result($result);
	$res.=multi_accordion_end();
	return $res;
}

function create_stats($params) {
	$res='';
	# lets get my id
	$p_viewerId=my_mysql_query_one('SELECT id FROM TbIdPerson WHERE firstname=\'Mark\' AND surname=\'Veltzer\'');

	# work stats
	$query=sprintf('SELECT COUNT(*) FROM TbWkWorkViewPerson,TbWkWorkView,TbWkWork,TbWkWorkType WHERE TbWkWorkViewPerson.viewerId=%s AND TbWkWorkViewPerson.viewId=TbWkWorkView.id AND TbWkWorkView.workId=TbWkWork.id AND TbWkWork.typeId=TbWkWorkType.id AND TbWkWorkType.isStudy=1',
		my_mysql_real_escape_string($p_viewerId)
	);
	$res.=make_stat($query,null,'number of views');

	$query=sprintf('SELECT COUNT(DISTINCT workId) FROM TbWkWorkViewPerson,TbWkWorkView,TbWkWork,TbWkWorkType WHERE TbWkWorkViewPerson.viewerId=%s AND TbWkWorkViewPerson.viewId=TbWkWorkView.id AND TbWkWorkView.workId=TbWkWork.id AND TbWkWorkType.id=TbWkWork.typeId AND TbWkWorkType.isStudy=1',
		my_mysql_real_escape_string($p_viewerId)
	);
	$res.=make_stat($query,null,'number of distinct works');

	$query=sprintf('SELECT COUNT(DISTINCT viewerId) FROM TbWkWorkViewPerson,TbWkWorkView,TbWkWork,TbWkWorkType WHERE TbWkWorkViewPerson.viewerId=%s AND TbWkWorkViewPerson.viewId=TbWkWorkView.id AND TbWkWorkView.workId=TbWkWork.id AND TbWkWorkType.id=TbWkWork.typeId AND TbWkWorkType.isStudy=1',
		my_mysql_real_escape_string($p_viewerId)
	);
	$res.=make_stat($query,null,'number of distinct viewers');

	$query=sprintf('SELECT COUNT(DISTINCT TbWkWork.typeId) FROM TbWkWorkViewPerson,TbWkWorkView,TbWkWork,TbWkWorkType WHERE TbWkWorkViewPerson.viewerId=%s AND TbWkWorkViewPerson.viewId=TbWkWorkView.id AND TbWkWorkView.workId=TbWkWork.id AND TbWkWorkType.id=TbWkWork.typeId AND TbWkWorkType.isStudy=1',
		my_mysql_real_escape_string($p_viewerId)
	);
	$res.=make_stat($query,null,'number of distinct work types');

	$query=sprintf('SELECT COUNT(DISTINCT locationId) FROM TbWkWorkViewPerson,TbWkWorkView,TbWkWork,TbWkWorkType WHERE TbWkWorkViewPerson.viewerId=%s AND TbWkWorkViewPerson.viewId=TbWkWorkView.id AND TbWkWorkView.workId=TbWkWork.id AND TbWkWorkType.id=TbWkWork.typeId AND TbWkWorkType.isStudy=1',
		my_mysql_real_escape_string($p_viewerId)
	);
	$res.=make_stat($query,null,'number of distinct locations');

	$query=sprintf('SELECT COUNT(DISTINCT deviceId) FROM TbWkWorkViewPerson,TbWkWorkView,TbWkWork,TbWkWorkType WHERE TbWkWorkViewPerson.viewerId=%s AND TbWkWorkViewPerson.viewId=TbWkWorkView.id AND TbWkWorkView.workId=TbWkWork.id AND TbWkWorkType.id=TbWkWork.typeId AND TbWkWorkType.isStudy=1',
		my_mysql_real_escape_string($p_viewerId)
	);
	$res.=make_stat($query,null,'number of distinct devices');

	$query=sprintf('SELECT COUNT(DISTINCT ratingId) FROM TbWkWorkReview,TbWkWork,TbWkWorkType WHERE TbWkWorkReview.reviewerId=%s AND TbWkWorkReview.workId=TbWkWork.id AND TbWkWork.typeId=TbWkWorkType.id AND TbWkWorkType.isStudy=1',
		my_mysql_real_escape_string($p_viewerId)
	);
	$res.=make_stat($query,null,'number of distinct ratings');

	$query=sprintf('SELECT SUM(TbWkWork.length) FROM TbWkWorkViewPerson,TbWkWork,TbWkWorkView,TbWkWorkType WHERE TbWkWorkViewPerson.viewerId=%s AND TbWkWorkViewPerson.viewId=TbWkWorkView.id AND TbWkWork.id=TbWkWorkView.workId AND TbWkWorkType.id=TbWkWork.typeId AND TbWkWorkType.isStudy=1',
		my_mysql_real_escape_string($p_viewerId)
	);
	$res.=make_stat($query,formatTimeperiod,'total time of works experienced');

	$query=sprintf('SELECT SUM(TbWkWork.size) FROM TbWkWorkViewPerson,TbWkWork,TbWkWorkView,TbWkWorkType WHERE TbWkWorkViewPerson.viewerId=%s AND TbWkWorkViewPerson.viewId=TbWkWorkView.id AND TbWkWork.id=TbWkWorkView.workId AND TbWkWorkType.id=TbWkWork.typeId AND TbWkWorkType.isStudy=1',
		my_mysql_real_escape_string($p_viewerId)
	);
	$res.=make_stat($query,formatSize,'total size of works experienced');

	$query=sprintf('SELECT AVG(TbWkWorkReview.ratingId) FROM TbWkWorkReview,TbWkWork,TbWkWorkType WHERE TbWkWorkReview.reviewerId=%s AND TbWkWorkType.id=TbWkWork.typeId AND TbWkWorkType.isStudy=1 AND TbWkWorkReview.workId=TbWkWork.id',
		my_mysql_real_escape_string($p_viewerId)
	);
	$res.=make_stat($query,null,'average rating');

	$query=sprintf('SELECT MAX(TbWkWorkReview.ratingId) FROM TbWkWorkReview,TbWkWork,TbWkWorkType WHERE TbWkWorkReview.reviewerId=%s AND TbWkWorkType.id=TbWkWork.typeId AND TbWkWorkType.isStudy=1 AND TbWkWorkReview.workId=TbWkWork.id',
		my_mysql_real_escape_string($p_viewerId)
	);
	$res.=make_stat($query,null,'maxium rating');

	$query=sprintf('SELECT MIN(TbWkWorkReview.ratingId) FROM TbWkWorkReview,TbWkWork,TbWkWorkType WHERE TbWkWorkReview.reviewerId=%s AND TbWkWorkType.id=TbWkWork.typeId AND TbWkWorkType.isStudy=1 AND TbWkWorkReview.workId=TbWkWork.id',
		my_mysql_real_escape_string($p_viewerId)
	);
	$res.=make_stat($query,null,'minimum rating');
	return $res;
}

function create_movie_stats($params) {
	$res='';
	# lets get my id
	$p_viewerId=my_mysql_query_one('SELECT id FROM TbIdPerson WHERE firstname=\'Mark\' AND surname=\'Veltzer\'');

	$query=sprintf('SELECT COUNT(*) FROM TbWkWorkViewPerson,TbWkWorkView,TbWkWork,TbWkWorkType WHERE TbWkWorkViewPerson.viewerId=%s AND TbWkWorkViewPerson.viewId=TbWkWorkView.id AND TbWkWorkView.workId=TbWkWork.id AND TbWkWork.typeId=TbWkWorkType.id AND TbWkWorkType.name=\'video movie\'',
		my_mysql_real_escape_string($p_viewerId)
	);
	$res.=make_stat($query,null,'number of movies seen');

	$query=sprintf('SELECT COUNT(*) FROM TbWkWorkViewPerson,TbWkWorkView,TbWkWork,TbWkWorkType WHERE TbWkWorkViewPerson.viewerId=%s AND TbWkWorkViewPerson.viewId=TbWkWorkView.id AND TbWkWorkView.workId=TbWkWork.id AND TbWkWork.typeId=TbWkWorkType.id AND TbWkWorkType.name=\'video movie\' AND TbWkWorkView.endViewDate is not NULL',
		my_mysql_real_escape_string($p_viewerId)
	);
	$res.=make_stat($query,null,'number of movies seen with date of view');

	$query=sprintf('SELECT COUNT(*) FROM TbWkWorkViewPerson,TbWkWorkView,TbWkWork,TbWkWorkType,TbWkWorkReview WHERE TbWkWorkViewPerson.viewerId=%s AND TbWkWorkViewPerson.viewId=TbWkWorkView.id AND TbWkWorkView.workId=TbWkWork.id AND TbWkWork.typeId=TbWkWorkType.id AND TbWkWorkType.name=\'video movie\' AND TbWkWorkReview.workId=TbWkWork.id',
		my_mysql_real_escape_string($p_viewerId)
	);
	$res.=make_stat($query,null,'number of movies seen with reviews');

	$query=sprintf('SELECT COUNT(*) FROM TbWkWorkViewPerson,TbWkWorkView,TbWkWork,TbWkWorkType,TbWkWorkReview WHERE TbWkWorkViewPerson.viewerId=%s AND TbWkWorkViewPerson.viewId=TbWkWorkView.id AND TbWkWorkView.workId=TbWkWork.id AND TbWkWork.typeId=TbWkWorkType.id AND TbWkWorkType.name=\'video movie\' AND TbWkWorkReview.workId=TbWkWork.id AND TbWkWorkView.endViewDate is not NULL',
		my_mysql_real_escape_string($p_viewerId)
	);
	$res.=make_stat($query,null,'number of movies seen with date and review');

	$query=sprintf('SELECT COUNT(*) FROM TbWkWorkViewPerson,TbWkWorkView,TbWkWork,TbWkWorkType,TbWkWorkReview,TbWkWorkExternal,TbExternalType WHERE TbWkWorkViewPerson.viewerId=%s AND TbWkWorkViewPerson.viewId=TbWkWorkView.id AND TbWkWorkView.workId=TbWkWork.id AND TbWkWork.typeId=TbWkWorkType.id AND TbWkWorkType.name=\'video movie\' AND TbWkWorkReview.workId=TbWkWork.id AND TbExternalType.name=\'imdb_title\' AND TbWkWorkExternal.workId=TbWkWork.id AND TbWkWorkExternal.externalId=TbExternalType.id',
		my_mysql_real_escape_string($p_viewerId)
	);
	$res.=make_stat($query,null,'number of movies seen with review and imdbid');

	$query=sprintf('SELECT COUNT(*) FROM TbWkWorkViewPerson,TbWkWorkView,TbWkWork,TbWkWorkType,TbWkWorkReview,TbWkWorkExternal,TbExternalType WHERE TbWkWorkViewPerson.viewerId=%s AND TbWkWorkViewPerson.viewId=TbWkWorkView.id AND TbWkWorkView.workId=TbWkWork.id AND TbWkWork.typeId=TbWkWorkType.id AND TbWkWorkType.name=\'video movie\' AND TbWkWorkReview.workId=TbWkWork.id AND TbWkWorkView.endViewDate is not NULL AND TbExternalType.name=\'imdb_title\' AND TbWkWorkExternal.workId=TbWkWork.id AND TbWkWorkExternal.externalId=TbExternalType.id',
		my_mysql_real_escape_string($p_viewerId)
	);
	$res.=make_stat($query,null,'number of movies seen with date and review and imdbid');

	$query=sprintf('SELECT COUNT(DISTINCT TbWkWork.id) FROM TbWkWorkViewPerson, TbWkWorkView,TbWkWork, TbWkWorkType WHERE TbWkWorkViewPerson.viewerId=%s AND TbWkWorkViewPerson.viewId=TbWkWorkView.id AND TbWkWorkView.workId=TbWkWork.id AND TbWkWork.typeId=TbWkWorkType.id AND TbWkWorkType.name=\'video movie\'',
		my_mysql_real_escape_string($p_viewerId)
	);
	$res.=make_stat($query,null,'number of distinct movies seen');

	$query=sprintf('SELECT COUNT(*)/COUNT(DISTINCT TbWkWork.id) FROM TbWkWorkViewPerson, TbWkWorkView,TbWkWork, TbWkWorkType WHERE TbWkWorkViewPerson.viewerId=%s AND TbWkWorkViewPerson.viewId=TbWkWorkView.id AND TbWkWorkView.workId=TbWkWork.id AND TbWkWork.typeId=TbWkWorkType.id AND TbWkWorkType.name=\'video movie\'',
		my_mysql_real_escape_string($p_viewerId)
	);
	$res.=make_stat($query,null,'average views per distinct movie');

	$query=sprintf('SELECT MIN(mytbl.mycnt) FROM (select COUNT(*) AS mycnt FROM TbWkWorkViewPerson, TbWkWorkView,TbWkWork, TbWkWorkType WHERE TbWkWorkViewPerson.viewerId=%s AND TbWkWorkViewPerson.viewId=TbWkWorkView.id AND TbWkWorkView.workId=TbWkWork.id AND TbWkWork.typeId=TbWkWorkType.id AND TbWkWorkType.name=\'video movie\' GROUP BY TbWkWork.id) AS mytbl',
		my_mysql_real_escape_string($p_viewerId)
	);
	$res.=make_stat($query,null,'minimum views per distinct movie');

	$query=sprintf('SELECT MAX(mytbl.mycnt) FROM (select COUNT(*) AS mycnt FROM TbWkWorkViewPerson, TbWkWorkView,TbWkWork, TbWkWorkType WHERE TbWkWorkViewPerson.viewerId=%s AND TbWkWorkViewPerson.viewId=TbWkWorkView.id AND TbWkWorkView.workId=TbWkWork.id AND TbWkWork.typeId=TbWkWorkType.id AND TbWkWorkType.name=\'video movie\' GROUP BY TbWkWork.id) AS mytbl',
		my_mysql_real_escape_string($p_viewerId)
	);
	$res.=make_stat($query,null,'maximum views per distinct movie');

	$query=sprintf('SELECT COUNT(*) FROM TbWkWorkViewPerson,TbWkWorkView,TbWkWork,TbWkWorkType WHERE TbWkWorkViewPerson.viewerId=%s AND TbWkWorkViewPerson.viewId=TbWkWorkView.id AND TbWkWorkView.workId=TbWkWork.id AND TbWkWork.typeId=TbWkWorkType.id AND TbWkWorkType.name=\'video movie\' AND TbWkWork.length is null',
		my_mysql_real_escape_string($p_viewerId)
	);
	$res.=make_stat($query,null,'number of movies seen without length');

	$query=sprintf('SELECT COUNT(DISTINCT TbWkWork.id) FROM TbWkWorkViewPerson,TbWkWorkView,TbWkWork,TbWkWorkType WHERE TbWkWorkViewPerson.viewerId=%s AND TbWkWorkViewPerson.viewId=TbWkWorkView.id AND TbWkWorkView.workId=TbWkWork.id AND TbWkWork.typeId=TbWkWorkType.id AND TbWkWorkType.name=\'video movie\' AND TbWkWork.length is null',
		my_mysql_real_escape_string($p_viewerId)
	);
	$res.=make_stat($query,null,'number of distinct movies seen without length');

	$query=sprintf('SELECT SUM(TbWkWork.length) FROM TbWkWorkViewPerson,TbWkWorkView,TbWkWork,TbWkWorkType WHERE TbWkWorkViewPerson.viewerId=%s AND TbWkWorkViewPerson.viewId=TbWkWorkView.id AND TbWkWorkView.workId=TbWkWork.id AND TbWkWork.typeId=TbWkWorkType.id AND TbWkWorkType.name=\'video movie\' AND TbWkWork.length is not null',
		my_mysql_real_escape_string($p_viewerId)
	);
	$res.=make_stat($query,formatTimeperiod,'total length of all movies seen that have length');

	$query=sprintf('SELECT AVG(TbWkWork.length) FROM TbWkWorkViewPerson,TbWkWorkView,TbWkWork,TbWkWorkType WHERE TbWkWorkViewPerson.viewerId=%s AND TbWkWorkViewPerson.viewId=TbWkWorkView.id AND TbWkWorkView.workId=TbWkWork.id AND TbWkWork.typeId=TbWkWorkType.id AND TbWkWorkType.name=\'video movie\' AND TbWkWork.length is not null',
		my_mysql_real_escape_string($p_viewerId)
	);
	$res.=make_stat($query,formatTimeperiod,'average length of all movies seen that have length');

	$query=sprintf('SELECT MIN(TbWkWork.length) FROM TbWkWorkViewPerson,TbWkWorkView,TbWkWork,TbWkWorkType WHERE TbWkWorkViewPerson.viewerId=%s AND TbWkWorkViewPerson.viewId=TbWkWorkView.id AND TbWkWorkView.workId=TbWkWork.id AND TbWkWork.typeId=TbWkWorkType.id AND TbWkWorkType.name=\'video movie\' AND TbWkWork.length is not null',
		my_mysql_real_escape_string($p_viewerId)
	);
	$res.=make_stat($query,formatTimeperiod,'minimum length of all movies seen that have length');

	$query=sprintf('SELECT MAX(TbWkWork.length) FROM TbWkWorkViewPerson,TbWkWorkView,TbWkWork,TbWkWorkType WHERE TbWkWorkViewPerson.viewerId=%s AND TbWkWorkViewPerson.viewId=TbWkWorkView.id AND TbWkWorkView.workId=TbWkWork.id AND TbWkWork.typeId=TbWkWorkType.id AND TbWkWorkType.name=\'video movie\' AND TbWkWork.length is not null',
		my_mysql_real_escape_string($p_viewerId)
	);
	$res.=make_stat($query,formatTimeperiod,'maximum length of all movies seen that have length');

	$query=sprintf('SELECT COUNT(*) FROM TbRating, TbWkWorkReview, TbWkWork, TbWkWorkType WHERE TbWkWorkReview.reviewerId=%s AND TbWkWorkReview.workId=TbWkWork.id AND TbWkWork.typeId=TbWkWorkType.id AND TbWkWorkType.name=\'video movie\' AND TbWkWorkReview.ratingId=TbRating.id',
		my_mysql_real_escape_string($p_viewerId)
	);
	$res.=make_stat($query,null,'number of movies reviewed');

	$query=sprintf('SELECT AVG(TbRating.value) FROM TbRating, TbWkWorkReview, TbWkWork, TbWkWorkType WHERE TbWkWorkReview.reviewerId=%s AND TbWkWorkReview.workId=TbWkWork.id AND TbWkWork.typeId=TbWkWorkType.id AND TbWkWorkType.name=\'video movie\' AND TbWkWorkReview.ratingId=TbRating.id',
		my_mysql_real_escape_string($p_viewerId)
	);
	$res.=make_stat($query,null,'average rating of all movies reviewed');

	$query=sprintf('SELECT MIN(TbRating.value) FROM TbRating, TbWkWorkReview, TbWkWork, TbWkWorkType WHERE TbWkWorkReview.reviewerId=%s AND TbWkWorkReview.workId=TbWkWork.id AND TbWkWork.typeId=TbWkWorkType.id AND TbWkWorkType.name=\'video movie\' AND TbWkWorkReview.ratingId=TbRating.id',
		my_mysql_real_escape_string($p_viewerId)
	);
	$res.=make_stat($query,null,'minimum rating of all movies reviewed');

	$query=sprintf('SELECT MAX(TbRating.value) FROM TbRating, TbWkWorkReview, TbWkWork, TbWkWorkType WHERE TbWkWorkReview.reviewerId=%s AND TbWkWorkReview.workId=TbWkWork.id AND TbWkWork.typeId=TbWkWorkType.id AND TbWkWorkType.name=\'video movie\' AND TbWkWorkReview.ratingId=TbRating.id',
		my_mysql_real_escape_string($p_viewerId)
	);
	$res.=make_stat($query,null,'maximum rating of all movies reviewed');

	$query=sprintf('SELECT COUNT(DISTINCT TbRating.id) FROM TbRating, TbWkWorkReview, TbWkWork, TbWkWorkType WHERE TbWkWorkReview.reviewerId=%s AND TbWkWorkReview.workId=TbWkWork.id AND TbWkWork.typeId=TbWkWorkType.id AND TbWkWorkType.name=\'video movie\' AND TbWkWorkReview.ratingId=TbRating.id',
		my_mysql_real_escape_string($p_viewerId)
	);
	$res.=make_stat($query,null,'number of distinct ratings of all movies reviewed');

	$query=sprintf('SELECT COUNT(DISTINCT TbWkWorkView.deviceId) FROM TbWkWorkViewPerson,TbWkWorkView,TbWkWork,TbWkWorkType WHERE TbWkWorkViewPerson.viewerId=%s AND TbWkWorkViewPerson.viewId=TbWkWorkView.id AND TbWkWorkView.workId=TbWkWork.id AND TbWkWork.typeId=TbWkWorkType.id AND TbWkWorkType.name=\'video movie\'',
		my_mysql_real_escape_string($p_viewerId)
	);
	$res.=make_stat($query,null,'number of distinct devices used to watch them');

	$query=sprintf('SELECT TbDevice.name,COUNT(TbDevice.name) FROM TbWkWorkViewPerson,TbWkWorkView,TbWkWork,TbWkWorkType,TbDevice WHERE TbWkWorkViewPerson.viewerId=%s AND TbWkWorkViewPerson.viewId=TbWkWorkView.id AND TbWkWorkView.workId=TbWkWork.id AND TbWkWork.typeId=TbWkWorkType.id AND TbWkWorkType.name=\'video movie\' AND TbDevice.id=TbWkWorkView.deviceId GROUP BY TbDevice.name',
		my_mysql_real_escape_string($p_viewerId)
	);
	$res.=make_table($query,'number of views per device');

	$query=sprintf('SELECT COUNT(DISTINCT TbWkWorkView.locationId) FROM TbWkWorkViewPerson,TbWkWorkView,TbWkWork,TbWkWorkType WHERE TbWkWorkViewPerson.viewerId=%s AND TbWkWorkViewPerson.viewId=TbWkWorkView.id AND TbWkWorkView.workId=TbWkWork.id AND TbWkWork.typeId=TbWkWorkType.id AND TbWkWorkType.name=\'video movie\'',
		my_mysql_real_escape_string($p_viewerId)
	);
	$res.=make_stat($query,null,'number of distinct location used to watch them');

	$query=sprintf('SELECT TbLocation.name,COUNT(TbLocation.name) FROM TbWkWorkViewPerson,TbWkWorkView,TbWkWork,TbWkWorkType,TbLocation WHERE TbWkWorkViewPerson.viewerId=%s AND TbWkWorkViewPerson.viewId=TbWkWorkView.id AND TbWkWorkView.workId=TbWkWork.id AND TbWkWork.typeId=TbWkWorkType.id AND TbWkWorkType.name=\'video movie\' AND TbLocation.id=TbWkWorkView.locationId GROUP BY TbLocation.name',
		my_mysql_real_escape_string($p_viewerId)
	);
	$res.=make_table($query,'number of views per location');

	$query=sprintf('select AVG(mytab.mycnt) from (select COUNT(*) AS mycnt from TbWkWorkViewPerson where TbWkWorkViewPerson.viewId in (select TbWkWorkView.id from TbWkWorkType, TbWkWork, TbWkWorkView where TbWkWork.id=TbWkWorkView.workId and TbWkWorkType.id=TbWkWork.typeId and TbWkWorkType.name=\'video movie\') GROUP BY TbWkWorkViewPerson.viewId) AS mytab;',
		my_mysql_real_escape_string($p_viewerId)
	);
	$res.=make_stat($query,null,'average number of viewers per view');

	return $res;
}

function create_person($params) {
	$firstname=$params['firstname'];
	$surname=$params['surname'];
	$res='';
	$external=my_mysql_query_hash('SELECT * FROM TbExternalType','id');
	$query=sprintf('SELECT * FROM TbIdPerson WHERE firstname=%s AND surname=%s',
		my_mysql_real_escape_string($firstname),
		my_mysql_real_escape_string($surname)
	);
	$row=my_mysql_query_one_row($query);
	$id=$row['id'];
	$res.='<ul>';
	$res.='<li>id: '.$id.'</li>';
	$res.='<li>Name: '.get_full_name($row,$honorifics).'</li>';
	// handle externals
	$query=sprintf('SELECT * FROM TbIdPersonExternal WHERE personId=%s',
		my_mysql_real_escape_string($id)
	);
	$result=my_mysql_query($query);
	while($row=mysql_fetch_assoc($result)) {
		$externalcode=$row['externalCode'];
		$externalid=$row['externalId'];
		$externalname=$external[$externalid]['name'];
		$externalidname=$external[$externalid]['idname'];
		$link=get_external_href($externalname,$externalcode);
		$link='<a href=\''.$link.'\'>'.$externalidname.': '.$externalcode.'</a>';
		$res.='<li>'.$link.'</li>';
	}
	my_mysql_free_result($result);
	// finish up
	$res.='</ul>';
	return $res;
}

?>
