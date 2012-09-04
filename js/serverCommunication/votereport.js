//used to collect article titles
var votingIndicatorCls = "withvoting";
var votingTitleTag = "H2";

// variables to deal with inclusion, deletion and positioning of result reports
// (one report per option)
var articleParentDomId = "impress";
var votingreportIndicatorCls = "resultreport";
var votingrefIndicatorCls = "resultReference";
var votingafterIndicatorCls = "beforeResults";
var votingTitles = new Array();
var votingIds = new Array();
var referenceChild = null;
var afterChild = null;
var defaultAfterChild = null;
var optionsPerArticle = 3;
var resultArticleShiftX = 1000;
var resultArticleShiftY = 0;
var resultArticlePrefix = "results-";

var artsep = "_#_";
var sep = "___";

// variables to handle vote report backend response (must be the same as in
// vote_configuration.inc and votereport_configuration.inc
var optionSeparator = "_###_";
var optionTitleSeparator = "_TT_";
var itemSeparator = "_#_";
// notice! : itemValueSeparator is called voteSeparatorInFile in backend
var itemValueSeparator = "___";
var voteOptionDefault = 'none';

var voteReportBackendTarget = "php/votereport/votereport.php";

// collect titles of articles with activated voting module
var articles = document.getElementsByTagName("article");
for ( var i = 0; i < articles.length; i++) {
	// var headlines = articles[i].getElementsByTagName("h2");
	var headlines = "";
	var childNodes = articles[i].childNodes;
	var classesAttr = articles[i].getAttribute("class");
	var addToVotingPages = false;
	if (classesAttr != null) {
		var classes = classesAttr.split(" ");
		for ( var c = 0; c < classes.length; c++) {
			if (classes[c] == votingIndicatorCls) {
				addToVotingPages = true;
			}
			if (classes[c] == votingrefIndicatorCls) {
				referenceChild = articles[i];
			}
			if (classes[c] == votingafterIndicatorCls) {
				afterChild = articles[i];
				defaultAfterChild = afterChild;
			}
		}
	}
	if (addToVotingPages == true) {
		for ( var j = 0; j < childNodes.length; j++) {
			if (childNodes[j].nodeName == votingTitleTag) {
				headlines = childNodes[j].firstChild.data;
				votingTitles[i] = headlines;
				votingIds[i] = i;
			}
		}
	}
}

function MakeReportRequest() {
	var xmlHttp = getXMLHttp();
	xmlHttp.onreadystatechange = function() {
		if (xmlHttp.readyState == 4) {
			HandleReportResponse(xmlHttp.responseText);
		}
	}
	xmlHttp.open("POST", voteReportBackendTarget, true);
	xmlHttp.setRequestHeader("Content-type",
			"application/x-www-form-urlencoded");
	xmlHttp.send(null);
}

function HandleReportResponse(resultString) {
	// alert("resultString : " + resultString);
	var articleParent = document.getElementById(articleParentDomId);
	var childNodes = document.getElementsByTagName("article");
	// remove previous results all articles except the first one
	for ( var j = 0; j < childNodes.length; j++) {
		// parse classes attribute
		var classesAttr = childNodes[j].getAttribute("class");
		if (classesAttr != null) {
			var classes = classesAttr.split(" ");
			for ( var c = 0; c < classes.length; c++) {
				if (classes[c] == votingreportIndicatorCls) {
					// the impress.js introduces an intermediate canvas-div >>
					// firstChild of articleParent
					articleParent.firstChild.removeChild(childNodes[j]);
				}
			}
		}
	}
	afterChild = defaultAfterChild;

	childNodes = document.getElementsByTagName("div");
	// remove previous results from first result article
	for ( var j = 0; j < childNodes.length; j++) {
		// parse classes attribute
		var classesAttr = childNodes[j].getAttribute("class");
		if (classesAttr != null) {
			var classes = classesAttr.split(" ");
			for ( var c = 0; c < classes.length; c++) {
				if (classes[c] == votingreportIndicatorCls) {
					// the impress.js introduces an intermediate canvas-div >>
					// firstChild of articleParent
					defaultAfterChild.removeChild(childNodes[j]);
				}
			}
		}
	}

	// add latest result state
	var resultsPerOption = resultString.split(optionSeparator);
	var newAfter = 10;
	for ( var i = 0; i < resultsPerOption.length; i++) {
		// get option name
		var titleAndResults = resultsPerOption[i].split(optionTitleSeparator);
		var results = null;
		if (titleAndResults.length > 1 && titleAndResults[1] != null
				&& titleAndResults[1] != '') {
			results = titleAndResults[1].split(itemSeparator);
		}
		if (i > 0 && i % optionsPerArticle == 0) {
			// create new after child to use
			afterChild = appendAdditionalResultArticle(afterChild,
					(i / optionsPerArticle));
			newAfter = 10;
		}

		if (titleAndResults[0] != voteOptionDefault) {
			newAfter = appendResultsOfOption(titleAndResults[0], results,
					afterChild, 250, parseInt(newAfter) + 10, top);
		}
	}

}

function appendAdditionalResultArticle(afterChild, resultArticleIdSuffix) {
	var xCoordinate = parseInt(afterChild.getAttribute("data-x"))
			+ resultArticleShiftX;
	var yCoordinate = parseInt(afterChild.getAttribute("data-y"))
			+ resultArticleShiftY;
	var articleId = resultArticlePrefix + resultArticleIdSuffix;
	var articleClasses = afterChild.getAttribute("class");
	articleClasses = articleClasses.replace(votingafterIndicatorCls, "");
	articleClasses = articleClasses + " " + votingreportIndicatorCls;

	// create new node
	var resultArticle = document.createElement("article");
	resultArticle.setAttribute("data-x", xCoordinate);
	resultArticle.setAttribute("data-y", yCoordinate);
	resultArticle.setAttribute("id", articleId);
	resultArticle.setAttribute("class", articleClasses);
	var textElem = document.createTextNode("");
	resultArticle.appendChild(textElem);
	// alert(referenceChild.getAttribute("id"));
	referenceChild.parentNode.insertBefore(resultArticle, referenceChild);
	document.fetchArticles();
	document.positionSlides();
	return resultArticle;
}

function appendResultsOfOption(optionTitle, array, resultNode, width, left, top) {

	// remove old one
	var old = document.getElementById("result-" + optionTitle);
	if (old != null) {
		resultNode.removeChild(old);
	}
	var div = document.createElement("div");
	div.setAttribute("id", "result-" + optionTitle);
	div.setAttribute("style", "top:" + top + "px;left:" + left + "px;width:"
			+ width + ";");
	div.setAttribute("class", "resultScrollDiv");

	var headline = document.createElement("h4");
	var titleSuffix = "";
	if (i > 0) {
		titleSuffix = " (" + (i + 1) + ")";
	}

	var headText = document.createTextNode(optionTitle + titleSuffix);
	headline.appendChild(headText);
	div.appendChild(headline);

	// insert results in div
	if (array != null && array.length > 0) {

		for ( var n = 0; n < array.length; n++) {
			var idAmountPair = array[n].split(itemValueSeparator);
			var elText = document.createTextNode("(" + idAmountPair[1] + ") "
					+ votingTitles[idAmountPair[0]]);
			div.appendChild(elText);
			var br = document.createElement("br");
			div.appendChild(br);
		}
	}
	resultNode.appendChild(div);
	return left + width;
}