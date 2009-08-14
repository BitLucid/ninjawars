<?php
/*
 * Utility routines for MySQL.
 */
//require_once('resources.php'); // *** So that database connection constants and paths can be dynamic. NO LONGER NEEDED.
// *** This is first accessed by the index page, so the path to the resources file is relative to index.php!!

class DBAccess {
	static private $pdo;             /// < ID - The pdo object used to connect to the database in safe ways.
	static private $instance;        /// < DBAccess - This is the "Singleton", which keeps only 1 db instance.
	static private $results, $rows, $data, $a_rows;
	
    
    function __construct()
    {
    	$this->Create(); // Makes the constructor call for the creation of the pdo connection.
    }

    function Create ($db=NULL) // *** first argument is no longer required, since it runs off resources file.
	{
		// *** Blank the result set and other data when the constructor is called.
		self::$results = null;
		self::$data = null;
		self::$rows = null;
		self::$a_rows = null;
		
		// *** DataLayer is a singleton, so if already instantiated just return that ***
		if (self::$instance === null)
		{	// *** DataLayer hasn't been instantiated yet, save the pointer, and return this ***
			try
			{	// *** SINGLE TIME CONNECTION TO THE DATABASE***
				self::$pdo = new PDO(CONNECTION_STRING);
				self::$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
			}
			catch (Exception $e)
			{   // *** We catch this error to keep the exception from throwing back essential connection data.
				throw new Exception('The Database connection failed.');
			}

			self::$instance = $this;
			return $this;
		}
		else
		{	// *** Util exists, return the existing instance, effectively ignoring the constructor ***
			return self::$instance;
		}
	}
	
	
	public function __destruct()
	{
		self::$instance = null;
	}

    # Use this function when the query will return multiple rows.  Use the Fetch
    # routine to loop through those rows.
    function Query ($query) {
		if (self::result = self::pdo->query($query)) // *** Runs the query through pdo and sets it as result.
		{
	            self::rows = self::result->rowCount(); 
	            // *** Sets both row counts, should probably be zero.
	            self::a_rows = self::rows;
		}
		else
		{
			if (DEBUG) // *** Only display the error if DEBUG is on, so for developers.
			{
				$errorInfo = self::pdo->errorInfo();
				die($errorInfo[2]);
			}
		}
    }


    # Use this function if the query will only return a
    # single data element.
    function QueryItem ($query) {
		self::QueryRow($query);
        return self::data[0];
    }

    # This function is useful if the query will only return a
    # single row.
    function QueryRow ($query) {
		self::Query($query);
		self::data = null;
		self::rows = null;
		if (isset($this->result))
		{
			self::data = self::result->fetch(PDO::FETCH_BOTH);
        	self::rows = self::result->rowCount();
        }
        return self::data;
    }
    
    
    function Insert ($query) {
		self::Query($query);
    }

    function Update ($query) {
		self::Query($query);
    }

    function Delete ($query) {
		self::Query($query);
    }


	// *** Get the data back.
    function Fetch ($row = null) {
    	self::data = null;
    	if(isset(self::result))
    	{
			if ($row === null)
			{
				self::data = self::result->fetch(PDO::FETCH_BOTH);
			}
			else
			{
				self::data = self::result->fetch(PDO::FETCH_BOTH, PDO::FETCH_ORI_ABS, $row);
			}
		}

		return self::data;
    }

    function FetchAssociative($row = null)
    {
    	self::data = null;
    	if (isset(self::result))
    	{
    		self::data = self::result->fetch(PDO::FETCH_ASSOC, PDO::FETCH_ORI_ABS, $row);
    	}
		return self::data;
    }
    
    function getRowCount()
    {
    	return self::a_rows;
    }
    /*
    * Alias of getRowcount().
    */
    function getAffectedRows()
    {
    	return self::getRowCount();
    }
    
    
    function getData()
    {
    	return self::data;
    }
	
	/* Wrapper function for the PDO full result set function fetchAll using only the default values */
	function fetchAll()
	{
		return self::result->fetchAll(); // *** Gets the fetchall from the pdo object.
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
		return self::QueryItem($sel);	
	}


	/*
	* Could use a method to utilize the pdo->fetchAll(PDO::FETCH_COLUMN, someColumnNum) to fetch column by column.
	* ...for when the display is occuring column-by-column.
	*/
    
}
?>
