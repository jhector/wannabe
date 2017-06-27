<?php
class LoginController extends BaseController {
	public function indexAction($db, $user) {
		global $twig;

		if (!isset($_POST['user'], $_POST['pass']))
			throw new Exception("Please supply a username and password");

		$username = mysql_real_escape_string(trim($_POST['user']));

		if ($username == "guest") {
			$this->vars['message'] = "red: Guest login disabled";
		} else {
			$data = $db->select("*", "user", "WHERE name='$username' LIMIT 1");

			if (empty($data)) {
				$this->vars['message'] = "red: User does not exist";
			} else {
				$pass = $_POST['pass'];
				if (sha1($pass) === $data[0]['password']) {
					$id = $data[0]['userid'];
					$hash = $data[0]['password'];

					$user->setUser($id, $hash);
					$user->setAdmin($data[0]['admin']);
				} else {
					$this->vars['message'] = "red: Wrong password";
				}
			}
		}

		$this->vars['admin'] = $user->isAdmin();

		$template = $twig->loadTemplate("welcome.twig");
		echo $template->render($this->vars);
		exit(0);
	}
}
?>
