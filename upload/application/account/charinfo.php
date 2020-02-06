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
    /**
     * =========================
     * 	View character
     * ========================= 
     */
    $db->gdb( $sid );

    //$_vgs = $vList[$l2cfg["gs"][$sid]["version"]];
    $_char_fail = true;
    $charID = isset( $_REQUEST["char"] ) ? intval( $_REQUEST["char"] ):0;
    if ( $charID > 0 ) {
        $query = $gdb[$sid]->SuperQuery( $qList[$vgs]["getCharacterInfo"], array("charID" => $charID) );
        if ( $gdb[$sid]->num_rows($query) == 1 ) {
            $char_data = $gdb[$sid]->fetch( $query );
            if ( strtolower($char_data["account_name"]) != strtolower($controller->GetName()) ) {
                unset( $char_data );
            } else {
                $_char_fail = false;
            }
        }
    }
    /*        if ($charID == 0)
    $charID = -1;*/
    if ( $_char_fail == false ) {
        $cache = $controller->GetCache( "l2char_{$charID}_s{$sid}" );
        if ( $cache ) {
            $profile = $cache;
        } else {
            if ( $char_data["accesslevel"] >= 0 ) {
                /**************************
                * items paperdoll
                **************************/
                $query_paperdoll = $gdb[$sid]->SuperQuery( $qList[$vgs]["getCharInventory"], array("charID" => $charID, "loc" => "PAPERDOLL") );
                $paperdoll = "";
                while ( $paperdoll_data = $gdb[$sid]->fetch($query_paperdoll) ) {
                    $name = ( $paperdoll_data["armorName"] != "" ) ? $paperdoll_data["armorName"]:( ($paperdoll_data["weaponName"] != "") ? $paperdoll_data["weaponName"]:$paperdoll_data["etcName"] );
                    $name = str_replace( "'", "\\'", $name );
                    $grade = ( $paperdoll_data["armorType"] != "" ) ? ( (strtolower($paperdoll_data["armorType"]) == "none") ? "ng":$paperdoll_data["armorType"] ):( ($paperdoll_data["weaponType"] != "") ? ((strtolower($paperdoll_data["weaponType"]) == "none") ? "ng":$paperdoll_data["weaponType"]):"" );
                    $grade = ( !empty($grade) ) ? '<img border=0 src='.TPLDIR.'/images/grade/grade_'.$grade.'.gif>':"";
                    $enchant = $paperdoll_data["enchant_level"] > 0 ? " +".$paperdoll_data["enchant_level"]:"";
                    $count = $controller->CountFormat( $paperdoll_data["count"] );
                    $img = ( $controller->IsImage($paperdoll_data["item_id"]) ) ? $paperdoll_data["item_id"]:"blank";
                    $type = $qList[$vgs]["itemType"][$paperdoll_data["loc_data"]];
                    $paperdoll .= "<div id='item' class='{$type}'><img border='0' src='".HTTP_HOME_URL."/items/{$img}.gif' onmouseover=\"Tip('{$name} {$count} {$enchant} {$grade}', FONTCOLOR, '#333333',BGCOLOR, '#FFFFFF', BORDERCOLOR, '#666666', FADEIN, 500, FADEOUT, 500, FONTWEIGHT, 'bold')\"></div>\n";
                }

                /**************************
                * items inventory
                **************************/
                $query_inventory = $gdb[$sid]->SuperQuery( $qList[$vgs]["getCharInventory"], array("charID" => $charID, "loc" => "INVENTORY") );
                $inv = "";
                while ( $inv_data = $gdb[$sid]->fetch($query_inventory) ) {
                    $name = ( $inv_data["armorName"] != "" ) ? $inv_data["armorName"]:( ($inv_data["weaponName"] != "") ? $inv_data["weaponName"]:$inv_data["etcName"] );
                    $name = str_replace( "'", "\\'", $name );
                    $grade = ( $inv_data["armorType"] != "" ) ? ( (strtolower($inv_data["armorType"]) == "none") ? "ng":$inv_data["armorType"] ):( ($inv_data["weaponType"] != "") ? ((strtolower($inv_data["weaponType"]) == "none") ? "ng":$inv_data["weaponType"]):"" );
                    $grade = ( !empty($grade) ) ? '<img border=0 src='.TPLDIR.'/images/grade/grade_'.$grade.'.gif>':"";
                    $enchant = $inv_data["enchant_level"] > 0 ? " +".$inv_data["enchant_level"]:"";
                    $count = ( $inv_data["count"] > 1 ) ? "(".$controller->CountFormat( $inv_data["count"] ).")":"";
                    $img = ( $controller->IsImage($inv_data["item_id"]) ) ? $inv_data["item_id"]:"blank";
                    $inv .= "<img class='floated' border='0' src='".HTTP_HOME_URL."/items/{$img}.gif' onmouseover=\"Tip('{$name} {$count} {$enchant} {$grade}', FONTCOLOR, '#333333',BGCOLOR, '#FFFFFF', BORDERCOLOR, '#666666', FADEIN, 500, FADEOUT, 500, FONTWEIGHT, 'bold')\">\n";
                }

                $tpl->LoadView( "character" );
                $tpl->Set( "prof", "<img src='".TPLDIR."/images/prof/{$char_data["base_class"]}.gif'>" );
                $tpl->Set( "charname", $char_data["char_name"] );
                $tpl->Set( "sex", "<img src='".TPLDIR."/images/face/{$char_data["race"]}_{$char_data["sex"]}.gif'>" );
                $tpl->Set( "race", $raceList[$char_data["race"]] );
                $tpl->Set( "level", $char_data["level"] );
                $tpl->Set( 'cp', $char_data["maxCp"] );
                $tpl->Set( 'hp', $char_data["maxHp"] );
                $tpl->Set( 'mp', $char_data["maxMp"] );
                $tpl->Set( 'pvp', $char_data["pvpkills"] );
                $tpl->Set( 'pk', $char_data["pkkills"] );
                $tpl->Set( 'karma', $char_data["karma"] );
                $tpl->Set( 'str', $char_data["STR"] );
                $tpl->Set( 'dex', $char_data["DEX"] );
                $tpl->Set( 'con', $char_data["CON"] );
                $tpl->Set( 'int', $char_data["_INT"] );
                $tpl->Set( 'wit', $char_data["WIT"] );
                $tpl->Set( 'men', $char_data["MEN"] );
                $tpl->Set( 'exp', $char_data["exp"] );
                $tpl->Set( 'sp', $char_data["sp"] );
                $tpl->Set( 'paperdoll', $paperdoll );
                $tpl->Set( 'inventory', $inv );
                $tpl->Build( "l2character" );
                $profile = $tpl->GetResult( "l2character" );
                if ( $l2cfg["cache"]["enable"] and $l2cfg["cache"]["char"] ) {
                    $controller->SetCache( "l2char_{$charID}_s{$sid}", $profile, $l2cfg["cache"]["char"] );
                }
            } else {
                $profile = "<div class='error'>{$lang["chars_12"]}</div>";
            }
        }
    }
} else
    exit;
?>