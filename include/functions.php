<?php
function random($len) {
	$file = fopen('/dev/urandom', 'r');
	$urandom = fread($file, $len);
	fclose($file);

	$ret = '';
	for ($i=0; $i<$len; $i++) 
		$ret .= dechex(ord($urandom[$i]));

	return $ret;
}
?>