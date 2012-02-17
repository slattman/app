<?php
# app framework data.class.php
# v1.4 Brad Slattman - slattman@gmail.com
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
		if (count($parameters)) {
			$link = new mysqli(dbhost, dbuser, dbpass, dbname);
			$sql = array_shift($parameters);
			if (!$stmt = mysqli_prepare($link, $sql)) {
				$link->close();
				$this->app()->error('Please check your sql statement : unable to prepare');
			}
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
			mysqli_free_result($result);
			mysqli_stmt_close($stmt);
			$link->close();
		}
	}

}

?>