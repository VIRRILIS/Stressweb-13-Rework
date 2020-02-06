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

	class IPtable {
		function Instance() {
			global $l2cfg;
			global $iptable;

			if ($l2cfg['iptable']) {
				if (!empty( $$iptable )) {
					exit(  );
				}


				if (is_array( $iptable )) {
					if (!in_array( $_SERVER['REMOTE_ADDR'], $iptable )) {
						self::log(  );
						return null;
					}
				} 
else {
					explode( '.', $iptable );
					$iptabletmp = '';

					if (( $iptabletmp[2] == '*' && $iptabletmp[3] == '*' )) {
						if (strrev( substr( strrev( $_SERVER['REMOTE_ADDR'] ), strpos( strrev( $_SERVER['REMOTE_ADDR'] ), '.', strpos( strrev( $_SERVER['REMOTE_ADDR'] ), '.' ) + 1 ) ) ) . '*.*' != $iptable) {
							self::log(  );
							return null;
						}
					} 
else {
						if ($iptabletmp[3] == '*') {
							if (strrev( substr( strrev( $_SERVER['REMOTE_ADDR'] ), strpos( strrev( $_SERVER['REMOTE_ADDR'] ), '.' ) ) ) . '*' != $iptable) {
								self::log(  );
								return null;
							}
						} 
else {
							if ($iptable != $_SERVER['REMOTE_ADDR']) {
								self::log(  );
							}
						}
					}
				}
			}

		}

		function log() {
			fopen( ROOT_DIR . 'log_auth.php', 'a+' );
			$fopen = '';

			if ($fopen) {
				fwrite( $fopen, '
[' . date( 'd.m.y H:i:s', time(  ) ) . '] Попытка доступа в АдминПанель с IP:' . $_SERVER['REMOTE_ADDR'] );
				fclose( $fopen );
			}

			exit( 'remote IP is not in list of permitted adresses' );
		}
	}


	if (!defined( 'STRESSWEB' )) {
		exit( 'Access denied...' );
	}

?>