<?php
class DB
{
	private $db;
	private static $instance = [];
	private function __construct($host, $port, $db, $username, $password)
	{
		if (!$this->db) {
			$this->db = new PDO("mysql:dbname={$db};host={$host}:{$port}", $username, $password, [
				PDO::ATTR_EMULATE_PREPARES => false,
				PDO::MYSQL_ATTR_FOUND_ROWS => true
			]);
		}
	}

	public static function instance($host, $port, $db, $username, $password)
	{
		$key = md5(serialize([$host, $port, $db, $username, $password]));
		if (empty(self::$instance[$key])) {
			self::$instance[$key] = new static($host, $port, $db, $username, $password);
		}
		return self::$instance[$key];
	}

	public function getData($sql, $values = [])
	{
		$dbh = $this->db->prepare($sql);
		foreach ($values as $k => $v) {
			$dbh->bindValue($k + 1, $v);
		}
		$dbh->execute();
		return $dbh->fetchAll();
	}

	public function execute($sql, $values = [])
	{
		$dbh = $this->db->prepare($sql);
		foreach ($values as $k => $v) {
			$dbh->bindValue($k + 1, $v);
		}
		if ($dbh->execute()) {
			if (stripos($sql, 'insert') === 0) {
				return $this->db->lastInsertId();
			}
			return $dbh->rowCount();
		}
	}
}
