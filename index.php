<?php

// This bit ensures that all output is GZip compressed if the browser supports it, saving bandwidth
if( substr_count( $_SERVER[ 'HTTP_ACCEPT_ENCODING' ], 'gzip' ) ) { ob_start( "ob_gzhandler" ); } else { ob_start(); }

session_start();

require_once( "User.class.php" );
require_once( "Patient.class.php" );

{	// Page variables
 
 $output = 'Hello, World';
 
 $pageTitle = 'MedSYS : manage clinic records efficiently';
	
 $pageHeader = '<!DOCTYPE html>
 <html>
	<head>
		<title>' . $pageTitle . '</title>
	</head>
	<body>
		<div class="wrapper">
			<div class="header"></div>
			<div class="body">
				<div class="side_panel left"></div>
				<div class="main_panel">';
				
$pageBody = '';				
				
$pageFooter = '
				</div>
			</div>
			<div class="footer"></div>
		</div>
	</body>
 </html>';

}

$section = "home";		// Default page loaded

if( isset( $_REQUEST[ "section" ] ) ) { $section = $_REQUEST[ "section" ]; }

if( isset( $_SESSION[ "medSys" ][ "loggedOn" ] ) ) {
	
	$user = new User( $_SESSION[ "medSys" ][ "loggedOn" ] );	// Who is currently logged on?

	switch( $section ) {
		
		case "access" : {		// Log out

			$action = "logOut";

			if( isset( $_REQUEST[ "action" ] ) ) {

				$action = $_REQUEST[ "action" ];

			}

			switch( $action ) {

				case "logOut" :
				default : {

					unset( $_SESSION[ "medSys" ][ "loggedOn" ] );

					// Redirect
					$host = $_SERVER[ 'HTTP_HOST' ];
					$uri = rtrim( dirname( $_SERVER[ 'PHP_SELF' ] ), '/\\' );

					// If no headers are sent, send one
					if( !headers_sent() ) {

						header( "Location: http://" . $host . $uri . "/" );
						exit;

					}

				}
				break;

				case "unRegister" : {

					// Ask user to confirm

				}
				break;

			}

		}
		break;		
		
		case "home" :
		default : {				// Default page
			
			$pageBody .= '
<ul>
	<li>
		<a href="?section=patients">patients</a>
	</li>
	<li>
		<a href="?section=entries">records</a>
	</li>
	<li>
		<a href="?section=access">' . $user -> getScreenName() . '</a>
	</li>
</ul>';
				
		}
		break;
		
		case "patients" : {		// Manage patients
		
			$action = "list";
			
			if( isset( $_REQUEST[ "action" ] ) ) { $action = $_REQUEST[ "action" ]; }
			
			switch( $action ) {
			
				case "list" : 
				default : {
				
					$patients = getPatients();
					
					if( count( $patients ) > 0 ) {
						
						$pageBody .= '
<table>
	<thead>
		<tr>
			<th>#</th>
			<th>patient ID</th>
			<th>name</th>
			<th>action</th>
		</tr>
	</thead>
	<tbody>';
			
						$count = 1;
						
						foreach( $patients as $patientID ) {
							
							$patient = new Patient( $patientID );
							
							$pageBody .= '
		<tr>
			<td>' . $count . '</td>
			<td>' . $patient -> getUniqueID() . '</td>
			<td>' . $patient -> getName() . '</td>
			<td>
				<ul>
					<li>
						<a href="?section=patients&amp;action=view&amp;target=' . $patient -> getUniqueID() . '">view</a>
					</li>
					<li>
						<a href="?section=patients&amp;action=edit&amp;target=' . $patient -> getUniqueID() . '">edit</a>
					</li>
				</ul>
			</td>
		</tr>';
		
							$count++;
						
						}
						
						$pageBody .= '
	</tbody>
</table>';
					
					}
					else {
					
						// No records to display
						$pageBody .= '
<div class="dialog">
	<p>There are no records to display</p>
</div>';
				
					}
				
				}
				break;
				
				case "add" : {
					
					if( isset( $_POST[ "name" ] ) ) { // Data returned, process it
						
						$patient = new Patient( "00000", $_POST[ "name" ], $_POST[ "gender" ], $_POST[ "dateOfRegistration" ] );

						if( $patient -> validate() ) {
						
							if( $patient -> save() ) {
								
								// Data saved
								$pageBody .= '
<div class="dialog success">
	<p>The patient data was saved successfully</p>
</div>';
							
							}
							else {
							
								// There was an issue
								$pageBody .= '
<div class="dialog error">
	<p>There was a problem saving the patient data</p>
</div>';
							
							}
							
						}
						else {
							
							$pageBody .= $patient -> displayForm( 2 );
						
						}
						
					}
					else { // Display the form
						
						$pageBody .= '
<div class="dialog">
	<form action="?section=patients&amp;action=add"
	      method="post">
		<fieldset class="info">
			<legend>patient info</legend>
			<div class="row">
				<label for="name">name</label>
				<input type="text"
					   name="name"
					   required="required" />
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
	</form>
</div>';
						
					}
				
				}
				break;
				
				case "edit" : {
					
					if( isset( $_REQUEST[ "target" ] ) ) {
						
						if( isPatient( 0, $_REQUEST[ "target" ] ) ) {
						
							$target = $_REQUEST[ "target" ];
							
							$patient = new Patient( $target );
							
							if( isset( $_REQUEST[ "name" ] ) ) { // Has data been submitted back from the form?
							
								// Populate object
								$patient -> setName( $_POST[ "name" ] );
								
								if( $patient -> validate() ) {
									
									if( $patient -> update() ) {
										
										// Success!
									$pageBody .= '
<div class="dialog">
	<p>The patient data was saved successfully</p>
</div>';
									
									}
									else {
										
										// There was a problem saving your data
									$pageBody .= '
<div class="dialog">
	<p>There was a problem saving the patient data</p>
</div>';
								
									}
								
								}
								else {
									
									$pageBody .= $patient -> displayForm( 2 );
									
								}
							
							}
							else {
								
								$pageBody .= $patient -> displayForm( 1 );
							
							}
							
						}
						else {
						
							$pageBody .= '
<div class="dialog error">
	<p>The patient ID provided: "' . $_REQUEST[ "target" ] . '" is not recognised on the system</p>
</div>';
						
						}
					
					}
					else {
						
						// Target not specified
								$pageBody .= '
<div class="dialog">
	<p>You have not specified a patient whose details to see. perhaps you meant to <a href="?section=patients">list</a> them</p>
</div>';
					
					}
				
				}
				break;
				
				case "view" : {
					
					if( isset( $_REQUEST[ "target" ] ) ) {
						
						if( isPatient( 0, $_REQUEST[ "target" ] ) ) {
						
							$target = $_REQUEST[ "target" ];
							
							$patient = new Patient( $target );

							// Display they record
									$pageBody .= '
<div class="dialog">
	<table>
		<tbody>
			<tr>
				<th>patient ID</th>
				<td>' . $patient -> getUniqueID() . '</td>
			</tr>
			<tr>
				<th>name</th>
				<td>' . $patient -> getName() . '</td>
			</tr>
			<tr>
				<th>date added</th>
				<td>' . $patient -> getDateOfRegistration() . '</td>
			</tr>
		</tbody>
	</table>
</div>';

						}
						else {
						
							$pageBody .= '
<div class="dialog error">
	<p>The patient ID provided: "' . $_REQUEST[ "target" ] . '" is not recognised on the system</p>
</div>';
							
						}
					
					}
					else {
						
						// Target not specified
						$pageBody .= '
<div class="dialog">
	<p>You have not specified a patient whose details to see. perhaps you meant to <a href="?section=patients">list</a> them</p>
</div>';
					
					}
				
				}
				break;
				
				case "delete" : {
					
					if( isset( $_REQUEST[ "target" ] ) ) {
						
						if( isPatient( $_REQUEST[ "target" ] ) ) {
						
							$target = $_REQUEST[ "target" ];
							
							$patient = new Patient( $target );
							
							if( $patient -> delete() ) {
								
								$pageBody .= '
<div class="dialog success">
	<p>Records deleted</p>
</div>';
						
							}
							else {
								
								$pageBody .= '
<div class="dialog error">
	<p>There was a problem deleteing patient : ' . $patient -> getUniqueID() . '&quot;s records</p>
</div>';
						
							}
							
						}
						else {
								
							$pageBody .= '
<div class="dialog error">
	<p>The patient ID provided is not recognised by the system</p>
</div>';
							
						}
					
					}
					else {
						
						// Target not specified
						$pageBody .= '
<div class="dialog">
	<p>You have not specified a patient whose details to see. perhaps you meant to <a href="?section=patients">list</a> them</p>
</div>';
					
					}
				
				}
				break;
				
				case "search" : {
					
					if( isset( $_REQUEST[ "q" ] ) ) {
					
						$query = '
SELECT
	`uniqueID`
FROM
	`patientDetails`
WHERE
	`name` = "' . $_REQUEST[ "q" ] . '"';
					
					}
					else {
						
						$pageBody .= '
<div class="dialog">
	<form action="?section=patients&amp;action=search">
		<fieldset class="info">
			<legend>search</legend>
			<div class="row">
				<input type="text"
				       name="q"
				       placeholder="lookig for something?"
				       required="required" />
			</div>
		</fieldset>
		<fieldset class="buttons">
			<button type="submit">search</button>
		</fieldset>
	</form>
</div>';
					
					}
				
				}
				break;
			
			}
		
		}
		break;
		
		case "entries" : {		// Make entries into patient files
			
			$action = "list";
			
			if( isset( $_REQUEST[ "action" ] ) ) { $action = $_REQUEST[  "action" ]; }
			
			switch( $action ) {
			
				case "list" :
				default : {
				
					$entries = getEntries();
					
					if( count( $entries ) > 0 ) {
						
						$pageBody .= '
<table>
	<thead>
		<tr>
			<th>#</th>
			<th>timestamp</th>
			<th>patient</th>
			<th>action</th>
		</tr>
	</thead>
	<tbody>';
						
						$count = 1;
						
						foreach( $entries as $entryID ) {

							$entry = new Entry( $entryID );
							
							$patient = new Patient( $entry -> getPatientID() );
							
							$pageBody .= '
		<tr>
			<td>' . $count . '</td>
			<td>' . $entry -> getTimestamp() . '</td>
			<td>' . $patient -> getName() . '</td>
			<td>
				<ul>
					<li>
						<a href="?section=entries&amp;action=view&amp;target=' . $entryID . '"></a>
					</li>
					<li>
						<a href="?section=entries&amp;action=view&amp;target=' . $entryID . '"></a>
					</li>
				</ul>
			</td>
		</tr>';
							
							$count++;
						
						}
						
						$pageBody .= '
	</tbody>
</table>';
					
					}
					else {
						
						$pageBody .= '
<div class="dialog">
	<p>No record to display</p>
</div>';
					
					}
				
				}
				break;
				
				case "add" : {
					
					if( isset( $_POST[ "name" ] ) ) { // Data returned, process it
						
						$entry = new Entry( "00000", $_POST[ "patient" ], $_POST[ "officer" ], $_POST[ "clerk" ], $_POST[ "entry" ] );

						if( $entry -> validate() ) {
						
							if( $entry -> save() ) {
								
								$pageBody .= '
<div class="dialog success">
	<p>Data saved</p>
</div>';
							
							}
							else {
							
								$pageBody .= '
<div class="dialog error">
	<p>There was an issue</p>
</div>';
							
							}
							
						}
						else {
							
							$pageBody .= '
<div class="dialog">' . $entry -> displayForm( 2 ) . '
</div>';
						
						}
						
					}
					else { // Display the form
						
						$pageBody .= '
<div class="dialog">
	<form action="?section=entries&amp;action=add"
	      method="post">
		<fieldset class="info">
			<legend>data</legend>
			<div class="row">
				<label>blood pressure ( BP )</label>
				<input type="text"
				       name="bloodPressure"
				       placeholder="blood pressure ( systolic/diastolic )"
				       required="required" />
			</div>
			<div class="row">
				<label>weight</label>
				<input type="text"
				       name="weight"
				       placeholder="weight in kilo grammes"
				       required="required" />
			</div>
			<div class="row">
				<label>height</label>
				<input type="text"
				       name="height"
				       placeholder="height in centi-metres"
				       required="required" />
			</div>
		</fieldset>
		<fieldset>
			<button type="reset">reset</button>
			<button type="submit">submit</button>
		</fieldset>
	</form>
</div>';
						
					}
				
				}
				break;
				
				case "edit" : {
					
					if( isset( $_REQUEST[ "target" ] ) ) {
						
						if( isEntry( $_REQUEST[ "target" ] ) ) {
							
							$target = $_REQUEST[ "target" ];
							
							$entry = new Entry( $target );
							
							if( isset( $_REQUEST[ "name" ] ) ) { // Has data been submitted back from the form?
							
								// Populate object
								$entry -> setName( $_POST[ "name" ] );
								
								if( $entry -> validate() ) {
									
									if( $entry -> save() ) {
										
										// Success!
										$pageBody .= '
<div class="dialog success">
	<p>The entry was succesfully saved</p>
</div>';
										
									}
									else {
										
										// There was a problem saving your data
										$pageBody .= '
<div class="dialog error">
	<p>There was a problem saving your data</p>
</div>';

									}
								
								}
								else {
									
									$pageBody .= '
<div class="dialog">' . $entry -> displayForm( 2 ) . '
</div>';
									
								}
							
							}
							else {
								
								$pageBody .= '
<div class="dialog">' . $entry -> displayForm( 1 ) . '
</div>';
							
							}

						}
						else {
							
							$pageBody .= '
<div class="dialog error">
	<p>The entry ID you have specified: "' . $_REQUEST[ "target" ] . '" is not identified on the systen</p>
</div>';
						
						}
					
					}
					else {
						
						// Target not specified
						$pageBody .= '
<div class="dialog error">
	<p>You need to specify an entry to edit, perhaps you wanted to <a href="?section=entries">list</a> them</p>
</div>';
						
					
					}
				
				}
				break;
				
				case "view" : {
					
					if( isset( $_REQUEST[ "target" ] ) ) {
	
						if( isEntry( $_REQUEST[ "target" ] ) ) {
						
							$target = $_REQUEST[ "target" ];
							
							$entry = new Entry( $target );
							
							// Display they record
							$pageBody .= '
<div class="dialog">
	<p></p>
</div>';
							
							
						}
						
						else {
							
							$pageBody .= '
<div class="dialog error">
	<p>The entry ID you have specified: "' . $_REQUEST[ "target" ] . '" is not identified on the systen</p>
</div>';
						
						}
					
					}
					else {
						
						// Target not specified
					
					}
				
				}
				break;
				
				case "delete" : {
					
					if( isset( $_REQUEST[ "target" ] ) ) {
						
						if( isEntry( $_REQUEST[ "target" ] ) ) {
						
							$target = $_REQUEST[ "target" ];
							
							$entry = new Entry( $target );
							
							if( $entry -> delete() ) {
								
								$pageBody .= '
<div class="dialog success">
	<p>The entry was succesfully deleted</p>
</div>';
							
							}
							else {
								
								$pageBody .= '
<div class="dialog error">
	<p>There was an error deleting the entry</p>
</div>';
							
							}
							
						}
						else {
							
							$pageBody .= '
<div class="dialog error">
	<p>The Entry ID you have provided: "' . $_REQUEST[ "target" ] . '" is not recognized</p>
</div>';
						
						}
					
					}
					else {
						
						// Target not specified
						$pageBody .= '
<div class="dialog error">
	<p>The entry ID you provided: "' . $_REQUEST[ "target" ] . '" is not recognised on the system</p>
</div>';
					
					}
				
				}
				break;
			
			}
		
		}
		break;

	}
	
}
else {

	$section = "access";

	if( isset( $_REQUEST[ "section" ] ) ) {

		$section = $_REQUEST[ "section" ];

	}

	switch( $section ) {

		case "access" : {

			$action = "logIn";

			if( isset( $_REQUEST[ "action" ] ) ) {

				$action = $_REQUEST[ "action" ];

			}

			switch( $action ) {

				case "logIn" : {

					if( isset( $_REQUEST[ "screenName" ] ) && isset( $_REQUEST[ "password" ] ) ) {

						$query = '
SELECT
	`uniqueID`
FROM
	`accountDetails`
WHERE
	`status` = "1"
AND
	`accessLevel` = "0"
AND
	`screenName` = "' . $_REQUEST[ "screenName" ] . '"
AND
	`password` = MD5( "' . $_REQUEST[ "password" ] . '" )
';

						try {

							if( $result = $dbh -> query( $query ) ) {

								$results = $result -> fetchAll();

								if( count( $results ) == 1 ) {

									 $_SESSION[ "medSys" ][ "loggedOn" ] = $results[ 0 ] [ "uniqueID" ];

								}
								else {

									// More than one matching entry, something is very wrong

									$pageBody .= '
<div class="dialog error">
	<p>There were multiple entries</p>
</div>';

								}

							}
							else {

								$pageBody .= '
<div class="dialog error">
	<h4>Log In Error : 001</h4>
	<p>There was an Error trying to log you in :(</p>
	<p>Please contact the administrator if this persists</p>
</div>';

							}

						}
						catch( PDOException $e ) {

							print "Error!: " . $e -> getMessage() . "<br/>";

							die();

						}

						// Redirect
						$host = $_SERVER[ 'HTTP_HOST' ];
						$uri = rtrim( dirname( $_SERVER[ 'PHP_SELF' ] ), '/\\' );

						// If no headers are sent, send one
						if( !headers_sent() ) {

							header( "Location: http://" . $host . $uri . "/" );
							exit;

						}

					}
					else {

						$pageBody .= '
<div class="dialog" style="width: 30em; margin: 5em auto;">
	<form action="?section=access&amp;action=logIn"
	      method="post">
		<fieldset class="info">
			<legend>log in</legend>
			<div class="row">
				<label for="screenname">username</label>
				<input type="text"
				       name="screenName"
				       placeholder="your username"
					   required="required" />
			</div>
			<div class="row">
				<label for="password">password</label>
				<input type="password"
				       name="password"
				       placeholder="your password"
					   required="required" />
			</div>
		</fieldset>
		<fieldset class="buttons">
			<button type="reset">reset</button>
			<button type="submit">submit</button>
		</fieldset>
	</form>
</div>';

					}

				}
				break;

			}

		}
		break;

	}

}


$format = "html";
 
if( isset( $_REQUEST[ "format" ] ) ) { $format = $_REQUEST[ "format" ]; }
 
switch( $format ) {
	 
	case "ajax" : {
		
		$output = $pageBody;
	
	}
	break;
	 
	case "html" :
	default : {
		
		$output = $pageHeader . $pageBody . $pageFooter;
	
	}
	break;
	
	case "xml" : {}
	break;
	
	case "json" : {}
	break;
	
	case "text" : {}
	break;
	
	case "csv" : {}
	break;
	
	case "pdf" : {}
	break;

}
 
echo $output;

?>
