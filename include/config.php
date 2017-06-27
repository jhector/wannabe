<?php
error_reporting(E_ALL & ~E_NOTICE);

require_once 'twig/lib/Twig/Autoloader.php';

define('SIGNATURE', 'AwCof0ibApQjZ06PA3ZIB98Kv9DGFj');

$config['db_host'] = "localhost";
$config['db_user'] = "arthur";
$config['db_pass'] = "yoloswag";
$config['db_name'] = "db_arthur";
$config['db_prefix'] = "6karuhf843_";

$controllers = array(
	'BaseController',
	'DefaultController',
	'PanelController',
	'BugController',
	'LostController',
	'LoginController',
	'ContactController',
	'NewsController'
	);

Twig_Autoloader::register();
$loader = new Twig_Loader_Filesystem('skeleton');
$twig = new Twig_Environment($loader);
$twig->addFilter($makestatus);
?>
