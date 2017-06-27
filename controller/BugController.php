<?php
class BugController extends BaseController {
	public function indexAction($db, $user) {
		global $twig;

		$this->vars['admin'] = $user->isAdmin();

		$tracked = $user->getBugs();
		$where = join(',', $tracked);

		if ($where == '')
			$where = '-1';

		$listed = $db->select("*", "bug", "WHERE id IN ($where)");

		$this->vars['bugs'] = $listed;

		$template = $twig->loadTemplate("bug.twig");
		echo $template->render($this->vars);
    	exit(0);
	}

	public function addAction($db, $user) {
		$allowed = array('gif', 'jpeg', 'jpg', 'png');
		$tmp = explode(".", $_FILES['prove']['name']);
		$ext = end($tmp);
		$name = basename($_FILES['prove']['name']);
		$filename = random(16).".".$ext;

		if (!isset($_POST['rating']) || empty($_POST['rating']))
			throw new Exception("You must supply a rating");
		if (!isset($_POST['title']) || empty($_POST['title']))
			throw new Exception("You must supply a title");

		$rating = mysql_real_escape_string($_POST['rating']);
		$title = mysql_real_escape_string($_POST['title']);
		$prove = "";

		if (intval($rating) < 1 || intval($rating) > 3)
			$rating = 1;

		if ($_FILES['prove']['size'] > 0) {
			if ($_FILES['prove']['size'] < 100000 && in_array($ext, $allowed)) {
				move_uploaded_file($_FILES['prove']['tmp_name'], "upload/".$filename);
				$prove = "upload/".$filename;
			} else {
				if (!in_array($ext, $allowed))
					throw new Exception("File extension not allowed");
				else
					throw new Exception("The file is larger than 100 kB");
			}
		}

		$prove = mysql_real_escape_string($prove);

		$data = array(
			'rating' => $rating,
			'title' => "'$title'",
			'prove' => "'$prove'",
			'approved' => 1
		);

		$id = $db->insert("bug", $data);
		$user->addBug($id);

		$this->indexAction($db, $user);
	}

	public function dlAction($db, $user) {
		$id = isset($_GET['id']) ? intval($_GET['id']) : -1;

		if (!in_array($id, $user->getBugs()))
			throw new Exception("You are not allowed to access this 'prove'");
		
		$data = $db->select("prove", "bug", "WHERE id=$id LIMIT 1");

		$file = "./".$data[0]['prove'];

		if (substr_count($file, "../") > 0)
			throw new Exception('No need to go back :), nothing of interest for you');

		if (file_exists($file)) {
			header('Content-Description: File Transfer');
			header('Content-Type: application/octet-stream');
			header('Content-Disposition: attachment; filename='.basename($file));
			header('Content-Transfer-Encoding: binary');
			header('Expires: 0');
			header('Cache-Control: must-revalidate');
			header('Pragma: public');
			header('Content-Length: ' . filesize($file));
			ob_clean();
			flush();
			readfile($file);
			exit;
		} else {
			throw new Exception("File doesn't exist");
		}

		$this->indexAction($db, $user);
	}
}
?>
