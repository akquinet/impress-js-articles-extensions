<?php
/*
 * This php script only works with PHP 5.3 or higher due to usage of the new way of accessing static variables of classes.
 */

	//load the configuration
	set_include_path('/var/www/templateProject/php');
	require_once('auth/auth_configuration.inc');

    function authenticate($id,$pw) {    	
    	$notset = AuthConfiguration::$pwNotSet;
    	//$authFile = "/opt/presentationSpace/login-data/auth.conf";
    	$users = file(AuthConfiguration::$authFile);
    	$rewrite = -1;
    	$i = 0;
    	foreach ($users as $user) {
    		if ($users[$i] != null) {
    			$str="".trim($user);
	    		$attr=explode(" ",$str);
	    		if (($attr[0] != null) && (strtolower($attr[0]) == strtolower($id))) {
	    			if (($attr[1] != null) && ($attr[2] != null)) {
		    			if (($attr[1] == AuthConfiguration::$pwNotSet) && ($attr[2] == AuthConfiguration::$pwNotSet)) {
		    				//rewrite auth file
		    				$rewrite=$i;
		    			} else {
		    				//check pwd
		    				if (trim($attr[1]) == trim($pw)) {
		    					return true;
		    				}
		    			}
		    		}
	    		}
    		}
    		$i = $i+1;
    	}
    	
    	if ($rewrite > -1) {
    		$fileContent="";
	    	$j = 0;			
	    	foreach ($users as $user) {
	    		if ($fileContent != "") {
	    			$fileContent = $fileContent."\n";
	    		}
	    		if ($rewrite == $j) {
	    			$fileContent = $fileContent.$id." ".$pw." ".time();
	    		} else {
	    			$fileContent = $fileContent.trim($user);
	    		}
	    		$j = $j + 1;
	    	}
	    	$file = fopen($authFile,"w");
			fputs($file, $fileContent);
			fclose($file);
			return true;
    	} 
    	return false;
    }
    
?>
