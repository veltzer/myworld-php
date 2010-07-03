<?php

require("include/utils.php");
require("include/na.php");

function create_business() {

# TODO:
# get the queries out of here and into a query manager...
# Handle creation of tables via my own class.

// sending query
$query=sprintf("SELECT id,name FROM TbBsCourses");
$result=mysql_query($query);
assert($result);

echo "Courses that I teach or have taught in the past...";
echo "<table style='empty-cells:show;width:100%;' border='1'>";
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

// sending query
$query=sprintf("SELECT name,remark,url,imagePath FROM TbBsCompanies where funcConsulting order by name asc");
$result=mysql_query($query);
assert($result);

echo "Companies that I consult or have consulted in the past...";
echo "<table style='empty-cells:show;width:100%' border='1'>";
echo "<tr>";
echo "<td>name</td>";
echo "<td>remark</td>";
echo "<td>image</td>";
echo "</tr>\n";

while($row=mysql_fetch_assoc($result)) {
	$name=$row["name"];
	$remark=$row["remark"];
	$url=$row["url"];
	$imagePath=get_root()."/".$row["imagePath"];
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

// sending query
$query=sprintf("SELECT name,remark,url,imagePath FROM TbBsCompanies where funcTeaching order by name asc");
$result=mysql_query($query);
assert($result);

echo "Companies that I teach or have taught in the past...";
echo "<table style='empty-cells:show;width:100%' border='1'>";
echo "<tr>";
echo "<td>name</td>";
echo "<td>remark</td>";
echo "<td>image</td>";
echo "</tr>\n";

while($row=mysql_fetch_assoc($result)) {
	$name=$row["name"];
	$remark=$row["remark"];
	$url=$row["url"];
	$imagePath=get_root()."/".$row["imagePath"];
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

// sending query
$query=sprintf("SELECT name,url,imagePath,fromDate FROM TbBsCompanies where funcCertification order by name asc");
$result=mysql_query($query);
assert($result);

echo "My Certifications...";
echo "<table style='empty-cells:show;width:100%' border='1'>";
echo "<tr>";
echo "<td>type of certification</td>";
echo "<td>date from which I am certified</td>";
echo "<td>image</td>";
echo "</tr>\n";
// printing table rows
while($row=mysql_fetch_assoc($result)) {
	$name=$row["name"];
	$url=$row["url"];
	$imagePath=get_root()."/".$row["imagePath"];
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
