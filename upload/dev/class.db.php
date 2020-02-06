<?php
/**
*
* @ IonCube v8.3 Loader By DoraemonPT
* @ PHP 5.3
* @ Decoder version : 1.0.0.7
* @ Author     : DoraemonPT
* @ Release on : 09.05.2014
* @ Website    : http://EasyToYou.eu
*
**/

	class db {
		protected $MySQLlink = false;
		protected $QueryResult = null;
		protected $dbhost = null;
		protected $dbuser = null;
		protected $dbpass = null;
		protected $dbname = null;
		protected $debug = null;

		function ldb($lid) {
			global $l2cfg;
			global $ldb;

			if (!isset( $ldb[$lid] )) {
				$ldb[$lid] = new db( $l2cfg['ls'][$lid]['dbhost'], $l2cfg['ls'][$lid]['dbuser'], $l2cfg['ls'][$lid]['dbpass'], $l2cfg['ls'][$lid]['dbname'], $l2cfg['mysql']['debug'] );
				return $ldb[$lid];
			}

		}

		function gdb($sid) {
			global $l2cfg;
			global $gdb;

			if (!isset( $gdb[$sid] )) {
				$gdb[$sid] = new db( $l2cfg['gs'][$sid]['dbhost'], $l2cfg['gs'][$sid]['dbuser'], $l2cfg['gs'][$sid]['dbpass'], $l2cfg['gs'][$sid]['dbname'], $l2cfg['mysql']['debug'] );
				return $gdb[$sid];
			}

		}

		function __construct($db_host, $db_user, $db_pass, $db_name, $debug = true) {
			$this->host = $db_host;
			$this->user = $db_user;
			$this->pass = $db_pass;
			$this->dbname = $db_name;
			$this->debug = $debug;

			if (!($this->MySQLlink = @mysqli_connect($this->host, $this->user, $this->pass)) && $this->debug == '1')
        {
            $this->error(@mysqli_connect_error(), @mysqli_connect_errno());
        }


			if (!(@mysqli_select_db( $this->MySQLlink, $this->dbname )))
				{
				if ($this->debug) {
					$this->error( @mysqli_error($this->MySQLlink ), @mysqli_errno($this->MySQLlink ) );
				} 
else {
					return false;
				}
			}


			if ($this->debug) {
				/**$this->hwid(  );*/
			}


			if (!defined( 'COLLATE' )) {
				define( 'COLLATE', 'utf8' );
			}


			if ($this->MySQLlink) {
				@mysqli_query( $this->MySQLlink,'SET NAMES ' . COLLATE . ''  );
			}

			return true;
		}

		function close() {
			if ($this->MySQLlink) {
				if ($this->QueryResult) {
					@mysqli_free_result( $this->QueryResult );
				}
				$result = @mysqli_close($this->MySQLlink);
				return $result;
			}
			else
			{
				return false;
			}
		}

		function query($query = '') {
			if ($this->dbname != '') {
				$dbselect = @mysqli_select_db($this->MySQLlink, $this->dbname  );

				if (!$dbselect) {
					@mysqli_close( $this->MySQLlink );
					$this->MySQLlink = $dbselect;
					return false;
				}
			}

			$this->QueryResult = null;

			if ($query != '') {
				$this->QueryResult = @mysqli_query($this->MySQLlink, $query );

				if (( $this->debug && @mysqli_errno( $this->MySQLlink ) )) {
					$this->error( @mysqli_error( $this->MySQLlink ), @mysqli_errno( $this->MySQLlink ), $query );
				}
			}

			return $this->QueryResult;
		}

		/*function num_rows($query_id = 0) {
			if ($query_id == 0) {
				$this->QueryResult;
				$query_id = $this->QueryResult;
			}

			return @mysqli_num_rows( $query_id );
		}*/
		
	 function num_rows($query_id = null)
		{
        if ($query_id == null)
           $query_id = $this->QueryResult;
        return @mysqli_num_rows($query_id);
		}

		/*function fetch($query_id = 0) {
			if ($query_id == 0) {
				$this->QueryResult;
				$query_id = $this->QueryResult;
			}

			return @mysqli_fetch_array( $query_id );
		}*/
		
		function fetch($query_id = null)
		{
			if ($query_id == null)
				$query_id = $this->QueryResult;
			return @mysqli_fetch_array($query_id);
		}

		function fetchAll($query_id = null)
		{
			if ($query_id == null)
				$query_id = $this->QueryResult;
			return @mysqli_fetch_all($query_id, MYSQLI_ASSOC);
		}
		
		function result($query_id = null, $rownum = 0)
    {
        if ($query_id == null)
            $query_id = $this->QueryResult;
        return @mysqli_data_seek($query_id, $rownum);
    }
	

		/*function affected() {
			if ($this->MySQLlink) {
				@mysqli_affected_rows( $this->MySQLlink );
				$result = @mysqli_affected_rows($this->MySQLlink);
				return $result;
			}

			return false;
		}*/
		
		function affected()
    {
        if ($this->MySQLlink)
        {
            $result = @mysqli_affected_rows($this->MySQLlink);
            return $result;
        }
        else
        {
            return FALSE;
        }
    }

		/*function safe($sql) {
			if ($this->MySQLlink) {
				return mysqli_real_escape_string( $sql, $this->MySQLlink );
			}

			return mysqli_escape_string( $sql );
		}*/
		function safe($sql)
    {
        if ($this->MySQLlink)
            return mysqli_real_escape_string($this->MySQLlink, $sql);
        else
            return addcslashes($sql, "'");
    }

		function lastId() 
		{
			if ($this->MySQLlink) 
			{
				//@mysqli_insert_id( $this->MySQLlink );
				$result = @mysqli_insert_id($this->MySQLlink);
				return $result;
			}
			else
			{
				return FALSE;
			}
		}

		/**function hwid() {
			error_reporting(  );
			$e = '';
			error_reporting( 0 );
			$host = ($_SERVER['HTTP_HOST'] ? $_SERVER['HTTP_HOST'] : @getenv( 'HTTP_HOST' ));
			str_replace( 'http://', '', $host );
			$host = '';

			if (strtolower( substr( $host, 0, 4 ) ) == 'www.') {
				substr( $host, 4 );
				$host = '';
			}

			explode( '@', file_get_contents( CONFDIR . 'key.php' ) );
			$keys = '';
			explode( '|', base64_decode( strtr( str_replace( '-', '', strrev( trim( $keys[7] ) ) ), '+Rc0OYVnNdD62qQ', '=cRO0VYNnDd26Qq' ) ) );
			$token = '';

			if (( count( $token ) != 3 || md5( $token[0] . $token[1] . 'be5bf830d02eba8b6501dd523d04c0ca' ) != $token[2] )) {
				exit( 'Подмена ключа' );
			}


			if ($token[1] < time(  )) {
				exit( 'Время лицензии истекло ' . date( 'd.m.Y H:i', $token[1] ) );
			}


			if (( ( $token[0] != $host || !defined( 'DEVELOP' ) ) || DEVELOP != 'STRESSWEB' )) {
				exit( base64_decode( 'PGRpdiBhbGlnbj0nbGVmdCc+VW5yZWdpc3RlciBWZXJzaW9uIDxhIGhyZWY9J2h0dHA6Ly9zdHJlc3N3ZWIucnUvJz5TVFJFU1MgV0VCPC9hPjxicj7QntCx0L3QsNGA0YPQttC10L3QsCDQvtGI0LjQsdC60LAg0LvQuNGG0LXQvdC30LjRgNC+0LLQsNC90LjRjy4g0JLQvtC30LzQvtC20L3Ri9C1INCy0LDRgNC40LDQvdGC0Ys6PGJyPjEuINCU0LDQvdC90LDRjyDQutC+0L/QuNGPINC90LUg0L/RgNC+0YjQu9CwINC70LjRhtC10L3Qt9C40YDQvtCy0LDQvdC40LU8YnI+Mi4g0JvQuNGG0LXQvdC30LjRjyDQstGL0LTQsNC90LAg0L3QsCDQtNGA0YPQs9C+0LUg0LTQvtC80LXQvdC90L7QtSDQuNC80Y88YnI+My4g0JLQvtC30LzQvtC20L3QviDQtNC+0L/Rg9GJ0LXQvdCwINC+0YjQuNCx0LrQsCDQsiDQvdCw0YHRgtGA0L7QudC60LDRhSDQtNC+0LzQtdC90LA8L2Rpdj4=' ) );
			}

			error_reporting( $e );
		}*/

		function SuperQuery($query = '', $param = array()) {
			return $this->query( $this->buildQuery( $query, $param ) );
		}

		function SuperResult($query = '', $param = array()) {
			return $this->result( $this->SuperQuery( $query, $param ), 0 );
		}

		function SuperFetchArray($query = '', $param = array()) {
			return $this->fetch( $this->SuperQuery( $query, $param ) );
		}

		function buildQuery($query = '', $param = array()) 
		{
			if (( !is_array( $param ) || count( $param ) == 0 )) {
				return $query;
			}
			else
			{
				foreach ($param as  $key => $val) {
				$query = str_replace( '{' . $key . '}', $val, $query );
				}

				return $query;
			}
		}

		function error($error, $error_num, $query = '') {
			if ($query) {
				
				$query = preg_replace( '/([0-9a-f]){32}/', '********************************', $query );
			}

			echo '<?xml version="1.0" encoding="iso-8859-1"?>
		<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
		<html xmlns="http://www.w3.org/1999/xhtml">
		<head>
		<title>MySQL Fatal Error</title>
		<meta http-equiv="Content-Type" content="text/html; charset=windows-1251" />
		<style type="text/css">
		<!--
		body { font-family: Tahoma, Arial, Helvetica, sans-serif; font-size: 11px; font-style: normal; color: #000000; }
		-->
		</style>
		</head>
		<body align="center">
			<font size="4">Ошибка MySQL!</font> 
			<br />========================<br />
			<br />			
			<u>MySQL вернул ошибку:</u> 
			<br /><strong>' . $error . '</strong>
			<br /><br />
			<u>Номер ошибки:</u> 
			<br /><strong>' . $error_num . '</strong>
			<br /><br />			
			<textarea name="" style="width: 600px; height: 250px;" wrap="virtual">' . $query . '</textarea><br />
		</body>
		</html>';
			exit(  );
		}
	}


	if (!defined( 'STRESSWEB' )) {
		exit( 'Access denied...' );
	}

?>