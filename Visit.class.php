<?php

class Entry extends Base {
	
	private $clerkID; /*
	private $practitionerID;
	private $patientID; */
	
	private $weight;
	private $height;
	private $bp;
	
	function setClerk( $clerkID ) { $this -> clerkID = $clerkID; }
	
	function getClerk() { return $this -> clerkID; }
	
	function setWeight( $weight ) { $this -> weight = $weight; }
	
	function getWeight() { return $this -> weight; }
	
	function setHeight( $height ) { $this -> height = $height; }
	
	function getHeight() { return $this -> height; }
	
	function setBP( $BP ) { $this -> BP = $BP; }
	
	function getBP() { return $this -> BP; }
	
/*	
	function setPractitioner( $practitionerID ) { $this -> practitionerID = $practitionerID; }
	
	function getPractitioner() { return $this -> practitionerID; }
	
	function setPatient( $patientID ) { $this -> patientID = $patientID; }
	
	function getPatient() { return $this -> patientID; }
	
	function setNotes( $notes ) { $this -> notes = $notes; }
	
	function getNotes() { $return $this -> notes; }
*/

	function validate( $returnType = 0 ) {
		
		$returnValue = true;
		
		return $returnValue;	
		
	}
	
	function displayForm( $action = "add" ) {
		
		$action = 'add';
		
		$returnValue = '
<form action="?section=entries&amp;action=' . $action . '"
      method="post">
	<fieldset class="info">
		<div class="row">
			<label>officer</label>
			<select name="officer">
				<option value="00000">-----</option>
			</select>
		</div>
		<div class="row">
			<label>notes</label>
			<textarea name="notes"
			          placeholder="notes"></textarea>
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
		
		$query = '';
		
		switch( $returnType ) {
			
			case "0" :
			default : {
				
				$returnValue = false;
			
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
		
		$query = '';
		
		switch( $returnType ) {
			
			case "0" :
			default : {
				
				$returnValue = false;
			
			}
			break;
			
			case "1" : {
				
				$returnValue = $query;
			
			}
			break;
		
		}
		
		return $returnValue;
	
	}
	
	function update( $returnType = 0 ) {
		
		GLOBAL $dbh;
		
		$query = '';
		
		switch( $returnType ) {
			
			case "0" :
			default : {
				
				$returnValue = false;
			
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
			default : {
				
				$returnValue = false;
			
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
	                      $patientID = "00000",
	                      $clerkID = "00000",
	                      $practitionerID = "00000",
	                      $notes = "" ) {
							  
		parent::__construct( $uniqueID );
		
		if( $uniqueID != "00000" ) {
			
			$this -> load();
			
		}
		else {
			
			$this -> setPatient( $patientID );
			$this -> setClerk( $clerkID );
			$this -> setPractitioner( $practitionerID );
			$this -> setNotes( $notes );
		
		}
	
	}

}

?>
