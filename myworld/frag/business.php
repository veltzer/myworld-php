<?php

# TODO:
# get the queries out of here and into a query manager...
# Handle creation of tables via my own class.

function create_courses() {
	$res="";
	// sending query
	$query=sprintf("SELECT id,name FROM TbBsCourses");
	$result=mysql_query($query);
	assert($result);

	$res.=get_start_table();
	$res.="<tr>";
	$res.="<td>name</td>";
	$res.="</tr>\n";

	while($row=mysql_fetch_assoc($result)) {
		$id=$row["id"];
		$name=$row["name"];
		$res.="</tr>";
		$res.="<td>{$name}</td>";
		$res.="</tr>\n";
	}
	assert(mysql_free_result($result));
	$res.="</table>";
	return $res;
}

function create_consulting() {
	$res="";
	// sending query
	$query=sprintf("SELECT name,remark,url,imagePath FROM TbBsCompanies where funcConsulting order by name asc");
	$result=mysql_query($query);
	assert($result);

	$res.=get_start_table();
	$res.="<tr>";
	$res.="<td>name</td>";
	$res.="<td>remark</td>";
	$res.="<td>image</td>";
	$res.="</tr>\n";

	while($row=mysql_fetch_assoc($result)) {
		$name=$row["name"];
		$remark=$row["remark"];
		$url=$row["url"];
		$imagePath=link_to_resource($row["imagePath"]);
		if($url!=NULL) {
			$name="<a href='{$url}'>{$name}</a>";
		}
		if($remark==NULL) {
			$remark=get_na_string();
		}
		$res.="</tr>";
		$res.="<td>{$name}</td>";
		$res.="<td>{$remark}</td>";
		$res.="<td><img src='{$imagePath}'/></td>";
		$res.="</tr>\n";
	}
	assert(mysql_free_result($result));
	$res.="</table>";
	return $res;
}

function create_teaching() {
	$res="";
	// sending query
	$query=sprintf("SELECT name,remark,url,imagePath FROM TbBsCompanies where funcTeaching order by name asc");
	$result=mysql_query($query);
	assert($result);

	$res.=get_start_table();
	$res.="<tr>";
	$res.="<td>name</td>";
	$res.="<td>remark</td>";
	$res.="<td>image</td>";
	$res.="</tr>\n";

	while($row=mysql_fetch_assoc($result)) {
		$name=$row["name"];
		$remark=$row["remark"];
		$url=$row["url"];
		$imagePath=link_to_resource($row["imagePath"]);
		if($url!=NULL) {
			$name="<a href='{$url}'>{$name}</a>";
		}
		if($remark==NULL) {
			$remark=get_na_string();
		}
		$res.="</tr>";
		$res.="<td>{$name}</td>";
		$res.="<td>{$remark}</td>";
		$res.="<td><img src='{$imagePath}'/></td>";
		$res.="</tr>\n";
	}
	assert(mysql_free_result($result));
	$res.="</table>";
	return $res;
}

function create_certification() {
	$res="";
	// sending query
	$query=sprintf("SELECT name,url,imagePath,fromDate FROM TbBsCompanies where funcCertification order by name asc");
	$result=mysql_query($query);
	assert($result);

	$res.=get_start_table();
	$res.="<tr>";
	$res.="<td>type of certification</td>";
	$res.="<td>date from which I am certified</td>";
	$res.="<td>image</td>";
	$res.="</tr>\n";
	// printing table rows
	while($row=mysql_fetch_assoc($result)) {
		$name=$row["name"];
		$url=$row["url"];
		$imagePath=link_to_resource($row["imagePath"]);
		$fromDate=$row["fromDate"];
		$res.="</tr>";
		$res.="<td><a href='{$url}'>{$name}</a></td>";
		$res.="<td>{$fromDate}</td>";
		$res.="<td><img src='{$imagePath}'/></td>";
		$res.="</tr>\n";
	}
	assert(mysql_free_result($result));
	$res.="</table>";
	return $res;
}

?>
