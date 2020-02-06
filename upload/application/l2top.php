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

$_act = ( isset($_POST["act"]) ) ? $controller->SafeData( $_POST["act"], 3 ):"";

function getVotes()
{
    global $controller, $l2cfg, $db;
    //проверяем таймер l2top
    if ( $controller->GetCache("l2top_timer", false) < time() ) 
	{
        //получаем голоса с л2топа
        $lines = file_get_contents( htmlspecialchars_decode($l2cfg["l2top"]["url"]) );
        if ( $lines ) {
            $lines = explode( "\n", $lines );
            //ставим таймер
            $cache_time = time() + 60 * 5.1;
            $controller->SetCache( "l2top_timer", $cache_time, 5.1, false );
            //заносим голоса в БД если таких еще нету
            for ( $i = 1; $i < count($lines) - 1; $i++ ) {
                $date = substr( $lines[$i], 0, 19 );
                $pchar = explode( "-", iconv("WINDOWS-1251", "UTF-8", strtolower(rtrim(substr($lines[$i], 20)))) );
                if ( count($pchar) == 2 ) {
                    $prefix = $pchar[0];
                    $char = $pchar[1];
                } else {
                    $prefix = "";
                    $char = $pchar[0];
                }
                $sel = $db->query( "SELECT * FROM `stress_l2top_bonus` WHERE `char`='{$char}' AND `date`='{$date}' AND `prefix`='{$prefix}'" );
                if ( $db->num_rows($sel) == 0 )
                    $db->query( "INSERT INTO `stress_l2top_bonus` SET `char` = '{$char}', `date`='{$date}', `prefix`='{$prefix}', `give`='0'" );
            }
        }
    }
}
$l2topServerList = array();
$l2top_check = false;

foreach ( $gsList as $i ) 
{
    if ( $l2cfg["gs"][$i]["l2top"]["enable"] ) {
        $l2top_check = true;
        $l2topServerList[$i] = $l2cfg["gs"][$i]["title"];
    }
}

if ( !$l2cfg["l2top"]["enable"] or !$l2top_check ) 
{
    $tpl->SetResult( "content", "<div class='error'>{$lang["l2top_err_9"]}</div>" );
} else {
    $L2TOP_ERR = "";
    $tpl->LoadView( "l2top" );
    $tpl->Set( 'voteid', $l2cfg["l2top"]["id"] );
    if ( $_act == "" ) {
        $tpl->Block( 'vote' );
        $tpl->Block( 'error', false );
        $tpl->Set( 'servers', $controller->select("sid", $l2topServerList, $sid, "style='width: 125px;'") );
        if ( $l2cfg["captcha"]["l2top"] and $l2cfg['captcha']['l2top_type'] == 'sw' ) {
            $tpl->Block( 'captcha' );
            $tpl->Set( "l2sec_code", "<div id=\"sw-captcha\" class='captcha'><a onclick=\"reload(); return false;\" href=\"#\"><img src=\"".HTTP_HOME_URL."/module/antibot.php\" alt=\"code\" border=\"0\" /></a></div>" );
        } else
            $tpl->Block( 'captcha', false );
        if ( $l2cfg['captcha']['l2top'] and $l2cfg['captcha']['l2top_type'] == 'recaptcha' ) {
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
    }
    /**************************
    * Vote
    **************************/
    if ( $_act == "get" ) {
        getVotes();
        $char_name = $db->safe( $_POST["char_name"] );
        $captcha = null;
        if ( $l2cfg["captcha"]["l2top"] and $l2cfg['captcha']['l2top_type'] == 'sw' ) {
            $sCode_post = strtoupper( $db->safe($_POST["l2sec_code"]) );
            $sCode_sess = $controller->sess_get( "seccode" );
            $controller->sess_unset( 'seccode' );
            if ( !$sCode_sess or $sCode_post != $sCode_sess )
                $captcha = true;
        }
        if ( $l2cfg["captcha"]["l2top"] and $l2cfg['captcha']['l2top_type'] == 'recaptcha' ) {
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


        if ( $captcha ) {
            $L2TOP_ERR = "<div class='error'>{$lang["err_code"]} <a href='".HTTP_HOME_URL."/index.php?f=l2top'>{$lang["back"]}</a></div>";
        } elseif ( empty($char_name) ) {
            $L2TOP_ERR = "<div class='error'>{$lang["l2top_err_1"]} <a href='".HTTP_HOME_URL."/index.php?f=l2top'>{$lang["back"]}</a></div>";
        } elseif ( strlen($char_name) <= 2 ) {
            $L2TOP_ERR = "<div class='error'>{$lang["l2top_err_2"]} <a href='".HTTP_HOME_URL."/index.php?f=l2top'>{$lang["back"]}</a></div>";
        } elseif ( is_numeric($char_name) ) {
            $L2TOP_ERR = "<div class='error'>{$lang["l2top_err_3"]} <a href='".HTTP_HOME_URL."/index.php?f=l2top'>{$lang["back"]}</a></div>";
        } else {

            $db->gdb( $sid );

            $checkChar = $gdb[$sid]->SuperQuery( $qList[$vgs]["l2top"]["getChar"], array("name" => $char_name) );
            if ( $gdb[$sid]->num_rows($checkChar) > 0 ) {
                $VoteData = $gdb[$sid]->fetch( $checkChar );
                $votesQuery = $db->query( "SELECT * FROM `stress_l2top_bonus` WHERE `char`='{$char_name}' AND `prefix`='{$l2cfg["gs"][$sid]["l2top"]["prefix"]}' AND `give`='0'" );
                $VotesCount = $db->num_rows( $votesQuery );
                if ( $VotesCount > 0 ) {
                    $BonusName = "";
                    $BonusCount = 0;
                    $success = false;
                    if ( $l2cfg["gs"][$sid]["l2top"]["bonus"] == "l2money" ) {
                        $BonusName = "кредитов";
                        $BonusCount = floatval( $l2cfg["gs"][$sid]["l2top"]["count"] * $VotesCount );

                        $db->ldb( $lid );

                        $ldb[$lid]->query( "UPDATE `accounts` SET `l2money` = `l2money` + {$BonusCount} WHERE `login` = '{$VoteData["account_name"]}'" );
                        if ( $ldb[$lid]->affected() > 0 )
                            $success = true;
                    }
                    if ( $l2cfg["gs"][$sid]["l2top"]["bonus"] == "items" ) {
                        $BonusName = $lang["l2top_err_10"];
                        if ( $l2cfg["gs"][$sid]["l2top"]["method"] == "telnet" and $VoteData["online"] != 1 ) {
                            $L2TOP_ERR = "<div class='error'>{$lang["need_online"]}<br /><a href='".HTTP_HOME_URL."/index.php?f=l2top'>{$lang["back"]}</a></div>";
                        } elseif ( $l2cfg["gs"][$sid]["l2top"]["method"] == "mysql" and $VoteData["online"] == 1 and $l2cfg["gs"][$sid]["l2top"]["table"] == "items" ) {
                            $L2TOP_ERR = "<div class='error'>{$lang["need_offline"]}<br /><a href='".HTTP_HOME_URL."/index.php?f=l2top'>{$lang["back"]}</a></div>";
                        } elseif ( $l2cfg["gs"][$sid]["l2top"]["method"] != "telnet" and $l2cfg["gs"][$sid]["l2top"]["method"] != "mysql" and $l2cfg["gs"][$sid]["l2top"]["method"] != "mysqltelnet" ) {
                            $L2TOP_ERR = "<div class='error'>Error! Try Again!</div>";
                        } else {
                            if ( $l2cfg["gs"][$sid]["l2top"]["method"] == "mysqltelnet" )
                                $l2cfg["gs"][$sid]["l2top"]["method"] = ( $VoteData["online"] == 1 ) ? "telnet":"mysql";
                            $ItemId = $l2cfg["gs"][$sid]["l2top"]["item_id"];
                            $BonusCount = $l2cfg["gs"][$sid]["l2top"]["count"] * $VotesCount;
                            if ( $l2cfg["gs"][$sid]["l2top"]["method"] == "telnet" ) {
                                if ( $telnet = @fsockopen($l2cfg["gs"][$sid]["host"], $l2cfg["gs"][$sid]["telnet"]["port"], $errno, $errstr, $l2cfg["gs"][$sid]["telnet"]["timeout"]) ) {
                                    @fputs( $telnet, $l2cfg["gs"][$sid]["telnet"]["pass"] );
                                    @fputs( $telnet, "\r\n" );
                                    if ( !empty($l2cfg["gs"][$sid]["telnet"]["gmname"]) ) {
                                        @fputs( $telnet, $l2cfg["gs"][$sid]["telnet"]["gmname"] );
                                        @fputs( $telnet, "\r\n" );
                                    }
                                    fputs( $telnet, "give {$char_name} {$ItemId} {$BonusCount}" );
                                    $success = true;
                                    @fclose( $telnet );
                                } else {
                                    $L2TOP_ERR = "<div class='error'>Error Telnet: {$errstr} ({$errno})</div>";
                                }
                            }
                            if ( $l2cfg["gs"][$sid]["l2top"]["method"] == "mysql" ) {

                                if ( $l2cfg["gs"][$sid]["l2top"]["table"] == "items_delayed" ) {
                                    $gdb[$sid]->query( "INSERT INTO `items_delayed` SET `owner_id`='{$VoteData["charID"]}',`item_id`='{$ItemId}',`count`='{$BonusCount}',`enchant_level`='0',`attribute`='-1',`attribute_level`='-1',`flags`='0',`payment_status`='0',`description`=''" );
                                    if ( $gdb[$sid]->affected() > 0 )
                                        $success = true;
                                } elseif ( $l2cfg["gs"][$sid]["l2top"]["table"] == "character_items" ) {
                                    $gdb[$sid]->query( "INSERT INTO `character_items` SET `owner_id`='{$VoteData["charID"]}',`item_id`='{$ItemId}',`count`='{$BonusCount}',`enchant_level`='0'" );
                                    if ( $gdb[$sid]->affected() > 0 )
                                        $success = true;
                                } else {
                                    $QueryItem = $gdb[$sid]->SuperQuery( $qList[$vgs]["l2top"]["getItem"], array("ownerID" => $VoteData["charID"], "itemID" => $ItemId) );
                                    if ( $item = $gdb[$sid]->fetch($QueryItem) ) {
                                        $gdb[$sid]->SuperQuery( $qList[$vgs]["l2top"]["setItem"], array("ownerID" => $VoteData["charID"], "itemID" => $ItemId, "count" => $BonusCount) );
                                        if ( $gdb[$sid]->affected() > 0 )
                                            $success = true;
                                    } else {
                                        $object_id = $gdb[$sid]->SuperFetchArray( $qList[$vgs]["l2top"]["getMax"] );
                                        $gdb[$sid]->SuperQuery( $qList[$vgs]["l2top"]["insItem"], array("charID" => $VoteData["charID"], "objectID" => $object_id["max"], "itemID" => $ItemId, "count" => $BonusCount) );
                                        if ( $gdb[$sid]->affected() > 0 )
                                            $success = true;
                                    }
                                }
                            }
                        }
                    }
                    if ( $success == true ) {
                        $db->query( "UPDATE `stress_l2top_bonus` SET `give`='1' WHERE `char`='{$char_name}' AND `prefix`='{$l2cfg["gs"][$sid]["l2top"]["prefix"]}' AND `give`='0' LIMIT {$VotesCount}" );
                        $L2TOP_ERR = "<div class='noerror'>{$lang["l2top_err_4"]} {$BonusName}: {$BonusCount}</div>";
                    }
                } else {
                    $L2TOP_ERR = "<div class='error'>{$lang["l2top_err_5"]}<br /><a href='".HTTP_HOME_URL."/index.php?f=l2top'>{$lang["back"]}</a></div>";
                }
            } else {
                $L2TOP_ERR = "<div class='error'>{$lang["l2top_err_6"]}<br /><a href='".HTTP_HOME_URL."/index.php?f=l2top'>{$lang["back"]}</a></div>";
            }
        }
    }
    if ( !empty($L2TOP_ERR) ) {
        $tpl->Block( 'vote', false );
        $tpl->Block( 'error' );
        $tpl->Set( "error", $L2TOP_ERR );
    }
    $tpl->Build( "content" );
}
?>