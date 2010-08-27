<?php

# TODO:
# Make this script go through the groups tables to render the creators and viewers.

function create_works() {
	$res='';
	// sending query
	$query=sprintf('SELECT * FROM TbWkProducer');
	$result=mysql_query($query);
	assert($result);
	$producers=array();
	while($row=mysql_fetch_assoc($result)) {
		$producers[$row['id']]=$row;
	}
	#debug: print the array...
	#print_r($producers);
	assert(mysql_free_result($result));

	// sending query
	$query=sprintf('SELECT * FROM TbWkWorkType');
	$result=mysql_query($query);
	assert($result);
	$types=array();
	while($row=mysql_fetch_assoc($result)) {
		$types[$row['id']]=$row;
	}
	#debug: print the array...
	#print_r($types);
	assert(mysql_free_result($result));

	// sending query
	$query=sprintf('SELECT * FROM TbLcNamed');
	$result=mysql_query($query);
	assert($result);
	$locations=array();
	while($row=mysql_fetch_assoc($result)) {
		$locations[$row['id']]=$row;
	}
	#debug: print the array...
	#print_r($locations);
	assert(mysql_free_result($result));

	// sending query
	$query=sprintf('SELECT * FROM TbIdPerson');
	$result=mysql_query($query);
	assert($result);
	$persons=array();
	while($row=mysql_fetch_assoc($result)) {
		$persons[$row['id']]=$row;
	}
	#debug: print the array...
	#print_r($persons);
	assert(mysql_free_result($result));

	// sending query
	$query=sprintf('SELECT id,creatorId,name,length,size,chapters,typeId,producerId,startViewDate,endViewDate,viewerId,locationId,remark,rating,review FROM TbWkWork');
	//$query=sprintf('SELECT * FROM TbWkWork');
	$result=mysql_query($query);
	assert($result);

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
		if($field->name=='remark') {
			$remarkid=$i;
		}
		if($field->name=='rating') {
			$ratingid=$i;
		}
		if($field->name=='review') {
			$reviewid=$i;
		}
	}
	$show_style='div';
	//$show_style='table';
	if($show_style=='table') {
		$res.=get_start_table();
		// printing table headers
		$res.='<tr>';
		for($i=0; $i<$fields_num; $i++) {
			$field=mysql_fetch_field($result,$i);
			$name=$field->name;
			if($field->name=='producerId') {
				$name='producer';
			}
			if($field->name=='viewerId') {
				$name='viewer';
			}
			if($field->name=='locationId') {
				$name='location';
			}
			if($field->name=='typeId') {
				$name='type';
			}
			if($field->name=='creatorId') {
				$name='creator';
			}
			$res.='<td>'.$name.'</td>';
		}
		$res.='</tr>';
	}
	if($show_style=='div') {
		$res.=multi_accordion_start();
	}
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
			$name=$types[$row[$typeid]]['name'];
			$s_type=$types[$row[$typeid]]['name'];
		} else {
			$s_type=get_na_string();
		}
		if($row[$locationid]!=NULL) {
			$s_location=$locations[$row[$locationid]]['name'];
		} else {
			$s_location=get_na_string();
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

		if($show_style=='table') {
			$res.='<tr>';
			// $row is array... foreach( .. ) puts every element
			// of $row to $cell variable
			foreach($row as $cell) {
				if($cell==NULL) {
					$cell=get_na_string();
				}
				$res.='<td>'.$cell.'</td>';
			}
			$res.='</tr>';
		}
		if($show_style=='div') {
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
			if($row[$remarkid]!=NULL) {
				$body.='<li>remark: '.$row[$remarkid].'</li>';
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
	}
	assert(mysql_free_result($result));
	if($show_style=='table') {
		$res.='</table>';
	}
	if($show_style=='div') {
		$res.=multi_accordion_end();
	}

	$res.='Some statistics...<br/>';
	$table='TbWkWork';

	$query=sprintf('SELECT count(*) FROM %s',mysql_real_escape_string($table));
	$result=mysql_query($query);
	assert($result);
	$row=mysql_fetch_row($result);
	$res.=$query.' = '.$row[0].'<br/>';
	assert(mysql_free_result($result));

	$query=sprintf('SELECT avg(rating) FROM %s',mysql_real_escape_string($table));
	$result=mysql_query($query);
	assert($result);
	$row=mysql_fetch_row($result);
	$res.=$query.' = '.$row[0].'<br/>';
	assert(mysql_free_result($result));

	$query=sprintf('SELECT count(distinct rating) FROM %s',mysql_real_escape_string($table));
	$result=mysql_query($query);
	assert($result);
	$row=mysql_fetch_row($result);
	$res.=$query.' = '.$row[0].'<br/>';
	assert(mysql_free_result($result));

	$query=sprintf('SELECT count(distinct viewerId) FROM %s',mysql_real_escape_string($table));
	$result=mysql_query($query);
	assert($result);
	$row=mysql_fetch_row($result);
	$res.=$query.' = '.$row[0].'<br/>';
	assert(mysql_free_result($result));

	$query=sprintf('SELECT count(distinct locationId) FROM %s',mysql_real_escape_string($table));
	$result=mysql_query($query);
	assert($result);
	$row=mysql_fetch_row($result);
	$res.=$query.' = '.$row[0].'<br/>';
	assert(mysql_free_result($result));

	$query=sprintf('SELECT count(distinct creatorId) from %s',mysql_real_escape_string($table));
	$result=mysql_query($query);
	assert($result);
	$row=mysql_fetch_row($result);
	$res.=$query.' = '.$row[0].'<br/>';
	assert(mysql_free_result($result));

	$query=sprintf('SELECT sum(length) from %s',mysql_real_escape_string($table));
	$result=mysql_query($query);
	assert($result);
	$row=mysql_fetch_row($result);
	$res.=$query.' = '.formatTimeperiod($row[0]).'<br/>';
	assert(mysql_free_result($result));

	$query=sprintf('SELECT sum(size) from %s',mysql_real_escape_string($table));
	$result=mysql_query($query);
	assert($result);
	$row=mysql_fetch_row($result);
	$res.=$query.' = '.formatSize($row[0]).'<br/>';
	assert(mysql_free_result($result));

	$query=sprintf('SELECT count(distinct typeId) from %s',mysql_real_escape_string($table));
	$result=mysql_query($query);
	assert($result);
	$row=mysql_fetch_row($result);
	$res.=$query.' = '.$row[0].'<br/>';
	assert(mysql_free_result($result));

	return $res;
}

?>
