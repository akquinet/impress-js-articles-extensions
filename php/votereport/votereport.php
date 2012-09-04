<?php
/*
 * This php script only works with PHP 5.3 or higher due to usage of the new way of accessing static variables of classes.
 */

//load the configuration
set_include_path('/var/www/templateProject/php');
require_once('vote/vote_configuration.inc');
require_once('votereport/votereport_configuration.inc');
require_once('auth/auth_configuration.inc');

$results = generateReport();
print("$results");

function generateReport() {

	//$mh="must-have";
	//$nh="nice-to-have";
	//$un="unsure";
	//$votefolder="/opt/templateProject/vote-data/";
	//$sep="___";
	//$artsep="_#_";
	$voters = getPermittedVoters();

	$i = 0;
	$articles = array();
	$results = "";	
	$textreport = "";
	 
	foreach (VoteConfiguration::$voteOptions as $option) {
		$resultsPerOption = array();
		foreach ($voters as $voter) {			
		 	$resultFile=VoteConfiguration::$voteResultsRoot.$voter.VoteConfiguration::$voteSeparatorInFile.$option.".txt";

		 	$resultArray = null;
		 
			if (file_exists($resultFile)) {
				$contents = file_get_contents($resultFile);
				$resultArray = explode(VoteConfiguration::$voteSeparatorInFile,$contents);
			}
			if ($resultArray != null) {
				foreach ($resultArray as $item) {
					if ($item != null && $item != '') {
						if (!isset($resultsPerOption[$item]) || $resultsPerOption[$item] == null || $resultsPerOption[$item] == '') {
							$resultsPerOption[$item] = 0;
						} 
						$resultsPerOption[$item] = $resultsPerOption[$item]+1; 
						$textreport= $textreport.$option." has been selected for article/item ".$item." : ".$resultsPerOption[$item]." times<br />";
					}
				}
			}
		}
		
		if ($results != "") {
			$results = $results.VoteReportConfiguration::$optionSeparator;
		}
		$results = $results.$option.VoteReportConfiguration::$optionTitleSeparator;
		
		arsort($resultsPerOption);
		$addItemSeparator = false;
		foreach ($resultsPerOption as $key => $val) {
			$textreport= $textreport.$key." has ".$val." ".$option." votes <br />";
			
			if ($addItemSeparator == true) {
				$results = $results.VoteReportConfiguration::$itemSeparator;
			}
			$results = $results.$key.VoteConfiguration::$voteSeparatorInFile.$val;
			$addItemSeparator = true;
		}		
		
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