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
function query($sql, array $bindings = [], bool $return_resultset = true): array | \PDOStatement
{
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
 * Wrapper to get a multi-dimensional array.
 */
function query_array($sql_query, array $bindings = []): array
{
    return query($sql_query, $bindings)->fetchAll(PDO::FETCH_ASSOC);
}

/**
 * Update query wrapper, returns the number of rows updated.
 */
function update_query($update_query, array $bindings = []): int
{
    $updates = query($update_query, $bindings, true); // Return the resultset
    return $updates->rowCount();
}

/**
 * Insert a row, if returning is used it will return the id
 */
function insert_query($insert_query, array $bindings = [], bool $return_resultset = true): array | \PDOStatement
{
    return query($insert_query, $bindings, $return_resultset);
}

/**
 * Run to just get the first row, for 1 row queries.
 */
function query_row($sql, array $bindings = []): array | bool
{
    return query($sql, $bindings)->fetch(PDO::FETCH_ASSOC);
}

/**
 * Get only the first result item.
 */
function query_item($sql, array $bindings = []): int | float | string | null
{
    $row = query_row($sql, $bindings);
    return (is_array($row) ? reset($row) : null);
}
