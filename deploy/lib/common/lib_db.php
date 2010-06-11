<?php
/*
 * Database abstractions
 *
 * @package db
 */

/**
 * Run bound queries on the database.
 *
 * Use: query('select all from players limit :count', array('count'=>10));
 * Or: query('select all from players limit :count', array('count'=>array(10, PDO::PARAM_INT)));
 *
 * Note that it returns foreachable resultset object unless an array is specifically requested.
**/
function query($sql, $bindings=array(), $return_resultset=false){
	DatabaseConnection::getInstance();
	$statement = DatabaseConnection::$pdo->prepare($sql);
	foreach($bindings as $binding => $value){
		if(is_array($value)){
		    $first = reset($value);
		    $last = end($value);
		    // Cast the bindings when something to cast to was sent in.
			$statement->bindParam($binding, $first, $last);
		} else {
			$statement->bindValue($binding, $value);
		}
	}
	$statement->execute();

	if($return_resultset){
		return $statement;  // Returns a foreachable resultset
	}
	
	// Otherwise returns an associative array.
	return $statement->fetchAll(PDO::FETCH_ASSOC); 
}

// Wrapper to explicitly & simply get a resultset.
function query_resultset($sql_query, $bindings=array()){
	return query($sql_query, $bindings, $resultset=true);
}

// Run to just get the first row, for 1 row queries.
function query_row($sql, $bindings=array()){
    $resultset = query_resultset($sql, $bindings);
	return $resultset->fetch(PDO::FETCH_ASSOC);
}

// Get only the first result item.
function query_item($sql, $bindings=array()){
	$row = query_row($sql, $bindings);
	return is_array($row)? reset($row) : null;
}

?>
