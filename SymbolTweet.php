
<?php 
//This class handles retrieving a list of tweets given a symbol
	require_once("TrendAnalysis.php");
	$tc = new TrendAnalysis;
	$symbol = $_POST['symbol'];
	//echo $symbol;
    if($symbol) { 
    			$stocksymbol = $symbol;
    			$stockTweetsArray = $tc->getStockTweets("\$".$symbol, 5);
				$stockTweets = $stockTweetsArray[0];
				$stockURLs = $stockTweetsArray[1];
				//var_dump($stockTweetsArray);

				for($ns = 0; $ns < sizeof ($stockTweets); $ns++) 
				{
					
					$responseText = $responseText.'
		 			<tr><td height="25" style="border:1px solid black">
					<a style="text-decoration: none" href="'.$stockURLs[$ns].'" target="_blank">'.$stockTweets[$ns].' </a>
									</td></tr>';
					//echo $responseText;
				}
				echo $responseText;
			}	
			
			//$tc->getPatternStocks("tp", 20);
?>	
  					
	
		

