<?php
/*
 * This php script only works with PHP 5.3 or higher due to usage of the new way of accessing static variables of classes.
 */

//load the configuration
set_include_path('/var/www/templateProject/php');
require_once('timer/timer_configuration.inc');


//default value for leadToken
$leadToken = TimerConfiguration::$defaultLeadToken;
if (file_exists(TimerConfiguration::$leadTokenFromFile)) {
	$leadToken = file_get_contents(TimerConfiguration::$leadTokenFromFile);
	$leadToken = trim($leadToken);
}

function startTimerForPage($pagename, $timeout) {
	$file_started_at = TimerConfiguration::$timerDataDir.TimerConfiguration::$dirSep.$pagename.TimerConfiguration::$fileSuffixStarted;
	if (file_exists($file_started_at) == false) {
		$stamp = time();		
		$startedfile = fopen($file_started_at,"w");
		fputs($startedfile, $stamp);
		fclose($startedfile);
		// print("$stamp");
		return $timeout;
	} else {
		$current = time();		
		$started = file_get_contents($file_started_at);
		$elapsed = $current - $started;
		
		$fileelapsed = TimerConfiguration::$timerDataDir.TimerConfiguration::$dirSep.$pagename.TimerConfiguration::$fileSuffixElapsed;
		$previously_elapsed = 0;
		if (file_exists($fileelapsed) == true) {
			$previously_elapsed = file_get_contents($fileelapsed);
		}
		
		$remaining = $timeout - $elapsed - $previously_elapsed;		
		return $remaining;
	}
}

function getRemainingTime($pagename,$timeout) {
	$file_started_at = TimerConfiguration::$timerDataDir.TimerConfiguration::$dirSep.$pagename.TimerConfiguration::$fileSuffixStarted;
	$fileelapsed = TimerConfiguration::$timerDataDir.TimerConfiguration::$dirSep.$pagename.TimerConfiguration::$fileSuffixElapsed;
		
	$previously_elapsed = 0;
	if (file_exists($fileelapsed) == true) {
			$previously_elapsed = file_get_contents($fileelapsed);
	}
	$elapsed = 0;
	if (file_exists($file_started_at) == true) {
		$current = time();	
		$started = file_get_contents($file_started_at);
		$elapsed = $current - $started;
	}
	$total = $timeout - $previously_elapsed -$elapsed;
	return $total;
}

function stopTimerForPage($pagename, $timeout) {
	$file_started_at = TimerConfiguration::$timerDataDir.TimerConfiguration::$dirSep.$pagename.TimerConfiguration::$fileSuffixStarted;
	$fileelapsed = TimerConfiguration::$timerDataDir.TimerConfiguration::$dirSep.$pagename.TimerConfiguration::$fileSuffixElapsed;
	if (file_exists($file_started_at) == true) {
		$current = time();	
		$started = file_get_contents($file_started_at);
		$elapsed = $current - $started;
		
		$previously_elapsed = 0;
		if (file_exists($fileelapsed) == true) {
			$previously_elapsed = file_get_contents($fileelapsed);
		}
		
		$total = $previously_elapsed + $elapsed;
		$remaining = $timeout -$total;
		// print ("elapsed since restarted:  $elapsed .... elapsed in total: $total");
		
		$file = fopen($fileelapsed,"w");
		fputs($file, $total);
		fclose($file);
		unlink($file_started_at);
		
		return $remaining;
	}
}

function formatMinSec($sec) {
	$min = floor($sec/60);	
		
	$output = "";
	if ($sec < 0) {
		$output = $output."-";
		$min = ceil($sec/60);		
		$min = $min*(-1);		
	}
	
	if ($min < 10) {
		$output = $output."0";		
	} 
	
	$output = $output.$min.":";
		
	$secsleft=0;
	if ($sec < 0) {
		$secsleft = (-$sec)-(($min*60));	
	} else {
		$secsleft=$sec-$min*60;
	}		
	if ($secsleft < 10) {
		$output = $output."0";		
	} 
	$output = $output.$secsleft;
	return $output;
}

function switchFromPageToPage($pagename_from, $timeout_from, $pagename_to, $timeout_to) {
	stopTimerForPage($pagename_from, $timeout_from);
	$running = startTimerForPage($pagename_to, $timeout_to);
	return $running;
}

$pagename=$_GET[TimerConfiguration::$timerRequestParamPageName];
$timeout=$_GET[TimerConfiguration::$timerRequestParamTimeout];
$function=$_GET[TimerConfiguration::$timerRequestParamFunction];
$token=$_GET[TimerConfiguration::$timerRequestParamToken];
// print("$function page $pagename with timeout $timeout");
// print("$leadToken"."-".$token."-");
if ("$token" == "$leadToken") {
	if ($function == "start") {
		$remaining = startTimerForPage($pagename, $timeout);
		$remMS = formatMinSec($remaining);
		print("$remaining >> $remMS");
	}
	if ($function == "stop") {
		$remaining = stopTimerForPage($pagename, $timeout);
		$remMS = formatMinSec($remaining);
		print("$remaining >> $remMS");
	} 
} else {
	$remaining = getRemainingTime($pagename,$timeout);
	$remMS = formatMinSec($remaining);
	print("$remaining >> $remMS");
}
?>
