<?php

# TODO:
# get the queries out of here and into a query manager...
# Handle creation of tables via my own class.

function create_courses() {
	// sending query
	$query=sprintf("SELECT id,name FROM TbBsCourses");
	$result=mysql_query($query);
	assert($result);

	echo get_start_table();
	echo "<tr>";
	echo "<td>name</td>";
	echo "</tr>\n";

	while($row=mysql_fetch_assoc($result)) {
		$id=$row["id"];
		$name=$row["name"];
		echo "</tr>";
		echo "<td>{$name}</td>";
		echo "</tr>\n";
	}
	assert(mysql_free_result($result));
	echo "</table>";
	echo "<br/>";
}

function create_consulting() {
	// sending query
	$query=sprintf("SELECT name,remark,url,imagePath FROM TbBsCompanies where funcConsulting order by name asc");
	$result=mysql_query($query);
	assert($result);

	echo get_start_table();
	echo "<tr>";
	echo "<td>name</td>";
	echo "<td>remark</td>";
	echo "<td>image</td>";
	echo "</tr>\n";

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
		echo "</tr>";
		echo "<td>{$name}</td>";
		echo "<td>{$remark}</td>";
		echo "<td><img src='{$imagePath}'/></td>";
		echo "</tr>\n";
	}
	assert(mysql_free_result($result));
	echo "</table>";
	echo "<br/>";
}

function create_teaching() {
	// sending query
	$query=sprintf("SELECT name,remark,url,imagePath FROM TbBsCompanies where funcTeaching order by name asc");
	$result=mysql_query($query);
	assert($result);

	echo "Companies that I teach or have taught in the past...";
	echo get_start_table();
	echo "<tr>";
	echo "<td>name</td>";
	echo "<td>remark</td>";
	echo "<td>image</td>";
	echo "</tr>\n";

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
		echo "</tr>";
		echo "<td>{$name}</td>";
		echo "<td>{$remark}</td>";
		echo "<td><img src='{$imagePath}'/></td>";
		echo "</tr>\n";
	}
	assert(mysql_free_result($result));
	echo "</table>";
	echo "<br/>";
}

function create_certification() {
	// sending query
	$query=sprintf("SELECT name,url,imagePath,fromDate FROM TbBsCompanies where funcCertification order by name asc");
	$result=mysql_query($query);
	assert($result);

	echo get_start_table();
	echo "<tr>";
	echo "<td>type of certification</td>";
	echo "<td>date from which I am certified</td>";
	echo "<td>image</td>";
	echo "</tr>\n";
	// printing table rows
	while($row=mysql_fetch_assoc($result)) {
		$name=$row["name"];
		$url=$row["url"];
		$imagePath=link_to_resource($row["imagePath"]);
		$fromDate=$row["fromDate"];
		echo "</tr>";
		echo "<td><a href='{$url}'>{$name}</a></td>";
		echo "<td>{$fromDate}</td>";
		echo "<td><img src='{$imagePath}'/></td>";
		echo "</tr>\n";
	}
	assert(mysql_free_result($result));
	echo "</table>";
	echo "<br/>";
}

?>
