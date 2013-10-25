<? 
require_once("../TrendAnalysis.php");
require_once("../JSON.php");
?>
<!DOCTYPE html>
<html lang="en">
<head>
<title>TweetTradr (Beta)</title>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width">
<META name=”Description” content=”Research and Analyze Financial Trading data from Twitter”>
<META name=”Keywords” content=”twitter, finance, trading, charts, tweets about finance, tweets about trading, markets, indexes, sentiment, trading sentiment, buy, sell, trading”>
<link href="css/styles.css" rel="stylesheet" type="text/css" media="all">
<link href="http://fonts.googleapis.com/css?family=Ubuntu:regular,bold" rel="stylesheet" type="text/css">
<link href="http://fonts.googleapis.com/css?family=Vollkorn:regular,italic,bold" rel="stylesheet" type="text/css">
<!--[if lt IE 9]>
<script src="js/html5.js"></script>
<script src="js/IE9.js"></script>
<![endif]-->

<?php
		$SymbolSentiment = array();
		$topstocksentiment = array();
		$json = new Services_JSON();
		$tc = new TrendAnalysis;
		$tc->setUserAgent("Mozilla/5.0 (compatible; TrendAnalysis/1.0;)");
?>

		<script type="text/javascript" src="https://www.google.com/jsapi"></script>
   		<script type="text/javascript">
      		google.load('visualization', '1', {packages: ['corechart']});
   		</script>
		
		<script type="text/javascript" src="http://ajax.googleapis.com/ajax/libs/jquery/1.3.0/jquery.min.js"></script>
		<script type="text/javascript" src="http://ajax.googleapis.com/ajax/libs/jqueryui/1.7.1/jquery-ui.min.js"></script>
		<script type="text/javascript" src="http://code.jquery.com/ui/1.10.3/jquery-ui.js"></script>
		<script src="js/vTicker.js"></script>
		
<script language="JavaScript" type="text/javascript">
		
		var current_promo = 1;

		function switch1(){
		if (current_promo == 1) {
  		  	  $("#visualization").hide();
   			  $("#visualization1").hide();
   			  $("#visualization2").hide();
			  $("#visualization3").fadeIn('slow');
   			  $("#visualization4").fadeIn('slow');
   			  $("#visualization5").fadeIn('slow');
   			  current_promo = 2;
			} else {
			  $("#visualization3").hide();
   			  $("#visualization4").hide();
   			  $("#visualization5").hide();
			  $("#visualization").fadeIn('slow');
   			  $("#visualization1").fadeIn('slow');
   			  $("#visualization2").fadeIn('slow');
   			  current_promo = 1;
			}
		}
		
			$(document).ready(function() {
			  $("#visualization3").hide();
   			  $("#visualization4").hide();
   			  $("#visualization5").hide();			
   			  setInterval(switch1, 5000 );
			});

			$(function() {
  				$('#example').vTicker('init', {speed: 10, 
    			pause: 8000,
   				 showItems: 2,
   				 padding:0.0});
			});
  
			function catchEnter(e){
   				var theKey=0;
   				e=(window.event)?event:e;
   					theKey=(e.keyCode)?e.keyCode:e.charCode;
  					 if(theKey=="13"){
						 showSymbolTweets();
				}
				}
 
    function drawVisualization() {
  // Create and populate the data table.
 		var data = new google.visualization.DataTable();
		var data1 = new google.visualization.DataTable();
		var data2 = new google.visualization.DataTable();
		var data3 = new google.visualization.DataTable();
		var data4 = new google.visualization.DataTable();
		var data5 = new google.visualization.DataTable();

		<?php $SymbolSentiment = $tc->performSentimentAnalysis("SPY")?>
		<?if($SymbolSentiment[0] >= -1 && $SymbolSentiment[0] <= 1) { $sentiment = 'Neutral'; $color1 = '#0000FF';}
	 	else if($SymbolSentiment[0] > 1) { $sentiment = 'Bullish'; $color1='#008000';} else { $sentiment = 'Bearish';$color1 = '#990000';}
     	?>
		data.addColumn('string', 'Timeline');
		data.addColumn('number', '$SPY -  <?php echo $sentiment ?>');
		data.addRow(["100", <?php echo $SymbolSentiment[4] ?>]);
		data.addRow(["80", <?php echo $SymbolSentiment[3] ?>]);
		data.addRow(["60", <?php echo $SymbolSentiment[2] ?>]);
		data.addRow(["40", <?php echo $SymbolSentiment[1] ?>]);
		data.addRow(["20", <?php echo $SymbolSentiment[0] ?>]);
		
		<?php $SymbolSentiment = $tc->performSentimentAnalysis("TLT")?>
		<?if($SymbolSentiment[0] >= -1 && $SymbolSentiment[0] <= 1) { $sentiment = 'Neutral';$color2 = '#0000FF'; } 
     	else if($SymbolSentiment[0] > 1) { $sentiment = 'Bullish';$color2 = '#008000';} else { $sentiment = 'Bearish';$color2 = '#990000';}
     	?>
     	data1.addColumn('string', 'Timeline');
		data1.addColumn('number', '$TLT -  <?php echo $sentiment ?>');
		data1.addRow(["100", <?php echo $SymbolSentiment[4] ?>]);
		data1.addRow(["80", <?php echo $SymbolSentiment[3] ?>]);
		data1.addRow(["60", <?php echo $SymbolSentiment[2] ?>]);
		data1.addRow(["40", <?php echo $SymbolSentiment[1] ?>]);
		data1.addRow(["20", <?php echo $SymbolSentiment[0] ?>]);
		
		<?php $SymbolSentiment = $tc->performSentimentAnalysis("GLD")?>
		<?if($SymbolSentiment[0] >= -1 && $SymbolSentiment[0] <= 1) { $sentiment = 'Neutral'; $color3 = '#0000FF';} 
     	else if($SymbolSentiment[0] > 1) { $sentiment = 'Bullish';$color3 = '#008000';} else { $sentiment = 'Bearish';$color3 = '#990000';}
     	?>
     	data2.addColumn('string', 'Timeline');
		data2.addColumn('number', '$GLD -  <?php echo $sentiment ?>');
		data2.addRow(["100", <?php echo $SymbolSentiment[4] ?>]);
		data2.addRow(["80", <?php echo $SymbolSentiment[3] ?>]);
		data2.addRow(["60", <?php echo $SymbolSentiment[2] ?>]);
		data2.addRow(["40", <?php echo $SymbolSentiment[1] ?>]);
		data2.addRow(["20", <?php echo $SymbolSentiment[0] ?>]);
		
		<?php $SymbolSentiment = $tc->performSentimentAnalysis("EEM")?>
		<?if($SymbolSentiment[0] >= -1 && $SymbolSentiment[0] <= 1) { $sentiment = 'Neutral'; $color4 = '#0000FF';} 
     	else if($SymbolSentiment[0] > 1) { $sentiment = 'Bullish';$color4 = '#008000';} else { $sentiment = 'Bearish';$color4 = '#990000';}
     	?>
     	data3.addColumn('string', 'Timeline');
		data3.addColumn('number', '$EEM -  <?php echo $sentiment ?>');
		data3.addRow(["100", <?php echo $SymbolSentiment[4] ?>]);
		data3.addRow(["80", <?php echo $SymbolSentiment[3] ?>]);
		data3.addRow(["60", <?php echo $SymbolSentiment[2] ?>]);
		data3.addRow(["40", <?php echo $SymbolSentiment[1] ?>]);
		data3.addRow(["20", <?php echo $SymbolSentiment[0] ?>]);
		
		<?php $SymbolSentiment = $tc->performSentimentAnalysis("IWM")?>
		<?if($SymbolSentiment[0] >= -1 && $SymbolSentiment[0] <= 1) { $sentiment = 'Neutral'; $color5 = '#0000FF';} 
     	else if($SymbolSentiment[0] > 1) { $sentiment = 'Bullish';$color5 = '#008000';} else { $sentiment = 'Bearish';$color5 = '#990000';}
     	?>
     	data4.addColumn('string', 'Timeline');
		data4.addColumn('number', '$IWM -  <?php echo $sentiment ?>');
		data4.addRow(["100", <?php echo $SymbolSentiment[4] ?>]);
		data4.addRow(["80", <?php echo $SymbolSentiment[3] ?>]);
		data4.addRow(["60", <?php echo $SymbolSentiment[2] ?>]);
		data4.addRow(["40", <?php echo $SymbolSentiment[1] ?>]);
		data4.addRow(["20", <?php echo $SymbolSentiment[0] ?>]);
		
		<?php $SymbolSentiment = $tc->performSentimentAnalysis("XLF")?>
		<?if($SymbolSentiment[0] >= -1 && $SymbolSentiment[0] <= 1) { $sentiment = 'Neutral'; $color6 = '#0000FF';} 
     	else if($SymbolSentiment[0] > 1) { $sentiment = 'Bullish';$color6 = '#008000';} else { $sentiment = 'Bearish';$color6 = '#990000';}
     	?>
     	data5.addColumn('string', 'Timeline');
		data5.addColumn('number', '$XLF -  <?php echo $sentiment ?>');
		data5.addRow(["100", <?php echo $SymbolSentiment[4] ?>]);
		data5.addRow(["80", <?php echo $SymbolSentiment[3] ?>]);
		data5.addRow(["60", <?php echo $SymbolSentiment[2] ?>]);
		data5.addRow(["40", <?php echo $SymbolSentiment[1] ?>]);
		data5.addRow(["20", <?php echo $SymbolSentiment[0] ?>]);
		
		var wrap = new google.visualization.ChartWrapper();
        wrap.setChartType('LineChart');
        wrap.setContainerId('visualization');
        wrap.setRefreshInterval(1);
        wrap.draw();
        var hAxisFont = 10;
	  new google.visualization.LineChart(document.getElementById('visualization')).
      draw(data, {curveType: "function",
                  width: 160, height: 140, legendFontSize:10, colors: ['<?echo $color1;?>'], legendTextColor: ['<?echo $color1;?>'],
                  vAxis: {maxValue: 5, title:'- Sentiment +', titleTextStyle: {fontSize: 14}}, legend:'in', hAxis: {title:'Tweets Timeline', titleTextStyle: {fontSize: 12}, textStyle: {fontSize: hAxisFont}} 
          });
		  
		new google.visualization.LineChart(document.getElementById('visualization1')).
      draw(data1, {curveType: "function",
                  width: 150, height: 140, legendFontSize:10, colors: ['<?echo $color2;?>'], legendTextColor: ['<?echo $color2;?>'],
                  vAxis: {maxValue: 5, title:'- Sentiment +', titleTextStyle: {fontSize: 14}}, legend:'in', hAxis: {title:'Tweets Timeline', titleTextStyle: {fontSize: 12}, textStyle: {fontSize: hAxisFont}}
          });
          
          new google.visualization.LineChart(document.getElementById('visualization2')).
      draw(data2, {curveType: "function",
                  width: 150, height: 140, legendFontSize:10, colors: ['<?echo $color3;?>'], legendTextColor: ['<?echo $color3;?>'],
                  vAxis: {maxValue: 5, title:'- Sentiment +', titleTextStyle: {fontSize: 14}}, legend:'in', hAxis: {title:'Tweets Timeline', titleTextStyle: {fontSize: 12}, textStyle: {fontSize: hAxisFont}} 
          });
		
		new google.visualization.LineChart(document.getElementById('visualization3')).
      draw(data3, {curveType: "function", 
                  width: 150, height: 140, legendFontSize:10, colors: ['<?echo $color4;?>'], legendTextColor: ['<?echo $color4;?>'],
                  vAxis: {maxValue: 5, title:'- Sentiment +', titleTextStyle: {fontSize: 14}}, legend:'in', hAxis: {title:'Tweets Timeline', titleTextStyle: {fontSize: 12}, textStyle: {fontSize: hAxisFont}}
          });
		
		new google.visualization.LineChart(document.getElementById('visualization4')).
      draw(data4, {curveType: "function",
                  width: 150, height: 140, legendFontSize:10, colors: ['<?echo $color5;?>'], legendTextColor: ['<?echo $color5;?>'],
                  vAxis: {maxValue: 5, title:'- Sentiment +', titleTextStyle: {fontSize: 14}}, legend:'in', hAxis: {title:'Tweets Timeline', titleTextStyle: {fontSize: 12}, textStyle: {fontSize: hAxisFont}}
          });
		
		new google.visualization.LineChart(document.getElementById('visualization5')).
      draw(data5, {curveType: "function",
                  width: 150, height: 140, legendFontSize:10, colors: ['<?echo $color6;?>'], legendTextColor: ['<?echo $color6;?>'],
                  vAxis: {maxValue: 5, title:'- Sentiment +', titleTextStyle: {fontSize: 14}}, legend:'in', hAxis: {title:'Tweets Timeline', titleTextStyle: {fontSize: 12}, textStyle: {fontSize: hAxisFont}}
          });
		}
		
function loadChart(symbol)
{
	var symbol = symbol.replace('$','');
	var url = "http://chart.finance.yahoo.com/z?t=3m&q=s&l=on&z=s&s="+symbol;
	//document.getElementById('chart').src = url;
	//showtrail(url, symbol, 30, 20);  
	document.getElementById('chartFrame').hidden = false;
	document.getElementById('chartFrame').src = url;
}

function showTweets(symbol)
{
	document.getElementsByName('stocksymbol')[0].value = symbol;
	showSymbolTweets();
}

function showSymbolTweets()
{
			var self = this;
			document.getElementById("tweetTable").innerHTML = "Loading...";
			document.getElementById('chartFrame').hidden = true;
			if (window.XMLHttpRequest) {
				self.xmlHttpReq = new XMLHttpRequest();
			}
			// IE
			else if (window.ActiveXObject) {
				self.xmlHttpReq = new ActiveXObject("Microsoft.XMLHTTP");
			}
			self.xmlHttpReq.open('POST', "SymbolTweet.php", true);
			self.xmlHttpReq.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
			self.xmlHttpReq.send(getquerystring());		
			self.xmlHttpReq.onreadystatechange = function() {
				if (self.xmlHttpReq.readyState == 4) {
					document.getElementById("tweetTable").innerHTML = "Loading...";
					//alert(self.xmlHttpReq.responseText);
					updateTable(self.xmlHttpReq.responseText);
					showSymbolChart();  // we are doing this since calling both functions at the same time messes up..probably due to webservice response collision
				}
			}
}

function showSymbolChart()
{
			var selfChart = this;
			//document.getElementById("visualization6").innerHTML = "Loading...";

			if (window.XMLHttpRequest) {
				selfChart.xmlHttpReq = new XMLHttpRequest();
			}
			// IE
			else if (window.ActiveXObject) {
				selfChart.xmlHttpReq = new ActiveXObject("Microsoft.XMLHTTP");
			}
			selfChart.xmlHttpReq.open('POST', "../SymbolSentiment_mobile.php", true);
			selfChart.xmlHttpReq.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
			selfChart.xmlHttpReq.send(getquerystring());		
			selfChart.xmlHttpReq.onreadystatechange = function() {
				if (selfChart.xmlHttpReq.readyState == 4) {
					document.getElementById("visualization6").innerHTML = "Loading...";
					//alert(self.xmlHttpReq.responseText);
					updateChart(selfChart.xmlHttpReq.responseText);
				}
			}
			//alert(getquerystring());
}

function getquerystring() {
				var word = document.getElementById("stocksymbol").value;
				qstr = 'symbol=' + escape(word);  // NOTE: no '?' before querystring
				return qstr;
			}

function updateTable(str){
				document.getElementById("tweetTable").innerHTML = str;
				parseScript(str);
			}
			
function updateChart(str){
				document.getElementById("visualization6").innerHTML = str;
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
			
$(function() {
    	$('#marketstats').show();
	});
	
google.setOnLoadCallback(drawVisualization);
   		</script>
   				
		<!-- START Worden Top Gainers Ticker Widget -->
			<script src="http://widgets.freestockcharts.com/script/WBIHorizontalTicker2.js?ver=12334" type="text/javascript"></script> 
			<link href="http://widgets.freestockcharts.com/WidgetServer/WBITickerblue.css" rel="stylesheet" type="text/css" />
			<script language="JavaScript" type="text/javascript">
 			   var gainTicker = new WBIHorizontalTicker('gainers');
 			   gainTicker.start();
			</script> 
			<!-- End Scrolling Ticker Widget -->
</head>


<body onLoad="showSymbolTweets();loadChart('TSLA')">
	<div id="headerwrap">
			<header id="mainheader" class="bodywidth clear">
				 <hgroup id="websitetitle">
						<h1><span class="bold">@</span>TweetTradr</h1>
						<h2>where Twitter and Trading intersect...</h2>
				 </hgroup>
				 <nav>
				  <iframe width="150" height="62" scrolling="no" frameborder="0" style="border:none;" src="http://widgets.freestockcharts.com/WidgetServer/WatchListWidget.aspx?sym=DIA,NYSE,SPY,GLD&style=WLBlueStyle&w=120"></iframe></li>
				</nav>
			</header>
	</div>
	<aside id="introduction" class="bodywidth clear">
	  <div id="introleft">
			<div id="example">
				<ul>
				<li><font color="blue">Here are some of the top financial Tweets...</font></li>
				<?php 		
					$financialnewsArray = unserialize($tc->getFinancialNews());
					//var_dump($financialnewsArray);
					$newsTweets = $financialnewsArray[0];
					$newsURLs = $financialnewsArray[1];
						//echo sizeof ($newsTweets);
						for($ns = 0; $ns < sizeof ($newsTweets); $ns++) {
						?>
						<li><a style="text-decoration: none" href="<?php echo $newsURLs[$ns];?>" target="_blank"><?php echo $newsTweets[$ns]; ?></a></li>
						<?}?>	
				</ul>
			</div>
	  </div>
	</aside>
	<div id="maincontent" class="bodywidth clear">
		<div id="aboutleft">
			 <h3>Market Twitter Sentiment</h3>
						<table>
								<tr>
								<td>
									<div id="visualization" style="width: 155px; height: 140px;"></div>
								</td>
								<td>
									<div id="visualization1" style="width: 150px; height: 140px;"></div>
								</td>
								<td>
									<div id="visualization2" style="width: 150px; height: 140px;"></div>
								</td>
								<td>
									<div id="visualization3" style="width: 150px; height: 140px;"></div>
								</td>
								<td>
									<div id="visualization4" style="width: 150px; height: 140px;"></div>
								</td>
								<td>
									<div id="visualization5" style="width: 150px; height: 140px;"></div>
								</td>
								</tr>
							</table>		
							<hr></hr>	
							<br/>
				<h3> Track Stock sentiment</h3>
						<div id ="symbols">
							Symbol:&nbsp;<input id="stocksymbol" name="stocksymbol" type="text" size="5" value="TSLA" onfocus="this.value =''" onkeyup="catchEnter(event);" id="stocksymbol">
							<input name="Search" id="Search" type="button" value="Get Tweets" onclick="showSymbolTweets();"/>&nbsp;&nbsp;&nbsp;&nbsp;
							<a href="javascript:showTweets('GOOG');">GOOG</a>&nbsp;&nbsp;
							<a href="javascript:showTweets('AAPL');">AAPL</a>&nbsp;&nbsp;
							<a href="javascript:showTweets('AMZN');">AMZN</a>&nbsp;&nbsp;
							<a href="javascript:showTweets('VIX');">VIX</a>&nbsp;&nbsp;
							<a href="javascript:showTweets('SCTY');">SCTY</a>&nbsp;&nbsp;
							<a href="javascript:showTweets('NFLX');">NFLX</a>&nbsp;&nbsp;
						</div><br/><br/>
							<div id="visualization6" style="width: 500px; height: 130px"></div><br/>
							<table id="tweetTable"></table>
		 </div>
  		<section id="articlesright">
   				 <h2>Most tweeted</h2>
						<BR/>
						<table width="100%">
						<tr><td width="53%">
						<h3>Top Performers</h3>
						<?
								$tweets = unserialize($tc->getPatternStocks("tp", 30));
								//var_dump($tweets);
							?>
							<ul class="style1">
								<?php foreach($tweets as $symbol): 
								if($symbol!="") {
								?>
  							      <li>
  							      <a href="#" onmouseover="loadChart('<?php echo $symbol;?>')" onmouseout="javascript:document.getElementById('chartFrame').src=''">
  							      <?php echo $symbol; ?></a></li>
								<?} endforeach; ?>
  						  </ul>
						</td>
						<td width="2%"></td>
						<td width="45%">  
						<h3>Long Plays</h3>
							<?
								$tweets = $tc->getLongStocks();
							?>
							<ul class="style1">
								<?php foreach($tweets as $symbol): 
								if($symbol!="") {
								?>
  							      <li>
  							      <a href="#" onmouseover="loadChart('<?php echo $symbol;?>')" onmouseout="javascript:document.getElementById('chartFrame').src=''">
  							      <?php echo $symbol; ?></a></li>
								<?} endforeach; ?>
  						  </ul>
						
						</td>
  						 </tr>
  						 <tr>
  						<td width="53%">  
  						<h3>Earnings Play</h3>
							<?
								$tweets = unserialize($tc->getPatternStocks("ep", 20));
							?>
							<ul class="style1">
								<?php foreach($tweets as $symbol): 
								if($symbol!="") {
								?>
  							      <li>
  							      <a href="#" onmouseover="loadChart('<?php echo $symbol;?>')" onmouseout="hidetrail()">
							      <?php echo $symbol; ?></a></li>
								<?} endforeach; ?>
							</ul>
						</td>
						<td width="2%"></td>
  						<td width="45%">  
						<h3>Short Plays</h3>
						<?
								$tweets = unserialize($tc->getPatternStocks("sp", 20));
							?>
							<ul class="style1">
								<?php foreach($tweets as $symbol): 
								if($symbol!="") {
								?>
  							      <li>
  							      <a href="#" onmouseover="loadChart('<?php echo $symbol;?>')" onmouseout="hidetrail()">
							      <?php echo $symbol; ?></a></li>
								<?} endforeach; ?>
							</ul>
						</td>
						</tr>
						</table>
						<br/>
						<div id="googlead">
							<script async src="//pagead2.googlesyndication.com/pagead/js/adsbygoogle.js"></script>
								<!-- MobileBannerUnit -->
								<ins class="adsbygoogle"
									 style="display:inline-block;width:320px;height:50px"
									 data-ad-client="ca-pub-3032609277893326"
									 data-ad-slot="1638386374"></ins>
								<script>
								(adsbygoogle = window.adsbygoogle || []).push({});
							</script>
						</div>
						<iframe id="chartFrame" frameborder="0" src="" height="200" width="360"></iframe>
		</section>		
	</div>
	<div id="footerwrap">
	  <footer id="mainfooter" class="bodywidth clear">
		<nav class="clear">
		  <ul>
			<li><a href="#">Home</a></li>
			<li><a href="#">About</a></li>
			<li><a href="#">Contact Us</a></li>
		  </ul>
		</nav>
		<p class="copyright">Website Template By <a target="_blank" href="http://www.tristarwebdesign.co.uk/">Tristar</a> &amp; Modified By <a target="_blank" href="http://www.os-templates.com/">OS Templates</a></p>
	  </footer>
	</div>
</body>
</html>
