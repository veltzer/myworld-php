<?php

function create_lilypond() {
	$res="";
	// sending query
	$query=sprintf("SELECT id,title,subtitle,composer,copyright,style,piece,poet FROM TbMsLilypond");
	$result=mysql_query($query);
	assert($result);

	$res.=get_start_table();
	$res.="<tr>";
	$res.="<td>title</td>";
	$res.="<td>subtitle</td>";
	$res.="<td>source</td>";
	$res.="<td>pdf</td>";
	$res.="<td>ps</td>";
	$res.="<td>midi</td>";
	$res.="<td>composer</td>";
	$res.="<td>copyright</td>";
	$res.="<td>style</td>";
	$res.="<td>piece</td>";
	$res.="<td>poet</td>";
	$res.="</tr>\n";

	while($row=mysql_fetch_assoc($result)) {
		$id=$row["id"];
		$title=val_or_na($row["title"]);
		$subtitle=val_or_na($row["subtitle"]);
		$composer=val_or_na($row["composer"]);
		$copyright=val_or_na($row["copyright"]);
		$style=val_or_na($row["style"]);
		$piece=val_or_na($row["piece"]);
		$poet=val_or_na($row["poet"]);
		$a_source=link_to_direct("GetBlob.php?table=TbMsLilypond&id=$id&field=source&type=text/plain");
		$a_pdf=link_to_direct("GetBlob.php?table=TbMsLilypond&id=$id&field=pdf&type=application/pdf");
		$a_ps=link_to_direct("GetBlob.php?table=TbMsLilypond&id=$id&field=ps&type=application/postscript");
		$a_midi=link_to_direct("GetBlob.php?table=TbMsLilypond&id=$id&field=midi&type=audio/midi");
		$res.="</tr>";
		$res.="<td>{$title}</td>";
		$res.="<td>{$subtitle}</td>";
		$res.="<td><a href='{$a_source}'>source</a></td>";
		$res.="<td><a href='{$a_pdf}'>pdf</a></td>";
		$res.="<td><a href='{$a_ps}'>ps</a></td>";
		$res.="<td><a href='{$a_midi}'>midi</a></td>";
		$res.="<td>{$composer}</td>";
		$res.="<td>{$copyright}</td>";
		$res.="<td>{$style}</td>";
		$res.="<td>{$piece}</td>";
		$res.="<td>{$poet}</td>";
		$res.="</tr>\n";
	}
	assert(mysql_free_result($result));
	$res.="</table>";
	return $res;
}
?>
