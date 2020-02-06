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

    if ( $_do == 'set' and $l2cfg["chpass_activate"] ) {
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

                $pass = $controller->PassEncode( $hash[1], $l2cfg['ls'][$_lid]['encode'] );
                $ldb[$_lid]->query( "UPDATE accounts SET password='".$ldb[$_lid]->safe($pass)."' WHERE `login`='".$ldb[$_lid]->safe($hash[0])."' AND `l2email`='".$ldb[$_lid]->safe($hash[2])."'" );

                if ( $ldb[$_lid]->affected() > 0 ) {
                    $_SESSION["swupass"] = md5( $pass );
                    $profile = "<div class='noerror'>Новый пароль установлен</div>";
                } else {
                    $profile = "<div class='error'>Ошибка базы данных!</div>";
                }
            }
        }
    }
    if ( $_do == "old" ) {
        $profile = <<< HTML
<div id='chaccpass'>
<form name="chpass" action="" method="post">
<h4>Смена пароля</h4>
<table cellpadding="0" cellspacing="0">
<tr>
	<td><label>{$lang["chpass_1"]}:</label><br />
	<input maxlength="16" name="l2oldpass" type="password" class="input"></td>
</tr>
<tr>
	<td><label>{$lang["chpass_2"]}:</label><br />
	<input maxlength="16" name="l2newpass1" type="password" class="input"></td>
</tr>
<tr>
	<td><label>{$lang["chpass_3"]}:</label><br>
	<input maxlength="16" name="l2newpass2" type="password" class="input"></td>
</tr>
<tr>
	<td><input type="submit" name="submit" value="{$lang["send"]}" class="chbutton"></td>
</tr>
</table>
</form>
</div>
HTML;

        if ( isset($_POST["submit"]) ) {
            $db->ldb( $_lid );

            $_l2old_pass = $ldb[$_lid]->safe( $_POST["l2oldpass"] );
            $_l2new_pass1 = $ldb[$_lid]->safe( $_POST["l2newpass1"] );
            $_l2new_pass2 = $ldb[$_lid]->safe( $_POST["l2newpass2"] );
            if ( empty($_l2old_pass) or empty($_l2new_pass1) or empty($_l2new_pass2) ) {
                $tpl->ShowError( $lang["error"], $lang["chpass_4"] );
            } elseif ( md5($controller->PassEncode($_l2old_pass, $l2cfg["ls"][$_lid]["encode"])) != $controller->GetPass() ) {
                $tpl->ShowError( $lang["error"], $lang["chpass_5"] );
            } elseif ( md5($controller->PassEncode($_l2new_pass1, $l2cfg["ls"][$_lid]["encode"])) == $controller->GetPass() ) {
                $tpl->ShowError( $lang["error"], $lang["chpass_6"] );
            } elseif ( strlen($_l2new_pass1) < 6 or strlen($_l2new_pass1) > 16 ) {
                $tpl->ShowError( $lang["error"], $lang["chpass_7"] );
            } elseif ( $_l2new_pass1 != $_l2new_pass2 ) {
                $tpl->ShowError( $lang["error"], $lang["chpass_8"] );
            } else {
                if ( $l2cfg['chpass_activate'] ) {
                    $email = $ldb[$_lid]->result( $ldb[$_lid]->query("SELECT l2email FROM accounts WHERE login='{$controller->GetName()}'"), 0 );
                    $time = time();
                    $hash = urlencode( base64_encode($controller->GetName().'|'.$_l2new_pass1.'|'.$email.'|'.$time.'|'.md5($controller->GetName().$_l2new_pass1.$email.$time.$l2cfg['salt'])) );
                    $host = HTTP_HOME_URL;
                    $activation_url = HTTP_HOME_URL."/index.php?f=cp&opt=chpass&do=set&hash={$hash}";
                    $mail_message = <<< HTML
Уважаемый {$controller->GetName()},
Вы воспользовались функцией 'смена пароля' для Вашей учетной записи. Для того, чтобы активировать новый пароль проследуйте по ссылке:
{$activation_url} 

------------------------------------------------
Ваш логин и новый пароль на сайте:
------------------------------------------------
Логин: {$controller->GetName()} 
Пароль: {$_l2new_pass1} 

С Уважением,
Администрация сервера {$host}
HTML;
                    $mail = new Email( $l2cfg );
                    $mail->send( $email, 'Смена пароля', $mail_message );
                    if ( $mail->send_error ) {
                        $tpl->ShowError( $lang["error"], $mail->smtp_msg );
                    } else {
                        $tpl->ShowError( $lang["message"], 'На Ваш E-Mail отправлено письмо с инструкциями по активации нового пароля.', false );
                    }

                } else {
                    $ldb[$_lid]->SuperQuery( $qList[$_vls]["setPassword"], array("pass" => $controller->PassEncode($_l2new_pass1, $l2cfg["ls"][$_lid]["encode"]), "login" => $controller->GetName()) );
                    if ( $ldb[$_lid]->affected() > 0 ) {
                        $_SESSION["swupass"] = md5( $controller->PassEncode($_l2new_pass1, $l2cfg["ls"][$_lid]["encode"]) );
                        $tpl->ShowError( "", $lang["chpass_9"], false );
                    } else {
                        $tpl->ShowError( $lang["error"], $lang["err_db"] );
                    }
                }
            }
        }
    }
} else
    exit;
?>