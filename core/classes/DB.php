<?php
/*
 * Name: GECode
 * Author: Marko Murumaa
 * Created: February 2015 - July 2015
 * Website: www.markofy.eu/gecode
*/

class DB {
	private static 	$_instance_1 = null;
	private static 	$_instance_2 = null;
	private $_pdo, 
			$_query, 
			$_error = false, 
			$_results,
			$_world_ins,
			$_count = 0;

	private function __construct($world_in) {
		try {
				
			$world = ($world_in == null) ? Session::get('world') : $world_in;

			if ($world == 1) {
				$database_name = Config::get('mysql/db_default');
			} elseif ($world == 2) {
				$database_name = Config::get('mysql/db_2');
			} else {
				$database_name = Config::get('mysql/db_default');
			}
			$this->_pdo = new PDO('mysql:host=' . Config::get('mysql/host') . ';dbname=' . $database_name . ';charset=utf8', Config::get('mysql/username'), Config::get('mysql/password'));
		} catch(PDOException $e) {
			header('Location: install.php');
			die();
		}
	}

	public static function getInstance($world_in = null) {
		$world = ($world_in == null) ? Session::get('world') : $world_in;

		if ($world == 1) {
			if(!isset(self::$_instance_1)) {
				self::$_instance_1 = new DB($world);
			}
			$return = self::$_instance_1;
		} elseif ($world == 2) {
			if(!isset(self::$_instance_2)) {
				self::$_instance_2 = new DB($world);
			}
			$return = self::$_instance_2;
		} else {
			if(!isset(self::$_instance_1)) {
				self::$_instance_1 = new DB($world);
			}
			$return = self::$_instance_1;
		}
		return $return;
	}

	public function query($sql, $params = array()) {
		$this->_error = false;
		if($this->_query = $this->_pdo->prepare($sql)) {
			$x = 1;
			if(count($params)) {
				foreach($params as $param) {
					$this->_query->bindValue($x, $param);
					$x++;
				}
			}

			if($this->_query->execute()) {
				$this->_results = $this->_query->fetchAll(PDO::FETCH_OBJ);
				$this->_count = $this->_query->rowCount();
			} else {
				$this->_error = true;
			}
		}
		return $this;
	}

	public function action($action, $table, $where = array()) {
		if(count($where) === 3) {
			$operators = array('=', '>', '<', '>=', '<=');

			$field 		= $where[0];
			$operator 	= $where[1];
			$value 		= $where[2];

			if(in_array($operator, $operators)) {
				$sql = "{$action} FROM {$table} WHERE {$field} {$operator} ?";
				
				if(!$this->query($sql, array($value))->error()) {
					return $this;
				}
			}
		}
		return false;
	}

	public function get($table, $where) {
		return $this->action('SELECT *', $table, $where);
	}

	public function delete($table, $where) {
		return $this->action('DELETE', $table, $where);
	}

	public function insert($table, $fields = array()) {
		if(count($fields)) {
			$keys = array_keys($fields);
			$values = null;
			$x = 1;

			foreach($fields as $field) {
				$values .= '?';
				if($x < count($fields)) {
					$values .= ', ';
				}
				$x++;
			}

			$sql = "INSERT INTO {$table} (`" . implode('`, `', $keys) . "`) VALUES ({$values})";

			if(!$this->query($sql, $fields)->error()) {
				return true;
			}
		}
		return false;
	}

	public function update($table, $id, $fields) {
		$set = '';
		$x = 1;

		foreach($fields as $name => $value) {
			$set .= "{$name} = ?";
			if($x < count($fields)) {
				$set .= ', ';
			}
			$x++;
		}

		$sql = "UPDATE {$table} SET {$set} WHERE id = {$id} ";
		
		if(!$this->query($sql, $fields)->error()) {
			return true;
		}		
		return false;
	}

	public function results() {
		return $this->_results;
	}

	public function first() {
		return $this->results()[0];
	}

	public function error() {
		return $this->_error;
	}

	public function count() {
		return $this->_count;
	}
}
