<?php
/*
 * Utility routines for the database.
 *
 * @package db
 * @subpackage db
 */
class DBAccess {
    var $pdo, $result, $rows, $data, $a_rows;

    function __construct()
    {
    	$this->Create(); // Makes the constructor call for the creation of the pdo connection.
    }
    // *** Constructor.
    function Create () {
        $this->pdo = new PDO(CONNECTION_STRING);

        // *** This could just use a function to call the database connection
        // with a static variable as PDO, or else instantiate it as static.
	}

    # Use this function when the query will return multiple rows.  Use the Fetch
    # routine to loop through those rows.
    function Query ($query) {
		if ($this->result = $this->pdo->query($query)) // *** Runs the query through pdo and sets it as result.
		{
	            $this->rows = $this->result->rowCount();
	            // *** Sets both row counts, should probably be zero.
	            $this->a_rows = $this->rows;
		}
		else // *** No result came back, not even 0 row result.
		{
			if (DEBUG) // *** Only display the error if DEBUG is on, so for developers.
			{
				$errorInfo = $this->pdo->errorInfo();
				die($errorInfo[2]);
			}
		}
    }

    /**
     * Create a prepared, protected query.
    **/
    function prepared($query, $binding_and_val){
    	$prepped = $this->pdo->prepare($query);
    	// bind the keys to the dummy strings...
    	// and the values as the values.
    	foreach($binding_and_val as $bind => $val){
    		$prepped->bindParam("$bind", $val, PDO::PARAM_STR);
    	}
    	$this->rows = $prepped->execute();
    	$this->result = $prepped->fetch(PDO::FETCH_BOTH);
    	if(DEBUG){ xdebug(); }
    	return $this->result;
    }


    # Use this function if the query will only return a
    # single data element.
    function QueryItem ($query) {
		$this->QueryRow($query);
        return $this->data[0];
    }

    # This function is useful if the query will only return a
    # single row.
    function QueryRow ($query, $type=PDO::FETCH_BOTH) {
		$this->Query($query);
		$this->data = null;
		if (is_object($this->result)){
			$this->data = $this->result->fetch($type);
        }
        return $this->data;
    }

    // Associative version of one row only.
    function QueryRowAssoc($query){
    	return $this->QueryRow($query, $type=PDO::FETCH_ASSOC);
    }

    // Should get multiple rows, only associative array.
    function QueryAssoc($query){
    	$this->Query($query); // Run the query
    	$res = $this->fetchAll();
    	return $res; // Fetch the results.
    }


    /* Wrapper function for the PDO full result set function fetchAll using only the default values */
	function fetchAll($query=null)
	{
	    if($query){
	        $this->Query($query);
	    }
	    $res = null;
	    if (is_object($this->result)){
	        $res = $this->result->fetchAll();
	    } else {
	        $rand = rand(1, 200);
	        if(1 == $rand){
	            error_log('Blank result object in util.php line 101 fetchAll func:'.print_r($this, true));
	        } // Periodic error logging.
	    }
		return $res; // *** Gets the fetchall from the pdo object.
	}

    /**
     * Gets a single row from the data set each call, or a specific row if requested.
    **/
    function Fetch ($row = null) {
    	$this->data = null;
    	if (isset($this->result))
    	{
			if ($row === null)
			{
				$this->data = $this->result->fetch(PDO::FETCH_BOTH);
			}
			else
			{
				$this->data = $this->result->fetch(PDO::FETCH_BOTH, PDO::FETCH_ORI_ABS, $row);
			}
		}

		return $this->data;
    }


    function FetchAssociative($row = null)
    {
    	$this->data = null;
    	if (isset($this->result))
    	{
    		$this->data = $this->result->fetch(PDO::FETCH_ASSOC, PDO::FETCH_ORI_ABS, $row);
    	}
		return $this->data;
    }

    function Insert ($query) {
		$this->Query($query);
		$this->last_insert_id = $this->result;
    }

    function Update ($query) {
		$this->Query($query);
    }

    function Delete ($query) {
		$this->Query($query);
		return $this->a_rows;
    }

    function getRowCount()
    {
    	return $this->a_rows;
    }

    /*
    * Alias of getRowcount().
    */
    function getAffectedRows()
    {
    	return $this->getRowCount();
    }


    function getData()
    {
    	return $this->data;
    }




	/**
	*   @brief A function to get the next sequence value after being given a table name and a id field name
	*   @param $id_field is a the primary key of the table.
	*   @param $table is a the table name.
	*   @param $full is a the full sequence name if it's different from. (not yet needed).
	*   @return int.
	*/
	function nextSequenceValue($id_field, $table, $full=null)
	{
		$sel = "SELECT nextval('".$table."_".$id_field."_seq')";
		return $this->QueryItem($sel);
	}

	function lastInsertId()
	{
		if($id = $this->last_insert_id){
			$this->last_insert_id = null;
			return $id;
		} else {
			return $this->pdo->lastInsertId();
		}
	}

	/*
	* Could use a method to utilize the pdo->fetchAll(PDO::FETCH_COLUMN, someColumnNum) to fetch column by column.
	*/

}
?>
