<?php

function create_lilypond() {
	$res='';
	// sending query
	$query=sprintf('SELECT id,uuid,title,subtitle,composer,poet,style,piece,copyright,pages,idyoutube FROM TbMsLilypond');
	$result=my_mysql_query($query);

	$res.=multi_accordion_start();

	while($row=mysql_fetch_assoc($result)) {
		$id=$row['id'];
		$s_uuid=$row['uuid'];
		$s_title=val_or_na($row['title']);
		$s_subtitle=val_or_na($row['subtitle']);
		$s_composer=val_or_na($row['composer']);
		$s_poet=val_or_na($row['poet']);
		$s_style=val_or_na($row['style']);
		$s_piece=val_or_na($row['piece']);
		$s_copyright=val_or_na($row['copyright']);
		$s_pages=val_or_na($row['pages']);
		$link_ly=link_to_direct('GetRsBlob.php?slug='.$s_uuid.'-ly');
		$link_pdf=link_to_direct('GetRsBlob.php?slug='.$s_uuid.'-pdf');
		$link_ps=link_to_direct('GetRsBlob.php?slug='.$s_uuid.'-ps');
		$link_midi=link_to_direct('GetRsBlob.php?slug='.$s_uuid.'-midi');
		$link_wav=link_to_direct('GetRsBlob.php?slug='.$s_uuid.'-wav');
		$link_mp3=link_to_direct('GetRsBlob.php?slug='.$s_uuid.'-mp3');
		$link_ogg=link_to_direct('GetRsBlob.php?slug='.$s_uuid.'-ogg');
		$s_a_ly='<a href='.$link_ly.'>ly</a>';
		$s_a_pdf='<a href='.$link_pdf.'>pdf</a>';
		$s_a_ps='<a href='.$link_ps.'>ps</a>';
		$s_a_midi='<a href='.$link_midi.'>midi</a>';
		$s_a_wav='<a href='.$link_wav.'>wav</a>';
		$s_a_mp3='<a href='.$link_mp3.'>mp3</a>';
		$s_a_ogg='<a href='.$link_ogg.'>ogg</a>';
		$s_idyoutube=$row['idyoutube'];

		$header=$s_title;
		if($row['composer']!=NULL) {
			$header.=' / '.$s_composer;
		}
		if($row['poet']!=NULL && $s_poet!=$s_composer) {
			$header.=', '.$s_poet;
		}
		$body='';
		$body.='<ul>';
		if($row['title']!=NULL) {
			$body.='<li>title: '.$s_title.'</li>';
		}
		if($row['subtitle']!=NULL) {
			$body.='<li>subtitle: '.$s_subtitle.'</li>';
		}
		if($row['composer']!=NULL) {
			$body.='<li>composer: '.$s_composer.'</li>';
		}
		if($row['poet']!=NULL) {
			$body.='<li>poet: '.$s_poet.'</li>';
		}
		if($row['style']!=NULL) {
			$body.='<li>style: '.$s_style.'</li>';
		}
		if($row['piece']!=NULL) {
			$body.='<li>piece: '.$s_piece.'</li>';
		}
		if($row['copyright']!=NULL) {
			$body.='<li>copyright: '.$s_copyright.'</li>';
		}
		if($row['pages']!=NULL) {
			$body.='<li>pages: '.$s_pages.'</li>';
		}
		$links=array();
		# TODO: only add the links if I have the blobs...
		array_push($links,$s_a_ly);
		array_push($links,$s_a_pdf);
		array_push($links,$s_a_ps);
		array_push($links,$s_a_midi);
		array_push($links,$s_a_wav);
		array_push($links,$s_a_mp3);
		array_push($links,$s_a_ogg);

		# lets look and add links to the pngs...
		for($i=0; $i<$s_pages; $i++) {
			$j=$i+1;
			$link=link_to_direct('GetRsBlob.php?slug='.$s_uuid.'-png'.$j);
			$link='<a href=\''.$link.'\'>png'.$j.'</a>';
			array_push($links,$link);
		}

		$body.='<li>links: '.join(', ',$links).'</li>';
		$body.='<li>uuid: '.$s_uuid.'</li>';
		$body.='</ul>';
		# lets put a link to play the audio, currently it looks like the
		# audio plugin can only play mp3 so that's the only link that we
		# put...
		$body.='You can play the automatically generated mp3 file here...<br/>';
		$body.=get_audio_player(
			$link_mp3,
			$row['title'],
			$row['composer'],
			$row['poet']
		);
		if($row['idyoutube']!=NULL) {
			$body.='Here is a youtube performance of this song that I like...<br/>';
			$body.=youtube_embed($s_idyoutube,0.5);
		}
		$res.=multi_accordion_entry($header,$body);
	}
	my_mysql_free_result($result);
	$res.=multi_accordion_end();
	return $res;
}
?>
