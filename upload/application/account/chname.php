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
    $profile = "<br /><center><h3>Смена ника персонажа</h3></center><br />";
    $c = 0;
    foreach ( $gsList as $i ) {
        if ( $l2cfg["gs"][$i]["ls"] == $_lid and $l2cfg["gs"][$i]["chname"]["enable"] ) {
            $money = $l2cfg["gs"][$i]["chname"]["money"] == "credits" ? "l2money":$l2cfg["gs"][$i]["chname"]["item_name"];
            $tmpServerList[$i] = $l2cfg["gs"][$i]["title"]." - ".$l2cfg["gs"][$i]["chname"]["price"]." ".$money;
            $c++;
        }
    }
    if ( $c ) {
        $db->gdb( $sid );
        // Обрабока запроса на смену ника
        if ( isset($_POST["chname"]) ) {
            $charId = intval( abs($_POST['charId']) );
            $char_name = $gdb[$sid]->safe( $_POST['char_name'] );
            if ( !$l2cfg["gs"][$sid]["chname"]["enable"] ) {
                $tpl->ShowError( "Ошибка", "Сервис для этого сервера отключен" );
            } elseif ( $charId == 0 ) {
                $tpl->ShowError( "Ошибка", "Вы не выбрали персонажа" );
            } elseif ( strlen($char_name) > 16 ) {
                $tpl->ShowError( "Ошибка", "Новый ник должен быть не больше 16 символов" );
            } else {
                $success = false;
                for ( $i = 0; $i < strlen($char_name); $i++ ) {
                    $char_name_arr[] = substr( $char_name, $i, 1 );
                }
                $letters_arr = explode( ",", $l2cfg["gs"][$sid]["chname"]["letters"] );
                $lerror = 0;
                foreach ( $char_name_arr as $char_letter ) {
                    if ( !in_array($char_letter, $letters_arr) )
                        $lerror++;
                }
                if ( $lerror > 0 )
                    $tpl->ShowError( "Ошибка", "Новый ник содержит не допустимые символы" );
                else {
                    $name_old = $gdb[$sid]->query( "SELECT `char_name` FROM `characters` WHERE `char_name`='{$char_name}'" );
                    if ( $gdb[$sid]->num_rows($name_old) > 0 ) {
                        $tpl->ShowError( "Ошибка", "Такой ник уже занят" );
                    } else {
                        if ( $l2cfg["gs"][$sid]["chname"]["money"] == "credits" ) {
                            $db->ldb( $_lid );

                            $money = $ldb[$_lid]->result( $ldb[$_lid]->query("SELECT `l2money` FROM `accounts` WHERE `login`='{$controller->GetName()}'"), 0 );
                            if ( $money >= $l2cfg["gs"][$sid]["chname"]["price"] ) {
                                $ldb[$_lid]->query( "UPDATE `accounts` SET `l2money`=`l2money`-{$l2cfg["gs"][$sid]["chname"]["price"]} WHERE `login`='{$controller->GetName()}'" );
                                if ( $ldb[$_lid]->affected() > 0 )
                                    $success = true;
                            } else
                                $tpl->ShowError( "Ошибка", "У вас не хватает кредитов для совершения операции!" );
                        }
                        if ( $l2cfg["gs"][$sid]["chname"]["money"] == "items" ) {
                            $ItemData = $gdb[$sid]->SuperFetchArray( $qList[$vList[$l2cfg["gs"][$sid]["version"]]]["getItem"], array("charID" => $charId, "itemID" => $l2cfg["gs"][$sid]["chname"]["item_id"]) );
                            if ( $ItemData["count"] >= $l2cfg["gs"][$sid]["chname"]["price"] ) {
                                $gdb[$sid]->query( "UPDATE `items` SET `count`=`count`-{$l2cfg["gs"][$sid]["chname"]["price"]} WHERE `object_id`='{$ItemData["object_id"]}'" );
                                if ( $gdb[$sid]->affected() > 0 )
                                    $success = true;
                            } else
                                $tpl->ShowError( "Ошибка", "У вас не хватает предметов для совершения операции!" );
                        }
                    }
                }
                if ( $success ) {
                    $gdb[$sid]->query( "UPDATE `characters` SET `char_name`='{$char_name}' WHERE `{$qList[$vList[$l2cfg["gs"][$sid]["version"]]]["fields"]["charID"]}`='{$charId}'" );
                    if ( $gdb[$sid]->affected() > 0 )
                        $tpl->ShowError( "Сообщение", "Ник Вашего персонажа успешно изменен!", false );
                    else
                        $tpl->ShowError( "Ошибка", "Ошибка базы данных" );
                }
            }
        }
        if ( $l2cfg["gs"][$sid]["chname"]["money"] == "credits" ) {
            $db->ldb( $_lid );
            $l2money = $ldb[$_lid]->result( $ldb[$_lid]->query("SELECT `l2money` FROM `accounts` WHERE `login`='{$controller->GetName()}'"), 0 );
            $profile .= "<div class='l2money'>На Вашем счету: ".$l2money." l2money</div>";
        }
        // Вывод основной формы
        $servSelect = $controller->select( "sid", $tmpServerList, $sid, "onchange=\"javascript: document.namesid.submit(); return false;\" style='width: 160px'" );
        $profile .= "
<div  id='namesid'>
<label>Сервер - Цена:</label><br />
<form action='".HTTP_HOME_URL."/index.php' method='GET' name='namesid'>
<input type='hidden' name='f' value='cp' />
<input type='hidden' name='opt' value='chname' />
{$servSelect}
</form>		
</div>
";

        if ( $l2cfg["gs"][$sid]["chname"]["money"] == "credits" ) {
            $selchars = $gdb[$sid]->query( "SELECT char_name, {$qList[$vList[$l2cfg["gs"][$sid]["version"]]]["fields"]["charID"]} AS charId FROM characters WHERE account_name='{$controller->GetName()}' AND online='0'" );
            $options = "";
            if ( $gdb[$sid]->num_rows($selchars) > 0 ) {
                while ( $data = $gdb[$sid]->fetch($selchars) ) {
                    $options .= "<option value='{$data["charId"]}'>{$data["char_name"]}</option>";
                }
            }
        } else {
            $selchars = $gdb[$sid]->query( "SELECT characters.char_name, characters.{$qList[$vList[$l2cfg["gs"][$sid]["version"]]]["fields"]["charID"]} AS charId, items.count FROM characters LEFT JOIN items ON characters.{$qList[$vList[$l2cfg["gs"][$sid]["version"]]]["fields"]["charID"]}=items.owner_id WHERE characters.account_name='{$controller->GetName()}' AND characters.online='0' AND items.item_id='{$l2cfg["gs"][$sid]["chsex"]["item_id"]}'" );
            $options = "";
            if ( $gdb[$sid]->num_rows($selchars) > 0 ) {
                while ( $data = $gdb[$sid]->fetch($selchars) ) {
                    $options .= "<option value='{$data["charId"]}'>{$data["char_name"]} ({$data['count']} {$l2cfg["gs"][$sid]["chname"]["item_name"]})</option>";
                }
            }
        }
        $letters = explode( ",", $l2cfg["gs"][$sid]["chname"]["letters"] );
        $alphabet = '<div id="alpha">';
        foreach ( $letters as $letter ) {
            $alphabet .= " <a href=''>".$letter."</a> ";
        }
        $alphabet .= "</div>";
        $profile .= <<< HTML
<script type="text/javascript">
$(document).ready(function(){
    $("#alpha a").click(function(){
		$("input[name=char_name]").val($("input[name=char_name]").val()+$(this).text());
		return false;		
	});
	$("#swdelete").click(function(){
		$("input[name=char_name]").val($("input[name=char_name]").val().substring(0,$("input[name=char_name]").val().length-1));
		return false;
	});
});
</script>
<div id="charname">
<form action="" method="post">
<label>Персонаж:</label><br />
<select id='charList' name='charId'>
<option value='0' selected> - Выберите персонажа - </option>
{$options}
</select><br />
{$alphabet}
<label>Новый ник</label><br />
<input type='text' readonly='readonly' name='char_name' maxlength='16' /> <a href='' id='swdelete'>DEL</a><br />
<div id='chresult'></div>
<span style='color: #ff0000;'>Внимание! Персонаж должен быть <b>OFFLINE</b></span><br />
<input value="Сменить" name="chname" type="submit" id="chbutton">
</form>
</div>
HTML;
    } else
        $profile = "Сервис отключен";
} else
    exit;
?>