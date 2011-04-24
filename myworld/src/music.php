<?php

function create_music() {
	$res='';
	$query=sprintf('SELECT id,title,artist FROM TbMsHearing ORDER BY date DSC LIMIT 30');
	$result=my_mysql_query($query);

	$res.=multi_accordion_start();

	while($row=mysql_fetch_assoc($result)) {
		$id=$row['id'];
		$s_title=$row['title'];
		$s_artist=$row['artist'];
		$s_subtitle=val_or_na($row['subtitle']);
		$s_composer=val_or_na($row['composer']);
		$s_poet=val_or_na($row['poet']);
		$s_style=val_or_na($row['style']);
		$s_piece=val_or_na($row['piece']);
		$s_copyright=val_or_na($row['copyright']);
		$s_pages=val_or_na($row['pages']);
		$s_epdfs=val_or_na($row['epdfs']);

		$header=$row['title'].' / '.$row['artist'];
		$body='';
		$body.='<ul>';
		$body.='<li>id: '.$row['id'].'</li>';
		$body.='<li>title: '.$row['title'].'</li>';
		$body.='<li>artist: '.$row['artist'].'</li>';
		$body.='</ul>';
		$res.=multi_accordion_entry($header,$body);
	}
	my_mysql_free_result($result);
	$res.=multi_accordion_end();
	return $res;
}
?>
