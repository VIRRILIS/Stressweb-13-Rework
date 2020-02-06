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
    $profile = "<br /><center><h3>Обменник кредитов на предметы</h3></center><br />";
    $c = 0;
    foreach ( $gsList as $i ) {
        if ( $l2cfg["gs"][$i]["ls"] == $_lid and $l2cfg["gs"][$i]["changer"]["enable"] ) {
            $tmpServerList[$i] = $l2cfg["gs"][$i]["title"]." : 1 {$l2cfg["gs"][$i]["changer"]["item_name"]} = {$l2cfg["gs"][$i]["changer"]["price"]} l2money";
            $c++;
        }
    }
    if ( $c ) {
        $db->gdb( $sid );
        // обработка запроса
        if ( isset($_POST["changer"]) ) {
            $charId = intval( abs($_POST['charId']) );
            $count = intval( abs($_POST['count']) );
            $price = $l2cfg["gs"][$sid]["changer"]["price"] * $count;
            if ( !$l2cfg["gs"][$sid]["changer"]["enable"] ) {
                $tpl->ShowError( "Ошибка", "Сервис для этого сервера отключен" );
            } elseif ( $charId == 0 ) {
                $tpl->ShowError( "Ошибка", "Вы не выбрали персонажа" );
            } elseif ( $count == 0 ) {
                $tpl->ShowError( "Ошибка", "Вы не выбрали количество предметов" );
            } else {
                $success = false;
                $bill = false;
                $db->ldb( $_lid );

                $money = $ldb[$_lid]->result( $ldb[$_lid]->query("SELECT `l2money` FROM `accounts` WHERE `login`='{$controller->GetName()}'"), 0 );
                if ( $money >= $price ) {
                    $online = $gdb[$sid]->result( $gdb[$sid]->query("SELECT `online` FROM `characters` WHERE `account_name`='{$controller->GetName()}' AND `{$qList[$vList[$l2cfg["gs"][$sid]["version"]]]["fields"]["charID"]}`='{$charId}'"), 0 );

                    if ( $l2cfg["gs"][$sid]["changer"]["method"] == "telnet" and $online != 1 )
                        $tpl->ShowError( $lang["error"], $lang["need_online"] );
                    elseif ( $l2cfg["gs"][$sid]["changer"]["method"] == "mysql" and $online == 1 and $l2cfg["gs"][$sid]["changer"]["table"] == "items" )
                        $tpl->ShowError( $lang["error"], $lang["need_offline"] );
                    elseif ( $l2cfg["gs"][$sid]["changer"]["method"] != "telnet" and $l2cfg["gs"][$sid]["changer"]["method"] != "mysql" and $l2cfg["gs"][$sid]["changer"]["method"] != "mysqltelnet" )
                        $tpl->ShowError( $lang["error"], "Error! Try Again!" );
                    else {
                        if ( $l2cfg["gs"][$sid]["changer"]["method"] == "mysqltelnet" )
                            $l2cfg["gs"][$sid]["changer"]["method"] = ( $online == 1 ) ? "telnet":"mysql";
                        $ldb[$_lid]->query( "UPDATE `accounts` SET `l2money`=`l2money`-{$price} WHERE `login`='{$controller->GetName()}'" );
                        if ( $ldb[$_lid]->affected() > 0 )
                            $bill = true;
                    }
                } else
                    $tpl->ShowError( "Ошибка", "У вас не хватает кредитов для совершения операции!" );
                if ( $bill ) {
                    if ( $l2cfg["gs"][$sid]["changer"]["method"] == "telnet" ) {
                        if ( $telnet = @fsockopen($l2cfg["gs"][$sid]["host"], $l2cfg["gs"][$sid]["telnet"]["port"], $errno, $errstr, $l2cfg["gs"][$sid]["telnet"]["timeout"]) ) {
                            @fputs( $telnet, $l2cfg["gs"][$sid]["telnet"]["pass"] );
                            @fputs( $telnet, "\r\n" );
                            if ( !empty($l2cfg["gs"][$sid]["telnet"]["gmname"]) ) {
                                @fputs( $telnet, $l2cfg["gs"][$sid]["telnet"]["gmname"] );
                                @fputs( $telnet, "\r\n" );
                            }
                            fputs( $telnet, "give {$charId} {$l2cfg["gs"][$sid]["changer"]["item_id"]} {$count}" );
                            $success = true;
                            @fclose( $telnet );
                        } else
                            $tpl->ShowError( $lang["error"], "Error Telnet: {$errstr} ({$errno})" );
                    }
                    if ( $l2cfg["gs"][$sid]["changer"]["method"] == "mysql" ) {

                        if ( $l2cfg["gs"][$sid]["changer"]["table"] == "items_delayed" ) {

                            $gdb[$sid]->query( "INSERT INTO `items_delayed` SET `owner_id`='{$charId}',`item_id`='{$l2cfg["gs"][$sid]["changer"]["item_id"]}',`count`='{$count}',`enchant_level`='0',`attribute`='-1',`attribute_level`='-1',`flags`='0',`payment_status`='0',`description`=''" );
                            if ( $gdb[$sid]->affected() > 0 )
                                $success = true;
                            else
                                $tpl->ShowError( $lang["error"], $lang["err_db"] );

                        } elseif ( $l2cfg["gs"][$sid]["changer"]["table"] == "character_items" ) {

                            $gdb[$sid]->query( "INSERT INTO `character_items` SET `owner_id`='{$charId}',`item_id`='{$l2cfg["gs"][$sid]["changer"]["item_id"]}',`count`='{$count}',`enchant_level`='0'" );
                            if ( $gdb[$sid]->affected() > 0 )
                                $success = true;

                        } else {

                            $object_id = $gdb[$sid]->query( "SELECT `object_id` FROM `items` WHERE `owner_id` = '{$charId}' AND `item_id` = '{$l2cfg["gs"][$sid]["changer"]["item_id"]}' AND `loc` = 'INVENTORY'" );
                            if ( $gdb[$sid]->num_rows($object_id) > 0 ) {
                                $object_id = $gdb[$sid]->result( $object_id, 0 );
                                $gdb[$sid]->query( "UPDATE `items` SET `count`=`count`+{$count} WHERE `object_id`='{$object_id}'" );
                                if ( $gdb[$sid]->affected() > 0 )
                                    $success = true;
                                else
                                    $tpl->ShowError( $lang["error"], $lang["err_db"] );
                            } else {
                                $object_id = $gdb[$sid]->result( $gdb[$sid]->query("SELECT MAX(`object_id`)+1 AS `object_id` FROM `items`"), 0 );
                                $gdb[$sid]->query( "INSERT INTO `items` (`owner_id`,`object_id`,`item_id`,`count`,`enchant_level`,`loc`,`loc_data`) VALUES ('{$charId}', '{$object_id}', '{$l2cfg["gs"][$sid]["changer"]["item_id"]}', '{$count}', '0', 'INVENTORY', '0')" );
                                if ( $gdb[$sid]->affected() > 0 )
                                    $success = true;
                                else
                                    $tpl->ShowError( $lang["error"], $lang["err_db"] );
                            }

                        }
                    }
                }
                if ( $success ) {
                    $tpl->ShowError( $lang["message"], "Вам зачислено {$count} {$l2cfg["gs"][$sid]["changer"]["item_name"]}", false );
                }
            }
        }
        $db->ldb( $_lid );
    	$l2money = $ldb[$_lid]->result( $ldb[$_lid]->query("SELECT `l2money` FROM `accounts` WHERE `login`='{$controller->GetName()}'"), 0 );
    	$profile .= "<div class='l2money'>На Вашем счету: ".$l2money." l2money</div>";
        // вывод основной формы
        $servSelect = $controller->select( "sid", $tmpServerList, $sid, "onchange=\"javascript: document.changersid.submit(); return false;\"" );
        $profile .= "
<div  id='changersid'>
<label>Сервер - Цена:</label><br />
<form action='".HTTP_HOME_URL."/index.php' method='GET' name='changersid'>
<input type='hidden' name='f' value='cp' />
<input type='hidden' name='opt' value='changer' />
{$servSelect}
</form>		
</div>
";

        $selchars = $gdb[$sid]->query( "SELECT char_name, {$qList[$vList[$l2cfg["gs"][$sid]["version"]]]["fields"]["charID"]} AS charId FROM characters WHERE account_name='{$controller->GetName()}'" );
        $options = "";
        if ( $gdb[$sid]->num_rows($selchars) > 0 )
            while ( $data = $gdb[$sid]->fetch($selchars) )
                $options .= "<option value='{$data["charId"]}'>{$data["char_name"]}</option>";
        $options_count = "";
        for ( $i = 1; $i <= 100; $i++ )
            $options_count .= "<option value='{$i}'>{$i}</option>";
        $profile .= <<< HTML
<div id="changerchar">
<form action="" method="post">
<label>Персонаж:</label><br />
<select id='charList' name='charId'>
<option value='0' selected> - Выберите персонажа - </option>
{$options}
</select><br />
<label>Количество {$l2cfg["gs"][$sid]["changer"]["item_name"]}</label><br />
<select id='countList' name='count'>
<option value='0' selected> - Выберите количество - </option>
{$options_count}
</select><br /><br />
<input value="Купить" name="changer" type="submit" id="chbutton">
</form>
</div>
HTML;
    } else
        $profile = "Сервис отключен";
} else
    exit;
?>