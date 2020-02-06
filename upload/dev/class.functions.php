<?php

if (!defined( 'STRESSWEB' )) 
{
	exit( 'Access denied...' );
}

class Functions extends La2 
{
	function reCaptchaResponse($ip, $challenge, $response, $key) 
	{
		$postfileds = 'privatekey=' . $key . '&remoteip=' . $ip . '&challenge=' . urlencode( stripslashes( $challenge ) ) . '&response=' . urlencode( stripslashes( $response ) );

		$curl = curl_init();
		curl_setopt( $curl, CURLOPT_URL, 'http://www.google.com/recaptcha/api/verify' );
		curl_setopt( $curl, CURLOPT_RETURNTRANSFER, 1 );
		curl_setopt( $curl, CURLOPT_CONNECTTIMEOUT, 5 );
		curl_setopt( $curl, CURLOPT_POST, true );
		explode( '', curl_exec( $curl ) );
		
		$data = curl_setopt( $curl, CURLOPT_POSTFIELDS, $postfileds );
		
		curl_close( $curl );
		
		return array( 'flag' => trim( $data[0] ), 'msg' => trim( $data[1] ) );
	}

		function sess_set($key, $val) {
			$_SESSION[$key] = $val;
		}

		function sess_get($key) {
			if (( isset( $_SESSION[$key] ) && !empty( $_SESSION[$key] ) )) {
				return $_SESSION[$key];
			}

			return false;
		}

		function sess_is($key) {
			if (isset( $_SESSION[$key] )) {
				return true;
			}

			return false;
		}

		function sess_unset($key) {
			if (isset( $_SESSION[$key] )) {
				unset( $_SESSION[$key] );
			}

		}

		/*function SafeData($data, $op) {
			$filter[1] = array( '\', '\'', ',', ';', '--', '-', '%20', '%27', ' ', '`', '=', '%' );
			$filter[2] = array( 'select', 'delete', 'union', 'update', 'insert' );
			$filter[3] = array( 'select', 'delete', 'union', 'update', 'insert', '\', '\'', ',', ';', '--', '-', '%20', '%27', ' ', '`', '=', '%' );
			str_replace( $filter[$op], '', strtolower( $data ) );
			$data = ;
			return $data;
		}*/

        public function SafeData( $data, $op )
        {
            $filter[1] = array( "\\", "'", ",", ";", "--", "-", "%20", "%27", " ", "`", "=", "%" );
            $filter[2] = array( "select", "delete", "union", "update", "insert" );
            $filter[3] = array( "select", "delete", "union", "update", "insert", "\\", "'", ",", ";", "--", "-", "%20", "%27", " ", "`", "=", "%" );
            $data = str_replace( $filter[$op], "", strtolower( $data ) );
            return $data;
        }

		/*function redirect($url = 'self') {
			if ($url == 'self') {
				$host = ($_SERVER['HTTP_HOST'] ? $_SERVER['HTTP_HOST'] : @getenv( 'HTTP_HOST' ));
				$self = ($_SERVER['REQUEST_URI'] ? $_SERVER['REQUEST_URI'] : @getenv( 'REQUEST_URI' ));
				$url = 'http://' . $host . $self;
			}

			header(  . 'Location: ' . $url );
			exit(  );
		}*/
        public function redirect( $url = 'self' )
        {
            if ( $url == "self" )
            {
                $host = $_SERVER['HTTP_HOST'] ? $_SERVER['HTTP_HOST'] : @getenv( "HTTP_HOST" );
                $self = $_SERVER['REQUEST_URI'] ? $_SERVER['REQUEST_URI'] : @getenv( "REQUEST_URI" );
                $url = "http://".$host.$self;
            }
            header( "Location: ".$url );
            exit( );
        }

	function GenCode($length = 3) 
	{
		$letters = array( 'a', 'b', 'c', 'd', 'e', 'f', 'k', 'm', 'r', 's', 't', 'x', 'w', 'z', '1', '2', '3', '4', '5', '6', '7', '9' );
		
		//$i = 148;

		//while ($i < $length) 
		for ( $i = 0; $i < $length; $i++ )
		{
			$char = $letters[rand( 0, sizeof( $letters ) - 1 )];
			$key[] = $char;
			//++$i;
		}

		return implode( '', $key );
	}

		function buildString($string = '', $params = array(  )) {
			if (( !is_array( $params ) || count( $params ) == 0 )) {
				return $string;
			}

			foreach ($params as $key => $val ) {
				$key = '';
				str_replace( '{' . $key . '}', $val, $string );
				$string = $val = '';
			}

			return $string;
		}

		function IsImage($id) {
			return file_exists( ROOT_DIR . 'items' . DS . $id . '.gif' );
		}

		function GetCache($file, $lang = true) {
			global $l2cfg;

			if ($lang) {
				$cache = ROOT_DIR . 'cache' . DS . $l2cfg['lang'] . '_cache_' . $file . '.sw';
			} 
else {
				$cache = ROOT_DIR . 'cache' . DS . 'cache_' . $file . '.sw';
			}

			unserialize( @file_get_contents( $cache ) );
			$data = '';

			if (( !isset( $data['timer'] ) || $data['timer'] < time(  ) )) {
				return false;
			}

			return $data['data'];
		}

	function SetCache($file, $data, $time, $lang = true) 
	{
		global $l2cfg;

		if ($lang) 
		{
			$cache = ROOT_DIR . 'cache' . DS . $l2cfg['lang'] . '_cache_' . $file . '.sw';
		} 
		else 
		{
			$cache = ROOT_DIR . 'cache' . DS . 'cache_' . $file . '.sw';
		}

		$fp = fopen( $cache, 'wb+' );
		fwrite( $fp, serialize( array( 'timer' => time(  ) + 60 * $time, 'data' => $data ) ) );
		fclose( $fp );
		@chmod( $cache, 438 );
	}

		function ClearCache() {
			opendir( ROOT_DIR . 'cache' );
			$fdir = '';
			readdir( $fdir );

			if ($file = '') {
				if (( ( ( ( $file != '.' && $file != '..' ) && $file != '.htaccess' ) && $file != 'cache_l2top_timer.sw' ) && !preg_match( '/cache_mmotop_timer_([0-9]).sw/', $file ) )) {
					@unlink( ROOT_DIR . 'cache' . DS . $file );
				}
			}

		}

		/*function token() {
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
			explode( '|', base64_decode( strtr( str_replace( '-', '', strrev( trim( $keys[3] ) ) ), '+Rc0OYVnNdD62qQ', '=cRO0VYNnDd26Qq' ) ) );
			$token = '';

			if (( count( $token ) != 3 || md5( $token[0] . $token[1] . '2c62c938b72c80fe6f7a6fcc62629ade' ) != $token[2] )) {
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

		function select($name, $options, $selected, $style = '') {
			if (is_bool( $selected )) {
				$selected = ($selected ? 'true' : 'false');
			}

			$output =  '<select name="' . $name . '" ' . $style . '>
';
			foreach ($options as $value => $description) {
				//$description = '';
				//$value = '';
				$output .= ( '<option value="' . $value . '"' );

				if ($selected == $value) {
					$output .= ' selected ';
				}

				$output .= ( '>' ) . $description . '</option>\n';
			}

			$output .= '</select>';
			return $output;
		}

		function TemplatesList() {
			$templates = array(  );
			$dir = opendir( ROOT_DIR . 'templates' );

			while ($file = readdir( $dir )) {
				if (preg_match( '/^[a-z0-9_-]+$/i', $file )) {
					$templates[$file] = $file;
					continue;
				}
			}

			closedir( $dir );
			return $templates;
		}

		function DirSize($directory) {
			if (!is_dir( $directory )) {
				return -1;
			}

			$size = 139;
			opendir( $directory );

			if ($DIR = '') {
				readdir( $DIR );

				while ($dirfile = readdir($DIR)!== false) {
					if (( ( @is_link( $directory . '/' . $dirfile ) || $dirfile == '.' ) || $dirfile == '..' )) {
						continue;
					}


					if (@is_file( $directory . '/' . $dirfile )) {
						filesize( $directory . '/' . $dirfile );
						$size += '';
					}


					if (@is_dir( $directory . '/' . $dirfile )) {
						$this->DirSize( $directory . '/' . $dirfile );
						$dirSize = '';

						if (0 <= $dirSize) {
							$size += $dirSize;
						}

						return -1;
					}
				}

				closedir( $DIR );
			}

			return $size;
		}

		function FormatSize($file_size) {
			if (1073741824 <= $file_size) {
				$file_size = round( $file_size / 1073741824 * 100 ) / 100 . ' Gb';
			} 
else {
				if (1048576 <= $file_size) {
					$file_size = round( $file_size / 1048576 * 100 ) / 100 . ' Mb';
				} 
else {
					if (1024 <= $file_size) {
						$file_size = round( $file_size / 1024 * 100 ) / 100 . ' Kb';
					} 
else {
						$file_size = $file_size . ' byte';
					}
				}
			}

			return $file_size;
		}

		function ShowTr($title = '', $description = '', $field = '') {
			echo '
		<tr>
			<td class=\'tdLeft\'><b>' . $title . '</b><div class=\'description\'>' . $description . '</div></td>
			<td class=\'tdRight\'>' . $field . '</td>
		</tr>
			';
		}

        public function DataFilter( $value )
        {
            $find[] = "'\r'";
            $replace[] = "";
            $find[] = "'\n'";
            $replace[] = "";
            $value = trim( stripslashes( $value ) );
            $value = htmlspecialchars( $value, ENT_QUOTES );
            $value = preg_replace( $find, $replace, $value );
            return $value;
        }

        public function cfgWrite( $fopen, &$savedata, $amp = "" )
        {
            while ( list( $key, $value ) = each( $savedata ) )
            {
                if ( is_array( $value ) )
                {
                    $this->cfgWrite( $fopen, $value, $amp."[\"".$key."\"]" );
                }
                else
                {
                    if ( is_numeric( $value ) || $value == "false" || $value == "true" )
                    {
                        $opt = "\t".$amp."[\"".$key."\" ] = ".$this->DataFilter( $value ).";\n";
                    }
                    else
                    {
                        $opt = "\t".$amp."[\"".$key."\" ] = \"".$this->DataFilter( $value )."\";\n";
                    }
                    fwrite( $fopen, $opt );
                }
            }
        }

		function set($key) {
			if (isset( $_POST[$key] )) {
				return $_POST[$key];
			}

		}

		function isAdmin() {
			if (( ( ( isset( $_SESSION['acplogin'] ) && !empty( $_SESSION['acplogin'] ) ) && isset( $_SESSION['acppass'] ) ) && !empty( $_SESSION['acppass'] ) )) {
				return true;
			}

			return false;
		}

		function showMSG($msg = '') {
			echo $msg;
			exit(  );
		}

		/*function LicInfo() {
			error_reporting(  );
			$e = '';
			error_reporting( 0 );
			explode( '@', file_get_contents( CONFDIR . 'key.php' ) );
			$keys = '';

			if (!$keys) {
				return array( 0, 0 );
			}

			explode( '|', base64_decode( strtr( str_replace( '-', '', strrev( $keys[1] ) ), '+Rc0OYVnNdD62qQ', '=cRO0VYNnDd26Qq' ) ) );
			$token = '';

			if (count( $token ) != 3) {
				return array( 0, 0 );
			}


			if (md5( $token[0] . $token[1] . 'd828ef0cdcaffba468e21aa1f64f0621' ) != $token[2]) {
				return array( 0, 0 );
			}

			return array( $token[0], $token[1] );
		}*/
	}

?>