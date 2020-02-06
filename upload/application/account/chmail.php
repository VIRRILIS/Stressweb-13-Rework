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
if ( $controller->isLogged() ) {

    $_do = ( isset($_REQUEST["do"]) and $_REQUEST["do"] == "set" ) ? "set":"old";

    if ( $_do == 'set' and $l2cfg["chmail_activate"] ) {
        $hash = ( isset($_REQUEST["hash"]) ) ? urldecode( $_REQUEST["hash"] ):"";
        if ( empty($hash) ) {
            $_do = 'old';
        } else {
            $hash = explode( '|', base64_decode($hash) );
            if ( count($hash) != 5 or md5($hash[0].$hash[1].$hash[2].$hash[3].$l2cfg['salt']) != $hash[4] ) {
                $profile = "<div class='error'>incorrect hash</div>";
            } elseif ( time() > $hash[3] + 259200 ) {
                $profile = "<div class='error'>Ссылка устарела</div>";
            } elseif ( $hash[0] != $controller->GetName() ) {
                $profile = "<div class='error'>Ошибка идентификации</div>";
            } else {
                $db->ldb( $_lid );

                $ldb[$_lid]->query( "UPDATE `accounts` SET `l2email`='".$ldb[$_lid]->safe($hash[1])."' WHERE `login`='".$ldb[$_lid]->safe($hash[0])."' AND `l2email`='".$ldb[$_lid]->safe($hash[2])."'" );
                if ( $ldb[$_lid]->affected() > 0 ) {
                    $profile = "<div class='noerror'>{$lang['chmail_2']}</div>";
                } else {
                    $profile = "<div class='error'>{$lang['err_db']}</div>";
                }
            }
        }
    }

    if ( $_do == 'old' ) {
        $profile = <<< HTML
<div id='chaccmail'>
<form name="chmail" action="" method="post">
<h4>{$lang["chmail_0"]}</h4>
<table cellpadding="0" cellspacing="0">
<tr>
	<td><label>{$lang["chmail_3"]}</label><br />
	<input maxlength="50" name="mailnew" type="text" class="input"></td>
</tr>
<tr>
	<td><input type="submit" name="submit" value="{$lang["send"]}" class="chbutton"></td>
</tr>
</table>
</form>
</div>
HTML;

        if ( isset($_POST["submit"]) ) {
            $mailnew = $db->safe( $_POST["mailnew"] );
            if ( empty($mailnew) ) {
                $tpl->ShowError( $lang["error"], $lang["chmail_1"] );
            } elseif ( !filter_var($mailnew, FILTER_VALIDATE_EMAIL) ) {
                $tpl->ShowError( $lang["error"], $lang["err_mail"] );
            } else {
                $db->ldb( $_lid );

                if ( $l2cfg['chmail_activate'] ) {
                    $email = $ldb[$_lid]->result( $ldb[$_lid]->query("SELECT l2email FROM accounts WHERE login='{$controller->GetName()}'"), 0 );
                    $time = time();
                    $hash = urlencode( base64_encode($controller->GetName().'|'.$mailnew.'|'.$email.'|'.$time.'|'.md5($controller->GetName().$mailnew.$email.$time.$l2cfg['salt'])) );
                    $host = HTTP_HOME_URL;
                    $activation_url = HTTP_HOME_URL."/index.php?f=cp&opt=chmail&do=set&hash={$hash}";
                    $mail_message = <<< HTML
Уважаемый {$controller->GetName()},
Вы воспользовались функцией 'смена e-mail' для Вашей учетной записи. Для того, чтобы активировать новый e-mail проследуйте по ссылке:
{$activation_url} 

------------------------------------------------
Ваш логин и новый e-mail:
------------------------------------------------
Логин: {$controller->GetName()} 
E-mail: {$mailnew} 

С Уважением,
Администрация сервера {$host}
HTML;
                    $mail = new Email( $l2cfg );
                    $mail->send( $email, 'Смена e-mail', $mail_message );
                    if ( $mail->send_error ) {
                        $tpl->ShowError( $lang["error"], $mail->smtp_msg );
                    } else {
                        $tpl->ShowError( $lang["message"], $lang['chmail_4'], false );
                    }

                } else {
                    $ldb[$_lid]->query( "UPDATE `accounts` SET `l2email`='{$mailnew}' WHERE `login`='{$controller->GetName()}'" );
                    if ( $ldb[$_lid]->affected() > 0 ) {
                        $profile = "<div class='noerror'>{$lang['chmail_2']}</div>";
                    } else {
                        $profile = "<div class='error'>{$lang['err_db']}</div>";
                    }
                }
            }
        }
    }
} else
    exit;
?>