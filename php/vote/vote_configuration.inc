<?php
/*
 * This file includes environment and installation/integration specific paths. It is included in the timer.php, vote.php and votereport.php.
 * All variables declared here are accessible with Configuration::$variable.
 * This php script only works with PHP 5.3 or higher due to usage of the new way of accessing static variables of classes. 
 */
class VoteConfiguration {
	
	static $voteResultsRoot = "/opt/presentationSpace/testoutput/vote-data/";
	function ensurePresenceOfVoteResultsRootDirectory() {
		if (!file_exists($this::$voteResultsRoot)) {
			mkdir($this::$voteResultsRoot,0770,true);
		}
	}
	
	//these param names must be the same as used inside vote.js to create the request
	static $voteRequestParamUser = 'user';
	static $voteRequestParamPassword = 'pw';
	static $voteRequestParamItem = 'article';
	static $voteRequestParamOption = 'option';
	
	static $voteResultSuccess = 'success';
	static $voteResultUpdated = 'updated';
	static $voteResultFailed = 'failed';
	static $voteSeparatorInFile = '___';
	static $voteOptionFileSuffix = '.txt';
	static $voteOptionMustHave = 'must-have';
	static $voteOptionNiceToHave = 'nice-to-have';
	static $voteOptionUnsure = 'unsure';
	static $voteOptionVeto = 'veto';
	//default option indicates, if a form with no vote option selected has been submitted
	static $voteOptionDefault = 'none';
	static $voteOptions = null;
	
	function addAllVoteOptionsToArray() {		
		$this::$voteOptions = array($this::$voteOptionMustHave,$this::$voteOptionNiceToHave,$this::$voteOptionUnsure,$this::$voteOptionVeto,$this::$voteOptionDefault);
	}
	
}

//initialize the configuration
$conf = new VoteConfiguration;
//assemble all vote options in an iterable array
$conf->addAllVoteOptionsToArray();
//ensure that required directories are available on the system
$conf->ensurePresenceOfVoteResultsRootDirectory();

?>