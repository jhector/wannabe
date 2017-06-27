<?php
class Database {
	public function __construct() {
		global $config;

		$conn = mysql_connect($config['db_host'], $config['db_user'], $config['db_pass']);

		if (!$conn)
			throw new Exception('Couldn\'t connect to  database'.mysql_error());

		$db_selected = mysql_select_db($config['db_name']);

		if (!$db_selected) 
			throw new Exception('Database doesn\'t exist: '.mysql_error());
	}

	public function exists($table, $condition) {
		global $config;

		$qry = "SELECT id FROM ".$config['db_prefix'].$table." ".$condition;
		$result = mysql_query($qry);

		if (!$result)
			throw new Exception(mysql_error());

		if (mysql_num_rows($result) > 0)
			return 1;
		else
			return 0;
	}

	public function select($what, $table, $condition) {
		global $config;

        if (preg_match('/[^a-zA-Z0-9_]information_schema[^a-zA-Z0-9_]/', strtolower($keys)))
			throw new Exception('Operation not allowed');

		$qry = "SELECT $what FROM ".$config['db_prefix'].$table." ".$condition;
		$result = mysql_query($qry);

		if (!$result)
			throw new Exception(mysql_error());

		$ret = array();
		while ($row = mysql_fetch_array($result))
			array_push($ret, $row);

		return $ret;
	}

	public function insert($table, $data) {
		global $config;

		$keys = array();
		$values = array();
		foreach($data as $key => $value) {
			array_push($keys, $key);
			array_push($values, $value);
		}

		$keys = "(".join(',', $keys).")";
		$values = "(".join(',', $values).")";

        if (preg_match('/[^a-zA-Z0-9_]information_schema[^a-zA-Z0-9_]/', strtolower($keys))) 
			throw new Exception("Operation not allowed");

        if (preg_match('/[^a-zA-Z0-9_]information_schema[^a-zA-Z0-9_]/', strtolower($values)))
			throw new Exception("Operation not allowed");

		$qry = "INSERT INTO ".$config['db_prefix'].$table." $keys VALUES $values";
		$result = mysql_query($qry);

		if (!$result)
			throw new Exception(mysql_error());

		return mysql_insert_id();
	}

	public function delete($table, $condition) {
		global $config;

		$qry = "DELETE FROM ".$config['db_prefix'].$table." ".$condition;
		$result = mysql_query($qry);

		if (!$result)
			throw new Exception(mysql_error());

		return true;
	}

	public function update($table, $data, $condition) {
		global $config;

		$set = '';
		$tmp = array();
		foreach($data as $key => $value) {
			array_push($tmp, $key."=".$value);
		}

		$set = join(", ", $tmp);
		$qry = "UPDATE ".$config['db_prefix'].$table." SET $set $condition";
		$result = mysql_query($qry);

		if (!$result)
			throw new Exception(mysql_error());
	}
}
?>
