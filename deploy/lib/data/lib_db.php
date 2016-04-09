<?php
/*
 * Database abstractions
 *
 * @package db
 */

use NinjaWars\core\data\DatabaseConnection;

/**
 * Run bound queries on the database.
 *
 * Use: query('select all from players limit :count', array('count'=>10));
 * Or: query('select all from players limit :count', array('count'=>array(10, PDO::PARAM_INT)));
 *
 * Note that it returns foreachable resultset object unless an array is specifically requested.
 */
function query($sql, $bindings=array(), $return_resultset=true) {
	DatabaseConnection::getInstance();
	$statement = DatabaseConnection::$pdo->prepare($sql);

	foreach ($bindings as $binding => $value) {
		if (is_array($value)) {
			$first = reset($value);
			$last  = end($value);
			// Cast the bindings when something to cast to was sent in.
			$statement->bindValue($binding, $first, $last);
		} else {
			$statement->bindValue($binding, $value);
		}
	}

	$statement->execute();

	if ($return_resultset) {
		return $statement;  // Returns a foreachable resultset
	} else {
		// Otherwise returns an associative array.
		return $statement->fetchAll(PDO::FETCH_ASSOC);
	}
}

/**
 * Wrapper to explicitly & simply get a resultset.
 */
function query_resultset($sql_query, $bindings=array()) {
	return query($sql_query, $bindings, true);
}

/**
 * Wrapper to explicitly & simply get a multi-dimensional array.
 */
function query_array($sql_query, $bindings=array()) {
	return query($sql_query, $bindings, false); // Set return_resultset to false to return the array.
}

/**
 * Insert sql, returns the id insert by default.
 */
function insert_query($insert_query, $bindings=array(), $sequence_name){
	query($insert_query, $bindings, true); // Don't try to return data in the initial query.
	$id = DatabaseConnection::$pdo->lastInsertId($sequence_name);
	return $id;
}

/**
 * Update query wrapper, returns the number of rows updated.
 */
function update_query($update_query, $bindings=array()){
	$updates = query($update_query, $bindings, true); // Return the resultset
	return $updates->rowCount();
}

/**
 * Run to just get the first row, for 1 row queries.
 */
function query_row($sql, $bindings=array()) {
    $resultset = query_resultset($sql, $bindings);
	return $resultset->fetch(PDO::FETCH_ASSOC);
}

/**
 * Get only the first result item.
 */
function query_item($sql, $bindings=array()) {
	$row = query_row($sql, $bindings);
	return (is_array($row) ? reset($row) : null);
}

/**
 * Shortcut for row count on a data set or pdo resultset.
 */
function rco($data){
	return (is_a($data, 'PDOStatement')? $data->rowCount() : count($data));
}

