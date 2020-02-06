<?php
/**
 * STRESS WEB
 * @author S.T.R.E.S.S.
 * @copyright 2008 - 2012 STRESS WEB
 * @version 13
 * @web http://stressweb.ru
 */
if ( !defined("STRESSWEB") )
    die( "Access denied..." );

if ( !$l2cfg["reg_enable"] ) {
    $tpl->SetResult( "content", "<div class='error'>{$lang["reg_err_0"]}</div>" );
} else {
    $_do = ( isset($_REQUEST["do"]) and $_REQUEST["do"] == "activate" and $l2cfg["reg_activate"] ) ? "activate":"reg";

    /**************************
    * Account Activation
    **************************/
    if ( $_do == "activate" and $l2cfg["reg_activate"] ) {
        $hash = ( isset($_REQUEST["hash"]) ) ? urldecode( $_REQUEST["hash"] ):"";
        if ( empty($hash) ) {
            $_do = 'reg';
        } else {
            $hash = explode( '|', base64_decode($hash) );
            if ( count($hash) != 6 or md5($hash[0].$hash[1].$hash[2].$hash[3].$hash[4].$l2cfg['salt']) != $hash[5] ) {
                $tpl->SetResult( 'content', "<div class='error'>incorrect hash</div>" );
            } elseif ( $sid != $hash[3] ) {
                $tpl->SetResult( 'content', "<div class='error'>incorrect server id</div>" );
            } else {
                $db->ldb( $lid );

                if ( $ldb[$lid]->num_rows($ldb[$lid]->query("SELECT login FROM accounts WHERE login='".$ldb[$lid]->safe($hash[0])."' LIMIT 1")) ) {
                    $tpl->SetResult( "content", "<div class='error'>Аккаунт уже существует</div>" );
                } else {
                    $ldb[$lid]->SuperQuery( $qList[$vls]["insAccount"], array("login" => $hash[0], "pass" => $controller->PassEncode($hash[1], $l2cfg["ls"][$lid]["encode"]), "l2email" => $hash[2]) );
                    if ( $ldb[$lid]->affected() > 0 ) {
                        $tpl->SetResult( "content", "<div class='noerror'>{$lang["validate_err_1"]}</div>" );
                        $db->gdb( $sid );
                        $ref_query = $gdb[$sid]->query( "SELECT `account_name`,`{$qList[$vList[$l2cfg["gs"][$sid]["version"]]]["fields"]["charID"]}` AS charID FROM `characters` WHERE `char_name`='{$hash[4]}'" );
                        if ( $gdb[$sid]->num_rows($ref_query) > 0 ) {
                            $ref_data = $gdb[$sid]->fetch( $ref_query );
                            $gdb[$sid]->query( "INSERT INTO `stress_referal` SET `account_referer`='".$ldb[$lid]->safe($hash[0])."',`account_name`='{$ref_data['account_name']}',`charId`='{$ref_data['charID']}',`char_name`='{$hash[4]}'" );
                            if ( $gdb[$sid]->affected() > 0 ) {
                                $tpl->ShowError( $lang["message"], "Вы участвуете в программе 'Пригласи друга'", false );
                            } else {
                                $tpl->ShowError( $lang["message"], "'Пригласи друга': ошибка базы данных" );
                            }
                        }
                    } else {
                        $tpl->SetResult( "content", "<div class='error'>{$lang["err_db"]}</div>" );
                    }
                }
            }
        }
    }

    /**************************
    * Account Registration
    **************************/
    if ( $_do == "reg" ) {

        if ( isset($_POST["register"]) ) {
            $captcha = null;
            if ( $l2cfg["captcha"]["reg"] and $l2cfg['captcha']['reg_type'] == 'sw' ) {
                $_l2code_post = strtoupper( $db->safe($_POST["l2sec_code"]) );
                $_l2code_sess = $controller->sess_get( 'seccode' );
                $controller->sess_unset( 'seccode' );
                if ( !$_l2code_sess or $_l2code_post != $_l2code_sess ) {
                    $captcha = true;
                }
            }
            if ( $l2cfg["captcha"]["reg"] and $l2cfg['captcha']['reg_type'] == 'recaptcha' ) {
                $challenge = ( isset($_POST['recaptcha_challenge_field']) ) ? $_POST['recaptcha_challenge_field']:null;
                $response = ( isset($_POST['recaptcha_response_field']) ) ? $_POST['recaptcha_response_field']:null;
                if ( $challenge == null or strlen($challenge) == 0 or $response == null or strlen($response) == 0 ) {
                    $captcha = true;
                } else {
                    $resp = $controller->reCaptchaResponse( $_SERVER['REMOTE_ADDR'], $challenge, $response, $l2cfg['captcha']['privatekey'] );
                    if ( $resp['flag'] == 'false' or $resp['msg'] != 'success' ) {
                        $captcha = true;
                    }
                }
            }

            $_l2friend = ( isset($_POST['l2friend']) ) ? $db->safe( htmlspecialchars(strip_tags(trim($_POST['l2friend']))) ):false;
            $_l2login = $db->safe( htmlspecialchars(strip_tags(trim($_POST["l2account"]))) );
            if ( isset($_POST['l2prefix']) )
                $_l2login = $db->safe( $_POST['l2prefix'] ).$_l2login;
            $_l2pass1 = $db->safe( $_POST["l2password1"] );
            $_l2pass2 = $db->safe( $_POST["l2password2"] );
            $_l2email = $db->safe( $_POST["l2email"] );
            if ( empty($_l2login) or empty($_l2pass1) or empty($_l2pass2) or empty($_l2email) ) {
                $tpl->ShowError( $lang["error"], $lang["reg_err_1"] );
            } elseif ( $captcha ) {
                $tpl->ShowError( $lang["error"], $lang["err_code"] );
            } elseif ( $_l2pass1 != $_l2pass2 ) {
                $tpl->ShowError( $lang["error"], $lang["reg_err_2"] );
            } elseif ( strlen($_l2login) < 4 or strlen($_l2login) > 14 ) {
                $tpl->ShowError( $lang["error"], $lang["reg_err_3_1"] );
            } elseif ( strlen($_l2pass1) < 6 or strlen($_l2pass1) > 16 ) {
                $tpl->ShowError( $lang["error"], $lang["reg_err_3_2"] );
            } elseif ( preg_match("/[\||\'|\<|\>|\[|\]|\"|\!|\?|\$|\@|\/|\\\|\&\~\*\{\+]/", $_l2login) ) {
                $tpl->ShowError( $lang["error"], $lang["reg_err_4"] );
            } elseif ( !filter_var($_l2email, FILTER_VALIDATE_EMAIL) ) {
                $tpl->ShowError( $lang["error"], $lang["err_mail"] );
            } else {

                $db->ldb( $lid );

                if ( $ldb[$lid]->num_rows($ldb[$lid]->query("SELECT * FROM accounts WHERE login='{$_l2login}' LIMIT 1")) ) {
                    $tpl->ShowError( $lang["error"], $lang["reg_err_6"] );
                } elseif ( !$l2cfg["reg_multi"] and $ldb[$lid]->num_rows($ldb[$lid]->query("SELECT * FROM accounts WHERE l2email='{$_l2email}' LIMIT 1")) ) {
                    $tpl->ShowError( $lang["error"], $lang["reg_err_7"] );
                } else {
                    $_login = strtolower( $_l2login );
                    $_pass = $controller->PassEncode( $ldb[$lid]->safe($_l2pass1), $l2cfg["ls"][$lid]["encode"] );
                    if ( !$l2cfg["reg_activate"] ) {
                        $ldb[$lid]->SuperQuery( $qList[$vls]["insAccount"], array("login" => $_login, "pass" => $_pass, "l2email" => $_l2email) );
                        if ( $ldb[$lid]->affected() == 1 ) {
                            $tpl->ShowError( $lang["message"], $lang["reg_err_8"], false );

                            $tpl->LoadView( "email_register_ok" );
                            $tpl->Set( 'login', $_login );
                            $tpl->Set( 'password', $_l2pass1 );
                            $tpl->Set( 'host', HTTP_HOME_URL );
                            $tpl->Build( "mail_message" );
                            $mail_message = $tpl->GetResult( "mail_message" );

                            $mail = new Email( $l2cfg );
                            $mail->send( $_l2email, 'Регистрация аккаунта', $mail_message );

                            $db->gdb( $sid );
                            $ref_query = $gdb[$sid]->query( "SELECT `account_name`,`{$qList[$vList[$l2cfg["gs"][$sid]["version"]]]["fields"]["charID"]}` AS charID FROM `characters` WHERE `char_name`='{$_l2friend}'" );
                            if ( $gdb[$sid]->num_rows($ref_query) > 0 ) {
                                $ref_data = $gdb[$sid]->fetch( $ref_query );
                                $gdb[$sid]->query( "INSERT INTO `stress_referal` SET `account_referer`='{$_l2login}',`account_name`='{$ref_data['account_name']}',`charId`='{$ref_data['charID']}',`char_name`='{$_l2friend}'" );
                                if ( $gdb[$sid]->affected() > 0 ) {
                                    $tpl->ShowError( $lang["message"], "Вы участвуете в программе 'Пригласи друга'", false );
                                } else {
                                    $tpl->ShowError( $lang["message"], "'Пригласи друга': ошибка базы данных" );
                                }
                            }
                        } else
                            $tpl->ShowError( $lang["error"], $lang["err_db"] );
                    } else {

                        $hash = urlencode( base64_encode($_login.'|'.$_l2pass1.'|'.$_l2email.'|'.$sid.'|'.$_l2friend.'|'.md5($_login.$_l2pass1.$_l2email.$sid.$_l2friend.$l2cfg['salt'])) );
                        $tpl->LoadView( "email_register" );
                        $tpl->Set( 'login', $_login );
                        $tpl->Set( 'password', $_l2pass1 );
                        $tpl->Set( 'host', HTTP_HOME_URL );
                        $tpl->Set( 'link', HTTP_HOME_URL."/index.php?f=register&do=activate&sid={$sid}&hash={$hash}" );
                        $tpl->Build( "mail_message" );
                        $mail_message = $tpl->GetResult( "mail_message" );

                        $mail = new Email( $l2cfg );
                        $mail->send( $_l2email, $lang["reg_err_11"], $mail_message );
                        if ( $mail->send_error ) {
                            $tpl->ShowError( $lang["error"], $mail->smtp_msg );
                        } else {
                            $tpl->ShowError( $lang["message"], $lang["reg_err_9"], false );
                        }
                    }
                }
            }
        }
        $tpl->LoadView( "register" );
        if ( $l2cfg["captcha"]["reg"] and $l2cfg['captcha']['reg_type'] == 'sw' ) {
            $tpl->template = '<script type="text/javascript">//<![CDATA[
function reload () {
	var rndval = new Date().getTime(); 
	document.getElementById(\'sw-captcha\').innerHTML = \'<a onclick="reload(); return false;" href="#"><img src="'.HTTP_HOME_URL.'/module/antibot.php?rndval=\' + rndval + \'" border="0"></a>\';
};
//]]></script>'.$tpl->template;
            $tpl->Block( 'captcha' );
            $tpl->Set( 'l2sec_code', "<div id=\"sw-captcha\" class='captcha'><a onclick=\"reload(); return false;\" href=\"#\"><img src=\"".HTTP_HOME_URL."/module/antibot.php\" alt=\"Код безопасности\" border=\"0\" /></a></div>" );
        } else
            $tpl->Block( 'captcha', false );
        if ( $l2cfg['captcha']['reg'] and $l2cfg['captcha']['reg_type'] == 'recaptcha' ) {
            $tpl->Set( 'code', '
            <script type="text/javascript">
 				var RecaptchaOptions = {
    				theme : \'white\'
 				};
 			</script>
			<script type="text/javascript"
		       src="http://www.google.com/recaptcha/api/challenge?k='.$l2cfg['captcha']['publickey'].'">
		    </script>
		    <noscript>
		       <iframe src="http://www.google.com/recaptcha/api/noscript?k='.$l2cfg['captcha']['publickey'].'"
		           height="300" width="500" frameborder="0"></iframe><br>
		       <textarea name="recaptcha_challenge_field" rows="3" cols="40">
		       </textarea>
		       <input type="hidden" name="recaptcha_response_field"
		           value="manual_challenge">
		    </noscript>' );
            $tpl->Block( 'recaptcha' );
        } else
            $tpl->Block( 'recaptcha', false );

        if ( count($gsList) > 1 ) {
            $l2servers = "";
            foreach ( $gsList as $key ) {
                $l2servers .= "<option value='{$key}'>{$l2cfg["gs"][$key]["title"]}</option>";
            }
            $tpl->Block( 'servers' );
            $tpl->Set( "servers", $l2servers );
        } else
            $tpl->Block( 'servers', false );
        if ( $l2cfg['reg_prefix'] ) {
            $prefix_option = '';
            for ( $i = 0; $i < 5; $i++ ) {
                $prefix = strtolower( $controller->GenCode(2) );
                $prefix_option .= "<option value='{$prefix}'>{$prefix}</option>";
            }
            $tpl->Block( 'prefix' );
            $tpl->Set( 'prefix', $prefix_option );
        } else
            $tpl->Block( 'prefix', false );
        $ref = 0;
        foreach ( $gsList as $i ) {
            if ( $l2cfg["gs"][$i]["referal_enable"] ) {
                $ref++;
            }
        }
        if ( $ref ) {
            $tpl->Block( 'referal' );
        } else {
            $tpl->Block( 'referal', false );
        }
        $tpl->Build( "content" );
    }

}
?>