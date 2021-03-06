<?php
function create_music($params) {
	$limit=50;
	if(array_key_exists('limit',$params)) {
		$limit=$params['limit'];
	}
	$res='';
	$query=sprintf('SELECT id,title,track,artist,album,comment,year,genre,filename,date FROM TbMsHearing ORDER BY date DESC LIMIT %s',
		$limit
	);
	$result=my_mysql_query($query);
	$res.=multi_accordion_start();
	while($row=$result->fetch_assoc()) {
		$header=$row['title'].' / '.$row['album'].' / '.$row['artist'];
		$body='';
		$body.='<ul>';
		$body.='<li>id: '.$row['id'].'</li>';
		$body.='<li>title: '.$row['title'].'</li>';
		$body.='<li>track: '.$row['track'].'</li>';
		$body.='<li>artist: '.$row['artist'].'</li>';
		$body.='<li>album: '.$row['album'].'</li>';
		$body.='<li>comment: '.$row['comment'].'</li>';
		$body.='<li>year: '.$row['year'].'</li>';
		$body.='<li>genre: '.$row['genre'].'</li>';
		$body.='<li>filename: '.$row['filename'].'</li>';
		$body.='<li>date: '.$row['date'].'</li>';
		$body.='</ul>';
		$res.=multi_accordion_entry($header,$body);
	}
	my_mysql_free_result($result);
	$res.=multi_accordion_end();
	return $res;
}
?>
