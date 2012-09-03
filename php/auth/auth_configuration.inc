<?php
/*
 * This file includes environment and installation/integration specific paths. It is included in the timer.php, vote.php and votereport.php.
 * All variables declared here are accessible with Configuration::$variable.
 * This php script only works with PHP 5.3 or higher due to usage of the new way of accessing static variables of classes. 
 */
class AuthConfiguration {
	static $configRoot = "/opt/templateProject";
	static $authFileDir = "/login-data";
	static $authFile = "auth.conf";		
	static $pwNotSet = "notyetset";
	
	function addConfigRootToAuthFileDirPath() {
		$this::$authFileDir = $this::$configRoot.$this::$authFileDir;
	}
	function ensurePresenceOfAuthFileDir() {
		if (!file_exists($this::$authFileDir)) {
			mkdir($this::$authFileDir,0770,true);
		}
	}
	function addConfigRootToAuthFilePath() {
		$this::$authFile = $this::$authFileDir."/".$this::$authFile;
	}
	
}
	
//initialize the configuration
$conf = new AuthConfiguration;
//reset values for static paths according to the configRoot using a function call
$conf->addConfigRootToAuthFileDirPath();
$conf->ensurePresenceOfAuthFileDir();
$conf->addConfigRootToAuthFilePath();

?>
