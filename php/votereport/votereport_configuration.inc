<?php
/*
 * This file includes environment and installation/integration specific paths. It is included in the timer.php, vote.php and votereport.php.
 * All variables declared here are accessible with Configuration::$variable.
 * This php script only works with PHP 5.3 or higher due to usage of the new way of accessing static variables of classes. 
 */
class VoteReportConfiguration {
	//these params must be the same as in votereport.js !!! otherwise the array parsing does not work properly
	static $optionSeparator = '_###_';
	static $optionTitleSeparator = '_TT_';
	static $itemSeparator = '_#_';
		
}

?>