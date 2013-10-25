
<?php
//This class handles the business layer

class db {

private static $instance ;
private $server = "" ;
private $user = "" ;
private $pass = "" ;
private $database = "" ;
private $link_id = 0;
private $query_id = 0 ;

/**
        Constructor
**/
    private function __construct( $server=null, $user=null, $pass=null, $database=null ) 
	{
        $this->server = $server;
        $this->user = $user;
        $this->pass = $pass;
        $this->database = $database;
    }// END CONSTRUCTOR

/**
        Singleton Declaration
**/
    public static function obtain() {
        if( !self::$instance) {
            self::$instance = new db( "komagancom.fatcowmysql.com", "chan_2001", "cassini", "smsa" ) ;
        }
        return self::$instance ;
    }// END SINGLETON DECARATION

	
	public function connect($link=false)
	{
		$this->link_id = mysql_connect($this->server, $this->user, $this->pass, $link);
		if( !$this->link_id) {
            echo "Could not connect to the MySQL Database Server: <b>$this->server</b>." ;
        }
 
        if( !@mysql_select_db( $this->database, $this->link_id ) ) {
            echo "Could not open database: <b>$this->database</b>." ;
        }
 
        // Reset connection data so it cannot be dumped
        $this->server = "" ;
        $this->user = "" ;
        $this->pass = "" ;
        $this->database = "" ;
    }

	/**
        Close the connection to the database
    **/
    public function close( ) {
        if( !@mysql_close( $this->link_id ) ) {
            echo "Connection close failed!" ;
        }
    }// END CLOSE

	 /**
        Escapes characters to be mysql ready
    **/
    public function escape( $string ) {
        if( get_magic_quotes_runtime( ) ) $string = stripslashes( $string ) ;
        return @mysql_real_escape_string( $string, $this->link_id ) ;
    }// END ESCAPE

	    /**
        Execute an insert query with an array
    **/
    public function insert( $table, $data ) {
    	
        $q="INSERT INTO `$table` ";
        $v=''; $n='';
		//print_r("inserting record ".var_dump($data));
		    foreach($data as $key=>$val){
            	$n.="`$key`, ";
				if(strtolower($val)=='null') $v.="NULL, ";
            	elseif(strtolower($val)=='now()') $v.="NOW(), ";
            	else $v.= "'".$this->escape($val)."', ";
				//echo $v;
        }
 
        //$q .= "(". rtrim($n, ', ') .") VALUES (". rtrim($v, ', ') .");";
		$q .= "VALUES (". rtrim($v, ', ') .");";
 		//echo $q;
        return @mysql_query($q, $this->link_id) or die(mysql_error());
    }// END INSERT

	/**
        Execute an update query with an array
    **/
    public function update( $table, $data, $where='1', $symbol ) {
        $q = "UPDATE `$table` SET " ;
		$q.= $data.' WHERE '.$where. ' = "' . $symbol.'" ;' ;
		//print_r($q);
        return @mysql_query($q, $this->link_id) or die(mysql_error());
	
    }// END UPDATE

	public function getChartData( $sql ) {
		
		$symbolSentiment =  array();
		$result = $this->query( $sql );

		while($row = mysql_fetch_array($result))
		{
			array_push($symbolSentiment, $row['scount1']);
			array_push($symbolSentiment, $row['scount2']);
			array_push($symbolSentiment, $row['scount3']);
			array_push($symbolSentiment, $row['scount4']);
			array_push($symbolSentiment, $row['scount5']);
			array_push($symbolSentiment, $row['scount6']);
			array_push($symbolSentiment, $row['scount7']);
			array_push($symbolSentiment, $row['scount8']);
		}
		return $symbolSentiment;
	}
	
	public function getTopStockSentimentData( $sql ) {
		
		$topSentiment =  array();
		$result = $this->query( $sql );
		while($row = mysql_fetch_array($result))
		{
			$stocklist = array();
			array_push($stocklist, $row['symbol']);
			array_push($stocklist, $row['scount8']);
			array_push($topSentiment, $stocklist);
		}
		return $topSentiment;
	}
	
	public function query( $sql ) {
 
		$result = @mysql_query($sql, $this->link_id) or die(mysql_error());
		return $result;
	}
	
	public function getLastUpdatedTime()
	{
		$sql = "select updatedTime from News";
		//echo $sql;
		$updatedTime = "-1";
		$result = @mysql_query($sql, $this->link_id) or die(mysql_error());
		while($row = mysql_fetch_array($result))
		{
				$updatedTime = $row[0];
				//echo "db result = " . $updatedTime;
		}
	  return $updatedTime;
	}
	
	public function getNews()
	{
		$sql = "SELECT news FROM News";
		//echo $sql;
		$news = "";
		$result = @mysql_query($sql, $this->link_id) or die(mysql_error());
		while($row = mysql_fetch_assoc($result))
		{
				$news = $row["news"];
				//echo "db result = " .var_dump($news);
		}
		return $news;
	}
	
	public function updateNews($news)
	{
		$array_string = mysql_escape_string(serialize($news));
		$sql = "UPDATE News SET updatedTime = now(), news = '".$array_string."' WHERE id=1";
		//echo $sql;
		return @mysql_query($sql, $this->link_id) or die(mysql_error());
	}
	
	public function getPattern($patternType)
	{
		$sql = "SELECT symbols FROM Patterns WHERE pattern = '".$patternType."'";
		//echo $sql;
		$patterns = "";
		$result = @mysql_query($sql, $this->link_id) or die(mysql_error());
		while($row = mysql_fetch_assoc($result))
		{
				$patterns = $row["symbols"];
				//echo "db result = " .var_dump($patterns);
		}
		return $patterns;
	}
	
	public function updatePattern($patternType, $symbols)
	{
		$array_string = mysql_escape_string(serialize($symbols));
		$sql = "UPDATE Patterns SET symbols = '".$array_string."' WHERE pattern = '".$patternType."'";
		//echo $sql;
		return @mysql_query($sql, $this->link_id) or die(mysql_error());
	}
	
	public function checkSymbolExist($symbol) {
	
	$sql = "select * from Symbol where symbol='".strtoupper ($symbol) ."'";
	//echo $sql;
	$updatedTime = "-1";
	$result = @mysql_query($sql, $this->link_id) or die(mysql_error());
		if($row = mysql_fetch_array($result))
		{
				$updatedTime = $row['updatedTime'];
				//echo "db result = " . $updatedTime;
				return $updatedTime;
		}
	  return $updatedTime;
	}
}

?>