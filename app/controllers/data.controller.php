<?php
# app framework data.class.php
# v1.5 Brad Slattman - slattman@gmail.com
# # # # # # # # # # # # # # # # # # # # # # # # #

class data extends app {
	
	function __construct() { parent::__construct(true); }
	
	function populate() {
		$result = $this->app()->data->result;
		if (isset($result) and get_class($this) !== __CLASS__) {
			foreach ($result as $k => $v) {
				$this->$k = $v;
			}
		}
	}

	function attributes() {
		$attributes = array();
		foreach($this->fields as $field) {
			if(property_exists($this, $field) and strlen($this->$field)) {
				$attributes[$field] = $this->$field;
			}
		}
		return $attributes;
	}
	
	function reference($array){
		if (strnatcmp(phpversion(),'5.3') >= 0) {
			$references = array();
			foreach($array as $key => $value)
				$references[$key] = &$array[$key];
			return $references;
		}
		return $array;
	}

	function query($sql = '', $types = '', $parameters = array()) {
		/*$args = func_get_args();
		echo "<pre>";
		print_r($args);
		exit;*/
		$result = array();
		array_unshift($parameters, $types);
		$mysqli = new mysqli(dbhost, dbuser, dbpass, dbname);
		if (mysqli_connect_errno()) {
			$result['error'] = 'Connection failed: '.mysqli_connect_error();
		}
		if ($stmt = $mysqli->prepare($sql)) {
	        $method = new ReflectionMethod('mysqli_stmt', 'bind_param');
	        $method->invokeArgs($stmt, $this->reference($parameters));
			$stmt->execute();
			$meta = $stmt->result_metadata();
			if (!$meta) {           
				$result['insert_id'] = $stmt->insert_id;
			} else {
				$stmt->store_result();
				$params = array();
				$row = array();
				while ($field = $meta->fetch_field()) {
					$params[] = &$row[$field->name];
				}
				$meta->close();
				$method = new ReflectionMethod('mysqli_stmt', 'bind_result');
				$method->invokeArgs($stmt, $params);           
				while ($stmt->fetch()) {
					$arr = array();
					foreach($row as $key => $val) {
						$arr[$key] = $val;
					}
					$result[] = $arr;
				}
				if (count($result) == 1) $result = $result[0];
				$stmt->free_result();
			}
			$stmt->close();
		}
		$mysqli->close();
		$this->bind($result, 'result');
	} 

	public function create() {
		$types = '';
		$params = array();
		$index = $this->index;
		$attributes = $this->attributes();
		foreach ($attributes as $k => $v) {
			if (is_numeric($v)) {
				$types .= 'i';
			} else {
				$types .= 's';
			}
			$params[] = '?';
		}
		$this->app()->data->query(
			"insert into ".$this->table." (`".implode("`, `", array_keys($attributes))."`) values (".implode(", ", $params).")", 
			$types, 
			array_values($attributes)
		);
		$this->$index = $this->app()->data->result->insert_id;
	}

	public function read() {
		$types = '';
		$params = array();
		$attributes = $this->attributes();
		foreach ($attributes as $k => $v) {
			if ($v) {
				if (is_numeric($v)) {
					$types .= 'i';
				} else {
					$types .= 's';
				}
				$params[] = $k.' = ?';
			}
		}
		$this->app()->data->query(
			"select * from ".$this->table." where ".implode(" and ", $params), 
			$types, 
			array_values($attributes)
		);
		$this->populate();
	}
	
	public function update() {
		$types = '';
		$params = array();
		$index = $this->index;
		$attributes = $this->attributes();
		foreach ($attributes as $k => $v) {
			if (is_numeric($v)) {
				$types .= 'i';
			} else {
				$types .= 's';
			}
			$params[] = $k.' = ?';
		}
		$this->app()->data->query(
			"update ".$this->table." set ".implode(", ", $params)." where ".$this->index."=".$this->$index, 
			$types, 
			array_values($attributes)
		);
	}
	
	public function delete() {
		$types = '';
		$params = array();
		$attributes = $this->attributes();
		foreach ($attributes as $k => $v) {
			if ($v) {
				if (is_numeric($v)) {
					$types .= 'i';
				} else {
					$types .= 's';
				}
				$params[] = $k.' = ?';
			}
		}
		$this->app()->data->query(
			"delete from ".$this->table." where ".implode(" and ", $params), 
			$types, 
			array_values($attributes)
		);
	}

}

?>