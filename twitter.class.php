<?php

require_once("twitteroauth.php");
require_once('TwitterAPIExchange.php');
session_start();	

class Twitter
{
	private $rppChunk_;
	private $settings = array();
	private $connection;
	public $consumerkey;
	public $consumersecret;
	public $accesstoken;
	public $accesstokensecret;
	public function __construct(){	$rppChunk_ = 100;}
	
 	public function initiateConnection()
 	{ 	
		$this->consumerkey = "qGW34IIVlNq9zz9c8NG4Q";
		$this->consumersecret = "bIErM56IjRWI3ZR5a1yR66aNmcu5LADNknUBNojOHZk";
		$this->accesstoken = "14465668-zaPAaqgEHUroUnNCqqbo0j0oMbaOfBqyxFshY1cmT";
		$this->accesstokensecret = "wjFzfg7krCugy77CAe5v8k6ShQGGvSV5P4gmHpv3uDmUv";
	}
 
	public function searchResults( $search = null, $page = null, $id = null )
	{
		$yesterday = mktime(0,0,0,date("m"),date("d")-1,date("Y"));
		$since = date("Y-m-d", $yesterday);
		$url = "http://tweet-2-rss.appspot.com/feed/ckomagan/U3V3dEVd/search/tweets.json?q=" . $search . "&rpp=" . $page . "&since_id=" . $id;
		//echo $url;
		//echo "<BR/>";
		$fetch_json = simplexml_load_file($url);
		//echo "fetch json =" .$fetch_json;
		return $fetch_json;
	}
	
	public function searchResults2($search, $count)
	{
		//$search = "@timberners_lee OR netneutrality OR #openinternet";
		$this->initiateConnection();
		//$search = str_replace("#", "%23", $search); 

   		$url = "https://api.twitter.com/1.1/search/tweets";
		//echo $search.$count;
		
		$query = array(
  			"q" => $search,
			"count" => $count,
			"result_type" => "recent",
  			"lang" => "en",
  			"exclude" => "retweets",
			);
		$tweets = $this->search($this->consumerkey, $this->consumersecret, $this->accesstoken, $this->accesstokensecret, $query);
		return $tweets;
	}
	
	public function getFinancialNews($source, $count)
	{
		$url = "https://api.twitter.com/1.1/statuses/user_timeline.json?screen_name=ckomagan";
		$this->initiateConnection();
		$query = array(
  			"screen_name" => $source,
			"count" => $count,
			"result_type" => "recent",
  			"lang" => "en",
  			"exclude_replies" => "true",
  			"contributor_details" => "false",
  			"include_rts" => "false"
			);
		$connection = new TwitterOAuth($this->consumerkey, $this->consumersecret, $this->accesstoken, $this->accesstokensecret);
  		return $connection->get('statuses/user_timeline', $query);
	}
	
	function search($cons_key, $cons_secret, $oauth_token, $oauth_token_secret, $query) {
  		$connection = new TwitterOAuth($cons_key, $cons_secret, $oauth_token, $oauth_token_secret);
  		return $connection->get('search/tweets', $query);
	}
	
	public function weeklyTrends()	
	{	
		$URL = "http://api.twitter.com/1/trends/1.json?exclude=hashtags";
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL,$URL);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER,1);
		$result = curl_exec($ch);
		curl_close($ch);
		$obj = json_decode($result);
		/*$contents = @file_get_contents("https://api.twitter.com/1/trends/weekly.json?exclude=hashtags");
		if (strpos($http_response_header[0], "200")) {
		  $json = json_decode($contents);
		  foreach ($json->trends as $trend) {
				echo $trend->name;
			  }
		} else {
				echo "No content found";
		}*/
		return $obj;
	}
	
	public function searchResultsWithText( $search = null)
	{
		$url = "http://tweet-2-rss.appspot.com/feed/ckomagan/U3V3dEVd/search/tweets.json?q=" . rawurlencode($search) ."&rpp=20&count=21";
		//echo $url;
		$fetch_json = simplexml_load_file($url);
		
		return $fetch_json;
	}
}

?>