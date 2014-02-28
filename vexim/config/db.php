<?php
class Database extends PDOStatement {
	protected $_debugValues = null;

	protected function __construct() {
    // need this empty construct()!
	}

	public function execute($values=array()) {
		global $log;
		$this->_debugValues = $values;
		try {
			$t = parent::execute($values);
			$raw = $this->replaceQuery();
			$method = substr($raw, 0, 6);
			if (strtolower($method) == 'insert' || strtolower($method) == 'update' || strtolower($method) == 'delete') {
				$log->lwrite(preg_replace( "/\r|\n/", "", preg_replace('/\s+/', ' ',$raw)));
				$log->lclose();
			}
		} catch (PDOException $e) {
			// maybe do some logging here?
			throw $e;
		}

		return $t;
	}

	public function _debugQuery($replaced=true) {
		$q = $this->queryString;
		if (!$replaced) {
			return $q;
		}

		return preg_replace_callback('/:([0-9a-z_]+)/i', array($this, '_debugReplace'), $q);
	}

	protected function replaceQuery() {
		$q = $this->queryString;
		$keys = array_keys($this->_debugValues);
		foreach ($keys as $key) {
			$q = str_replace($key, $this->_debugValues[$key], $q);
		}
		return $q;
	}
	
	protected function _debugReplace($m) {
		$v = $this->_debugValues[$m[1]];
		if ($v === null) {
			return "NULL";
		}
		if (!is_numeric($v)) {
			$v = str_replace("'", "''", $v);
		}

		return "'". $v ."'";
	}
}
/*
// have a look at http://www.php.net/manual/en/pdo.constants.php
$options = array(
  PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
  PDO::ATTR_STATEMENT_CLASS => array('MyPDOStatement', array()),
);

// create PDO with custom PDOStatement class
$pdo = new PDO($dsn, $username, $password, $options);

// prepare a query
$query = $pdo->prepare("INSERT INTO mytable (column1, column2, column3)
  VALUES (:col1, :col2, :col3)");

// execute the prepared statement
$query->execute(array(
  'col1' => "hello world",
  'col2' => 47.11,
  'col3' => null,
));

// output the query and the query with values inserted
var_dump( $query->queryString, $query->_debugQuery() );*/

?>