<?php

// url to use this script:
// http://veltzer.net/~mark/php/pages/GetBlob.php?table=TbMsLilypond&id=5&field=pdf&type=application/pdf&name_field=filebasename

require('setup.php');
my_include('src/utils.php');

$p_table = my_get_get('table');
$p_select_field = my_get_get('select_field');
$p_select_id = my_get_get('select_id');
$p_data_field = my_get_get('data_field');
$p_name_field = my_get_get('name_field');
$p_mime_field = my_get_get('mime_field');

$debug=0;

# security...
assert($p_table=='TbImage');
assert($p_select_field=='id');
assert($p_data_field=='smallData');
assert($p_name_field=='slug');
assert($p_mime=='smallMime');

my_mysql_connect();
$query=sprintf('SELECT %s,%s,%s FROM %s where %s=%s',
	// not real escaping is on purpose here...
	mysql_real_escape_string($p_data_field),
	mysql_real_escape_string($p_name_field),
	mysql_real_escape_string($p_mime_field),
	mysql_real_escape_string($p_table),
	mysql_real_escape_string($p_select_field),
	my_mysql_real_escape_string($p_select_id)
);
if($debug) {
	echo $query.'<br/>';
}
$result=my_mysql_query_one_row($query);
$fileContent=$result[$p_data_field];
$fileName=$result[$p_name_field];
$fileMime=$result[$p_mime_field];
$fileLength=strlen($fileContent);
# You can see more HTTP headers that may improve stuff in
# http://en.wikipedia.org/wiki/List_of_HTTP_headers
# ideas are: Content-MD5, Content-Length, Last-Modified
# if you want to debug HTTP headers just use wget -S on the
# command line and compared the headers that you are generating
# with the headers that a regular content generates by using
# the web server...
header('Content-type: '.$fileMime);
header('Cache-Control: no-cache');
header('Content-Length: '.$fileLength);
header('Content-Disposition: attachment; filename='.$fileName.'.png');
echo $fileContent;
my_mysql_disconnect();

?>
