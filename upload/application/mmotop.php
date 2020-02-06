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
    global $controller, $l2cfg, $db, $sid;
    //проверяем таймер mmotop
    if ( $controller->GetCache("mmotop_timer_{$sid}", false) < time() ) {
        //получаем голоса с mmotop
        $lines = file( htmlspecialchars_decode($l2cfg["gs"][$sid]["mmotop"]["url"]) );
        if ( $lines != array() ) {
            //ставим таймер
            $cache_time = time() + 60 * 30;
            $controller->SetCache( "mmotop_timer_{$sid}", $cache_time, 30, false );
            //заносим голоса в БД если таких еще нету
            for ( $i = 0; $i < count($lines); $i++ ) {
                $line = explode( "\t", $lines[$i] );
                $mmo_id = trim( $line[0] );
                $mmo_date = explode( " ", trim($line[1]) );
                $temp_date = explode( ".", $mmo_date[0] );
                $mmo_date = $temp_date[2]."-".$temp_date[1]."-".$temp_date[0]." ".$mmo_date[1];
                $mmo_ip = trim( $line[2] );
                $mmo_char_name = trim( $line[3] );

                if ( strlen($mmo_char_name) <= 0 )
                    continue;

                $sel = $db->query( "SELECT * FROM `stress_mmotop` WHERE `mmoid`='{$mmo_id}' AND `sid`='{$sid}'" );
                if ( $db->num_rows($sel) > 0 )
                    continue;

                $db->query( "INSERT INTO `stress_mmotop` SET `mmoid`='{$mmo_id}',`charname`='{$mmo_char_name}',`ip`='{$mmo_ip}',`date`='{$mmo_date}',`sid`='{$sid}'" );
                //$pchar = explode("-", iconv("WINDOWS-1251", "UTF-8", strtolower(rtrim(substr($lines[$i], 20)))));
            }
        }
    }
}

$mmoServerList = array();
foreach ( $gsList as $i ) {
    if ( $l2cfg["gs"][$i]["mmotop"]["enable"] ) {
        $l2cfg["mmotop"]["enable"] = true;
        $mmoServerList[$i] = $l2cfg["gs"][$i]["title"];
    }
}

if ( !$l2cfg["mmotop"]["enable"] ) {
    $tpl->SetResult( "content", "<div class='error'>{$lang["mmotop_0"]}</div>" );
} else {
    $MMO_ERR = "";

    $tpl->LoadView( "mmotop" );
    if ( $_act == "" ) {
        $tpl->Block( 'vote' );
        $tpl->Block( 'error', false );
        $tpl->Set( 'servers', $controller->select("sid", $mmoServerList, $sid, "style='width: 125px;'") );
        if ( $l2cfg["captcha"]["mmotop"] and $l2cfg['captcha']['mmotop_type'] == 'sw' ) {
            $tpl->Block( 'captcha' );
            $tpl->Set( "l2sec_code", "<div id=\"sw-captcha\" class='captcha'><a onclick=\"reload(); return false;\" href=\"#\"><img src=\"".HTTP_HOME_URL."/module/antibot.php\" alt=\"code\" border=\"0\" /></a></div>" );
        } else
            $tpl->Block( 'captcha', false );
        if ( $l2cfg['captcha']['mmotop'] and $l2cfg['captcha']['mmotop_type'] == 'recaptcha' ) {
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
        if ( $l2cfg["captcha"]["mmotop"] and $l2cfg['captcha']['mmotop_type'] == 'sw' ) {
            $sCode_post = strtoupper( $db->safe($_POST["l2sec_code"]) );
            $sCode_sess = $controller->sess_get( "seccode" );
            $controller->sess_unset( 'seccode' );
            if ( !$sCode_sess or $sCode_post != $sCode_sess )
                $captcha = true;
        }
        if ( $l2cfg["captcha"]["mmotop"] and $l2cfg['captcha']['mmotop_type'] == 'recaptcha' ) {
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
            $MMO_ERR = "<div class='error'>{$lang["err_code"]} <a href='".HTTP_HOME_URL."/index.php?f=mmotop'>{$lang["back"]}</a></div>";
        } elseif ( empty($char_name) ) {
            $MMO_ERR = "<div class='error'>{$lang["mmotop_1"]} <a href='".HTTP_HOME_URL."/index.php?f=mmotop'>{$lang["back"]}</a></div>";
        } else {
            $db->gdb( $sid );

            $checkChar = $gdb[$sid]->query( "SELECT account_name, {$qList[$vgs]["fields"]["charID"]} AS charID, online FROM `characters` WHERE `char_name`='{$char_name}'" );
            if ( $gdb[$sid]->num_rows($checkChar) > 0 ) {
                $VoteData = $gdb[$sid]->fetch( $checkChar );
                $votesQuery = $db->query( "SELECT * FROM `stress_mmotop` WHERE `charname`='{$char_name}' AND `sid`='{$sid}' AND `deliver`='0'" );
                $VotesCount = $db->num_rows( $votesQuery );
                if ( $VotesCount > 0 ) {
                    $BonusName = "";
                    $BonusCount = 0;
                    $success = false;
                    if ( $l2cfg["gs"][$sid]["mmotop"]["bonus"] == "l2money" ) {
                        $BonusName = $lang["mmotop_4"];
                        $BonusCount = floatval( $l2cfg["gs"][$sid]["mmotop"]["count"] * $VotesCount );

                        $db->ldb( $lid );

                        $ldb[$lid]->query( "UPDATE `accounts` SET `l2money`=`l2money`+{$BonusCount} WHERE `login` = '{$VoteData["account_name"]}'" );
                        if ( $ldb[$lid]->affected() > 0 )
                            $success = true;
                    }
                    if ( $l2cfg["gs"][$sid]["mmotop"]["bonus"] == "items" ) {
                        $BonusName = $lang["mmotop_5"];
                        if ( $l2cfg["gs"][$sid]["mmotop"]["method"] == "telnet" and $VoteData["online"] != 1 ) {
                            $MMO_ERR = "<div class='error'>{$lang["need_online"]}<br /><a href='".HTTP_HOME_URL."/index.php?f=mmotop'>{$lang["back"]}</a></div>";
                        } elseif ( $l2cfg["gs"][$sid]["mmotop"]["method"] == "mysql" and $VoteData["online"] == 1 and $l2cfg["gs"][$sid]["mmotop"]["table"] == "items" ) {
                            $MMO_ERR = "<div class='error'>{$lang["need_offline"]}<br /><a href='".HTTP_HOME_URL."/index.php?f=mmotop'>{$lang["back"]}</a></div>";
                        } elseif ( $l2cfg["gs"][$sid]["mmotop"]["method"] != "telnet" and $l2cfg["gs"][$sid]["mmotop"]["method"] != "mysql" and $l2cfg["gs"][$sid]["mmotop"]["method"] != "mysqltelnet" ) {
                            $MMO_ERR = "<div class='error'>Error! Try Again!</div>";
                        } else {
                            if ( $l2cfg["gs"][$sid]["mmotop"]["method"] == "mysqltelnet" )
                                $l2cfg["gs"][$sid]["mmotop"]["method"] = ( $VoteData["online"] == 1 ) ? "telnet":"mysql";
                            $ItemId = $l2cfg["gs"][$sid]["mmotop"]["item_id"];
                            $BonusCount = $l2cfg["gs"][$sid]["mmotop"]["count"] * $VotesCount;
                            if ( $l2cfg["gs"][$sid]["mmotop"]["method"] == "telnet" ) {
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
                                    $MMO_ERR = "<div class='error'>Error Telnet: {$errstr} ({$errno})</div>";
                                }
                            }
                            if ( $l2cfg["gs"][$sid]["mmotop"]["method"] == "mysql" ) {

                                if ( $l2cfg["gs"][$sid]["mmotop"]["table"] == "items_delayed" ) {
                                    $gdb[$sid]->query( "INSERT INTO `items_delayed` SET `owner_id`='{$VoteData["charID"]}',`item_id`='{$ItemId}',`count`='{$BonusCount}',`enchant_level`='0',`attribute`='-1',`attribute_level`='-1',`flags`='0',`payment_status`='0',`description`=''" );
                                    if ( $gdb[$sid]->affected() > 0 )
                                        $success = true;
                                } elseif ( $l2cfg["gs"][$sid]["mmotop"]["table"] == "character_items" ) {
                                    $gdb[$sid]->query( "INSERT INTO `character_items` SET `owner_id`='{$VoteData["charID"]}',`item_id`='{$ItemId}',`count`='{$BonusCount}',`enchant_level`='0'" );
                                    if ( $gdb[$sid]->affected() > 0 )
                                        $success = true;
                                } else {
                                    if ( $gdb[$sid]->num_rows($gdb[$sid]->query("SELECT `count` FROM `items` WHERE `owner_id`='{$VoteData["charID"]}' AND `item_id`='{$ItemId}' AND `loc`='INVENTORY'")) > 0 ) {
                                        $gdb[$sid]->query( "UPDATE `items` SET `count`=`count`+{$BonusCount} WHERE `owner_id`='{$VoteData["charID"]}' AND `item_id`='{$ItemId}' AND `loc`='INVENTORY'" );
                                        if ( $gdb[$sid]->affected() > 0 )
                                            $success = true;
                                    } else {
                                        $object_id = $gdb[$sid]->result( $gdb[$sid]->query("SELECT MAX(`object_id`)+1 AS `object_id` FROM `items`"), 0 );
                                        $gdb[$sid]->query( "INSERT INTO `items` (`owner_id`,`object_id`,`item_id`,`count`,`enchant_level`,`loc`,`loc_data`) VALUES ('{$VoteData["charID"]}', '{$object_id}', '{$ItemId}', '{$BonusCount}', '0', 'INVENTORY', '0')" );
                                        if ( $gdb[$sid]->affected() > 0 )
                                            $success = true;
                                    }
                                }
                            }
                        }
                    }
                    if ( $success ) {
                        $db->query( "UPDATE `stress_mmotop` SET `account_name`='{$VoteData["account_name"]}',`charid`='{$VoteData["charID"]}',`deliver`='1',`date_deliver`='".date("Y-m-d H:i:s", time())."' WHERE `charname`='{$char_name}' AND `sid`='{$sid}' AND `deliver`='0' LIMIT {$VotesCount}" );
                        $MMO_ERR = "<div class='noerror'>{$lang["mmotop_6"]} {$BonusName}: {$BonusCount}</div>";
                    }
                } else {
                    $MMO_ERR = "<div class='error'>{$lang["mmotop_3"]}<br /><a href='".HTTP_HOME_URL."/index.php?f=mmotop'>{$lang["back"]}</a></div>";
                }
            } else {
                $MMO_ERR = "<div class='error'>{$lang["mmotop_2"]}<br /><a href='".HTTP_HOME_URL."/index.php?f=mmotop'>{$lang["back"]}</a></div>";
            }
        }
    }
    if ( !empty($MMO_ERR) ) {
        $tpl->Block( 'vote', false );
        $tpl->Block( 'error' );
        $tpl->Set( "error", $MMO_ERR );
    }
    $tpl->Build( "content" );
}
?>