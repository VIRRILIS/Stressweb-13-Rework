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
$_do = ( isset($_REQUEST["do"]) and $_REQUEST["do"] == "newpass" ) ? "newpass":"forget";

if ( $_do == "newpass" and $l2cfg["forget_activate"] ) {
    $hash = ( isset($_REQUEST["hash"]) ) ? urldecode( $_REQUEST["hash"] ):"";
    if ( empty($hash) ) {
        $_do = 'forget';
    } else {
        $hash = explode( '|', base64_decode($hash) );
        if ( count($hash) != 6 or md5($hash[0].$hash[1].$hash[2].$hash[3].$hash[4].$l2cfg['salt']) != $hash[5] ) {
            $tpl->SetResult( 'content', "<div class='error'>incorrect hash</div>" );
        } elseif ( time() > $hash[3] + 259200 ) {
            $tpl->SetResult( 'content', "<div class='error'>{$lang["lost_err_2"]}</div>" );
        } elseif ( $hash[4] != $sid ) {
            $tpl->SetResult( 'content', "<div class='error'>incorrect server id</div>" );
        } else {
            $db->ldb( $lid );

            $ldb[$lid]->query( "UPDATE `accounts` SET `password`='".$ldb[$lid]->safe($controller->PassEncode($hash[1], $l2cfg["ls"][$lid]["encode"]))."' WHERE `login`='".$ldb[$lid]->safe($hash[0])."' AND `l2email`='".$ldb[$lid]->safe($hash[2])."'" );
            if ( $ldb[$lid]->affected() > 0 ) {
                $tpl->SetResult( 'content', "<div class='noerror'>{$lang["lost_err_8"]}</div>" );
            } else {
                $tpl->SetResult( 'content', "<div class='error'>{$lang["err_db"]}</div>" );
            }
        }
    }
}

if ( $_do == "forget" ) {
    if ( $controller->isLogged() ) {
        $tpl->SetResult( 'content', "<div class='error'>{$lang["lost_err_4"]}</div>" );
    } else {

        $tpl->LoadView( "lostpassword" );
        if ( count($lsList) > 1 ) {
            $l2servers = "";
            foreach ( $gsList as $key ) {
                $l2servers .= "<option value='{$key}'>{$l2cfg["gs"][$key]["title"]}</option>";
            }
            $tpl->Block( 'servers' );
            $tpl->Set( "servers", $l2servers );
        } else
            $tpl->Block( 'servers', false );

        if ( $l2cfg['captcha']['repass'] and $l2cfg['captcha']['repass_type'] == 'sw' ) {
            $tpl->template = '<script type="text/javascript">//<![CDATA[
function reload () {
	var rndval = new Date().getTime(); 
	document.getElementById(\'captcha-repass\').innerHTML = \'<a onclick="reload(); return false;" href="#"><img src="'.HTTP_HOME_URL.'/module/antibot.php?rndval=\' + rndval + \'" border="0"></a>\';
};
//]]></script>'.$tpl->template;
            $tpl->Block( 'captcha' );
            $tpl->Set( "captcha", "<div id=\"captcha-repass\" class='captcha'><a onclick=\"reload(); return false;\" href=\"#\"><img src=\"".HTTP_HOME_URL."/module/antibot.php\" alt=\"code\" border=\"0\" /></a></div>" );
        } else
            $tpl->Block( 'captcha', false );
        if ( $l2cfg['captcha']['repass'] and $l2cfg['captcha']['repass_type'] == 'recaptcha' ) {
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
        $tpl->Build( 'content' );

        if ( isset($_POST['repass']) ) {

            $captcha = null;
            if ( $l2cfg["captcha"]["repass"] and $l2cfg['captcha']['repass_type'] == 'sw' ) {
                $code_post = strtoupper( $db->safe($_POST["seccode"]) );
                $code_sess = $controller->sess_get( 'seccode' );
                $controller->sess_unset( 'seccode' );
                if ( !$code_sess or $code_post != $code_sess )
                    $captcha = true;
            }
            if ( $l2cfg["captcha"]["repass"] and $l2cfg['captcha']['repass_type'] == 'recaptcha' ) {
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

            $login = $db->safe( trim($_POST["login"]) );
            $email = $db->safe( $_POST["email"] );

            if ( empty($login) or empty($email) )
                $tpl->ShowError( $lang["error"], $lang["reg_err_1"] );
            elseif ( $captcha )
                $tpl->ShowError( $lang["error"], $lang["err_code"] );
            elseif ( !filter_var($email, FILTER_VALIDATE_EMAIL) )
                $tpl->ShowError( $lang["error"], $lang["err_mail"] );
            else {
                if ( !isset($_SESSION["rep_err_auth_{$lid}"]) )
                    $_SESSION["rep_err_auth_{$lid}"] = 0;
                if ( isset($_SESSION["rep_err_time_{$lid}"]) and ($_SESSION["rep_err_time_{$lid}"] + 600) > time() )
                    $tpl->ShowError( $lang["error"], $controller->buildString($lang["lost_err_9"], array('minutes' => date("i", ($_SESSION["rep_err_time_{$lid}"] + 660 - time())))) );
                else {
                    if ( isset($_SESSION["rep_err_time_{$lid}"]) and $_SESSION["rep_err_time_{$lid}"] > 0 ) {
                        $_SESSION["rep_err_time_{$lid}"] = 0;
                        $_SESSION["rep_err_auth_{$lid}"] = 0;
                    }

                    $db->ldb( $lid );

                    if ( $ldb[$lid]->num_rows($ldb[$lid]->query("SELECT login FROM accounts WHERE login='{$login}' AND l2email='{$email}' LIMIT 1")) == 0 ) {
                        $_SESSION["rep_err_auth_{$lid}"]++;
                        if ( $_SESSION["rep_err_auth_{$lid}"] == 3 )
                            $_SESSION["rep_err_time_{$lid}"] = time();
                        $tpl->ShowError( $lang["error"], $controller->buildString($lang["lost_err_1"].'<br />'.$lang["lost_err_10"], array('count' => $_SESSION["rep_err_auth_{$lid}"])) );
                    } else {

                        $new_pass = $controller->GenCode( rand(6, 10) );

                        if ( !$l2cfg["forget_activate"] ) {

                            $ldb[$lid]->query( "UPDATE accounts SET password='".$controller->PassEncode($new_pass, $l2cfg['ls'][$lid]['encode'])."' WHERE login='{$login}' AND l2email='{$email}'" );
                            if ( $ldb[$lid]->affected() == 1 )
                                $tpl->SetResult( 'content', "<div class='noerror'>{$lang["lost_err_3"]}<br /><b>{$lang["login"]}</b> {$login}<br /><b>{$lang["pass"]}</b> {$new_pass}</div>", true );
                            else {
                                $tpl->SetResult( 'content', "<div class='error'>{$lang["err_db"]}</div>", true );
                            }

                        } else {
                            $time = time();
                            $hash = urlencode( base64_encode($login.'|'.$new_pass.'|'.$email.'|'.$time.'|'.$sid.'|'.md5($login.$new_pass.$email.$time.$sid.$l2cfg['salt'])) );
                            $tpl->LoadView( "email_lostpassword" );
                            $tpl->Set( "host", HTTP_HOME_URL );
                            $tpl->Set( "login", $login );
                            $tpl->Set( "password", $new_pass );
                            $tpl->Set( "link", HTTP_HOME_URL."/index.php?f=forget&do=newpass&sid={$sid}&hash={$hash}" );
                            $tpl->Build( "forget" );
                            $mail_message = $tpl->GetResult( "forget" );

                            $mail = new Email( $l2cfg );
                            $mail->send( $email, $lang["lost_err_0"], $mail_message );
                            if ( $mail->send_error ) {
                                $tpl->SetResult( 'content', $mail->smtp_msg, true );
                            } else {
                                $tpl->SetResult( 'content', "<div class='noerror'>{$lang["lost_err_7"]}</div>", true );
                            }
                        }
                    }
                }
            }
        }

    }
}
?>