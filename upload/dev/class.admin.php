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

	class Admin extends Functions {
		protected $adm_name = null;
		protected $adm_pass = null;
		protected $adm_nick = null;
		protected $adm_scod = null;
		protected $ses_scod = null;
		protected $message = '';
		protected $adm_data = array(  );
		protected $captcha = null;

		function __construct() {
			$this->logout(  );
			$this->login(  );
			//parent::token(  );
		}

		function logout() {
			global $l2cfg;

			if (( isset( $_REQUEST['exit'] ) && $this->SafeData( $_REQUEST['exit'], 3 ) == 'yes' )) {
				unset( $_SESSION[acplogin] );
				unset( $_SESSION[acppass] );
				unset( $_SESSION[acpnick] );
				$this->redirect( ADMFILE );
			}

		}

		function login() {
			global $db;
			global $l2cfg;

			
			
			if (isset( $_POST['doLogin'] )) {
				$this->adm_name = (isset( $_POST['acp_login'] ) ? $db->safe( $_POST['acp_login'] ) : false);
				$this->adm_pass = (isset( $_POST['acp_pass'] ) ? $this->PassEncode( $_POST['acp_pass'] ) : false);
				$this->adm_scod = (isset( $_POST['sec_code'] ) ? strtoupper( $db->safe( $_POST['sec_code'] ) ) : false);
				$this->ses_scod = (isset( $_SESSION['seccode'] ) ? $db->safe( $_SESSION['seccode'] ) : false);
				
				//echo '<pre>debug:<br>';
				//print_r( $l2cfg['captcha']['admin_type'] );
				//echo '</pre>';
				
				if ($l2cfg['captcha']['admin_type'] == 'sw') {
					$this->adm_scod = (isset( $_POST['sec_code'] ) ? strtoupper( $db->safe( $_POST['sec_code'] ) ) : false);
					$this->ses_scod = (isset( $_SESSION['seccode'] ) ? $db->safe( $_SESSION['seccode'] ) : false);

					if ( !$this->adm_scod || !$this->ses_scod || $this->adm_scod != $this->ses_scod ) {
						$this->captcha = true;
					}

					unset( $_SESSION[seccode] );
				}


				if ($l2cfg['captcha']['admin_type'] == 'recaptcha') {
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


				if (( !$this->adm_name || !$this->adm_pass )) {
					$this->redirect( ADMFILE . '?err=Error' );
					return null;
				}


				if ($this->captcha) {
					$this->redirect( ADMFILE . '?err=Captcha' );
					return null;
				}

				/*print_r( array(
					base64_encode( pack('H*', sha1(utf8_encode('123456'))) ),
					base64_encode( pack('H*', sha1(utf8_encode(@mysqli_escape_string('123456')))))
				));*/
				
				$sel = $db->query( 'SELECT * FROM stress_admin WHERE login=\'' . $this->adm_name . '\'' );

				if ( $db->num_rows( $sel ) > 0 ) {
					$this->adm_data = $db->fetch( $sel );

					print_r( array( $this->adm_data['password'], $this->adm_pass ) );
					
					if ($this->adm_data['password'] == $this->adm_pass) {
						$_SESSION['acplogin'] = $this->adm_data['login'];
						$_SESSION['acppass'] = md5( $this->adm_data['password'] );
						$_SESSION['acpnick'] = $this->adm_data['nick'];
						$this->redirect( ADMFILE );
						return null;
					}

					//$this->redirect( ADMFILE . '?err=passError' );
					return null;
				}

				//$this->redirect( ADMFILE . '?err=loginError' );
			}

		}
	}

?>