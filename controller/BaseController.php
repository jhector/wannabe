<?php
class BaseController {
	protected $action;
	protected $site;
	protected $vars;

	public function __construct() {
		if (isset($_GET['action']))
			$this->action = strtolower($_GET['action'])."Action";
		elseif (isset($_POST['action']))
			$this->action = strtolower($_POST['action'])."Action";
		else 
			$this->action = "indexAction";
		
		if (isset($_GET['site']))
			$this->site = strtolower($_GET['site']);
		elseif (isset($_POST['site']))
			$this->site = strtolower($_POST['site']);
		else 
			$this->site = "welcome";

		$this->vars = array();
	}

	public function run($db, $user) {
		if (in_array($this->action, get_class_methods($this))) {
			$this->{$this->action}($db, $user);
		} else {
			throw new Exception("Controller doesn't have a handler for the action: ".$this->action);
		}
	}
}
?>