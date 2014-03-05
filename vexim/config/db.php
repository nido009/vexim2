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
			$raw = $this->replaceQuery();
			$raw = strtolower(preg_replace( "/\r|\n/", "", preg_replace('/\s+/', ' ',$raw)));
			$method = substr($raw, 0, 6);
			if (strtolower($method) == 'insert' || strtolower($method) == 'update' || strtolower($method) == 'delete') {
				$q = null;
				if (isset($this->_debugValues[':user_id'])) {
					global $dbh;
					$t = $dbh->prepare("SELECT * FROM users WHERE user_id = '".$this->_debugValues[':user_id']."'");
					$t->execute();
					$row = $t->fetch();
					$q = "Query with user ID '".$this->_debugValues[':user_id']."' => '".$row['username']."'";
				}
				if (isset($this->_debugValues[':domain_id'])) {
					global $dbh;
					$t = $dbh->prepare("SELECT * FROM domains WHERE domain_id = '".$this->_debugValues[':domain_id']."'");
					$t->execute();
					$row = $t->fetch();
					if ($q == null) {
						$q = "Query with domain ID '".$this->_debugValues[':domain_id']."' => '".$row['domain']."'";
					} else {
						$q .= " and domain ID '".$this->_debugValues[':domain_id']."' => '".$row['domain']."'";
					}
				}
				if (!empty($q)) {
					$log->lwrite($q);
				}
				$log->lwrite($raw);
				$log->lclose();
			}
			$t = parent::execute($values);
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

?>