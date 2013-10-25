
<?php
//This class handles the business layer

define( "REFRESH_INTERVAL", 15);
define( "DEFAULT_DATATYPE", "json" );
require_once("twitter.class.php");
include("db.php");

class TrendAnalysis {

	private $response_;
	private $type_;
	private $userAgent_;
	private $headers_ = array( 'Expect:', 'X-Twitter-Client: ',
					'X-Twitter-Client-Version: ',
					'X-Twitter-Client-URL: ' );

	private $buyCounter_;
	private $sellCounter_;
	private static $con_;
	private $tweetTimelineData = array();
	private $data = array();
	private static $breakoutPattern = array();
	private static $tweetwords = array();
	public function __construct ( $type = DEFAULT_DATATYPE ) {
		
		$this->username_ = 'ckomagan';
		date_default_timezone_set('America/New_York');
		$this->response_ = array();
		$this->userAgent_ = '';
		$this->type_ = $type;
		$this->buyCounter_= 0;
		$this->sellCounter_= 0;
		$this->buySentiment_ = array ('buy', 'long', 'ramping', 'nHOD', 'HOD', 'still holding', 'buying more', 'on the move', 'upgraded', 'upgrading', 'looks oversold', 'is oversold', 'holding', 'adding', 'added', 'buying calls', 'is testing highs', 'is breaking out', 'follow through on', 'follow through', 'is the next', 'spikes', 'is spiking', 'accumulation', 'bullish', 'is bullish', 'is rebounding', 'scaling back in', 'is shooting up', 'could jump from here', 'just shot up', 'crazy move on', 'is poised to clear', 'is breaking out', 'is poised to break out', 'is rising', 'is going up', 'could go up', 'back in',  'is ripping', 'ripping into close', 'about to fly');
		$this->sellSentiment_ = array ('selling', 'sold', 'sold my', 'puts', 'averaging down', 'trades lower', 'slumping', 'remain short', 'short', 'closed', 'liquidating', 'out of', 'downgraded', 'overbought', 'keep shorting', 'great entry to short', 'top is in', 'bearish', 'watch out below', 'rally will fail', 'momemtum still negative', ' is going down', 'is breaking lower', 'cracking', 'closing', 'could go down', 'is still in downtrend', 'bearishness', 'is tanking', 'weakness', 'breakdown', 'could go lower', 'is making new lows', 'keeps going lower', 'dissapointing');
		$this->buyotherSentiment_ = array ('positive', 'squeeze', 'surged', 'short squeeze', 'squeeze', 'fry the shorts', 'nice pop', 'high rsi', 'solid breakout', 'worked today', 'aggressive with', 'buy the dips', 'buying on dip', 'crazy move', 'more upside', 'another move up', 'great entry to buy', 'buying calls', 'buying call positions','surging today', 'stock will rally', 'headed for a rally', 'bottoming', 'bottom is in', ' hammer', 'momo is up', 'uptrend', 'loading up', 'cover', 'strong support', 'strong earnings', 'bear trap', 'rebound', 'gap up', 'inverse head and shoulder', 'inverse H & S', 'got more', 'adding', 'go long', 'bought the dip', 'buying the dip', 'accumulation', 'breaks higher', 'good for higher price', 'higher from here', 'strong support', 'go higher', 'picking up more', 'cup and handle', 'double bottom', 'intraday high', 'trend is higher', 'bottom hammer', '52 weeh high', 'new high', 'breakout', 'smoking hot', 'high volume breakout', 'volume up', 'gets some love', 'looking good');
		$this->sellotherSentiment_ = array ('down', 'broken', 'drop is imminent', 'futures slide', 'careful', 'doom and gloom', 'move downside', 'selling more', 'double top','took some off', 'bearish', 'falling', 'pulling back', 'buying put', 'downgrade', 'correction', 'climax run', 'breakdown', 'head and shoulder', 'ugly action', 'climaxed', 'exhausion gap', 'shooting star', 'blow off top', 'momo is down', 'die', 'hit resistance', 'crushed', 'earnings disappointment', 'crash', 'dead cat bounce', 'head and shoulder', 'H & S', 'failed rally', 'distribution', 'spells a big threat', 'watch out below', 'rally ended today', 'exhaustion gap', 'high volume breakdown', 'trend is lower', '52 week low');

		$this->con_ = db::obtain();
		$this->con_->connect();
		//echo 'Connected successfully'; 
	}

	public function setUserAgent ( $agent = NULL ) {

		if ( empty($agent) ) {
			throw new Exception("User-Agent cannot be empty!");
		}

		$this->userAgent_ = $agent;
	}

	public function getUserAgent ( ) {
		return $this->userAgent_;
	}
 
    function isCacheTimeOver()
	{
		date_default_timezone_set('America/New_York');
		$now = date("Y-m-d H:i:s" ,time());
		$updatedTime = $this->con_->getLastUpdatedTime();
		$diffTime = round((strtoTime($now) - strtoTime($updatedTime))/60);
		$cacheTimeOver = $diffTime < REFRESH_INTERVAL ?-1 : 1;
		//echo "Last cached = ".$diffTime." mins ago ".$cacheTimeOver;
		return $cacheTimeOver;
	}
	
	public function performSentimentAnalysis( $symbol )
	{		
		$symbolSentiment =  array();
		$this->buyCounter_ = 0;
		$this->sellCounter_ = 0;
		//echo "Size of = ".sizeof($this->buySentiment_).", ";

		if($this->con_->checkSymbolExist($symbol) != -1)
		{
			if ( $this->isCacheTimeOver() < 0) 
			{
				//echo "Not updating";
				$symbolSentiment = $this->getSentimentData( $symbol );		//if it is already present and within xx minutes, get existing data
			}
			else {
				//echo "I am updating now";
				$symbolSentiment = $this->updateSentimentData( $symbol );	//update an existing symbol
			}
		}
		else{
				$symbolSentiment = $this->addSentimentData( $symbol ); 		//add a brand new symbol
			}
		return $symbolSentiment;
	}
	
	//This function checks if the tweet expresses a positive statement based on specific filter defined above
	public function checkPositiveSentiment( $symbol, $tweet )
	{
			//echo "Tweet = ".$tweet." ";
			foreach ($this->buySentiment_ as $word ) { 
					$beforeString = $word." ".$symbol;
					$afterString = $symbol." ".$word;
					//echo "After String =".$afterString;
					if(stripos($tweet, $beforeString) !== false || stripos($tweet, $afterString) !== false) 
						{ 
							//echo "match is ".$word; 
							$this->buyCounter_ += 1;
							break;
						}
				}
				
			$tweetArray = explode(" ",$tweet);
			$matchArray = array_intersect($this->buyotherSentiment_, $tweetArray);
			if(sizeof($matchArray) >= 1)
			{
					//print_r("positive->".print_r($matchArray));
					$this->buyCounter_ += 1;
					//break;	
			}
				
		//echo " Buy counter = ".$this->buyCounter_;
	}
	
	//This function checks if the tweet expresses a negative statement based on specific filter defined above
	public function checkNegativeSentiment( $symbol, $tweet )
	{
		foreach ($this->sellSentiment_ as $word ) { 
					$beforeString = $word." ".$symbol;
					$afterString = $symbol." ".$word;
					if(stripos($tweet, $beforeString) !== false || stripos($tweet, $afterString) !== false) 
					{ 
						//echo "match"; 
						$this->sellCounter_ += 1;
						break;
					}
				}
				
		$tweetArray = explode(" ",$tweet);
			$matchArray = array_intersect($this->sellotherSentiment_, $tweetArray);
			if(sizeof($matchArray) >= 1)
			{
					//print_r("positive->".print_r($matchArray));
					$this->buyCounter_ += 1;
					//break;	
			}
		//echo " Sell counter = ".$this->sellCounter_;
	}
	
	public function getSentimentData( $symbol )
	{
			//echo "getting chart data";
			$symbolSentiment = $this->con_->getChartData("select scount1, scount2, scount3, scount4, scount5 from Symbol where symbol= '".$symbol."'");
			return $symbolSentiment;
	}
	
	public function addSentimentData ( $symbol )
	{
			$data = array();
			//echo "calling add sentiment data";
			$tweetTimelineData = $this->splitSearchData( $symbol );
			array_push($this->data, NULL);
			array_push($this->data, $symbol);
			array_push($this->data, $tweetTimelineData[0]); //$tweetTimelineData[0] contains the latest tweets
			array_push($this->data, $tweetTimelineData[1]);
			array_push($this->data, $tweetTimelineData[2]);
			array_push($this->data, $tweetTimelineData[3]);
			array_push($this->data, $tweetTimelineData[4]);
			array_push($this->data, '');
			array_push($this->data, 'now()');
			//$this->con_->insert('Symbol', $this->data, "symbol", $symbol);
			$this->con_->insert('Symbol', $this->data);
			//echo $tweetTimelineData;
			return $tweetTimelineData;
	}
	
	public function updateSentimentData ( $symbol )
	{
			//echo 'calling update...';
			$tweetTimelineData = $this->splitSearchData ( $symbol );
			//print_r($tweetTimelineData); //$tweetTimelineData[0] contains the latest tweets
			$sqlpre = 'scount1 = ' . $tweetTimelineData[0] . ', scount2= '. $tweetTimelineData[1] . ', scount3=' .$tweetTimelineData[2] .',  scount4=' .$tweetTimelineData[3] .', scount5=' .$tweetTimelineData[4] .', updatedTime = now()';
			//echo $sqlpre;
			$this->con_->update('Symbol', $sqlpre, "symbol", $symbol);
			return $tweetTimelineData;
	}
	
	public function createChart( $buy, $sell ) {} 
	
	public function splitSearchData ( $symbol )
	{
			$Twitter = new Twitter;
			$id = NULL;
			$totalSplitCounter_ = 0;
			$tweetTimelineData = array();
			$totalTweets = 0;
			$tweets = $Twitter->searchResults2('\$'.$symbol, 102);
			//var_dump($tweets);
			
			$splitCounter_ = 0;
			$rpp = 0;$batch = 0;
			//echo("Tweets = ".$tweets);
			//echo "<BR/><BR/>";
			foreach($tweets->statuses as $line)
			{
				$diff20 = 0; $diffBatch = 20; 
				$totalSplitCounter_ += 1;
				$splitCounter_ += 1;
				$content = $line->text;
				//echo "Content = ".$content;
				//echo "<BR/>";
				$totalTweets += 1;
				$lastbuyCounter_ = $this->buyCounter_;
				$this->checkPositiveSentiment($symbol, $content);
				if ($this->buyCounter_ == $lastbuyCounter_)
				{
					$this->checkNegativeSentiment($symbol, $content);
				}
				if ($rpp < $diffBatch) {
						$diff = $this->buyCounter_ - $this->sellCounter_; //calculate the diff for one tweet
						$diff20 += $diff; //aggregate the diff for 20 tweets
						$rpp += 1;
						}
				if ($rpp == $diffBatch) {
						//echo "rpp = ".$rpp;
						$batch += 1;
						//echo " batch = ". $batch.", diff20 = " . $diff20. ", ";
						array_push($tweetTimelineData, $diff20); //add 20 tweets per array element
						$this->buyCounter_ = 0;
						$this->sellCounter_ = 0;
						//print_r($tweetTimelineData);
						$rpp = 0; $diff = 0;
						}
				}
				if($totalTweets < 20) { $tweetTimelineData[0] = $diff20; }
				if($totalTweets < 40) { $tweetTimelineData[1] = $tweetTimelineData[0]; }
				if($totalTweets < 60) { $tweetTimelineData[2] = $tweetTimelineData[1]; }
				if($totalTweets < 80) { $tweetTimelineData[3] = $tweetTimelineData[2]; }
				if($totalTweets < 100) { $tweetTimelineData[4] = $tweetTimelineData[3]; }
				
			//echo "Total Tweets processed -> " . $totalTweets;
			return $tweetTimelineData;
	}
	
	public function getTopStockSentiment ()
	{
		$topstocksentiment = array();
		$topstocksentiment = $this->con_->getTopStockSentimentData("SELECT DISTINCT symbol, scount8 FROM `Symbol` order BY (scount8+0) DESC");
		return $topstocksentiment;
	}
	
	//This function retrieves tweets based on certain search string
	public function getPatternStocks($patternType, $count)
	{
		$Twitter = new Twitter;
		$tweetWords = array(); 
		if ( $this->isCacheTimeOver() > 0 ) //if it is over 15 minutes
		{
			echo "updating";
			if($patternType == "tp")
			{
				$tweets = $Twitter->searchResults2('"52 week highs"', $count);
			}
			if($patternType == "ch")
			{
				$tweets = $Twitter->searchResults2('"cup and handle"', $count);
			}
			if($patternType == "vb")
			{
				$tweets = $Twitter->searchResults2('"volume breakout"', $count);
			}
			if($patternType == "br")
			{
				$tweets = $Twitter->searchResults2('"bottom reversal"', $count);
			}
			if($patternType == "rs")
			{
				$tweets = $Twitter->searchResults2('"relative strength"', $count);
			}
			if($patternType == "ep")
			{
				$tweets = $Twitter->searchResults2('"earnings play"', $count);
			}
			if($patternType == "sp")
			{
				$tweets = $Twitter->searchResults2('"short candidate"', $count);
			}
			//var_dump($tweets);
			foreach($tweets->statuses as $line){
					$tweetText = $line->text;
					$tweetWord = $this->getPatternText($tweetText);
					if(!in_array($tweetWord, $tweetWords)) {
						if(sizeof($tweetWords) < 5) {
							//echo sizeof($tweetWords);
							array_push($tweetWords, $tweetWord);
							}
						else {
							break;
							}
					}
				}
				$this->con_->updatePattern($patternType, $tweetWords);
			//var_dump($tweetWords);
		}
		else{
			//echo "retrieving...";
			return $this->con_->getPattern($patternType);		
		}
		return $tweetWords;
	}
	
	public function getLongStocks()
	{
		//if ( $this->isCacheTimeOver() > 0 ) //if it is over 15 minutes
		//{
			$ch = array_slice( unserialize($this->getPatternStocks("ch", 20)), 0, 2 );
			$vb = array_slice( unserialize($this->getPatternStocks("vb", 20)), 0, 1 );
			$rs = array_slice( unserialize($this->getPatternStocks("rs", 20)), 0, 2 );
			$br = array_slice( unserialize($this->getPatternStocks("br", 20)), 0, 1 );
			$breakoutPattern = array_merge($ch, $vb, $br, $rs);
		//}
		return $breakoutPattern;
	}
	
	//This function handles retrieving a list of tweets given a symbol
	public function getStockTweets($symbol, $count)
	{
		$Twitter = new Twitter;
		$tweetsArray = array(); 
		$stockTweets = array(); 
		$stockURLs = array();
		$stockTweetsArray = array();
		$tweets = $Twitter->searchResults2($symbol, $count);
		
		foreach($tweets->statuses as $line){
							$stockTweet = $line->text;
							$stockURL = $line->entities->urls[0]->url;
							//var_dump($line);
							//var_dump($stockURL);
							array_push($stockTweets, $stockTweet);
							if(is_null(trim($stockURL)) || trim($stockURL) == "" || trim($stockURL) == "NULL") {
									$stockURL = "";
								}
							//echo( $stockURL );
							//echo "<BR/>";
							array_push($stockURLs, $stockURL);
							//echo( sizeof($stockURLs) );
						}
				//echo "in php = ".sizeof($stockTweets).", ".sizeof($stockURLs);
				array_push($stockTweetsArray, $stockTweets);
				array_push($stockTweetsArray, $stockURLs);
				//var_dump($stockTweetsArray);
		return $stockTweetsArray;
	}
	
	//This function filters the tweet string based on specific filter criteria
    public function getPatternText($text)
	{
		//echo "Tweet Text = ".$text;
		$text = preg_replace('/[^A-Za-z\$#_ -]/', '', $text); //remove all special characters
		//echo $text;
		//echo "<BR/>";
		$words = explode(' ', $text); //explore string into seperate words
		$tweetWord = ""; 
		foreach ($words as $key => $word)
		{	
			if (strtoupper($word) == $word && strlen($word) > 1 && strlen($word) < 6)		//check if the word is all UPPERCASE
			{
				if(strpos($word, '$') !== false) //only grab words with $ sign
				{
					$tweetWord = $word;
					//echo $tweetWord;
					//		echo "<BR/>";

				}
			}
		}
		return $tweetWord;
	}
	
	//This function retrieves the top Financial news from a set of predefined news sources
	public function getFinancialNews()
	{
		$Twitter = new Twitter;
		$newsTweets = array(); 
		$newsURLs = array();
		$financialnewsArray = array();
		$newsSources = array("YahooFinance", "BloombergNews", "moneymorning", "Finance_Wire", "FinancialTimes", "Stockpickr", "stocktwits", "breakoutstocks", "bespokeinvest", "chessNwine", "markflowchatter", "abnormalreturns");
		if ($this->isCacheTimeOver() > 0) 
		{
			//echo "updating...";
			for($ns = 0; $ns < sizeof ($newsSources); $ns++)
				{	
					$tweets = $Twitter->getFinancialNews($newsSources[$ns], 3);
					//var_dump($tweets);
					foreach($tweets as $line){
							$newsTweet = $line->text;
							$newsURL = $line->entities->urls[0]->url;
							//var_dump($newsURL);
							array_push($newsTweets, '<font color="red">@'.$newsSources[$ns].'</font>: '.$newsTweet);
							if(is_null(trim($newsURL)) || trim($newsURL) == "" || trim($newsURL) == "NULL") {
									$newsURL = "";
								}
							//echo( $newsURL );
							//echo "<BR/>";
							array_push($newsURLs, $newsURL);
						}
				}
				array_push($financialnewsArray, $newsTweets);
				array_push($financialnewsArray, $newsURLs);
				$this->con_->updateNews($financialnewsArray);
				//var_dump($financialnewsArray);
				return $financialnewsArray;
			}
			else{
				//echo "retrieving...";
				//var_dump($this->con_->getNews());
				return $this->con_->getNews();
			}
	    return $financialNewsArray;
	}


	public function searchResults2($symbol, $count)
	{
		$Twitter = new Twitter;
		$Twitter->searchResults2($symbol, $count);
	}
	
}

?>