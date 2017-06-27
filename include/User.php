<?php
class User {
	public $id;
	public $admin;
	public $hash;
	public $bugs;
	public $mac;

	public function __construct($db) {
		if (!isset($_COOKIE['user_id'], $_COOKIE['user_hash'], $_COOKIE['user_bugs'], $_COOKIE['user_mac'])) {
			$this->admin = 0;
			$this->id = random(16);
			$this->hash = sha1($this->id);
			$this->bugs = array();
			$this->mac = sha1(SIGNATURE . $this->id . $this->hash . base64_encode(serialize($this->bugs)));

			setcookie('user_id', $this->id);
			setcookie('user_hash', $this->hash);
			setcookie('user_bugs', base64_encode(serialize($this->bugs)));
			setcookie('user_mac', $this->mac);
		} else {
			$verify = SIGNATURE . $_COOKIE['user_id'] . $_COOKIE['user_hash'] . $_COOKIE['user_bugs'];

			if (sha1($verify) != $_COOKIE['user_mac']) {
				setcookie('user_id', '', time()-3600);
				setcookie('user_hash', '', time()-3600);
				setcookie('user_bugs', '', time()-3600);
				setcookie('user_mac', '', time()-3600);
				throw new Exception("Cookie has been modified");
			}

			$this->id = $_COOKIE['user_id'];
			$this->hash = $_COOKIE['user_hash'];
			$this->mac = $_COOKIE['user_mac'];
			$this->admin = $db->exists("user", "WHERE userid='".mysql_real_escape_string($_COOKIE['user_id'])."' AND admin=1 LIMIT 1");

			$this->bugs = unserialize(base64_decode($_COOKIE['user_bugs']));
			if (!is_array($this->bugs))
				$this->bugs = array();

			$this->bugs = array_map('intval', $this->bugs);
		}
	}

	public function setUser($id, $hash) {
		$this->id = $id;
		$this->hash = $hash;
		$this->bugs = array();
		$this->mac = sha1(SIGNATURE . $this->id . $this->hash . base64_encode(serialize($this->bugs)));

		$this->updateCookie();
	}

	public function updateCookie() {
		setcookie('user_id', $this->id);
		setcookie('user_hash', $this->hash);
		setcookie('user_bugs', base64_encode(serialize($this->bugs)));
		setcookie('user_mac', $this->mac);
	}

	public function addBug($bug_id) {
		array_push($this->bugs, intval($bug_id));

		$this->mac = sha1(SIGNATURE . $this->id . $this->hash . base64_encode(serialize($this->bugs)));
		$this->updateCookie();
	}

	public function getId() {
		return $this->id;
	}

	public function getHash() {
		return $this->hash;
	}

	public function isAdmin() {
		return $this->admin;
	}

	public function setAdmin($status) {
		$this->admin = $status;
	}

	public function getBugs() {
		return $this->bugs;
	}
}
?>
