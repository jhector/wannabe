<?php
require_once 'include/password-policy.php';

include('extension/filter.php');

include('include/config.php');
include('include/functions.php');

include('include/Database.php');
include('include/User.php');

foreach ($controllers as $controller) {
	include("controller/".$controller.".php");
}

try {
	$db = new Database();
	$user = new User($db);

	$front = new DefaultController();
	$controller = ucfirst(strtolower($_GET['site']))."Controller";

	if (class_exists($controller))
		$front = new $controller();

	$front->run($db, $user);

} catch (Exception $e) {
	$vars = array('error' => $e->getMessage(), 'admin' => 0);

	$template = $twig->loadTemplate("error.twig");
	echo $template->render($vars);
}
?>
