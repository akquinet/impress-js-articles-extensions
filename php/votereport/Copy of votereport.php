<?php
/*
 * This php script only works with PHP 5.3 or higher due to usage of the new way of accessing static variables of classes.
 */

	//load the configuration
	set_include_path('/var/www/templateProject/php');
	require_once('vote/vote_configuration.inc');
	require_once('auth/auth_configuration.inc');
	
$results = generateReport();
print("$results");

function generateReport() {

	$mh="must-have";
	$nh="nice-to-have";
	$un="unsure";
	$votefolder="/opt/templateProject/vote-data/";
	$sep="___";
	$artsep="_#_";
	
	$voters = getPermittedVoters();
	
	$i = 0;
	$articles = array();
	$results = "";
	  
    foreach ($voters as $voter) {
       $mhFile=$votefolder.$voter.$sep.$mh.".txt";
	   $nhFile=$votefolder.$voter.$sep.$nh.".txt";
	   $unFile=$votefolder.$voter.$sep.$un.".txt";
	
       $mhArray = null;
	   $nhArray = null;
	   $unArray = null;
	   
	    
	   if (file_exists($mhFile)) {
		   $mhContents = file_get_contents($mhFile);
		   $mhArray = explode($sep,$mhContents);
	   }
	   if (file_exists($nhFile)) {
		   $nhContents = file_get_contents($nhFile);
		   $nhArray = explode($sep,$nhContents);
	   } 
	   if (file_exists($unFile)) {
		   $unContents = file_get_contents($unFile);
		   $unArray = explode($sep,$unContents);
	   }
    	
    	if ($mhArray != null) {
    		$pos = 1;
		    foreach ($mhArray as $item){		   		
		   		if (($item != null)) {
		   			if ($articles[trim($item)] == null) {
		   				$articles[trim($item)] = array();
		   				$articles[trim($item)][0] = trim($item);
		   			}
		   		    if ($articles[trim($item)][$pos] == null || $articles[trim($item)][$pos] == "") {
		   		    	$articles[trim($item)][$pos] = 0;
		   		    }
		   			$articles[trim($item)][$pos] = $articles[trim($item)][$pos] + 1; 
		   		}
		    }
	   }
		if ($nhArray != null) {
    		$pos = 2;
		    foreach ($nhArray as $item){		   		
		   		if (($item != null)) {
		   			if ($articles[trim($item)] == null) {
		   				$articles[trim($item)] = array();
		   				$articles[trim($item)][0] = trim($item);
		   			}
		   		    if ($articles[trim($item)][$pos] == null || $articles[trim($item)][$pos] == "") {
		   		    	$articles[trim($item)][$pos] = 0;
		   		    }
		   			$articles[trim($item)][$pos] = $articles[trim($item)][$pos] + 1; 
		   		}
		    }
	   }
		if ($unArray != null) {
		   $pos = 3;
		    foreach ($unArray as $item){
		    	if (($item != null)) {
		   			if ($articles[trim($item)] == null) {
		   				$articles[trim($item)] = array();
		   				$articles[trim($item)][0] = trim($item);
		   			}
		   		    if ($articles[trim($item)][$pos] == null || $articles[trim($item)][$pos] == "") {
		   		    	$articles[trim($item)][$pos] = 0;
		   		    }
		   			$articles[trim($item)][$pos] = $articles[trim($item)][$pos] + 1; 
		   		}
		    }
	   }    	
    	$i++;
    }

	$k = 0;
	$max=4;
	foreach ($articles as $article) {
		if ($results != "") {
			$results = $results.$artsep;
		}
		$results = $results.$article[0];
		
		for ($n=1; $n<$max; $n++) {
			$amount = 0;
			if ($article[$n] != null && $article[$n] != "") {
				$amount = $article[$n];
			}
			$results = $results.$sep.$amount;
		}
		$k++;
	}
	
	return $results;
}

function getPermittedVoters() {
	$authFile = AuthConfiguration::$authFile;
	$voters = array();
	
    if (file_exists($authFile)) {
    	$users = file($authFile);
    	$i = 0;
    	foreach ($users as $user) {
    			$attr = explode(" ",$user);
    			if ($attr[0] != null && $attr[0] != "") {
    				$voters[$i] = $attr[0];	
    			}
    		$i = $i + 1;
    	}
    }
    return $voters;
}

?>