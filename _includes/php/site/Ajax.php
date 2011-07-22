<?php
/**
 * This file is called from the Boris javascript, used to retrieve the file list
 *
 */
    
    // get the type of Boris function to call - post or get
	$functionname = isset($_POST['functionname']) ? $_POST['functionname'] : $_GET['functionname'];
	if (empty($functionname)){ return false; }

    // define path for file imports - from here to doc root
	define ('INCLUDE_PATH', "../../..");
        
    // retrieve any additional arguments sent through by jquery - post or get
    if (isset($_GET) && count($_GET > 0)) {
	    foreach($_GET as $key => $value) {
	        $args[$key] = $value;
	    }
	}
	
	if (isset($_POST) && count($_POST > 0)) {
	    foreach($_POST as $key => $value) {
	        $args[$key] = $value;
	    }
	}

    	
    // initialise Boris
	include_once("./Boris.php");
	$boris = new Boris();
	
    // retrieve the Ajax request
	$return = $boris->$functionname($args);
	
	// return the results, in JSON format
	if ($return !== false) {
		echo json_encode($return);
	}
?>