<?php
/*
 * This file includes environment and installation/integration specific paths. It is included in the timer.php, vote.php and votereport.php.
 * All variables declared here are accessible with Configuration::$variable.
 * This php script only works with PHP 5.3 or higher due to usage of the new way of accessing static variables of classes. 
 */
class InitAuthConfiguration {
	static $configRoot = "/opt/templateProject";
	static $ldapConfFileDir = "/ldap-data";
	static $ldapConfFile = "ldap.conf";		
	
	function addConfigRootToLdapConfFileDirPath() {
		$this::$ldapConfFileDir = $this::$configRoot.$this::$ldapConfFileDir;
	}
	function ensurePresenceOfLdapConfFileDir() {
		if (!file_exists($this::$ldapConfFileDir)) {
			mkdir($this::$ldapConfFileDir,0770,true);
		}
	}
	function addConfigRootToLdapConfFilePath() {
		$this::$ldapConfFile = $this::$ldapConfFileDir."/".$this::$ldapConfFile;
	}
	
}
	
//initialize the configuration
$conf = new InitAuthConfiguration;
//reset values for static paths according to the configRoot using a function call
$conf->addConfigRootToLdapConfFileDirPath();
$conf->ensurePresenceOfLdapConfFileDir();
$conf->addConfigRootToLdapConfFilePath();

?>
