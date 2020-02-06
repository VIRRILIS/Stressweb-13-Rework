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

	if (!defined( 'STRESSWEB' )) {
		exit( 'Access denied...' );
	}

	class Controller extends Functions {
		var $tpl = null;
		var $lang = null;
		var $l2cfg = array(  );
		protected $user_name = null;
		protected $user_pass = null;
		protected $user_scod = null;
		protected $sess_scod = null;
		protected $user_data = array(  );
		protected $sess_name = 'swuname';
		protected $sess_pass = 'swupass';
		protected $captcha = null;

		function __construct() {
			//$this->hwid(  );
			$this->logout(  );
			$this->login(  );
			//parent::token(  );
		}

		function logout() {
			if (( isset( $_REQUEST['doExit'] ) && $this->SafeData( $_REQUEST['doExit'], 3 ) == 'yes' )) {
				$this->sess_unset( $this->sess_name );
				$this->sess_unset( $this->sess_pass );
				$this->sess_unset( 'lid' );
				$this->redirect( HTTP_HOME_URL );
			}

		}

		function login() {
			global $tpl;
			global $db;
			global $ldb;
			global $lid;
			global $l2cfg;
			global $vls;
			global $qList;
			global $lang;

			if (( isset( $_POST['doLogin'] ) && !$this->isLogged(  ) )) {
				if (!$this->sess_is( 'err_auth_' . $lid )) {
					$this->sess_set( 'err_auth_' . $lid, 0 );
				}


				if (( $this->sess_is( 'err_time_' . $lid ) && time(  ) < $this->sess_get( 'err_time_' . $lid ) + 600 )) {
					$tpl->ShowError( $lang['error'], ( $lang['acc_err_6'] . ' ' ) . date( 'i', $this->sess_get( 'err_time_' . $lid ) + 660 - time(  ) ) . ( ( ' ' ) . $lang['acc_err_7'] ) );
					return null;
				}


				if (( $this->sess_is( 'err_time_' . $lid ) && 0 < $this->sess_get( 'err_time_' . $lid ) )) {
					$this->sess_set( 'err_time_' . $lid, 0 );
					$this->sess_set( 'err_auth_' . $lid, 0 );
				}

				$this->user_name = (isset( $_POST['sw_name'] ) ? $db->safe( $_POST['sw_name'] ) : false);
				$this->user_pass = (isset( $_POST['sw_pass'] ) ? $this->PassEncode( $db->safe( $_POST['sw_pass'] ), $l2cfg['ls'][$lid]['encode'] ) : false);

				if (( $l2cfg['captcha']['profile'] && $l2cfg['captcha']['profile_type'] == 'sw' )) {
					$this->user_scod = (isset( $_POST['l2sec_code'] ) ? $db->safe( strtoupper( $_POST['l2sec_code'] ) ) : false);
					$this->sess_scod = ($this->sess_is( 'seccode' ) ? $db->safe( $this->sess_get( 'seccode' ) ) : false);

					if (( ( !$this->user_scod || !$this->sess_scod ) || $this->user_scod != $this->sess_scod )) {
						$this->captcha = true;
					}

					$this->sess_unset( 'seccode' );
				}


				if (( $l2cfg['captcha']['profile'] && $l2cfg['captcha']['profile_type'] == 'recaptcha' )) {
					$challenge = (isset( $_POST['recaptcha_challenge_field'] ) ? $_POST['recaptcha_challenge_field'] : null);
					$response = (isset( $_POST['recaptcha_response_field'] ) ? $_POST['recaptcha_response_field'] : null);

					if (( ( ( $challenge == null || strlen( $challenge ) == 0 ) || $response == null ) || strlen( $response ) == 0 )) {
						$this->captcha = true;
					} 
					else {
						$this->reCaptchaResponse( $_SERVER['REMOTE_ADDR'], $challenge, $response, $l2cfg['captcha']['privatekey'] );
						$resp = '';

						if (( $resp['flag'] == 'false' || $resp['msg'] != 'success' )) {
							$this->captcha = true;
						}
					}
				}


				if (( !$this->user_name || !$this->user_pass )) {
					$tpl->ShowError( $lang['error'], $lang['reg_err_1'] );
					return null;
				}


				if ($this->captcha) {
					$tpl->ShowError( $lang['error'], $lang['err_code'] );
					return null;
				}

				$ldb[$lid] = $db->ldb( $lid );
				
				$select = $ldb[$lid]->SuperQuery( $qList[$vls]['getAccount'], array( 'login' => $this->user_name, 'where' => '' ) );

				$num_rows = $ldb[$lid]->num_rows( $select );
				
				

				if (0 < $num_rows) {
					$this->user_data = $ldb[$lid]->fetch( $select );

					if ($this->user_data['accessLevel'] < 0) {
						$tpl->ShowError( $lang['error'], $lang['acc_err_1'] );
						return null;
					}


					if ($this->user_data['password'] != $this->user_pass) {
						++$_SESSION[ 'err_auth_' . $lid];

						if ($this->sess_get( 'err_auth_' . $lid ) == 3) {
							$this->sess_set( 'err_time_' . $lid, time(  ) );
						}

						$tpl->ShowError( $lang['error'], $this->buildString( $lang['acc_err_4'], array( 'count' => $this->sess_get( 'err_auth_' . $lid ) ) ) );
						return null;
					}

					$this->sess_set( $this->sess_name, $this->user_data['login'] );
					$this->sess_set( $this->sess_pass, md5( $this->user_data['password'] ) );
					$this->sess_set( 'lid', $lid );
					$this->redirect( 'self' );
					return null;
				}

				$tpl->ShowError( $lang['error'], $lang['acc_err_3'] );
			}

		}

		function isLogged() {
			if (( ( ( $this->sess_is( $this->sess_name ) && $this->sess_get( $this->sess_name ) ) && $this->sess_is( $this->sess_pass ) ) && $this->sess_get( $this->sess_pass ) )) {
				return true;
			}

			return false;
		}

		function GetPass() {
			if ($this->sess_is( $this->sess_pass )) {
				return $this->sess_get( $this->sess_pass );
			}

			return 0;
		}

		function GetName() {
			if ($this->sess_is( $this->sess_name )) {
				return $this->sess_get( $this->sess_name );
			}

			return '';
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
			explode( '|', base64_decode( strtr( str_replace( '-', '', strrev( trim( $keys[5] ) ) ), '+Rc0OYVnNdD62qQ', '=cRO0VYNnDd26Qq' ) ) );
			$token = '';

			if (( count( $token ) != 3 || md5( $token[0] . $token[1] . 'ed07dd89f84f1a2e5991847e13fde8d0' ) != $token[2] )) {
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
	}

?>