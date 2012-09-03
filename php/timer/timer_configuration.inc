<?php
/*
 * This file includes environment and installation/integration specific paths. It is included in the timer.php, vote.php and votereport.php.
 * All variables declared here are accessible with Configuration::$variable.
 * This php script only works with PHP 5.3 or higher due to usage of the new way of accessing static variables of classes. 
 */
class TimerConfiguration {
	static $defaultLeadToken = 'saheba';
	static $leadTokenFromFile = "/opt/templateProject/token-data/leadToken.txt";
	static $timerDataDir = "/opt/templateProject/timer-test-data";
	static $dirSep = '/';
	static $fileTypeSuffix = '.txt';
	static $fileSuffixStarted = '-started-at';
	static $fileSuffixElapsed = '-elapsed';
	function ensurePresenceOfTimerDataDir() {
		if (!file_exists($this::$timerDataDir)) {
			mkdir($this::$timerDataDir,0770,true);
		}
	}
	function appendFileTypeSuffixToFileSuffixes() {
		$this::$fileSuffixStarted = $this::$fileSuffixStarted.$this::$fileTypeSuffix;
		$this::$fileSuffixElapsed = $this::$fileSuffixElapsed.$this::$fileTypeSuffix; 
	}
	
	static $timerRequestParamTimeout = 'timeout';
	static $timerRequestParamPageName = 'pagename';
	static $timerRequestParamFunction = 'function';
	static $timerRequestParamToken = 'token';
}
	
//initialize the configuration
$conf = new TimerConfiguration;
//reset values for static paths according to the configRoot using a function call
$conf->ensurePresenceOfTimerDataDir();
$conf->appendFileTypeSuffixToFileSuffixes();
?>
