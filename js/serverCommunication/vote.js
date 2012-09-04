//available voting options
var SELECTABLE_VOTING_OPTIONS = ['must have','nice to have', 'unsure', 'VETO'];

//some css settings
var css_votingIndicatorClass = "withvoting";

var css_bg_colorFailed = 'colorFailed';
var css_bg_colorSuccess = 'colorSuccess';
var css_bg_colorUpdated = 'colorUpdated';

// into which tag to place the voting forms as a dom child
var votingSectionIntoTagName = 'article';

//where to send the request and what return values indicate success or updated state
var voteBackendTarget = 'php/vote/vote.php';
var voteBackendResultSuccess = 'success';
var voteBackendResultUpdated = 'updated';

// how to name the items in the voting forms
var prefixDiv = 'div-voting-article-';
var prefixOption = 'option-article-';
var prefixForm = 'form-voting-article-';

/**
 * This function is called once when the page is loaded. You can change to which
 * tag to add the voting section by setting a different value for
 * votingSectionIntoTagName right in the beginning of the vote.js script.
 */
function addVoteSections() {

	var articles = document.getElementsByTagName(votingSectionIntoTagName);
	for ( var i = 0; i < articles.length; i++) {
		var article = articles[i];
		var withvoting = false;

		var classValuesArticle = article.getAttribute("class");

		var classesArticle = classValuesArticle.split(" ");
		for ( var j = 0; j < classesArticle.length; j++) {
			if (classesArticle[j] == css_votingIndicatorClass) {
				withvoting = true;
			}
		}

		if (withvoting == true) {
			var divVote = document.createElement("div");
			divVote.setAttribute("class", "voting");
			divVote.setAttribute("id", prefixDiv + i);

			var formVote = document.createElement("form");
			formVote.setAttribute("id", prefixForm + i);
			
			for ( var k = 0; k < SELECTABLE_VOTING_OPTIONS.length; k++) {
				
				var option = document.createElement("input");
				option.setAttribute("type", "radio");
				option.setAttribute("name", prefixOption + i);
				option.setAttribute("value", SELECTABLE_VOTING_OPTIONS[k].replace(" ", "-"));
				option.setAttribute("class", "radiovoting");
				formVote.appendChild(option);
				var optionText = document.createTextNode(SELECTABLE_VOTING_OPTIONS[k]+" ");
				formVote.appendChild(optionText);
			}
//			var optionMH = document.createElement("input");
//			optionMH.setAttribute("type", "radio");
//			optionMH.setAttribute("name", prefixOption + i);
//			optionMH.setAttribute("value", "must-have");
//			optionMH.setAttribute("class", "radiovoting");
//			formVote.appendChild(optionMH);
//			var mhText = document.createTextNode("must have ");
//			formVote.appendChild(mhText);
//
//			var optionNH = document.createElement("input");
//			optionNH.setAttribute("type", "radio");
//			optionNH.setAttribute("name", prefixOption + i);
//			optionNH.setAttribute("value", "nice-to-have");
//			optionNH.setAttribute("class", "radiovoting");
//			formVote.appendChild(optionNH);
//			var nhText = document.createTextNode("nice to have ");
//			formVote.appendChild(nhText);
//
//			var optionU = document.createElement("input");
//			optionU.setAttribute("type", "radio");
//			optionU.setAttribute("name", prefixOption + i);
//			optionU.setAttribute("value", "unsure");
//			optionU.setAttribute("class", "radiovoting");
//			formVote.appendChild(optionU);
//			var uText = document.createTextNode("unsure | id: ");
//			formVote.appendChild(uText);

			var uText = document.createTextNode("| id: ");
			formVote.appendChild(uText);
			
			var inputID = document.createElement("input");
			inputID.setAttribute("type", "text");
			inputID.setAttribute("value", "username");
			inputID.setAttribute("name", "username");
			inputID.setAttribute("class", "inputvoting");
			formVote.appendChild(inputID);

			var pwText = document.createTextNode(" pw: ");
			formVote.appendChild(pwText);
			var inputPW = document.createElement("input");
			inputPW.setAttribute("type", "password");
			inputPW.setAttribute("value", "");
			inputPW.setAttribute("name", "password");
			inputPW.setAttribute("class", "inputvoting");
			formVote.appendChild(inputPW);

			var hidden = document.createElement("input");
			hidden.setAttribute("type", "hidden");
			hidden.setAttribute("name", "resultTarget");
			hidden.setAttribute("value", "div-voting-article-" + i);
			formVote.appendChild(hidden);

			var submitButton = document.createElement("input");
			submitButton.setAttribute("type", "button");
			submitButton.setAttribute("value", "vote");
			submitButton.setAttribute("onclick", "vote('" + i + "');");
			formVote.appendChild(submitButton);

			divVote.appendChild(formVote);
			article.appendChild(divVote);
		}
		// if (i == 7) {
		// alert("article "+i+" classes="+classValuesArticle+" withvoting
		// ?"+withvoting+"\n contains:"+article);
		// }
	}
}

/** This function is called by an onclick event on the submit button inside the voting form of an article.
 *  It simply converts the request into a voteMobile request. A mobile request does have a different 
 *  formVoteSuffix namely 'mobile', while a standard vote request created inside an embedded voting section of the presentation has the form id as the formVoteSuffix. */ 
function vote(resultTarget) {
	return voteMobile(resultTarget, resultTarget);
}

/** this function is called by an onclick event on the submit button inside the standalone voting form */
function voteMobile(resultTarget, formVoteSuffix) {

	var selected = "none";
	// try to add or change the vote on a certain article
	var options = document.getElementsByName(prefixOption + formVoteSuffix);
	for ( var i = 0; i < options.length; i++) {
		if (options[i].checked == true) {
			selected = options[i].value;
		}
	}

	// send vote to server
	var formVote = document.getElementById(prefixForm + formVoteSuffix);
	var currentID = formVote.username.value;
	var currentPW = formVote.password.value;

	var params = "user=" + currentID + "&pw=" + currentPW + "&option="
			+ selected + "&article=" + resultTarget;
	// alert(params);
	MakeVoteRequest(params, formVoteSuffix);

}


function MakeVoteRequest(params, formVoteSuffix) {
	var xmlHttp = getXMLHttp();
	xmlHttp.onreadystatechange = function() {
		if (xmlHttp.readyState == 4) {
			HandleVoteResponse(xmlHttp.responseText, formVoteSuffix);
		}
	}
	xmlHttp.open("POST", voteBackendTarget, true);
	xmlHttp.setRequestHeader("Content-type",
			"application/x-www-form-urlencoded");
	xmlHttp.send(params);
}

function HandleVoteResponse(result, formVoteSuffix) {
	// alert("result: "+result+"::");
	// if vote was successful apply id+pw to all other forms
	var formVote = document.getElementById(prefixForm + formVoteSuffix);
	var currentID = formVote.username.value;
	var currentPW = formVote.password.value;
	if (result == voteBackendResultSuccess) {
		var idFields = document.getElementsByName("username");
		for ( var i = 0; i < idFields.length; i++) {
			idFields[i].value = currentID;
		}
		var pwFields = document.getElementsByName("password");
		for ( var i = 0; i < pwFields.length; i++) {
			pwFields[i].value = currentPW;
		}
	}

	var colorClass = css_bg_colorFailed;
	if (result == voteBackendResultSuccess) {
		colorClass = css_bg_colorSuccess;
	}
	if (result == voteBackendResultUpdated) {
		colorClass = css_bg_colorUpdated;
	}

	var voteDiv = document.getElementById(prefixDiv + formVoteSuffix);

	var classes = voteDiv.getAttribute("class");
	if (classes == null) {
		classes = "";
	}
	classes = classes.replace(css_bg_colorUpdated, "");
	classes = classes.replace(css_bg_colorFailed, "");
	classes = classes.replace(css_bg_colorSuccess, "");
	classes = classes + " " + colorClass;
	voteDiv.setAttribute("class", classes);
}

addVoteSections();