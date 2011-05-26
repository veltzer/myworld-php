<?php
$directory=$_GET['path'];
if(is_dir($directory)) {
	echo "{ label: 'name', identifier: 'path', items: [ ";
	$handler = opendir($directory);
	while ($file = readdir($handler)) {
		if ($file != '.' && $file != '..') {
			$path=$directory.$file;
			if(is_dir($path)) {
				echo "{ path: '",$path,"', name: '",$file,"', type:'item' ,children: [ { path: '", $path,"', type:'sub'} ]},";
			} else {
				echo "{ path: '",$path,"', name: '",$file,"', type:'item' },";
			}
		}
	}
	closedir($handler);
	echo "] }";
} else {
	echo "ERROR: path is ",$directory;
}
?>
