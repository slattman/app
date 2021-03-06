<?php
/*

	Copyright 2012 the app framework - slattman@gmail.com
	This file is part of the app framework.

	The app framework is free software: you can redistribute it and/or modify
	it under the terms of the GNU General Public License as published by
	the Free Software Foundation, either version 3 of the License, or
	(at your option) any later version.

	The app framework is distributed in the hope that it will be useful,
	but WITHOUT ANY WARRANTY; without even the implied warranty of
	MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
	GNU General Public License for more details.

	You should have received a copy of the GNU General Public License
	along with the app framework.  If not, see <http://www.gnu.org/licenses/>.

*/

class data extends app {
	
	function __construct() { parent::__construct(true); }
	
	function populate() {
		$result = $this->result;
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
		$result = array();
		array_unshift($parameters, $types);
		$mysqli = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
		if (mysqli_connect_errno()) {
			$result['error'] = 'Connection failed: ' . mysqli_connect_error();
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
		$this->query(
			"insert into " . $this->table . " (`" . implode("`, `", array_keys($attributes)) . "`) values (" . implode(", ", $params) . ")", 
			$types, 
			array_values($attributes)
		);
		$this->$index = $this->result->insert_id;
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
		$this->query(
			"select * from " . $this->table . " where " . implode(" and ", $params), 
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
		$this->query(
			"update " . $this->table . " set " . implode(", ", $params) . " where " . $this->index . "=" . $this->$index, 
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
		$this->query(
			"delete from " . $this->table . " where " . implode(" and ", $params), 
			$types, 
			array_values($attributes)
		);
	}

}

?>