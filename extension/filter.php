<?php
require_once 'twig/lib/Twig/SimpleFilter.php';

$makestatus = new Twig_SimpleFilter('makestatus', function($string) {
	return preg_replace('/(red|green): (.*)/e', '\'<div style="color:$1;">\'.strtolower("$2").\'</div>\'', $string);
}, array('is_safe' => array('html')));
?>