<?php
/*
the panel controller is only available as admin
*/
class PanelController extends BaseController {
	public function indexAction($db, $user) {
		global $twig;

		if (!$user->isAdmin())
			throw new Exception("You don't have the permission to view this site", 1);

		$this->vars['admin'] = $user->isAdmin();

		$template = $twig->loadTemplate("panel.twig");
		echo $template->render($this->vars);
		exit(0);
	}

	public function saveAction($db, $user) {
		throw new Exception("The save function hasn't been implemented yet");		
	}

	public function prevAction($db, $user) {
		global $twig;
		global $makestatus;

		if (!$user->isAdmin())
			throw new Exception("You don't have the permission to view this site", 1);

		if (!isset($_POST['title']))
			throw new Exception("Please enter a title");

		if (!isset($_POST['text']))
			throw new Exception("Please enter a text");

		$data = $db->select('password', 'user', "WHERE name='admin' LIMIT 1");

		if (sha1($_POST['password']) !== $data[0]['password'])
			throw new Exception("You need to provide your admin password before you can perform an action");

		$prev = $twig->loadTemplate("panel.twig");
		$out = $prev->render(array('title' => $_POST['title'], 'text' => $_POST['text'], 'author' => 'admin', 'created' => 'now', 'prev' => '1', 'admin' => $user->isAdmin()));

		$tmp = new Twig_Environment(new Twig_Loader_String());
		$tmp->addFilter($makestatus);

		echo $tmp->render($out);	
	}
}
?>
