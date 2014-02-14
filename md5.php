<?php

	$str = "password";
	
	if( isset( $_REQUEST[ "str" ] ) ) { $str = $_REQUEST[ "str" ]; }

	echo md5( $str );

?>
