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
    /**************************
    * teleport
    **************************/
    if ( isset($_POST['do']) and ($_POST['do'] == 'totown') ) {
        $_sid = intval( $_POST["server"] );
        if ( $l2cfg["gs"][$_sid]["teleport"]["enable"] ) {
            $coordinats = array( "1" => array("name" => "Dark Elven Village", "x" => "9745", "y" => "15606", "z" => "-4574"), "2" => array("name" => "Town of Aden", "x" => "147450", "y" => "26741", "z" => "-2204"), "3" => array("name" => "Dwarven Village", "x" => "115113", "y" => "-178212", "z" => "-901"), "4" => array("name" => "Town of Dion", "x" => "15670", "y" => "142983", "z" => "-2705"), "5" => array("name" => "Elven Village", "x" => "46934", "y" => "51467", "z" => "-2977"), "6" => array("name" => "Floran Village", "x" => "17838", "y" => "170274", "z" => "-3508"), "7" => array("name" => "Orc Village", "x" => "-44836", "y" => "-112352", "z" => "-239"), "8" => array("name" => "Town of Giran", "x" => "83400", "y" => "147943", "z" => "-3404"), "9" => array("name" => "Talking Island Village", "x" => "-84318", "y" => "244579", "z" => "-3730"), "10" => array("name" => "Gludin Village", "x" => "-80826", "y" => "149775", "z" => "-3043"), "11" => array("name" => "Town of Gludio", "x" => "-12672", "y" =>
                "122776", "z" => "-3116"), "12" => array("name" => "Heine", "x" => "111322", "y" => "219320", "z" => "-3538"), "13" => array("name" => "Hunters Village", "x" => "117110", "y" => "76883", "z" => "-2695"), "14" => array("name" => "Ivory Tower", "x" => "85337", "y" => "12728", "z" => "-3787"), "15" => array("name" => "Town of Oren", "x" => "82956", "y" => "53162", "z" => "-1495"), "16" => array("name" => "Rune Township", "x" => "43799", "y" => "-47727", "z" => "-798"), "17" => array("name" => "Town of Goddard", "x" => "147928", "y" => "-55273", "z" => "-2734"), "18" => array("name" => "Town of Schuttgart", "x" => "87386", "y" => "-143246", "z" => "-1293"), "19" => array("name" => "Enchat valley", "x" => "122310", "y" => "43087", "z" => "-4537"), );
            $char = intval( $_POST["charID"] );
            $vgs_tmp = $vList[$l2cfg["gs"][$_sid]["version"]];

            $db->gdb( $_sid );

            $r = $gdb[$_sid]->query( "SELECT * FROM `characters` WHERE `{$qList[$vgs_tmp]["fields"]["charID"]}`='{$char}' AND `account_name`='{$controller->GetName()}'" );
            if ( $gdb[$_sid]->num_rows($r) > 0 ) {
                $row = $gdb[$_sid]->fetch( $r );
                if ( ($row["lastteleport"] + 60 * $l2cfg["gs"][$_sid]["teleport"]["time"]) > time() )
                    $tpl->ShowError( "Информация", "{$lang["chars_1"]} ".date("i {$lang["chars_1_m"]} s {$lang["chars_1_s"]}", ($row["lastteleport"] + ($l2cfg["gs"][$_sid]["teleport"]["time"] * 60)) - time())."" );
                elseif ( $row['online'] )
                    $tpl->ShowError( "Ошибка", $lang["chars_2"] );
                elseif ( $row['in_jail'] )
                    $tpl->ShowError( "Ошибка", $lang["chars_3"] );
                else {
                    $x_ch = $row["x"];
                    $y_ch = $row["y"];
                    $z_ch = $row["z"];
                    $s = 1;
                    foreach ( $coordinats as $town ) {
                        $loc[$s] = $town["name"];
                        $x_city[$s] = $town["x"];
                        $y_city[$s] = $town["y"];
                        $z_city[$s] = $town["z"];
                        $result_x[$s] = abs( $x_ch - $x_city[$s] );
                        $result_y[$s] = abs( $y_ch - $y_city[$s] );
                        $result_all[$s] = abs( $result_x[$s] + $result_y[$s] );
                        $s++;
                    }
                    $val = min( $result_all );
                    $t = 1;
                    foreach ( $result_all as $value ) {
                        if ( $value == $val ) {
                            $city_id = $t;
                            break;
                        }
                        $t++;
                    }
                    if ( $city_id != 0 ) {
                        $SQL = "UPDATE `characters` SET x='{$x_city[$city_id]}',`y`='{$y_city[$city_id]}',`z`='{$z_city[$city_id]}',`lastteleport`='".time()."' WHERE `{$qList[$vgs_tmp]["fields"]["charID"]}`='{$char}'";
                        if ( $gdb[$_sid]->query($SQL) )
                            $tpl->ShowError( $lang["message"], "{$lang["chars_4"]} ({$coordinats[$city_id]['name']}).", false );
                        else
                            $tpl->ShowError( $lang["error"], $lang["err_db"] );
                    }
                }
            } else
                $tpl->ShowError( $lang["error"], "Error!" );
        } else
            $tpl->ShowError( $lang["message"], $lang["chars_5"] );
    }
    /**************************
    * characters
    **************************/
    $cache = $controller->GetCache( "l2login_".$controller->GetName()."_".$_lid );
    if ( $cache ) {
        $profile = $cache;
    } else {
        $db->ldb( $_lid );

        $account_data = $ldb[$_lid]->SuperFetchArray( $qList[$_vls]["getAccount"], array("login" => $controller->GetName(), "where" => "") );
        $account_data["lastactive"] = $controller->DateFormat( $account_data["lastactive"], TIMEZONE );
        $profile = "<table cellpadding='0' cellspacing='0' id='l2'>
			<thead>
			<tr>
				<th colspan='6'>{$lang["hello"]}, <b>{$controller->GetName()}</b>!<br>{$lang["chars_6"]} {$account_data["lastactive"]} IP {$account_data["lastIP"]}</th>
			</tr>
			<tr>
				<th width=''>Nick</th>
				<th width=''>Status</th>
				<th width=''>Level</th>
				<th width=''>Game Time</th>
				<th width=''>Last Visit</th>
				<th width=''>Action</th>
			</tr>
			</thead>";

        foreach ( $gsList as $_sid ) {
            $db->gdb( $_sid );

            if ( $l2cfg["gs"][$_sid]["ls"] == $_lid ) {
                $_vgs = $vList[$l2cfg["gs"][$_sid]["version"]];
                $profile .= "
				<tr>
					<th colspan='6' class='serv'>{$l2cfg["gs"][$_sid]["title"]}</th>
				</tr>";

                $query_chars = $gdb[$_sid]->SuperQuery( $qList[$_vgs]["getAccountCharacters"], array("account" => $controller->GetName()) );

                if ( $gdb[$_sid]->num_rows($query_chars) > 0 ) {
                    while ( $char_data = $gdb[$_sid]->fetch($query_chars) ) {
                        if ( $char_data["accesslevel"] < 0 ) {
                            $status = "<span class='l2offline'>{$lang["chars_7"]}</span>";
                        } elseif ( isset( $char_data["in_jail"] ) && $char_data["in_jail"] ) {
                            $status = "<span class='l2offline'>{$lang["chars_8"]}</span>";
                        } else {
                            $status = $char_data["online"] ? "<span class='l2online'>Online</span>":"<span class='l2offline'>Offline</span>";
                        }
                        $char_data["onlinetime"] = round( $char_data["onlinetime"] / 3600 );
                        $char_data["lastAccess"] = $controller->DateFormat( $char_data["lastAccess"], TIMEZONE );
                        $cinfolink = HTTP_HOME_URL.( ($l2cfg["mod_rewrite"]) ? "/cp/char{$char_data["charID"]}/s{$_sid}":"/index.php?f=cp&opt=charinfo&char={$char_data["charID"]}&sid={$_sid}" );
                        $profile .= "
						<tr>
							<td class='name'><a href='{$cinfolink}'>{$char_data["char_name"]}</a><br>{$lang["stat_clan"]}: {$char_data["clan_name"]}</td>
							<td>{$status}</td>
							<td>{$char_data["level"]}</td>
							<td>{$char_data["onlinetime"]}{$lang["chars_9"]}</td>
							<td>{$char_data["lastAccess"]}</td>
							<td>
								<form method='post' action=''>
								<input type='hidden' name='do' value='totown'>
								<input type='hidden' name='server' value='{$_sid}'>
								<input type='hidden' name='charID' value='{$char_data['charID']}'>
								<input type='submit' name='buttondown' id='l2button' value='{$lang["chars_10"]}'/></form>
							</td>
						</tr> ";
                    }
                } else {
                    $profile .= "
					<tr>
						<td colspan='6'><div class='error'>{$lang["chars_11"]}</div></td>
					</tr>";
                }
            }
        }

        $profile .= "</table>";
        if ( $l2cfg["cache"]["enable"] and $l2cfg["cache"]["login"] ) {
            $controller->SetCache( "l2login_".$controller->GetName()."_".$_lid, $profile, $l2cfg["cache"]["login"] );
        }
    }
} else
    exit;
?>