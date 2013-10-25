
    function drawVisualization() {
  // Create and populate the data table.
 		var data = new google.visualization.DataTable();
		var data1 = new google.visualization.DataTable();
		var data2 = new google.visualization.DataTable();
		
		
		var wrap = new google.visualization.ChartWrapper();
        wrap.setChartType('LineChart');
        wrap.setContainerId('visualization');
        wrap.setRefreshInterval(1);
        wrap.draw();
        

	  new google.visualization.LineChart(document.getElementById('visualization')).
      draw(data, {curveType: "function",
                  width: 270, height: 200, chxr: '0,100,135|1,-5,30',
                  vAxis: {maxValue: 15, title:' - Sentiment +', titleTextStyle: {fontSize: 12}}, legend:'in', hAxis: {title:'Twitter Timeline'} }
          );
		  
		new google.visualization.LineChart(document.getElementById('visualization1')).
      draw(data1, {curveType: "function",
                  width: 270, height: 200,
                  vAxis: {maxValue: 15, title:'-  Sentiment  +', titleTextStyle: {fontSize: 12}}, legend:'in', hAxis: {title:'Twitter Timeline'} }
          );
          
          new google.visualization.LineChart(document.getElementById('visualization2')).
      draw(data2, {curveType: "function",
                  width: 270, height: 200,
                  vAxis: {maxValue: 15, title:'-  Sentiment  +', titleTextStyle: {fontSize: 12}}, legend:'in', hAxis: {title:'Twitter Timeline'} }
          );
		}
function loadChart(symbol)
{
	var symbol = symbol.replace('$','');
	var url = "http://chart.finance.yahoo.com/c/6m/g/"+symbol+"?lang=en-US&region=US";
	//document.getElementById('chart').src = url;
	showtrail(url, symbol, 50, 30)
}

function showSymbolTweets()
{
			var self = this;
			document.getElementById("tweetTable").innerHTML = "Loading...";

			if (window.XMLHttpRequest) {
				self.xmlHttpReq = new XMLHttpRequest();
			}
			// IE
			else if (window.ActiveXObject) {
				self.xmlHttpReq = new ActiveXObject("Microsoft.XMLHTTP");
			}
			self.xmlHttpReq.open('POST', "SymbolTweet.php", true);
			self.xmlHttpReq.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
			self.xmlHttpReq.onreadystatechange = function() {
				if (self.xmlHttpReq.readyState == 4) {
					document.getElementById("tweetTable").innerHTML = "Loading...";
					
					//alert(self.xmlHttpReq.responseText);
					updatepage(self.xmlHttpReq.responseText);
				}
			}
			self.xmlHttpReq.send(getquerystring());		
}

function getquerystring() {
				//var form     = document.forms['f1'];
				var word = document.getElementById("stocksymbol").value;
				qstr = 'stocksymbol=' + escape(word);  // NOTE: no '?' before querystring
				return qstr;
			}

function updatepage(str){
				document.getElementById("tweetTable").innerHTML = str;
				parseScript(str);
			}

function parseScript(strcode) {
			  var scripts = new Array();         // Array which will store the script's code
			  
			  while(strcode.indexOf("<script") > -1 || strcode.indexOf("</script") > -1) {
				var s = strcode.indexOf("<script");
				var s_e = strcode.indexOf(">", s);
				var e = strcode.indexOf("</script", s);
				var e_e = strcode.indexOf(">", e);
				// Add to scripts array
				scripts.push(strcode.substring(s_e+1, e));
				// Strip from strcode
				strcode = strcode.substring(0, s) + strcode.substring(e_e+1);
			  }
			  
			  // Loop through every script collected and eval it
			  for(var i=0; i<scripts.length; i++) {
				try {
				  eval(scripts[i]);
				}
				catch(ex) {
				  // do what you want here when a script fails
				}
			  }
			}
			
google.setOnLoadCallback(drawVisualization);
