<?php

require("include/utils.php");
require("include/db.php");
require("include/na.php");

function create_lilypond() {

// sending query
$query=sprintf("SELECT id,title,subtitle,composer,copyright,style,piece,poet FROM TbMsLilypond");
$result=mysql_query($query);
assert($result);

echo "Some music I maintain in lilypond format...";
echo "<table style='empty-cells:show;width:100%;' border='1'>";
echo "<tr>";
echo "<td>title</td>";
echo "<td>subtitle</td>";
echo "<td>composer</td>";
echo "<td>copyright</td>";
echo "<td>style</td>";
echo "<td>piece</td>";
echo "<td>poet</td>";
echo "</tr>\n";

while($row=mysql_fetch_assoc($result)) {
	$id=$row["id"];
	$title=$row["title"];
	$subtitle=$row["subtitle"];
	$composer=$row["composer"];
	$copyright=$row["copyright"];
	$style=$row["style"];
	$piece=$row["piece"];
	$poet=$row["poet"];
	echo "</tr>";
	echo "<td>{$title}</td>";
	echo "<td>{$subtitle}</td>";
	echo "<td>{$composer}</td>";
	echo "<td>{$copyright}</td>";
	echo "<td>{$style}</td>";
	echo "<td>{$piece}</td>";
	echo "<td>{$poet}</td>";
	echo "</tr>\n";
}
assert(mysql_free_result($result));
echo "</table>";
echo "<br/>";

}
?>
