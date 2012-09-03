var articlesForTimer = document.getElementsByTagName("article");
var timeouts = new Array();
for ( var i = 0; i < articlesForTimer.length; i++) {
	timeouts[i] = 60;
}

function handleTimers() {

	var articles = document.getElementsByTagName("article");
	for ( var i = 0; i < articles.length; i++) {
		var article = articles[i];
		var hastimer = false;
		var isactive = false;
		var resultTarget = "none";

		var spans = article.getElementsByTagName("span");

		var classValuesArticle = article.getAttribute("class");
		var classesArticle = classValuesArticle.split(" ");
		for ( var j = 0; j < classesArticle.length; j++) {
			if (classesArticle[j] == "active") {
				isactive = true;
			}
		}
		if (spans != null) {
			for ( var j = 0; j < spans.length; j++) {
				var span = spans[j];
				if (span != null) {
					var classValues = span.getAttribute("class");
					if (classValues != null) {
						var classes = classValues.split(" ");
						for ( var k = 0; k < classes.length; k++) {
							if (classes[k] == "timer") {
								hastimer = true;
								resultTarget = span;
							}
						}
					}
				}
			}
		}

		if (hastimer == true) {
			var token = "&token=noToken";
			
			var inputToken = document.getElementById("tokenInput");	
			if (inputToken != null && inputToken.value != '' && inputToken.value != 'undefined') {
				//manages that the token param is just added in case of a defined token
				token = '&token=' + inputToken.value;
			} 

			if ((isactive == true) && (resultTarget != "none")) {
				MakeTimerRequest("?pagename=article-" + i + "&timeout="
						+ timeouts[i] + "&function=start" + token, resultTarget);
			}
			if (isactive == false && inputToken != null && inputToken.value != '' && inputToken.value != 'undefined') {
				MakeTimerRequest("?pagename=article-" + i + "&timeout="
						+ timeouts[i] + "&function=stop" + token, resultTarget);
			}
		}
	}
}

var timerUpdateID = window.setInterval("handleTimers()", 1000);

function MakeTimerRequest(params, resultTarget) {
	var xmlHttp = getXMLHttp();
	xmlHttp.onreadystatechange = function() {
		if (xmlHttp.readyState == 4) {
			HandleTimerResponse(xmlHttp.responseText, resultTarget);
		}
	}
	xmlHttp.open("GET", "php/timer/timer.php" + params, true);
	xmlHttp.send(null);
}

function HandleTimerResponse(formattedTime, resultTarget) {
//	alert("received formattedTime: "+formattedTime);
	var hasChildren = resultTarget.hasChildNodes();
	
	if (hasChildren) {
		resultTarget.firstChild.data = formattedTime;
	} else {
		var text = document.createTextNode(formattedTime);
		resultTarget.appendChild(text);
	}
}