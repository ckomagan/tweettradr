<?php 
	require_once("TrendAnalysis.php");
	$tc = new TrendAnalysis;
    $symbol = $_POST['symbol'];
	if($symbol) { 
		$SymbolSentiment = $tc->performSentimentAnalysis($symbol);
		if($SymbolSentiment[0] >= -1 && $SymbolSentiment[0] <= 1) { $sentiment = 'Neutral'; $color7 = '#0000FF';}
	 	else if($SymbolSentiment[0] > 1) { $sentiment = 'Bullish'; $color7='#008000';} else { $sentiment = 'Bearish';$color7 = '#990000';}
     	
		echo '<script type="text/javascript">
		    var tmp = '. time().';
			var data6 = new google.visualization.DataTable();
			
			data6.addColumn("string", "Timeline");
			data6.addColumn("number", "$' . $symbol . ' - '.$sentiment.'");
			data6.addRow(["100", '.$SymbolSentiment[4].']);
			data6.addRow(["80", '.$SymbolSentiment[3].']);
			data6.addRow(["60", '.$SymbolSentiment[2].']);
			data6.addRow(["40", '.$SymbolSentiment[1].']);
			data6.addRow(["20", '.$SymbolSentiment[0].']);
			new google.visualization.LineChart(document.getElementById("visualization6")).
					  draw(data6, {curveType: "function",
								  width: 460, height: 130, legendFontSize:10, colors: ["'.$color7.'"], legendTextColor: ["'.$color7.'"],
								  vAxis: {maxValue: 10, title:"-  Sentiment  +", titleTextStyle: {fontSize: 14}}, legend:"in", hAxis: {title:"Tweets Timeline", titleTextStyle: {fontSize: 12}, textStyle: {fontSize: 10}}
						  });
		 </script>';
		}?>