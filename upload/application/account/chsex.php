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
    $profile = "<br /><center><h3>Смена пола персонажа</h3></center><br />";
    $c = 0;
    foreach ( $gsList as $i ) {
        if ( $l2cfg["gs"][$i]["ls"] == $_lid and $l2cfg["gs"][$i]["chsex"]["enable"] ) {
            $money = $l2cfg["gs"][$i]["chsex"]["money"] == "credits" ? "l2money":$l2cfg["gs"][$i]["chsex"]["item_name"];
            $tmpServerList[$i] = $l2cfg["gs"][$i]["title"]." - ".$l2cfg["gs"][$i]["chsex"]["price"]." ".$money;
            $c++;
        }
    }
    if ( $c ) {
        $db->gdb( $sid );
        // Обработка запроса на смену пола
        if ( isset($_POST["chsex"]) ) {
            $charId = intval( abs($_POST['charId']) );
            $sex = intval( abs($_POST['sex']) );
            if ( !$l2cfg["gs"][$sid]["chsex"]["enable"] ) {
                $tpl->ShowError( "Ошибка", "Сервис для этого сервера отключен" );
            } elseif ( $charId == 0 ) {
                $tpl->ShowError( "Ошибка", "Вы не выбрали персонажа" );
            } else {
                $success = false;
                $sex_old = $gdb[$sid]->result( $gdb[$sid]->query("SELECT `sex` FROM `characters` WHERE `account_name`='{$controller->GetName()}' AND `{$qList[$vList[$l2cfg["gs"][$sid]["version"]]]["fields"]["charID"]}`='{$charId}'"), 0 );
                if ( $sex_old == $sex )
                    $tpl->ShowError( "Ошибка", "У Вашего персонажа выбраный пол" );
                else {
                    if ( $l2cfg["gs"][$sid]["chsex"]["money"] == "credits" ) {
                        $db->ldb( $_lid );

                        $money = $ldb[$_lid]->result( $ldb[$_lid]->query("SELECT `l2money` FROM `accounts` WHERE `login`='{$controller->GetName()}'"), 0 );
                        if ( $money >= $l2cfg["gs"][$sid]["chsex"]["price"] ) {
                            $ldb[$_lid]->query( "UPDATE `accounts` SET `l2money`=`l2money`-{$l2cfg["gs"][$sid]["chsex"]["price"]} WHERE `login`='{$controller->GetName()}'" );
                            if ( $ldb[$_lid]->affected() > 0 )
                                $success = true;
                        } else
                            $tpl->ShowError( "Ошибка", "У вас не хватает кредитов для совершения операции!" );
                    }
                    if ( $l2cfg["gs"][$sid]["chsex"]["money"] == "items" ) {
                        $ItemData = $gdb[$sid]->SuperFetchArray( $qList[$vList[$l2cfg["gs"][$sid]["version"]]]["getItem"], array("charID" => $charId, "itemID" => $l2cfg["gs"][$sid]["chsex"]["item_id"]) );
                        if ( $ItemData["count"] >= $l2cfg["gs"][$sid]["chsex"]["price"] ) {
                            $gdb[$sid]->query( "UPDATE `items` SET `count`=`count`-{$l2cfg["gs"][$sid]["chsex"]["price"]} WHERE `object_id`='{$ItemData["object_id"]}'" );
                            if ( $gdb[$sid]->affected() > 0 )
                                $success = true;
                        } else
                            $tpl->ShowError( "Ошибка", "У вас не хватает предметов для совершения операции!" );
                    }
                }

                if ( $success ) {
                    $gdb[$sid]->query( "UPDATE `characters` SET `sex`='{$sex}' WHERE `{$qList[$vList[$l2cfg["gs"][$sid]["version"]]]["fields"]["charID"]}`='{$charId}'" );
                    if ( $gdb[$sid]->affected() > 0 )
                        $tpl->ShowError( "Сообщение", "Пол Вашего персонажа успешно изменен!", false );
                    else
                        $tpl->ShowError( "Ошибка", "Ошибка базы данных" );
                }
            }
        }
        if ( $l2cfg["gs"][$sid]["chsex"]["money"] == "credits" ) {
            $db->ldb( $_lid );
            $l2money = $ldb[$_lid]->result( $ldb[$_lid]->query("SELECT `l2money` FROM `accounts` WHERE `login`='{$controller->GetName()}'"), 0 );
            $profile .= "<div class='l2money'>На Вашем счету: ".$l2money." l2money</div>";
        }
        // Основной вывод формы смены пола
        $servSelect = $controller->select( "sid", $tmpServerList, $sid, "onchange=\"javascript: document.sexsid.submit(); return false;\" style='width: 160px'" );
        $profile .= "
<div  id='sexsid'>
<label>Сервер - Цена:</label><br />
<form action='".HTTP_HOME_URL."/index.php' method='GET' name='sexsid'>
<input type='hidden' name='f' value='cp' />
<input type='hidden' name='opt' value='chsex' />
{$servSelect}
</form>		
</div>
";
        if ( $l2cfg["gs"][$sid]["chsex"]["money"] == "credits" ) {
            $selchars = $gdb[$sid]->query( "SELECT char_name, {$qList[$vList[$l2cfg["gs"][$sid]["version"]]]["fields"]["charID"]} AS charId, sex FROM characters WHERE account_name='{$controller->GetName()}' AND online='0'" );
            $options = "";
            if ( $gdb[$sid]->num_rows($selchars) > 0 ) {
                while ( $data = $gdb[$sid]->fetch($selchars) ) {
                    $data["sex"] = $data["sex"] ? "Жен":"Муж";
                    $options .= "<option value='{$data["charId"]}'>{$data["char_name"]} ({$data["sex"]})</option>";
                }
            }
        } else {
            $selchars = $gdb[$sid]->query( "SELECT characters.char_name, characters.{$qList[$vList[$l2cfg["gs"][$sid]["version"]]]["fields"]["charID"]} AS charId, characters.sex, items.count FROM characters LEFT JOIN items ON characters.{$qList[$vList[$l2cfg["gs"][$sid]["version"]]]["fields"]["charID"]}=items.owner_id WHERE characters.account_name='{$controller->GetName()}' AND characters.online='0' AND items.item_id='{$l2cfg["gs"][$sid]["chsex"]["item_id"]}'" );
            $options = "";
            if ( $gdb[$sid]->num_rows($selchars) > 0 ) {
                while ( $data = $gdb[$sid]->fetch($selchars) ) {
                    $data["sex"] = $data["sex"] ? "Жен":"Муж";
                    $options .= "<option value='{$data["charId"]}'>{$data["char_name"]} ({$data["sex"]}) {$data['count']} {$l2cfg["gs"][$sid]["chsex"]["item_name"]}</option>";
                }
            }
        }
        $profile .= <<< HTML
<div id="sexchar">
<form action="" method="post">
<label>Персонаж:</label><br /> 
<select id='charList' name='charId'>
<option value='0' selected> - Выберите персонажа - </option>
{$options}
</select><br />		
<label>Пол</label><br />
<input type='radio' name='sex' value='0' checked='checked'> Муж. <input type='radio' name='sex' value='1'> Жен.<br />
<span style='color: #ff0000;'>Внимание! Персонаж должен быть <b>OFFLINE</b></span><br />
<input value="Сменить" name="chsex" type="submit" id="chbutton">
</form>
</div>
HTML;
    } else
        $profile = "Сервис отключен";
} else
    exit;
?>