<?php
class DatabaseConnection
{
	// ************************
	// *** Member Variables ***

	static public $pdo;        /// < PDO - The database abstraction layer object 
	static private $instance;  /// < DatabaseConnection - Self-reference for singleton pattern

	// *** /Member Variables ***
	// *************************

	public function __construct()
    {
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
		{	// *** Connection exists, return the existing instance, effectively ignoring the constructor ***
			return self::$instance;
		}
	}

	public function nextSequenceValue($id_field, $table)
	{
		return $this->pdo->fetchColumn("SELECT nextval('".$table."_".$id_field."_seq')");
	}
}
?>
