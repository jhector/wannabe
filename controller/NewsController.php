<?php
class NewsController extends BaseController {
	public function indexAction($db, $user) {
		global $twig;

		$news = $db->select("*", "news", "ORDER BY created DESC LIMIT 10");

		$this->vars['news'] = $news;
		$this->vars['admin'] = $user->isAdmin();

		$template = $twig->loadTemplate("news.twig");
		echo $template->render($this->vars);
		exit(0);
	}
}
?>
