<?php

require("include/utils.php");
require("include/db.php");
require("include/na.php");

function create_works() {

// sending query
$query=sprintf("SELECT * FROM TbWkProducer");
$result=mysql_query($query);
$producers=array();
assert($result);
while($row=mysql_fetch_assoc($result)) {
	$producers[$row['id']]=$row;
}
#debug: print the array...
#print_r($producers);
assert(mysql_free_result($result));

// sending query
$query=sprintf("SELECT * FROM TbWkWorkType");
$result=mysql_query($query);
$types=array();
assert($result);
while($row=mysql_fetch_assoc($result)) {
	$types[$row['id']]=$row;
}
#debug: print the array...
#print_r($types);
assert(mysql_free_result($result));

// sending query
$query=sprintf("SELECT * FROM TbLcNamed");
$result=mysql_query($query);
$locations=array();
assert($result);
while($row=mysql_fetch_assoc($result)) {
	$locations[$row['id']]=$row;
}
#debug: print the array...
#print_r($locations);
assert(mysql_free_result($result));

// sending query
$query=sprintf("SELECT * FROM TbIdPerson");
$result=mysql_query($query);
$persons=array();
assert($result);
while($row=mysql_fetch_assoc($result)) {
	$persons[$row['id']]=$row;
}
#debug: print the array...
#print_r($persons);
assert(mysql_free_result($result));

// sending query
$query=sprintf("SELECT * FROM TbWkWork");
$result=mysql_query($query);
assert($result);

$fields_num=mysql_num_fields($result);

echo "Works that I have viewed";
#echo "<div style='overflow: auto; height:400px;'>";
echo "<table style='empty-cells:show;' border='1'><tr>";
// printing table headers
for($i=0; $i<$fields_num; $i++)
{
	$field=mysql_fetch_field($result);
	if($field->name=='producerId') {
		$field->name="producer";
		$prodid=$i;
	}
	if($field->name=='viewerId') {
		$field->name="viewer";
		$viewerid=$i;
	}
	if($field->name=='locationId') {
		$field->name="location";
		$locationid=$i;
	}
	if($field->name=='typeId') {
		$field->name="type";
		$typeid=$i;
	}
	if($field->name=='creatorId') {
		$field->name="creator";
		$creatorid=$i;
	}
	if($field->name=='size') {
		$sizeid=$i;
	}
	if($field->name=='length') {
		$lengthid=$i;
	}
	echo "<td>{$field->name}</td>";
}
echo "</tr>\n";
// printing table rows
while($row=mysql_fetch_row($result))
{
	#handle producers
	#<a href="url">Link text</a>
	if($row[$prodid]!=NULL) {
		$url=$producers[$row[$prodid]]['url'];
		$name=$producers[$row[$prodid]]['name'];
		$row[$prodid]="<a href='{$url}'>{$name}</a>";
	} else {
		$row[$prodid]=get_na_string();
	}
	if($row[$typeid]!=NULL) {
		$name=$types[$row[$typeid]]['name'];
		$row[$typeid]=$name;
	} else {
		$row[$typeid]=get_na_string();
	}
	if($row[$locationid]!=NULL) {
		$name=$locations[$row[$locationid]]['name'];
		$row[$locationid]=$name;
	} else {
		$row[$locationid]=get_na_string();
	}
	if($row[$viewerid]!=NULL) {
		$name=$persons[$row[$viewerid]]['firstname']." ".$persons[$row[$viewerid]]['surname'];
		$row[$viewerid]=$name;
	} else {
		$row[$viewerid]=get_na_string();
	}
	if($row[$creatorid]!=NULL) {
		$name=$persons[$row[$creatorid]]['firstname']." ".$persons[$row[$creatorid]]['surname'];
		$row[$creatorid]=$name;
	} else {
		$row[$creatorid]=get_na_string();
	}
	if($row[$sizeid]!=NULL) {
		$row[$sizeid]=formatSize($row[$sizeid]);
	}
	if($row[$lengthid]!=NULL) {
		$row[$lengthid]=formatTimeperiod($row[$lengthid]);
	}
	echo "<tr>";

	// $row is array... foreach( .. ) puts every element
	// of $row to $cell variable
	foreach($row as $cell) {
		if($cell==NULL) {
			$cell=get_na_string();
		}
		echo "<td>$cell</td>";
	}

	echo "</tr>\n";
}
assert(mysql_free_result($result));
echo "</table>";
echo "<br/>";
#echo "</div>";

echo "Some statistics...<br/>";
$table="TbWkWork";

$query=sprintf("SELECT count(*) FROM %s",mysql_real_escape_string($table));
$result=mysql_query($query);
assert($result);
$row=mysql_fetch_row($result);
echo $query.' = '.$row[0]."<br/>";
assert(mysql_free_result($result));

$query=sprintf("SELECT avg(rating) FROM %s",mysql_real_escape_string($table));
$result=mysql_query($query);
assert($result);
$row=mysql_fetch_row($result);
echo $query.' = '.$row[0]."<br/>";
assert(mysql_free_result($result));

$query=sprintf("SELECT count(distinct rating) FROM %s",mysql_real_escape_string($table));
$result=mysql_query($query);
assert($result);
$row=mysql_fetch_row($result);
echo $query.' = '.$row[0]."<br/>";
assert(mysql_free_result($result));

$query=sprintf("SELECT count(distinct viewerId) FROM %s",mysql_real_escape_string($table));
$result=mysql_query($query);
assert($result);
$row=mysql_fetch_row($result);
echo $query.' = '.$row[0]."<br/>";
assert(mysql_free_result($result));

$query=sprintf("SELECT count(distinct locationId) FROM %s",mysql_real_escape_string($table));
$result=mysql_query($query);
assert($result);
$row=mysql_fetch_row($result);
echo $query.' = '.$row[0]."<br/>";
assert(mysql_free_result($result));

$query=sprintf("SELECT count(distinct creatorId) from %s",mysql_real_escape_string($table));
$result=mysql_query($query);
assert($result);
$row=mysql_fetch_row($result);
echo $query.' = '.$row[0]."<br/>";
assert(mysql_free_result($result));

$query=sprintf("SELECT sum(length) from %s",mysql_real_escape_string($table));
$result=mysql_query($query);
assert($result);
$row=mysql_fetch_row($result);
echo $query.' = '.formatTimeperiod($row[0])."<br/>";
assert(mysql_free_result($result));

$query=sprintf("SELECT sum(size) from %s",mysql_real_escape_string($table));
$result=mysql_query($query);
assert($result);
$row=mysql_fetch_row($result);
echo $query.' = '.formatSize($row[0])."<br/>";
assert(mysql_free_result($result));

$query=sprintf("SELECT count(distinct typeId) from %s",mysql_real_escape_string($table));
$result=mysql_query($query);
assert($result);
$row=mysql_fetch_row($result);
echo $query.' = '.$row[0]."<br/>";
assert(mysql_free_result($result));

echo "<br/>";

}

?>
