<?php
class ContactController extends BaseController {
	public function indexAction($db, $user) {
		global $twig;

		$this->vars['admin'] = $user->isAdmin();

		$template = $twig->loadTemplate("contact.twig");
		echo $template->render($this->vars);
		exit(0);
	}

	public function sendAction($db, $user) {
		$this->vars['success'] = "Successfully send.";

		$this->indexAction($db, $user);
	}

	public function editAction($db, $user) {
		
	}
}
?>
