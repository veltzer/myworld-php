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
	$producers=my_mysql_query_hash('SELECT * FROM TbWkProducer','id');
	$types=my_mysql_query_hash('SELECT * FROM TbWkWorkType','id');
	$locations=my_mysql_query_hash('SELECT * FROM TbLcNamed','id');
	$devices=my_mysql_query_hash('SELECT * FROM TbDevice','id');
	$persons=my_mysql_query_hash('SELECT * FROM TbIdPerson','id');

	// sending query
	if($type=='audio') {
		$query=sprintf('SELECT TbWkWork.id,TbWkWork.creatorId,TbWkWork.name,TbWkWork.imdbid,TbWkWork.length,TbWkWork.size,TbWkWork.chapters,TbWkWork.typeId,TbWkWork.producerId,TbWkWork.startViewDate,TbWkWork.endViewDate,TbWkWork.viewerId,TbWkWork.locationId,TbWkWork.deviceId,TbWkWork.rating,TbWkWork.review FROM TbWkWork,TbWkWorkType where TbWkWork.typeId=TbWkWorkType.id and TbWkWorkType.isAudio=1 order by TbWkWork.endViewDate');
	} else {
		$query=sprintf('SELECT TbWkWork.id,TbWkWork.creatorId,TbWkWork.name,TbWkWork.imdbid,TbWkWork.length,TbWkWork.size,TbWkWork.chapters,TbWkWork.typeId,TbWkWork.producerId,TbWkWork.startViewDate,TbWkWork.endViewDate,TbWkWork.viewerId,TbWkWork.locationId,TbWkWork.deviceId,TbWkWork.rating,TbWkWork.review FROM TbWkWork,TbWkWorkType where TbWkWork.typeId=TbWkWorkType.id and TbWkWorkType.isVideo=1 order by TbWkWork.endViewDate');
	}
	//$query=sprintf('SELECT * FROM TbWkWork');
	$result=my_mysql_query($query);

	$fields_num=mysql_num_fields($result);

	// analyzing positions of ids in the data
	for($i=0; $i<$fields_num; $i++) {
		$field=mysql_fetch_field($result,$i);
		if($field->name=='name') {
			$nameid=$i;
		}
		if($field->name=='producerId') {
			$producerid=$i;
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
		if($field->name=='typeId') {
			$typeid=$i;
		}
		if($field->name=='creatorId') {
			$creatorid=$i;
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
		if($field->name=='startViewDate') {
			$startviewdateid=$i;
		}
		if($field->name=='endViewDate') {
			$endviewdateid=$i;
		}
		if($field->name=='rating') {
			$ratingid=$i;
		}
		if($field->name=='review') {
			$reviewid=$i;
		}
		if($field->name=='imdbid') {
			$imdbidid=$i;
		}
	}
	$res.=multi_accordion_start();
	// printing table rows
	while($row=mysql_fetch_row($result))
	{
		#handle producers
		#<a href='url'>Link text</a>
		if($row[$producerid]!=NULL) {
			$url=$producers[$row[$producerid]]['url'];
			$name=$producers[$row[$producerid]]['name'];
			$s_producer='<a href=\''.$url.'\'>'.$name.'</a>';
		} else {
			$s_producer=get_na_string();
		}
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
		if($row[$creatorid]!=NULL) {
			$s_creator=get_full_name($persons[$row[$creatorid]]);
		} else {
			$s_creator=get_na_string();
		}
		if($row[$sizeid]!=NULL) {
			$s_size=formatSize($row[$sizeid]);
		}
		if($row[$lengthid]!=NULL) {
			$s_length=formatTimeperiod($row[$lengthid]);
		}
		if($row[$nameid]!=NULL) {
			if($row[$creatorid]!=NULL) {
				$header=$row[$nameid].' / '.$s_creator;
			} else {
				$header=$row[$nameid];
			}
		} else {
			if($row[$creatorid]!=NULL) {
				$header=$s_creator;
			} else {
				$header='Huh?!?';
			}
		}

		$body='';
		$body.='<ul>';
		if($row[$nameid]!=NULL) {
			$body.='<li>name: '.$row[$nameid].'</li>';
		}
		if($row[$imdbidid]!=NULL) {
			$body.='<li>imdbid: '.$row[$imdbidid].'</li>';
		}
		if($row[$creatorid]!=NULL) {
			$body.='<li>creator: '.$s_creator.'</li>';
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
		if($row[$producerid]!=NULL) {
			$body.='<li>producer: '.$s_producer.'</li>';
		}
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
		if($row[$ratingid]!=NULL) {
			$body.='<li>rating: '.$row[$ratingid].'</li>';
		}
		if($row[$reviewid]!=NULL) {
			$body.='<li>review: '.$row[$reviewid].'</li>';
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
	$res.=make_stat('SELECT count(*) FROM TbWkWork',null);
	$res.=make_stat('SELECT avg(rating) FROM TbWkWork',null);
	$res.=make_stat('SELECT count(distinct rating) FROM TbWkWork',null);
	$res.=make_stat('SELECT avg(rating) FROM TbWkWork',null);
	$res.=make_stat('SELECT count(distinct rating) FROM TbWkWork',null);
	$res.=make_stat('SELECT count(distinct viewerId) FROM TbWkWork',null);
	$res.=make_stat('SELECT count(distinct locationId) FROM TbWkWork',null);
	$res.=make_stat('SELECT count(distinct creatorId) from TbWkWork',null);
	$res.=make_stat('SELECT sum(length) from TbWkWork',formatTimeperiod);
	$res.=make_stat('SELECT sum(size) from TbWkWork',formatSize);
	$res.=make_stat('SELECT count(distinct typeId) from TbWkWork',null);
	return $res;
}

?>
