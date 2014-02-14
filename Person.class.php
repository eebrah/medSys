<?php

Class Person extends Base {

	private $name;
	private $gender;
	
	function setName( $name ) { $this -> name = $name; }
	
	function getName() { return $this -> name; }
	
	function setGender( $gender ) { $this -> gender = $gender; }
	
	function getGender() { return $this -> gender; }
	
	function save( $returnType = 0 ) {
		
		GLOBAL $dbh;
		
		$query = '
INSERT INTO `personDetails` (
	  `uniqueID`
	, `name`
	, `gender`
)
VALUES (
	  "' . $this -> getUniqueID(). '"
	, "' . $this -> getName() . '"
	, "' . $this -> getGender() . '"
)';
		
		switch( $returnType ) {
			
			case "0" :
			default : {		// return a boolean result

				$returnValue = false;

				try {

					$dbh -> beginTransaction();

						$dbh -> exec( $query );

					$dbh -> commit();

					$returnValue = true;

				}
				catch( PDOException $e ) {

				   print "Error[ 101 ]: " . $e -> getMessage() . "<br/>";
				   die();

				}

			}
			break;
			
			case "1" : {
				
				$returnValue = $query;
			
			}
			break;
		
		}
		
		return $returnValue;
	
	}
	
	function load( $returnType = 0 ) {
		
		GLOBAL $dbh;
		
		$query = '
SELECT
	  `name`
	, `gender`
FROM
	`personDetails`
WHERE
	`uniqueID` = "' . $this -> getUniqueID() . '"';
		
		switch( $returnType ) {
			
			case "0" :
			default : {	// return a boolean result

				$returnValue = false;

				try {

					$statement = $dbh -> prepare( $query );
					$statement -> execute();

					$row = $statement -> fetch();

					$this -> setName( $row[ "name" ] );
					$this -> setGender( $row[ "gender" ] );
					
					$returnValue = true;

				}
				catch( PDOException $e ) {

				   print "Error[ 102 ]: " . $e -> getMessage() . "<br/>";
				   die();

				}

			}
			break;
			
			case "1" : {
				
				$returnValue = $query;
			
			}
			break;
		
		}
		
		return $returnValue;
	
	}
	
	function update($returnType = 0 ) {
		
		GLOBAL $dbh;
		
		$query = '
UPDATE
	`personDetails`
SET
	  `name` = "' . $this -> getName() . '"
	, `gender` = "' . $this -> getGender() . '"
WHERE
	`uniqueID` = "' . $this -> getUniqueID() . '"';
		
		switch( $returnType ) {
			
			case "0" :
			default : {		// return a boolean result

				$returnValue = false;

				try {

					$dbh -> beginTransaction();

						$dbh -> exec( $query );

					$dbh -> commit();

					$returnValue = true;

				}
				catch( PDOException $e ) {

				   print "Error[ 101 ]: " . $e -> getMessage() . "<br/>";
				   die();

				}

			}
			break;
			
			case "1" : {
				
				$returnValue = $query;
			
			}
			break;
		
		}
		
		return $returnValue;
	
	}
	
	function delete( $returnType = 0 ) {
		
		GLOBAL $dbh;
		
		$query = '';
		
		switch( $returnType ) {
			
			case "0" :
			default : {		// return a boolean result

				$returnValue = false;

				try {

					$dbh -> beginTransaction();

						$dbh -> exec( $query );

					$dbh -> commit();

					$returnValue = true;

				}
				catch( PDOException $e ) {

				   print "Error[ 101 ]: " . $e -> getMessage() . "<br/>";
				   die();

				}

			}
			break;
			
			case "1" : {
				
				$returnValue = $query;
			
			}
			break;
		
		}
		
		return $returnValue;
	
	}
		
	function __construct( $uniqueID = "00000",
	                      $name = "",
	                      $gender = 0 ) {
							  
		parent::__construct( $uniqueID );
		
		if( $uniqueID != '00000' ) {
			
			$this -> load();
		
		}
		else {
			
			$this -> setName( $name );
			$this -> setGender( $gender );
		
		}
		
	}
}

?>
