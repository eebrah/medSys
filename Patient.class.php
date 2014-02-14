<?php

require_once( "Person.class.php" );

Class Patient extends Person {
	
	private $patientID;	
	private $dateOfRegistration;		// Date at which the member was registered
	
	private $status = false;
	
	private $contacts = Array();		// Each student can have multiple people to contact, typically three
	
	private $validationErrors = Array();
	
	function setPatientID( $schoolID ) { $this -> patienID = $schoolID; }
	
	function getPatientID() { return $this -> patientID; }
	
	function setDateOfRegistration( $dateOfRegistration ) { $this -> dateOfRegistration = $dateOfRegistration; }
	
	function getDateOfRegistration() { return $this -> dateOfRegistration; }

	function setStatus( $status ) { $this -> status = $status; }

	function getStatus() { return $this -> status; }
	
	function addContact( $contactID ) { array_push( $this -> contacts, $contactID ); }
	
	function getContacts() { return $this -> contacts; }
	
	function validate( $returnType = 0 ) {
		
		switch( $returnType ) {
			
			case "0" :
			default : {
				
				$returnValue = false;
				
				if( strlen( $this -> getName() ) > 0 ) { 
					
					$returnValue = true; 
				
				}
				else {
					
					$this -> validationErrors[ "name" ] = "name too short";
				
				}
				
				if( $this -> getDateOfRegistration() < date( "Y-m-d H:i:s" ) ) { 
					
					$returnValue = true;
				
				}
				else {
					
					$this -> validationErrors[ "dateOfRegistration" ] = "the provided date of registration is in the future";
				
				}
				
			}
			break;
			
			case "1" : {
				
				$returnValue = Array();
			
			}
			break;
		
		}
		
		return $returnValue;
		
	}
	
	function displayForm( $formType = 0 ) {
		
		$action = "add";
		
		switch( $formType ) {
			
			case "0" :
			default : {
				
				$action = "add";
			
			}
			break;
			
			case "1" : 
			case "2" : {
				
				$action = "edit";
			
			}
			break;
			
		}
		
		$returnValue = '
<form action="?section=patients&amp;action=' . $action . '"
      method="post">
	<fieldset class="info">
		<legend>patient info</legend>
		<input type="hidden"
		       name="target"
		       value="' . $this -> getUniqueID() . '" />
		<div class="row">
			<label for="name">name</label>
			<input type="text"
			       name="name"
			       required="required"';
			       
		if( $formType > 0 ) {
			
			$returnValue .= '
				   value="' . $this -> getName() . '"';
		
		}
			       
		$returnValue .= ' />
		</div>
		<div class="row">
			<label for="gender">gender</label>
			<select name="gender">
				<option value="0">---</option>
				<option value="1">male</option>
				<option value="2">female</option>
			</select>
		</div>
		<div class="row">
			<label for="dateOfRegistration">date of registration</label>
			<input type="datetime"
			       name="dateOfRegistration"
			       value="' . date( "Y-m-d H:i:s") . '" />
		</div>
	</fieldset>
	<fieldset class="buttons">
		<button type="reset">reset</button>
		<button type="submit">submit</button>
	</fieldset>
</form>';
		
		return $returnValue;
	
	}
	
	function save( $returnType = 0 ) {
		
		GLOBAL $dbh;
		
		$query = '
INSERT INTO `patientDetails` (
	  `uniqueID`
	, `dateOfRegistration`
)
VALUES (
	  "' . $this -> getUniqueID() . '"
	, "' . $this -> getDateOfRegistration() . '"
)';
		
		switch( $returnType ) {
			
			case "0" :
			default : {		// return a boolean result

				$returnValue = false;

				try {

					$dbh -> beginTransaction();

						$dbh -> exec( parent::save( 1 ) );

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
	
	function load($returnType = 0 ) {
		
		GLOBAL $dbh;
		
		$query = '
SELECT
	  `dateOfRegistration`
	, `status`
FROM
	`patientDetails`
WHERE
	`uniqueID` = "' . $this -> getUniqueID() . '"';
		
		switch( $returnType ) {
			
			case "0" :
			default : {// return a boolean result

				$returnValue = false;
				
				if( parent::load() ) {
					
					try {

						$statement = $dbh -> prepare( $query );
						$statement -> execute();

						$row = $statement -> fetch();

						$this -> setDateOfRegistration( $row[ "dateOfRegistration" ] );
						$this -> setStatus( $row[ "status" ] );
						
						$returnValue = true;

					}
					catch( PDOException $e ) {

					   print "Error[ 102 ]: " . $e -> getMessage() . "<br/>";
					   die();

					}
					
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
	`patientDetails`
SET
	  `dateOfRegistration` = "' . $this -> getDateOfRegistration() . '"
	, `status` = "' . $this -> getStatus() . '"
WHERE
	`uniqueID` = "' . $this -> getUniqueID() . '"';
		
		switch( $returnType ) {
			
			case "0" :
			default : {		// return a boolean result

				$returnValue = false;

				try {

					$dbh -> beginTransaction();

						$dbh -> exec( parent::update( 1 ) );

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
		
		$query = '
DELETE
	*
FROM
	`patientDetails`
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
	
	function __construct( $uniqueID = "00000",
						  $name = "",
						  $gender = 0,
	                      $dateOfRegistration = "0000-00-00" ) {
		
		parent::__construct( $uniqueID, $name, $gender );
		
		if( $uniqueID != "00000" ) {
			
			$this -> load();
			
		}
		else {
			
			$this -> setDateOfRegistration( date( "Y-m-d H:i:s" ) );
			
			if( $dateOfRegistration != "0000-00-00" ) {
			
				$this -> setDateOfRegistration( $dateOfRegistration );
				
			}
		
		}
	
	}
	

}

function getPatients( $returnType = 0 ) {
	
	GLOBAL $dbh;
	
	$query = '
SELECT
	`uniqueID`
FROM
	`patientDetails`
WHERE
	1
ORDER BY
	`dateOfRegistration`';
	
	switch( $returnType ) {

		case "0" : {

			$returnValue = Array();

			try {

				$statement = $dbh -> prepare( $query );
				$statement -> execute();

				$results = $statement -> fetchAll();

				foreach( $results as $result ) {

					array_push( $returnValue, $result[ "uniqueID" ] );

				}

			}
			catch( PDOException $e ) {

			   print "Error!: " . $e -> getMessage() . "<br/>";
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

function isPatient( $returnType = 0, $patientID ) {
	
	GLOBAL $dbh;
	
	$query = '
SELECT
	*
FROM
	`patientDetails`
WHERE
	`uniqueID` = "' . $patientID . '"
AND
	`status` = 1';
	
	switch( $returnType ) {
		
		case "0" :
		default : {

			$returnValue = true;

			try {

				$statement = $dbh -> prepare( $query );
				$statement -> execute();

				$results = $statement -> fetchAll();

				if( count( $results ) > 0 ) {

					$returnValue = true;

				}

			}
			catch( PDOException $e ) {

			   print "Error!: " . $e -> getMessage() . "<br/>";
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

?>
