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
/**
 * =========================
 * 	Robokassa Result Script
 * ========================= 
 */
if ( !$l2cfg["rb"]["enable"] ) {
    $controller->showMSG( $lang["don_err_0"] );
} else {

    $act_arr = array( "result", "success", "fail" );
    $act = ( isset($_REQUEST["act"]) and in_array($_REQUEST["act"], $act_arr) ) ? $_REQUEST["act"]:"";

    if ( $act == "result" ) {
        if ( isset($_POST['InvId']) and !empty($_POST['InvId']) and isset($_POST['OutSum']) and !empty($_POST['OutSum']) and isset($_POST["shpa"]) and $_POST["shpa"] != '' and isset($_POST["shpb"]) and !empty($_POST["shpb"]) ) {

            // чтение параметров
            $sid = intval( $_POST["shpa"] );
            $lid = in_array( $l2cfg["gs"][$sid]["ls"], $lsList ) ? intval( $l2cfg["gs"][$sid]["ls"] ):reset( $lsList );
            $OutSumm = preg_match( "/^[0-9.]+$/", $_POST["OutSum"] ) ? $_POST["OutSum"]:false;
            $InvId = intval( $_POST["InvId"] );
            $RND = preg_match( "/^[0-9A-Z]{8}+$/", $_POST["shpb"] ) ? $db->safe( $_POST["shpb"] ):false;
            $MD5 = strtoupper( $controller->SafeData($_POST["SignatureValue"], 3) );

            if ( !$OutSumm or !$RND ) {
                $controller->showMSG( 'Error: Incorrect params' );
            } else {
                $db->gdb( $sid );
            }
            if ( !isset($gdb[$sid]) ) {
                $controller->showMSG( "Error: incorrect server! Order: ".$InvId );
            }
            $MD5_2 = strtoupper( md5("$OutSumm:$InvId:{$l2cfg["rb"]["mrhpass2"]}:shpa=$sid:shpb=$RND") );
            if ( $MD5 != $MD5_2 ) {
                $controller->showMSG( "Error: incorrect control sum! Order: ".$InvId );
            }
            $sel = $gdb[$sid]->query( "SELECT * FROM `stress_auto_rb` WHERE `InvId`='{$InvId}' AND `RND`='{$RND}' AND stage='P'" );
            if ( $gdb[$sid]->num_rows($sel) != 1 ) {
                $controller->showMSG( "Error: order does not exist! Order: ".$InvId );
            }
            $data = $gdb[$sid]->fetch( $sel );
            if ( $data["OutSum"] != $OutSumm ) {
                $controller->showMSG( "Error: incorrect sum! Order: ".$InvId );
            }

            $gdb[$sid]->query( "UPDATE `stress_auto_rb` SET `stage`='E',`comment`='Покупка оплачена, но не доставлена' WHERE `InvId`='{$InvId}' AND `RND`='{$RND}'" );

            $success = false;
            
            $charId = $qList[$vgs]["fields"]["charID"];
            $cdata = $gdb[$sid]->fetch( $gdb[$sid]->query("SELECT `account_name`, `online` FROM `characters` WHERE `{$charId}`='{$data["charId"]}'"));

            if ( $l2cfg["gs"][$sid]["rb"]["product"] == "l2money" ) {
                
				$db->ldb( $lid );

                $ldb[$lid]->query( "UPDATE `accounts` SET `l2money`=`l2money`+{$data['OutCount']} WHERE `login`='{$cdata["account_name"]}'" );
                if ( $ldb[$lid]->affected() > 0 )
                    $success = true;
            }

            if ( $l2cfg["gs"][$sid]["rb"]["product"] == "items" ) {

                if ( $l2cfg["gs"][$sid]['rb']['table'] == 'items_delayed' ) {

                    $gdb[$sid]->query( "INSERT INTO `items_delayed` SET `owner_id`='{$data["charId"]}',`item_id`='{$l2cfg["gs"][$sid]["rb"]["item_id"]}',`count`='{$data['OutCount']}',`enchant_level`='0',`attribute`='-1',`attribute_level`='-1',`flags`='0',`payment_status`='0',`description`=''" );
                    if ( $gdb[$sid]->affected() > 0 )
                        $success = true;

                } elseif ( $l2cfg["gs"][$sid]["rb"]["table"] == "character_items" ) {

                    $gdb[$sid]->query( "INSERT INTO `character_items` SET `owner_id`='{$data["charId"]}',`item_id`='{$l2cfg["gs"][$sid]["rb"]["item_id"]}',`count`='{$data['OutCount']}',`enchant_level`='0'" );
                    if ( $gdb[$sid]->affected() > 0 )
                        $success = true;

                } else {

                    if ( $cdata['online'] ) {
                        $controller->showMSG( "Error: character online! Order: ".$InvId );
                    }

                    if ( $gdb[$sid]->num_rows($gdb[$sid]->query("SELECT `count` FROM `items` WHERE `owner_id`='{$data["charId"]}' AND item_id='{$l2cfg["gs"][$sid]["rb"]["item_id"]}' AND `loc`='INVENTORY'")) > 0 ) {
                        $gdb[$sid]->query( "UPDATE `items` SET `count`=`count`+{$data["OutCount"]} WHERE `owner_id`='{$data["charId"]}' AND item_id='{$l2cfg["gs"][$sid]["rb"]["item_id"]}' AND `loc`='INVENTORY'" );
                        if ( $gdb[$sid]->affected() > 0 )
                            $success = true;
                    } else {
                        $obj = $gdb[$sid]->result( $gdb[$sid]->query("SELECT MAX(object_id)+1 FROM items"), 0 );
                        $gdb[$sid]->query( "INSERT INTO `items` SET `owner_id`='{$data["charId"]}',`object_id`='{$obj}',`count`='{$data["OutCount"]}',`item_id`='{$l2cfg["gs"][$sid]["rb"]["item_id"]}',`enchant_level`='0',`loc`='INVENTORY',`loc_data`='0'" );
                        if ( $gdb[$sid]->affected() > 0 )
                            $success = true;
                    }
                }
            }
            if ( $success ) {
                $gdb[$sid]->query( "UPDATE `stress_auto_rb` SET `stage`='F',`success`='1',`comment`='Покупка доставлена' WHERE `InvId`='{$InvId}' AND `RND`='{$RND}'" );
                $controller->showMSG( "OK{$InvId}" );
            } else
                $controller->showMSG( "Error: database error! Order: ".$InvId );

        } else
            $tpl->SetResult( "content", "Bad params" );
    } elseif ( $act == "success" )
        $tpl->SetResult( "content", "<div class='noerror'>{$lang["don_err_1"]}</div>" );
    elseif ( $act == "fail" )
        $tpl->SetResult( "content", "<div class='error'>{$lang["don_err_2"]}</div>" );
    else
        $tpl->SetResult( "content", "404!!!" );
}
?>