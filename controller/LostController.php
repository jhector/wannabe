<?php
class LostController extends BaseController {
	public function indexAction($db, $user) {
		global $twig;

		$this->vars['admin'] = $user->isAdmin();

		$template = $twig->loadTemplate("lost.twig");
		echo $template->render($this->vars);
		exit(0);
	}

	public function resetAction($db, $user) {
		global $twig;

		if (!isset($_GET['username']))
			throw new Exception("Please specify a username to reset the password");

		$username = trim(strtolower($_GET['username']));

		if ($username == "admin")
			throw new Exception("The password of the admin user cannot be reseted");

		$code = random(20);

		$data = $db->select("id, email", "user", "WHERE name='".mysql_real_escape_string($username)."'");

		if (empty($data)) {
			$this->vars['complete'] = "red: Username not found";
			$this->indexAction($db, $user);
			exit(0);
		}

		$send = 1; // mail-send not implemented yet

		if (!$send) {
			$this->vars['complete'] = "red: Failed to send mail";
			$this->indexAction($db, $user);
			exit(0);
		}

		$reset = array(
			'reset' => "'".mysql_real_escape_string($code)."'"
		);

		$db->update("user", $reset, "WHERE id=".intval($data[0]['id']));

		$this->vars['resetid'] = $data[0]['id'];

		$template = $twig->loadTemplate("reset.twig");
		echo $template->render($this->vars);	
	}

	public function updateAction($db, $user) {
		$id = $_GET['id'];
		$pass = $_GET['pass'];
		$pass2 = $_GET['pass2'];
		$code = $_GET['code'];

		if (empty($pass) || empty($pass2))
			throw new Exception("You must enter a new password");

		if (sha1($pass) != sha1($pass2))
			throw new Exception("Entered passwords don't match");
                        
    	$rules = array();
		$rules['min_length'] = 20;
    	$rules['max_length'] = 64;

    	$policy = new PasswordPolicy($rules);

    	$policy->min_lowercase_chars = 2;
    	$policy->min_uppercase_chars = 2;
    	$policy->min_numeric_chars = 4;

    	$valid = $policy->validate($pass);

		if (!$valid) {
        	$msg = 'Your password does not match the criteria:<br />';

			foreach ($policy->get_errors() as $k => $error)
            	$msg .= $error . "<br />";

			throw new Exception($msg);			
		}             

		if (empty($code))
			throw new Exception("Please sepcify a reset code");			

		$data = $db->select("id", "user", "WHERE reset='".mysql_real_escape_string($code)."' AND reset <>''");

		if (empty($data))
			throw new Exception("Invalid reset code");

		if ($data[0]['id'] == $id) {
			$reset = array(
				'password' => "'".mysql_real_escape_string(sha1($pass))."'",
				'reset' => "''"
			);

			$db->update("user", $reset, "WHERE id=".intval($id));

			$this->vars['complete'] = "green: Password successfully reseted";
			$this->indexAction($db, $user);
		} else {
			throw new Exception("Invalid user-id specified");
		}
	}
}
?>
