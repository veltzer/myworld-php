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
	$res.='<td>category</td>';
	$res.='<td>name</td>';
	$res.='</tr>';

	while($row=mysql_fetch_assoc($result)) {
		$id=$row['id'];
		$category=$row['category'];
		$name=$row['name'];
		$res.='</tr>';
		$res.='<td>'.$category.'</td>';
		$res.='<td>'.$name.'</td>';
		$res.='</tr>';
	}
	my_mysql_free_result($result);
	$res.='</table>';
	return $res;
}

function create_consulting($params) {
	$res='';
	// sending query
	$query=sprintf('SELECT id,name,slug,remark,url FROM TbOrganization where funcConsulting order by name asc');
	$result=my_mysql_query($query);

	$res.=get_start_table();
	$res.='<tr>';
	$res.='<td>name</td>';
	$res.='<td>remark</td>';
	$res.='<td>image</td>';
	$res.='</tr>';

	while($row=mysql_fetch_assoc($result)) {
		$id=$row['id'];
		$name=$row['name'];
		$slug=$row['slug'];
		$remark=$row['remark'];
		$link_img=link_to_direct('GetBlob.php?table=TbOrganization&sfield=id&id='.$id.'&field=smallImage&type=image/png&name_field=slug');
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
	my_mysql_free_result($result);
	$res.='</table>';
	return $res;
}

function create_teaching($params) {
	$res='';
	// sending query
	$query=sprintf('SELECT id,name,slug,remark,url FROM TbOrganization where funcTeaching order by name asc');
	$result=mysql_query($query);
	assert($result);

	$res.=get_start_table();
	$res.='<tr>';
	$res.='<td>name</td>';
	$res.='<td>remark</td>';
	$res.='<td>image</td>';
	$res.='</tr>';

	while($row=mysql_fetch_assoc($result)) {
		$id=$row['id'];
		$name=$row['name'];
		$slug=$row['slug'];
		$remark=$row['remark'];
		$link_img=link_to_direct('GetBlob.php?table=TbOrganization&sfield=id&id='.$id.'&field=smallImage&type=image/png&name_field=slug');
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
	my_mysql_free_result($result);
	$res.='</table>';
	return $res;
}

function create_certification($params) {
	$res='';
	// sending query
	$query=sprintf('SELECT id,name,slug,url,fromDate FROM TbOrganization where funcCertification order by name asc');
	$result=mysql_query($query);
	assert($result);

	$res.=get_start_table();
	$res.='<tr>';
	$res.='<td>type of certification</td>';
	$res.='<td>date from which I am certified</td>';
	$res.='<td>image</td>';
	$res.='</tr>';
	// printing table rows
	while($row=mysql_fetch_assoc($result)) {
		$id=$row['id'];
		$name=$row['name'];
		$slug=$row['slug'];
		$url=$row['url'];
		$fromDate=$row['fromDate'];
		$link_img=link_to_direct('GetBlob.php?table=TbOrganization&sfield=id&id='.$id.'&field=smallImage&type=image/png&name_field=slug');
		$res.='</tr>';
		$res.='<td><a href=\''.$url.'\'>'.$name.'</a></td>';
		$res.='<td>'.$fromDate.'</td>';
		$res.='<td><img src=\''.$link_img.'\'/></td>';
		$res.='</tr>';
	}
	my_mysql_free_result($result);
	$res.='</table>';
	return $res;
}

?>
