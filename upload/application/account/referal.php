<?php
/**
 * STRESS WEB
 * @author S.T.R.E.S.S.
 * @copyright 2008 - 2010 STRESS WEB
 * @version 13
 * @web http://stressweb.ru
 */
if ( !defined("STRESSWEB") )
    die( "Access denied..." );
if ( $controller->isLogged() ) {
    while ( $l2cfg["gs"][$sid]["ls"] != $_lid ) {
        $sid++;
    }
    $profile = "<br /><center><h3>{$lang['ref_1']}</h3></center><br />";
    $c = 0;
    foreach ( $gsList as $i ) {
        if ( $l2cfg["gs"][$i]["ls"] == $_lid ) {
            $tmpServerList[$i] = $l2cfg["gs"][$i]["title"];
            $c++;
        }
    }
    if ( $c ) {

        $db->gdb( $sid );
        // Обработка запроса на получение бонусов
        if ( isset($_POST["getbonus"]) ) {
            $referer = intval( abs($_POST['id']) );
            $charId = intval( abs($_POST['char']) );
            if ( !$l2cfg["gs"][$sid]["referal_enable"] ) {
                $tpl->ShowError( $lang['error'], $lang['ref_2'] );
            } elseif ( $charId == 0 ) {
                $tpl->ShowError( $lang['error'], $lang['ref_3'] );
            } elseif ( $referer == 0 ) {
                $tpl->ShowError( $lang['error'], $lang['ref_4'] );
            } else {
                $success = false;
                $rr = $gdb[$sid]->query( "SELECT account_referer, char_name FROM stress_referal WHERE id='{$referer}' AND charId='{$charId}' AND success='0' AND account_name='{$controller->GetName()}'" );
                if ( $gdb[$sid]->num_rows($rr) == 0 ) {
                    $tpl->ShowError( $lang['error'], $lang['err_db'] );
                } else {
                    $dd = $gdb[$sid]->fetch( $rr );

                    if ( $l2cfg['gs'][$sid]['referal_type'] == 'level' ) {
                        $qq1 = $gdb[$sid]->SuperQuery( $qList[$vgs]['getByLevel'], array('account' => $dd['account_referer'], 'level' => $l2cfg["gs"][$i]["referal_condition"]) );
                        if ( $gdb[$sid]->num_rows($qq1) > 0 ) {

                            $BonusCount = $l2cfg["gs"][$sid]["referal_count"];

                            if ( $l2cfg["gs"][$sid]["referal_bonus"] == "credits" ) {
                                $db->ldb( $lid );
                                $ldb[$lid]->query( "UPDATE `accounts` SET `l2money`=`l2money`+{$BonusCount} WHERE `login` = '{$controller->GetName()}'" );
                                if ( $ldb[$lid]->affected() > 0 )
                                    $success = true;
                            }
                            if ( $l2cfg["gs"][$sid]["referal_bonus"] == "items" ) {
                                $online = $gdb[$sid]->result( $gdb[$sid]->query("SELECT `online` FROM `characters` WHERE `account_name`='{$controller->GetName()}' AND `{$qList[$vList[$l2cfg["gs"][$sid]["version"]]]["fields"]["charID"]}`='{$charId}'"), 0 );
                                if ( $l2cfg["gs"][$sid]["referal_method"] == "telnet" and $online != 1 ) {
                                    $tpl->ShowError( $lang['error'], $lang["need_online"] );
                                } elseif ( $l2cfg["gs"][$sid]["referal_method"] == "mysql" and $online == 1 and $l2cfg["gs"][$sid]["referal_table"] == "items" ) {
                                    $tpl->ShowError( $lang['error'], $lang["need_offline"] );
                                } elseif ( $l2cfg["gs"][$sid]["referal_method"] != "telnet" and $l2cfg["gs"][$sid]["referal_method"] != "mysql" and $l2cfg["gs"][$sid]["referal_method"] != "mysqltelnet" ) {
                                    $tpl->ShowError( $lang['error'], 'Error! Try Again!' );
                                } else {
                                    if ( $l2cfg["gs"][$sid]["referal_method"] == "mysqltelnet" )
                                        $l2cfg["gs"][$sid]["referal_method"] = ( $online == 1 ) ? "telnet":"mysql";
                                    $ItemId = $l2cfg["gs"][$sid]["referal_item_id"];
                                    if ( $l2cfg["gs"][$sid]["referal_method"] == "telnet" ) {
                                        if ( $telnet = @fsockopen($l2cfg["gs"][$sid]["host"], $l2cfg["gs"][$sid]["telnet"]["port"], $errno, $errstr, $l2cfg["gs"][$sid]["telnet"]["timeout"]) ) {
                                            @fputs( $telnet, $l2cfg["gs"][$sid]["telnet"]["pass"] );
                                            @fputs( $telnet, "\r\n" );
                                            if ( !empty($l2cfg["gs"][$sid]["telnet"]["gmname"]) ) {
                                                @fputs( $telnet, $l2cfg["gs"][$sid]["telnet"]["gmname"] );
                                                @fputs( $telnet, "\r\n" );
                                            }
                                            fputs( $telnet, "give {$dd['char_name']} {$ItemId} {$BonusCount}" );
                                            $success = true;
                                            @fclose( $telnet );
                                        } else {
                                            $tpl->ShowError( $lang['error'], "Error Telnet: {$errstr} ({$errno})" );
                                        }
                                    }
                                    if ( $l2cfg["gs"][$sid]["referal_method"] == "mysql" ) {
                                        if ( $l2cfg["gs"][$sid]["referal_table"] == "items_delayed" ) {
                                            $gdb[$sid]->query( "INSERT INTO `items_delayed` SET `owner_id`='{$charId}',`item_id`='{$ItemId}',`count`='{$BonusCount}',`enchant_level`='0',`attribute`='-1',`attribute_level`='-1',`flags`='0',`payment_status`='0',`description`=''" );
                                            if ( $gdb[$sid]->affected() > 0 )
                                                $success = true;
                                        } elseif ( $l2cfg["gs"][$sid]["referal_table"] == "character_items" ) {
                                            $gdb[$sid]->query( "INSERT INTO `character_items` SET `owner_id`='{$charId}',`item_id`='{$ItemId}',`count`='{$BonusCount}',`enchant_level`='0'" );
                                            if ( $gdb[$sid]->affected() > 0 )
                                                $success = true;
                                        } else {
                                            if ( $gdb[$sid]->num_rows($gdb[$sid]->query("SELECT `count` FROM `items` WHERE `owner_id`='{$charId}' AND `item_id`='{$ItemId}' AND `loc`='INVENTORY'")) > 0 ) {
                                                $gdb[$sid]->query( "UPDATE `items` SET `count`=`count`+{$BonusCount} WHERE `owner_id`='{$charId}' AND `item_id`='{$ItemId}' AND `loc`='INVENTORY'" );
                                                if ( $gdb[$sid]->affected() > 0 )
                                                    $success = true;
                                            } else {
                                                $object_id = $gdb[$sid]->result( $gdb[$sid]->query("SELECT MAX(`object_id`)+1 AS `object_id` FROM `items`"), 0 );
                                                $gdb[$sid]->query( "INSERT INTO `items` (`owner_id`,`object_id`,`item_id`,`count`,`enchant_level`,`loc`,`loc_data`) VALUES ('{$charId}', '{$object_id}', '{$ItemId}', '{$BonusCount}', '0', 'INVENTORY', '0')" );
                                                if ( $gdb[$sid]->affected() > 0 )
                                                    $success = true;
                                            }
                                        }
                                    }
                                }
                            }
                        } else {
                            $tpl->ShowError( $lang['error'], $lang['ref_5'] );
                        }
                    }
                }
                if ( $success ) {
                    $gdb[$sid]->query( "UPDATE `stress_referal` SET `date`='".date('Y-m-d H:i:s')."',`success`='1' WHERE `id`='{$referer}'" );
                    $tpl->ShowError( $lang['message'], "{$lang["ref_6"]} {$l2cfg["gs"][$sid]["referal_item_name"]}: {$BonusCount}", false );
                }
            }
        }
        // Основной вывод
        $servSelect = $controller->select( "sid", $tmpServerList, $sid, "onchange=\"javascript: document.sexsid.submit(); return false;\" style='width: 160px'" );
        $profile .= "
<div  id='sexsid'>
<label>{$lang['ref_7']}:</label><br />
<form action='".HTTP_HOME_URL."/index.php' method='GET' name='sexsid'>
<input type='hidden' name='f' value='cp' />
<input type='hidden' name='opt' value='referal' />
{$servSelect}
</form>		
</div>
";

        $sel_referals = $gdb[$sid]->query( "SELECT id, account_referer, charId, char_name FROM stress_referal WHERE account_name='{$controller->GetName()}' AND success='0'" );
        $bonus = "
        <style>
		.referal {width: 400px; margin: 0 auto;}
		.referal th {text-align: left;}
		.referal td {width: 33%}
		</style>
		<table cellpadding=0 cellspacing=0 class=referal>
		<tr>
			<th>{$lang['ref_8']}</th>
			<th>{$lang['ref_9']}</th>
			<th>{$lang['ref_10']}</th>
			<th>&nbsp;</th>
		</tr>
		";
        $sc = 0;
        if ( $gdb[$sid]->num_rows($sel_referals) > 0 ) {

            while ( $data = $gdb[$sid]->fetch($sel_referals) ) {

                if ( $l2cfg['gs'][$sid]['referal_type'] == 'level' ) {
                    $qq = $gdb[$sid]->SuperQuery( $qList[$vgs]['getByLevel'], array('account' => $data['account_referer'], 'level' => $l2cfg["gs"][$i]["referal_condition"]) );
                    if ( $gdb[$sid]->num_rows($qq) > 0 ) {
                        $sc++;
                        $ref_data = $gdb[$sid]->fetch( $qq );
                        $bonus .= "
						<tr>
							<td>{$ref_data['char_name']}</td>
							<td>{$data['char_name']}</td>
							<td>{$l2cfg["gs"][$sid]["referal_count"]} {$l2cfg["gs"][$sid]["referal_item_name"]}</td>
							<td>
								<form action='' method='post'>
								<input type='hidden' name='id' value='{$data['id']}'>
								<input type='hidden' name='char' value='{$data['charId']}'>
								<input type='submit' name='getbonus' value='{$lang['ref_11']}'>
								</form>
							</td>
						</tr>";
                    }
                }

            }
        }
        if ( $sc == 0 ) {
            $bonus .= "
				<tr>
					<td colspan=4>{$lang['ref_12']}</td>
				</tr>";
        }
        $bonus .= '</table>';
        $profile .= <<< HTML
<div id="sexchar"> 
{$bonus}	
</div>
HTML;
    } else
        $profile = $lang['ref_13'];
} else
    exit;
?>