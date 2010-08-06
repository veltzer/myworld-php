<?php

function create_lilypond() {
	$show_style="div";
	$res="";
	// sending query
	$query=sprintf("SELECT id,title,subtitle,composer,copyright,style,piece,poet FROM TbMsLilypond");
	$result=mysql_query($query);
	assert($result);

	if($show_style=="table") {
		$res.=get_start_table();
		$res.="<tr>";
		$res.="<td>title</td>";
		$res.="<td>subtitle</td>";
		$res.="<td>ly</td>";
		$res.="<td>pdf</td>";
		$res.="<td>ps</td>";
		$res.="<td>midi</td>";
		$res.="<td>composer</td>";
		$res.="<td>copyright</td>";
		$res.="<td>style</td>";
		$res.="<td>piece</td>";
		$res.="<td>poet</td>";
		$res.="</tr>\n";
	}
	if($show_style=="div") {
		$res.="<div class=\"accordion2\">";
	}

	while($row=mysql_fetch_assoc($result)) {
		$id=$row["id"];
		$title=val_or_na($row["title"]);
		$subtitle=val_or_na($row["subtitle"]);
		$composer=val_or_na($row["composer"]);
		$copyright=val_or_na($row["copyright"]);
		$style=val_or_na($row["style"]);
		$piece=val_or_na($row["piece"]);
		$poet=val_or_na($row["poet"]);
		$a_ly=link_to_direct("GetBlob.php?table=TbMsLilypond&id=$id&field=ly&type=text/plain&name_field=filebasename");
		$a_pdf=link_to_direct("GetBlob.php?table=TbMsLilypond&id=$id&field=pdf&type=application/pdf&name_field=filebasename");
		$a_ps=link_to_direct("GetBlob.php?table=TbMsLilypond&id=$id&field=ps&type=application/postscript&name_field=filebasename");
		$a_midi=link_to_direct("GetBlob.php?table=TbMsLilypond&id=$id&field=midi&type=audio/midi&name_field=filebasename");
		$a_ly="<a href='{$a_ly}'>ly</a>";
		$a_pdf="<a href='{$a_pdf}'>pdf</a>";
		$a_ps="<a href='{$a_ps}'>ps</a>";
		$a_midi="<a href='{$a_midi}'>midi</a>";
		
		if($show_style=="table") {
			$res.="<tr>";
			$res.="<td>{$title}</td>";
			$res.="<td>{$subtitle}</td>";
			$res.="<td>{$a_ly}</td>";
			$res.="<td>{$a_pdf}</td>";
			$res.="<td>{$a_ps}</td>";
			$res.="<td>{$a_midi}</td>";
			$res.="<td>{$composer}</td>";
			$res.="<td>{$copyright}</td>";
			$res.="<td>{$style}</td>";
			$res.="<td>{$piece}</td>";
			$res.="<td>{$poet}</td>";
			$res.="</tr>\n";
		}
		if($show_style=="div") {
			$res.="<h3>{$title} / {$composer}</h3>";
			$res.="<p><ul>";
			$res.="<li>subtitle: ${subtitle}</li>";
			$res.="<li>composer: ${composer}</li>";
			$res.="<li>style: ${style}</li>";
			$res.="<li>piece: ${piece}</li>";
			$res.="<li>poet: ${poet}</li>";
			$res.="<li>links ${a_ly}, ${a_pdf}, ${a_ps}, ${a_midi}</li>";
			$res.="</ul></p>";
		}
	}
	assert(mysql_free_result($result));
	if($show_style=="table") {
		$res.="</table>";
	}
	if($show_style=="div") {
		$res.="</div>";
	}
	return $res;
}
?>
