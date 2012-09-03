<?php
/*
 * This php script only works with PHP 5.3 or higher due to usage of the new way of accessing static variables of classes.
 */

	//load the configuration
	set_include_path('/var/www/templateProject/php');
	require_once('vote/vote_configuration.inc');
	require('auth/auth.php');

	$id=$_POST[VoteConfiguration::$voteRequestParamUser];
	$pw=$_POST[VoteConfiguration::$voteRequestParamPassword];
	$option=$_POST[VoteConfiguration::$voteRequestParamOption];
	$article=$_POST[VoteConfiguration::$voteRequestParamItem];

	$voteResult = vote($id,$pw,$option,$article);
	print("$voteResult");
  
   function vote($id,$pw,$option,$article) {
   		//	return " ... updated with ".$id." and ".$pw." and option:".$option." for:".$article;
	   //authentication
	   $result=authenticate($id,$pw);
	   if ($result == false) {
	   		return VoteConfiguration::$voteResultFailed;
	   } 
	   
	   $sep=VoteConfiguration::$voteSeparatorInFile;   
	   
	   //check each array if there is already a vote
	   $previouslySelectedOption=VoteConfiguration::$voteOptionDefault;
	   $numOfOptions=count(VoteConfiguration::$voteOptions);
	   for ($i=0;$i<$numOfOptions;$i++) {
	   		if (VoteConfiguration::$voteOptions[$i] != VoteConfiguration::$voteOptionDefault) {
		   		$optionFile=VoteConfiguration::$voteResultsRoot.$id.$sep.VoteConfiguration::$voteOptions[$i].VoteConfiguration::$voteOptionFileSuffix;
			    if (file_exists($optionFile)) {
				   $optionFileContents = file_get_contents($optionFile);
				   $votesWithOption = explode($sep,$optionFileContents);
				   //iterate the votesWithOption array and check
				   foreach ($votesWithOption as $item) {
				   		if (($item != null) && (trim($item) == $article)) {
				   			$previouslySelectedOption = VoteConfiguration::$voteOptions[$i];
				   		}
				   }
			    }
	   		}
	   }
	   
   		$votesWithSelectedOption = null;
	   	$selectedOptionFile=VoteConfiguration::$voteResultsRoot.$id.$sep.$option.VoteConfiguration::$voteOptionFileSuffix;
	   	if (file_exists($selectedOptionFile)) {
		   $selectedOptionFileContents = file_get_contents($selectedOptionFile);
		   $votesWithSelectedOption = explode($sep,$selectedOptionFileContents);
	   	}
	   
	   $result = VoteConfiguration::$voteResultSuccess;
	   if ($previouslySelectedOption != $option) {
	   		//if previously selected option is the same as the current selection, nothing has to be done
	   		//otherwise the previous selection has to be removed, if it is not the default option
		   if ($previouslySelectedOption != VoteConfiguration::$voteOptionDefault) {
		   		//remove old vote
		   		$voteFile = VoteConfiguration::$voteResultsRoot.$id.$sep.$previouslySelectedOption.VoteConfiguration::$voteOptionFileSuffix;
		   		$voteContents = file_get_contents($voteFile);
		   		$votes = explode($sep,$voteContents);
		   		$cleanedVoteContent = '';
		   		$prefix=''; 
		   		foreach ($votes as $voted) {
		   			if ($cleanedVoteContent != '') {
		   				$prefix=$sep;
		   			}
		   			if (trim($voted) != trim($article)) {
		   				$cleanedVoteContent = "trimmed-art-".trim($article)."trimmed-voted-".trim($voted).$cleanedVoteContent.$prefix.$voted;
		   			}
		   		}
		   		$vfile = fopen($voteFile,"w");
				fputs($vfile, $cleanedVoteContent);
				fclose($vfile);
				$result = VoteConfiguration::$voteResultUpdated;
		   }
		   // and the currently selected option has to be written as a vote into the appropriate user specific file
		   if ($option != VoteConfiguration::$voteOptionDefault) {
			    $file = VoteConfiguration::$voteResultsRoot.$id.$sep.$option.VoteConfiguration::$voteOptionFileSuffix;
			    $prefix = '';
			    
			    if (file_exists($file)) {    	
			    	$fileContents = file_get_contents($file);
			    	if (($fileContents != null) || ($fileContents != '')) {
			    		$prefix=$sep;
			    	}    	
			    }
			    
			    $ofile = fopen($file,"a");
				fputs($ofile, $prefix.$article);
				fclose($ofile);		
			}
	   }
		return $result;
   }
?>
