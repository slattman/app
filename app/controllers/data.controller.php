<?php
# app framework data.class.php
# v1.5 Brad Slattman - slattman@gmail.com
# # # # # # # # # # # # # # # # # # # # # # # # #

class data extends app {
	
	function __construct() { parent::__construct(true); }

	function reference($array){
		if (strnatcmp(phpversion(),'5.3') >= 0) {
			$references = array();
			foreach($array as $key => $value)
				$references[$key] = &$array[$key];
			return $references;
		}
		return $array;
	}

	function query() {
		$parameters = func_get_args();
		if (count($parameters) == 3) {
			$link = new mysqli(dbhost, dbuser, dbpass, dbname);
			$sql = array_shift($parameters);
			if (!$stmt = mysqli_prepare($link, $sql)) {
				$link->close();
				$this->app()->error('Please check your sql statement : unable to prepare: '.$sql);
			}
			array_splice($parameters, 1, 1, $parameters[1]);
			call_user_func_array(array($stmt, 'bind_param'), $this->reference($parameters));
			mysqli_stmt_execute($stmt);
			$result = mysqli_stmt_result_metadata($stmt);
			$fields = array();
			while ($field = mysqli_fetch_field($result)) {
				$name = $field->name;
				$fields[$name] = &$$name;
			}
			array_unshift($fields, $stmt);
			call_user_func_array('mysqli_stmt_bind_result', $fields);
			array_shift($fields);
			$results = array();
			while (mysqli_stmt_fetch($stmt)) {
				$temp = array();
				foreach($fields as $key => $val) { $temp[$key] = $val; }
				array_push($results, $temp);
			}
			if (count($results) == 1) $results = $results[0];
			$this->bind($results, 'results');
			$this->populate();
			mysqli_free_result($result);
			mysqli_stmt_close($stmt);
			$link->close();
		} else {
			$this->app()->error("Invalid parameter count.");
		}
		return;
	}
	
	function populate() {
		if (isset($this->app()->data->results) and get_class($this) !== __CLASS__) {
			$results = $this->app()->data->results;
			if (is_object($results)) {
				foreach ($results as $k => $v) {
					$this->$k = $v;
				}
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

	public function create() {
		$types = '';
		$params = array();
		$attributes = $this->attributes();
		foreach ($attributes as $k => $v) {
			if (is_numeric($v)) {
				$types .= 'i';
			} else {
				$types .= 's';
			}
			$params[] = '?';
		}
		$this->app()->data->query("insert into ".$this->table." (".implode(", ", array_keys($attributes)).") values (".implode(", ", $params).")", $types, array_values($attributes));
		$this->$index = mysql_insert_id();
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
		$this->app()->data->query("select * from ".$this->table." where ".implode(" and ", $params), $types, array_values($attributes));
		$this->populate();
	}
	
	public function update() {
		$types = '';
		$params = array();
		$attributes = $this->attributes();
		foreach ($attributes as $k => $v) {
			if (is_numeric($v)) {
				$types .= 'i';
			} else {
				$types .= 's';
			}
			$params[] = $k.' = ?';
		}
		$this->app()->data->query("update ".$this->table." set ".implode(", ", $params)." where ".$this->index."=".$this->$index, $types, array_values($attributes));
	}
	
	public function delete() {
		$this->app()->data->query("delete from ".$this->table." where ".$this->index." = ?", array('i', $this->$index));
	}

}

?>