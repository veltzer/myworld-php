<?php

# TODO:
# get the queries out of here and into a query manager...
# Handle creation of tables via my own class.

function create_courses($params) {
	$res='';
	// sending query
	$query=sprintf('SELECT id,category,name FROM TbBsCourses order by category,name asc');
	$result=my_mysql_query($query);

	$res.=get_start_table();
	$res.='<tr>';
	$res.='<td style=\'width:20%\'>category</td>';
	$res.='<td style=\'width:80%\'>name</td>';
	$res.='</tr>';

	while($row=$result->fetch_assoc()) {
		$id=$row['id'];
		$category=$row['category'];
		$name=$row['name'];
		$res.='</tr>';
		$res.='<td>'.$category.'</td>';
		$res.='<td>'.$name.'</td>';
		$res.='</tr>';
	}
	$result->free();
	$res.='</table>';
	return $res;
}

function create_consulting($params) {
	$res='';
	// sending query
	$query=sprintf('SELECT id,name,slug,imageId,remark,url FROM TbOrganization where funcConsulting order by name asc');
	$result=my_mysql_query($query);

	$res.=get_start_table();
	$res.='<tr>';
	$res.='<td style=\'width:20%\'>name</td>';
	$res.='<td style=\'width:80%\'>remark</td>';
	$res.='<td style=\'width:20%\'>image</td>';
	$res.='</tr>';

	while($row=$result->fetch_assoc()) {
		$id=$row['id'];
		$name=$row['name'];
		$slug=$row['slug'];
		$remark=$row['remark'];
		$imageId=$row['imageId'];
		$link_img=link_to_direct('GetBlob.php?table=TbImage&select_field=id&select_id='.$imageId.'&data_field=smallData&name_field=slug&mime_field=smallMime');
		$url=$row['url'];
		if($url!=NULL) {
			$name='<a href=\''.$url.'\'>'.$name.'</a>';
		}
		if($remark==NULL) {
			$remark=get_na_string();
		}
		$res.='</tr>';
		$res.='<td>'.$name.'</td>';
		$res.='<td>'.$remark.'</td>';
		$res.='<td><img src=\''.$link_img.'\'/></td>';
		$res.='</tr>';
	}
	$result->free();
	$res.='</table>';
	return $res;
}

function create_teaching($params) {
	$res='';
	// sending query
	$query=sprintf('SELECT id,name,slug,imageId,remark,url FROM TbOrganization where funcTeaching order by name asc');
	$result=my_mysql_query($query);

	$res.=get_start_table();
	$res.='<tr>';
	$res.='<td style=\'width:20%\'>name</td>';
	$res.='<td style=\'width:80%\'>remark</td>';
	$res.='<td style=\'width:20%\'>image</td>';
	$res.='</tr>';

	while($row=$result->fetch_assoc()) {
		$id=$row['id'];
		$name=$row['name'];
		$slug=$row['slug'];
		$remark=$row['remark'];
		$imageId=$row['imageId'];
		$link_img=link_to_direct('GetBlob.php?table=TbImage&select_field=id&select_id='.$imageId.'&data_field=smallData&name_field=slug&mime_field=smallMime');
		$url=$row['url'];
		if($url!=NULL) {
			$name='<a href=\''.$url.'\'>'.$name.'</a>';
		}
		if($remark==NULL) {
			$remark=get_na_string();
		}
		$res.='</tr>';
		$res.='<td>'.$name.'</td>';
		$res.='<td>'.$remark.'</td>';
		$res.='<td><img src=\''.$link_img.'\'/></td>';
		$res.='</tr>';
	}
	$result->free();
	$res.='</table>';
	return $res;
}

function create_certification($params) {
	$res='';
	// sending query
	$query=sprintf('SELECT id,name,slug,url,imageId,fromDate FROM TbOrganization where funcCertification order by name asc');
	$result=my_mysql_query($query);

	$res.=get_start_table();
	$res.='<tr>';
	$res.='<td style=\'width:20%\'>type of certification</td>';
	$res.='<td style=\'width:80%\'>date from which I am certified</td>';
	$res.='<td style=\'width:20%\'>image</td>';
	$res.='</tr>';
	// printing table rows
	while($row=$result->fetch_assoc()) {
		$id=$row['id'];
		$name=$row['name'];
		$slug=$row['slug'];
		$url=$row['url'];
		$fromDate=$row['fromDate'];
		$imageId=$row['imageId'];
		$link_img=link_to_direct('GetBlob.php?table=TbImage&select_field=id&select_id='.$imageId.'&data_field=smallData&name_field=slug&mime_field=smallMime');
		$res.='</tr>';
		$res.='<td><a href=\''.$url.'\'>'.$name.'</a></td>';
		$res.='<td>'.$fromDate.'</td>';
		$res.='<td><img src=\''.$link_img.'\'/></td>';
		$res.='</tr>';
	}
	$result->free();
	$res.='</table>';
	return $res;
}

?>
