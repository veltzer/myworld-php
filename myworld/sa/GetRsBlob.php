<?php

// url to use this script:
// http://veltzer.net/~mark/php/pages/GetBlob.php?table=TbMsLilypond&id=5&field=pdf&type=application/pdf&name_field=filebasename

require('setup.php');
my_include('src/utils.php');

$p_slug = $_GET['slug'];

$debug=0;

my_mysql_connect();
$query=sprintf('SELECT id,name,slug,mime,data FROM TbRsBlob where slug="%s"',
	$p_slug
);
if($debug==1) {
	echo $query.'<br/>';
}
$result=my_mysql_query($query);
# make sure we really have a result
assert($result);
# we should only get one result...
assert(mysql_num_rows($result)==1);
$row=$result->fetch_assoc();
$r_id=$row['id'];
$r_name=$row['name'];
$r_slug=$row['slug'];
$r_mime=$row['mime'];
$r_data=$row['data'];
# You can see more HTTP headers that may improve stuff in
# http://en.wikipedia.org/wiki/List_of_HTTP_headers
# ideas are: Content-MD5, Content-Length, Last-Modified
# if you want to debug HTTP headers just use wget -S on the
# command line and compared the headers that you are generating
# with the headers that a regular content generates by using
# the web server...
header('Content-type: '.$r_mime);
header('Cache-Control: no-cache');
header('Content-Length: '.strlen($r_data));
header('Content-Disposition: attachment; filename='.$r_name);
echo $r_data;
my_mysql_disconnect();

?>
