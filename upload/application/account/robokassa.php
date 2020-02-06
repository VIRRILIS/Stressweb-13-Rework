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
    $_act = isset( $_GET["act"] ) ? $controller->SafeData( $_GET["act"], 3 ):"";
    if ( $l2cfg["rb"]["enable"] ) {
        $tpl->LoadView( "robokassa" );

        if ( $_act == "" ) {
            $tpl->Block( 'stage1' );
            $tpl->Block( 'stage2', false );
            while ( $l2cfg["gs"][$sid]["ls"] != $_lid ) {
                $sid++;
            }
            if ( count($gsList) > 1 ) {
                $tpl->Block( 'server' );
                foreach ( $gsList as $i ) {
                    if ( $l2cfg["gs"][$i]["ls"] == $_lid )
                        $tmpServerList[$i] = $l2cfg["gs"][$i]["title"];
                }
                $servSelect = $controller->select( "sid", $tmpServerList, $sid, "style='width: 160px' id='rbsid'" );
                $tpl->Set( "serverList", $servSelect );
                if ( $l2cfg["mod_rewrite"] ) {
                    $tpl->Block( 'remove' );
                    $action_sid = "/cp/robo/s";
                } else {
                    $tpl->Block( 'remove', false );
                    $action_sid = "/index.php";
                }
                $tpl->Set( "action_sid", HTTP_HOME_URL.$action_sid );
            } else
                $tpl->Block( 'server', false );

            $db->gdb( $sid );

            $where = '';
            if ( $l2cfg["gs"][$sid]["rb"]["product"] == "items" and $l2cfg["gs"][$sid]["rb"]["table"] == "items" )
                $where = ' AND online=0';

            $selchars = $gdb[$sid]->query( "SELECT char_name, {$qList[$vList[$l2cfg["gs"][$sid]["version"]]]["fields"]["charID"]} AS charId FROM characters WHERE account_name='{$controller->GetName()}'{$where}" );
            $options = "";
            if ( $gdb[$sid]->num_rows($selchars) > 0 ) {
                while ( $data = $gdb[$sid]->fetch($selchars) ) {
                    $options .= "<option value='{$data["charId"]}'>{$data["char_name"]}</option>";
                }
                $tpl->Block( 'isChar' );
                $tpl->Block( 'noChar', false );
                $rnd = strtoupper( substr(md5(uniqid(microtime(), 1)).getmypid(), 1, 8) );
                $tpl->Set( "charOptions", $options );
                $tpl->Set( "rnd", $rnd );
                $action = ( $l2cfg["mod_rewrite"] ) ? "/cp/bill/{$rnd}/s{$sid}":"/index.php?f=cp&opt=robo&act=bill&rnd={$rnd}&sid={$sid}";
                $tpl->Set( "action", HTTP_HOME_URL.$action );
            } else {
            	$tpl->Block( 'noChar' );
                $tpl->Block( 'isChar', false );
            }
        }
        if ( $_act == "bill" ) {
            $db->gdb( $sid );

            if ( isset($_POST["bill"]) ) {
                $rnd = preg_match( "/^[0-9A-Z]{8}+$/", $_POST["rnd"] ) ? $_POST["rnd"]:$controller->redirect( "index.php?err=bill" );
                $gdb[$sid]->query( "
					INSERT INTO `stress_auto_rb` SET
						`InvId` = '".time()."',
						`RND` = '{$rnd}',
				  		`charId` = '".intval($_POST["char"])."',
				  		`trans_date` = '".time()."',
				  		`user_ip` = '".$gdb[$sid]->safe($_SERVER["REMOTE_ADDR"])."',
				  		`stage` = 'S',
				  		`success` = '0',
				  		`comment` = 'Not Send'
				  	" );
                $controller->redirect();
            }
            $tpl->Block( 'stage1', false );
            $tpl->Block( 'stage2' );
            $i = 0;
            $options = "";
            while ( $i++ <= 499 )
                $options .= "<option value={$i}>{$i}</option>";
            $tpl->Set( "options", $options );
            $rnd = preg_match( "/^[0-9A-Z]{8}+$/", $_GET["rnd"] ) ? $_GET["rnd"]:$controller->redirect( "index.php?err=rnd" );
            $charId = $qList[$vList[$l2cfg["gs"][$sid]["version"]]]["fields"]["charID"];
            $query = $gdb[$sid]->query( "SELECT s.*,c.char_name FROM `stress_auto_rb` s LEFT JOIN `characters` c ON s.charId=c.{$charId} WHERE s.RND='{$rnd}' AND (s.stage='S' OR s.stage='P')" );
            if ( $gdb[$sid]->num_rows($query) == 1 ) {
                $data = $gdb[$sid]->fetch( $query );
                $tpl->Set( "serverName", $l2cfg["gs"][$sid]["title"] );
                $tpl->Set( "charName", $data["char_name"] );
                $tpl->Set( "itemName", $l2cfg["gs"][$sid]["rb"]["money"] );
                $tpl->Set( "valuta", $l2cfg["gs"][$sid]["rb"]["valuta"] );
                $tpl->Set( "order", $data["id"] );
                $tpl->Set( "InvId", $data['InvId'] );
                $tpl->Set( "shpa", $sid );
                $tpl->Set( "shpb", $rnd );
            } else
                $controller->redirect( "index.php?err=table" );
        }
        $tpl->Build( "l2donate" );
        $profile = $tpl->GetResult( "l2donate" );
    } else
        $profile = "<div class='error'>{$lang["don_err_0"]}</div>";
} else
    exit;
?>