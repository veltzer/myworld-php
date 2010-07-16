<?php

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
echo "<td>source</td>";
echo "<td>pdf</td>";
echo "<td>ps</td>";
echo "<td>midi</td>";
echo "<td>composer</td>";
echo "<td>copyright</td>";
echo "<td>style</td>";
echo "<td>piece</td>";
echo "<td>poet</td>";
echo "</tr>\n";

while($row=mysql_fetch_assoc($result)) {
	$id=$row["id"];
	$title=val_or_na($row["title"]);
	$subtitle=val_or_na($row["subtitle"]);
	$composer=val_or_na($row["composer"]);
	$copyright=val_or_na($row["copyright"]);
	$style=val_or_na($row["style"]);
	$piece=val_or_na($row["piece"]);
	$poet=val_or_na($row["poet"]);
	$a_source="GetBlob.php?table=TbMsLilypond&id=$id&field=source&type=text/plain";
	$a_pdf="GetBlob.php?table=TbMsLilypond&id=$id&field=pdf&type=application/pdf";
	$a_ps="GetBlob.php?table=TbMsLilypond&id=$id&field=ps&type=application/postscript";
	$a_midi="GetBlob.php?table=TbMsLilypond&id=$id&field=midi&type=audio/midi";
	echo "</tr>";
	echo "<td>{$title}</td>";
	echo "<td>{$subtitle}</td>";
	echo "<td><a href='{$a_source}'>source</a></td>";
	echo "<td><a href='{$a_pdf}'>pdf</a></td>";
	echo "<td><a href='{$a_ps}'>ps</a></td>";
	echo "<td><a href='{$a_midi}'>midi</a></td>";
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
