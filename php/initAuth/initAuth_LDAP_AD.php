<?php
/*
 * This php script only works with PHP 5.3 or higher due to usage of the new way of accessing static variables of classes.
 */
	//load the configuration
	set_include_path('/var/www/templateProject/php');
	require_once('auth/auth_configuration.inc');

	function initAuthFile() {
    	if (file_exists(AuthConfiguration::$authFile)) {
    		return;
        }
       
        $ldappwFile = AuthConfiguration::$configRoot."/ldap-data/ldap-pw.conf";
	    $ldapuserFile = AuthConfiguration::$configRoot."/ldap-data/ldap-user.conf";
	    $ldapurlFile = AuthConfiguration::$configRoot."/ldap-data/ldap-url.conf";
	    $ldapbasednFile = AuthConfiguration::$configRoot."/ldap-data/ldap-basedn.conf";
	    $ldapfilterFile = AuthConfiguration::$configRoot."/ldap-data/ldap-filter.conf";
        
        //load active users from ldap and write initial auth file 
	    // using ldap bind
	    $ldaprdn  = trim(file_get_contents($ldapuserFile));     // ldap rdn or dn
	    $ldappass = trim(file_get_contents($ldappwFile));  // associated password
	    $ldapurl = trim(file_get_contents($ldapurlFile));
	
	    // connect to ldap server
	    $ldapconn = ldap_connect($ldapurl)
	            or die("Could not connect to LDAP server.");
	
	    // Set some ldap options for talking to
	    ldap_set_option($ldapconn, LDAP_OPT_PROTOCOL_VERSION, 3);
	    ldap_set_option($ldapconn, LDAP_OPT_REFERRALS, 0);
	
	    if ($ldapconn) {
	
	            // binding to ldap server
	            $ldapbind = @ldap_bind($ldapconn, $ldaprdn, $ldappass);
	             
	            // verify binding
	            if ($ldapbind) {
	                $base_dn=trim(file_get_contents($ldapbasednFile));
	            	$filter=trim(file_get_contents($ldapfilterFile));
	                
	                if (!($search=ldap_search($ldapconn,$base_dn,$filter))) {
					     die("Durchsuchen des LDAP-Servers fehlgeschlagen:".$base_dn."::".$filter);
					}
					$anzahl = ldap_count_entries($ldapconn,$search);
					$info = ldap_get_entries($ldapconn, $search);
					
					$uidFileContent = "";
					
					for ($i=0; $i<$anzahl; $i++) {
					    //add user id to file
					    $uid=utf8_decode($info[$i]["samaccountname"][0]);
					    if ($uid != "") {
					    	if ($uidFileContent != "") {
					    		$uidFileContent =$uidFileContent."\n";
					    	}
					    	$uidFileContent =$uidFileContent.$uid." ".AuthConfiguration::$pwNotSet." ".AuthConfiguration::$pwNotSet;
					    }
					}
					
					//ensure target folder for auth.conf exists
					if (!file_exists(AuthConfiguration::$authFileDir)) {
						mkdir(AuthConfiguration::$authFileDir,0770,true);
					}
					
					$file = fopen(AuthConfiguration::$authFile,"w");
					fputs($file, $uidFileContent);
					fclose($file);
					
	            } else {
	                echo "LDAP bind failed...\n";
	            }
	
	    }
    }

	//create the auth file
	initAuthFile();
    

?>
