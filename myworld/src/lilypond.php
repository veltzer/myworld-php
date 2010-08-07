<?php

function create_lilypond() {
	$show_style="div";
	$res="";
	// sending query
	$query=sprintf("SELECT id,title,subtitle,composer,poet,style,piece,copyright FROM TbMsLilypond");
	$result=mysql_query($query);
	assert($result);

	if($show_style=="table") {
		$res.=get_start_table();
		$res.="<tr>";
		$res.="<td>title</td>";
		$res.="<td>subtitle</td>";
		$res.="<td>composer</td>";
		$res.="<td>poet</td>";
		$res.="<td>style</td>";
		$res.="<td>piece</td>";
		$res.="<td>copyright</td>";
		$res.="<td>ly</td>";
		$res.="<td>pdf</td>";
		$res.="<td>ps</td>";
		$res.="<td>midi</td>";
		$res.="<td>wav</td>";
		$res.="<td>mp3</td>";
		$res.="<td>ogg</td>";
		$res.="</tr>\n";
	}
	if($show_style=="div") {
		$res.="<div class=\"accordion2\">";
	}

	while($row=mysql_fetch_assoc($result)) {
		$id=$row["id"];
		$s_title=val_or_na($row["title"]);
		$s_subtitle=val_or_na($row["subtitle"]);
		$s_composer=val_or_na($row["composer"]);
		$s_poet=val_or_na($row["poet"]);
		$s_style=val_or_na($row["style"]);
		$s_piece=val_or_na($row["piece"]);
		$s_copyright=val_or_na($row["copyright"]);
		$link_ly=link_to_direct("GetBlob.php?table=TbMsLilypond&id=$id&field=ly&type=text/plain&name_field=filebasename");
		$link_pdf=link_to_direct("GetBlob.php?table=TbMsLilypond&id=$id&field=pdf&type=application/pdf&name_field=filebasename");
		$link_ps=link_to_direct("GetBlob.php?table=TbMsLilypond&id=$id&field=ps&type=application/postscript&name_field=filebasename");
		$link_midi=link_to_direct("GetBlob.php?table=TbMsLilypond&id=$id&field=midi&type=audio/midi&name_field=filebasename");
		$link_wav=link_to_direct("GetBlob.php?table=TbMsLilypond&id=$id&field=wav&type=audio/x-wav&name_field=filebasename");
		$link_mp3=link_to_direct("GetBlob.php?table=TbMsLilypond&id=$id&field=mp3&type=audio/mpeg&name_field=filebasename");
		$link_ogg=link_to_direct("GetBlob.php?table=TbMsLilypond&id=$id&field=ogg&type=audio/ogg&name_field=filebasename");
		$s_a_ly="<a href='{$link_ly}'>ly</a>";
		$s_a_pdf="<a href='{$link_pdf}'>pdf</a>";
		$s_a_ps="<a href='{$link_ps}'>ps</a>";
		$s_a_midi="<a href='{$link_midi}'>midi</a>";
		$s_a_wav="<a href='{$link_wav}'>wav</a>";
		$s_a_mp3="<a href='{$link_mp3}'>mp3</a>";
		$s_a_ogg="<a href='{$link_ogg}'>ogg</a>";
		
		if($show_style=="table") {
			$res.="<tr>";
			$res.="<td>{$s_title}</td>";
			$res.="<td>{$s_subtitle}</td>";
			$res.="<td>{$s_composer}</td>";
			$res.="<td>{$s_poet}</td>";
			$res.="<td>{$s_style}</td>";
			$res.="<td>{$s_piece}</td>";
			$res.="<td>{$s_copyright}</td>";
			$res.="<td>{$s_a_ly}</td>";
			$res.="<td>{$s_a_pdf}</td>";
			$res.="<td>{$s_a_ps}</td>";
			$res.="<td>{$s_a_midi}</td>";
			$res.="<td>{$s_a_wav}</td>";
			$res.="<td>{$s_a_mp3}</td>";
			$res.="<td>{$s_a_ogg}</td>";
			$res.="</tr>\n";
		}
		if($show_style=="div") {
			$header=$s_title;
			if($row["composer"]!=NULL) {
				$header.=" / ".$s_composer;
			}
			if($row["poet"]!=NULL && $s_poet!=$s_composer) {
				$header.=", ".$s_poet;
			}
			$res.="<h3>{$header}</h3>";
			$res.="<div><ul>";
			if($row["subtitle"]!=NULL) {
				$res.="<li>subtitle: ${s_subtitle}</li>";
			}
			if($row["composer"]!=NULL) {
				$res.="<li>composer: ${s_composer}</li>";
			}
			if($row["poet"]!=NULL) {
				$res.="<li>poet: ${s_poet}</li>";
			}
			if($row["style"]!=NULL) {
				$res.="<li>style: ${s_style}</li>";
			}
			if($row["piece"]!=NULL) {
				$res.="<li>piece: ${s_piece}</li>";
			}
			if($row["copyright"]!=NULL) {
				$res.="<li>copyright: ${s_copyright}</li>";
			}
			$res.="<li>links: ${s_a_ly}, ${s_a_pdf}, ${s_a_ps}, ${s_a_midi}, ${s_a_wav}, ${s_a_mp3}, ${s_a_ogg}</li>";
			$res.="</ul>";
			# lets put a link to play the mp3
			$res.="[audio:${link_mp3}]";
			$res.="</div>";
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
