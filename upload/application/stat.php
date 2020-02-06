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

$_url = ( $l2cfg["mod_rewrite"] ) ? HTTP_HOME_URL.'/stat':HTTP_HOME_URL.'/index.php?f=stat';

$_act = isset( $_REQUEST['act'] ) ? $controller->SafeData( $_REQUEST["act"], 1 ):'';

$l2_content = "";

function stat_menu()
{
    global $tpl, $l2cfg, $sid, $gsList, $_url;

    $l2_servers = "";
    if ( count($gsList) > 1 ) {
        foreach ( $gsList as $i ) {
            if ( $l2cfg["gs"][$i]["stat"]["enable"] ) {
                $link = ( $l2cfg["mod_rewrite"] ) ? '/s'.$i:'&sid='.$i;
                $class = ( $sid == $i ) ? ' class="activ"':'';
                $l2_servers .= " <a href='{$_url}{$link}'{$class}>".$l2cfg["gs"][$i]["title"]."</a>";
            }
        }
    }
    $tpl->LoadView( "statistic_menu" );
    $tpl->Set( "l2servers", $l2_servers );
    //general
    if ( $l2cfg["gs"][$sid]["stat"]["general"] ) {
        $tpl->Block( 'general' );
        $tpl->Set( 'link_general', $_url.(($l2cfg["mod_rewrite"]) ? '/general/s'.$sid:'&act=general&sid='.$sid) );
    } else
        $tpl->Block( 'general', false );
    //online
    if ( $l2cfg["gs"][$sid]["stat"]["online"] ) {
        $tpl->Block( 'online' );
        $tpl->Set( 'link_online', $_url.(($l2cfg["mod_rewrite"]) ? '/online/s'.$sid:'&act=online&sid='.$sid) );
    } else
        $tpl->Block( 'online', false );
    //top
    if ( $l2cfg["gs"][$sid]["stat"]["top"] ) {
        $tpl->Block( 'top' );
        $tpl->Set( 'link_top', $_url.(($l2cfg["mod_rewrite"]) ? '/top/s'.$sid:'&act=top&sid='.$sid) );
    } else
        $tpl->Block( 'top', false );
    //pvp
    if ( $l2cfg["gs"][$sid]["stat"]["pvp"] ) {
        $tpl->Block( 'pvp' );
        $tpl->Set( 'link_pvp', $_url.(($l2cfg["mod_rewrite"]) ? '/pvp/s'.$sid:'&act=pvp&sid='.$sid) );
    } else
        $tpl->Block( 'pvp', false );
    //pk
    if ( $l2cfg["gs"][$sid]["stat"]["pk"] ) {
        $tpl->Block( 'pk' );
        $tpl->Set( 'link_pk', $_url.(($l2cfg["mod_rewrite"]) ? '/pk/s'.$sid:'&act=pk&sid='.$sid) );
    } else
        $tpl->Block( 'pk', false );
    //clan
    if ( $l2cfg["gs"][$sid]["stat"]["clan"] ) {
        $tpl->Block( 'clan' );
        $tpl->Set( 'link_clan', $_url.(($l2cfg["mod_rewrite"]) ? '/clan/s'.$sid:'&act=clan&sid='.$sid) );
    } else
        $tpl->Block( 'clan', false );
    //castles
    if ( $l2cfg["gs"][$sid]["stat"]["castles"] ) {
        $tpl->Block( 'castles' );
        $tpl->Set( 'link_castles', $_url.(($l2cfg["mod_rewrite"]) ? '/castles/s'.$sid:'&act=castles&sid='.$sid) );
    } else
        $tpl->Block( 'castles', false );
    //epic
    if ( $l2cfg["gs"][$sid]["stat"]["epic"] ) {
        $tpl->Block( 'epic' );
        $tpl->Set( 'link_epic', $_url.(($l2cfg["mod_rewrite"]) ? '/epic/s'.$sid:'&act=epic&sid='.$sid) );
    } else
        $tpl->Block( 'epic', false );
    //raid
    if ( $l2cfg["gs"][$sid]["stat"]["raid"] ) {
        $tpl->Block( 'raid' );
        $tpl->Set( 'link_raid', $_url.(($l2cfg["mod_rewrite"]) ? '/raid/s'.$sid:'&act=raid&sid='.$sid) );
    } else
        $tpl->Block( 'raid', false );
    //olympiad
    if ( $l2cfg["gs"][$sid]["stat"]["olympiad"] ) {
        $tpl->Block( 'olympiad' );
        $tpl->Set( 'link_olympiad', $_url.(($l2cfg["mod_rewrite"]) ? '/olympiad/s'.$sid:'&act=olympiad&sid='.$sid) );
    } else
        $tpl->Block( 'olympiad', false );
    //rich
    if ( $l2cfg["gs"][$sid]["stat"]["rich"] ) {
        $tpl->Block( 'rich' );
        $tpl->Set( 'link_rich', $_url.(($l2cfg["mod_rewrite"]) ? '/rich/s'.$sid:'&act=rich&sid='.$sid) );
    } else
        $tpl->Block( 'rich', false );

    $tpl->Build( "content" );
}
/**
 * ================================
 * 		Main stat page
 * ================================ 
 */
if ( $_act == '' ) {
    stat_menu();
}
/**
 * ================================
 * 		General Statistic
 * ================================ 
 */
if ( ($_act == "general") ) {

    if ( !$l2cfg["gs"][$sid]["stat"]["general"] or !$l2cfg["gs"][$sid]["stat"]["enable"] ) {
        $controller->redirect( $_url );
    }

    $general = $controller->GetCache( "l2stat_general_s{$sid}" );

    if ( $general ) {
        $tpl->SetResult( 'content', $general );
    } else {

        stat_menu();

        $db->gdb( $sid );
        $db->ldb( $lid );

        $online = $gdb[$sid]->result( $gdb[$sid]->query("SELECT count(0) FROM `characters` WHERE `online`>'0'"), 0 );
        if ( $l2cfg["gs"][$sid]["fake"]["enable"] ) {
            $online = intval( $online * (1 + $l2cfg["gs"][$sid]["fake"]["percent"] / 100) );
        }
        $login = $controller->GetStatus( $l2cfg["ls"][$lid]["host"], $l2cfg["ls"][$lid]["port"] );
        $game = $controller->GetStatus( $l2cfg["gs"][$sid]["host"], $l2cfg["gs"][$sid]["port"] );

        $countAcc = $ldb[$lid]->SuperResult( $qList[$vls]["getCountAccounts"], array("where" => "") );
        $countChars = $gdb[$sid]->SuperResult( $qList[$vgs]["getCountCharacters"], array("where" => "") );
        $countClans = $gdb[$sid]->SuperResult( $qList[$vgs]["getCountClans"] );

        $countHuman = ( $countChars > 0 ) ? round( $gdb[$sid]->SuperResult($qList[$vgs]["getCountHuman"]) / ($countChars / 100) ):0;
        $countElf = ( $countChars > 0 ) ? round( $gdb[$sid]->SuperResult($qList[$vgs]["getCountElf"]) / ($countChars / 100) ):0;
        $countDElf = ( $countChars > 0 ) ? round( $gdb[$sid]->SuperResult($qList[$vgs]["getCountDElf"]) / ($countChars / 100) ):0;
        $countOrc = ( $countChars > 0 ) ? round( $gdb[$sid]->SuperResult($qList[$vgs]["getCountOrc"]) / ($countChars / 100) ):0;
        $countDwarf = ( $countChars > 0 ) ? round( $gdb[$sid]->SuperResult($qList[$vgs]["getCountDwarf"]) / ($countChars / 100) ):0;
        $countKamael = ( $countChars > 0 ) ? round( $gdb[$sid]->SuperResult($qList[$vgs]["getCountKamael"]) / ($countChars / 100) ):0;

        $countDawn = $gdb[$sid]->SuperResult( $qList[$vgs]["getCountDawn"] );
        $countDusk = $gdb[$sid]->SuperResult( $qList[$vgs]["getCountDusk"] );
        $totalSS = $countDawn + $countDusk;
        $countDawn_p = ( $countDawn > 0 ) ? round( $countDawn / ($totalSS / 100) ):0;
        $countDusk_p = ( $countDusk > 0 ) ? round( $countDusk / ($totalSS / 100) ):0;

        $tpl->LoadView( "statistic" );
        $tpl->Set( 'ServerName', $l2cfg["gs"][$sid]["title"] );
        $tpl->Set( 'exp', $l2cfg["gs"][$sid]["rates"]["exp"] );
        $tpl->Set( 'sp', $l2cfg["gs"][$sid]["rates"]["sp"] );
        $tpl->Set( 'adena', $l2cfg["gs"][$sid]["rates"]["adena"] );
        $tpl->Set( 'items', $l2cfg["gs"][$sid]["rates"]["items"] );
        $tpl->Set( 'spoil', $l2cfg["gs"][$sid]["rates"]["spoil"] );
        $tpl->Set( 'quests', $l2cfg["gs"][$sid]["rates"]["quest"] );
        $tpl->Set( 'login', $login );
        $tpl->Set( 'game', $game );
        $tpl->Set( 'online', $online );
        $tpl->Set( 'accounts', $countAcc );
        $tpl->Set( 'characters', $countChars );
        $tpl->Set( 'clans', $countClans );
        $tpl->Set( 'human', $countHuman );
        $tpl->Set( 'elf', $countElf );
        $tpl->Set( 'delf', $countDElf );
        $tpl->Set( 'orc', $countOrc );
        $tpl->Set( 'dwarf', $countDwarf );
        $tpl->Set( 'kamael', $countKamael );
        $tpl->Set( 'dawn', $countDawn );
        $tpl->Set( 'dusk', $countDusk );
        $tpl->Set( 'dawnpc', $countDawn_p );
        $tpl->Set( 'duskpc', $countDusk_p );
        $tpl->Build( 'content' );
        if ( $l2cfg["cache"]["enable"] and $l2cfg["cache"]["stat"] ) {
            $controller->SetCache( "l2stat_general_s{$sid}", $tpl->GetResult('content'), $l2cfg["cache"]["stat"] );
        }
    }
}
/**
 * ================================
 * 		Top/PvP/PK/Online Players
 * 		Clan view 
 * ================================ 
 */
if ( $_act == "online" or $_act == "top" or $_act == "pvp" or $_act == "pk" or $_act == "clanview" ) {

    if ( !$l2cfg["gs"][$sid]["stat"][$_act] or !$l2cfg["gs"][$sid]["stat"]["enable"] ) {
        $controller->redirect( $_url );
    }

    $clanid = isset( $_REQUEST["clan"] ) ? intval( $_REQUEST["clan"] ):'';

    $data = $controller->GetCache( "l2stat_{$_act}{$clanid}_s{$sid}" );

    if ( $data ) {
        $tpl->SetResult( 'content', $data );
    } else {

        stat_menu();

        $db->gdb( $sid );

        switch ( $_act ) {
            case 'online':
                {
                    $l2_content = "<div class='l2title'>..:: ".$l2cfg["gs"][$sid]["title"]." - {$lang["stat_nowplay"]} ::..</div>";
                    $sel = $gdb[$sid]->SuperQuery( $qList[$vgs]["getOnline"] );
                    break;
                }
            case 'top':
                {
                    $l2_content = "<div class='l2title'>..:: ".$l2cfg["gs"][$sid]["title"]." - {$lang["stat_top"]} ".$l2cfg["gs"][$sid]["stat"]["count"]." {$lang["stat_players"]} ::..</div>";
                    $sel = $gdb[$sid]->SuperQuery( $qList[$vgs]["getTop"], array("order" => "exp", "limit" => $l2cfg["gs"][$sid]["stat"]["count"]) );
                    break;
                }
            case 'pvp':
                {
                    $l2_content = "<div class='l2title'>..:: ".$l2cfg["gs"][$sid]["title"]." - {$lang["stat_top"]} ".$l2cfg["gs"][$sid]["stat"]["count"]." PvP ::..</div>";
                    $sel = $gdb[$sid]->SuperQuery( $qList[$vgs]["getTop"], array("order" => "pvpkills", "limit" => $l2cfg["gs"][$sid]["stat"]["count"]) );
                    break;
                }
            case 'pk':
                {
                    $l2_content = "<div class='l2title'>..:: ".$l2cfg["gs"][$sid]["title"]." - {$lang["stat_top"]} ".$l2cfg["gs"][$sid]["stat"]["count"]." PK ::..</div>";
                    $sel = $gdb[$sid]->SuperQuery( $qList[$vgs]["getTop"], array("order" => "pkkills", "limit" => $l2cfg["gs"][$sid]["stat"]["count"]) );
                    break;
                }
            case 'clanview':
                {
                    if ( $clanid == 0 or $clanid == '' )
                        $clanid = -1;
                    $sel = $gdb[$sid]->SuperQuery( $qList[$vgs]["getClan"], array("clanid" => $clanid) );
                    $TopClan = $gdb[$sid]->fetch( $sel );
                    $l2_content = "<div class='l2title'>..:: ".$l2cfg["gs"][$sid]["title"]." - {$lang["stat_clanmem"]} ".$TopClan["clan_name"]." ::..</div>";
                    $sel = $gdb[$sid]->SuperQuery( $qList[$vgs]["getClanCharacters"], array("clanid" => $clanid) );
                    break;
                }
        }

        $l2_content .= "
			<table id='l2top' cellpadding='0' cellspacing='0'>
			<thead>
			<tr>
				<th width='25px'></th>
				<th class='name'>{$lang["stat_nick"]}</th>
				<th>{$lang["stat_clan"]}</th>
				<th>PvP/PK</th>
				<th>{$lang["stat_gametime"]}</th>
				<th>{$lang["stat_status"]}</th>
			</tr>
			</thead>";

        if ( $gdb[$sid]->num_rows($sel) == 0 ) {
            $l2_content .= "<tr><td colspan='6'><div class='error'>{$lang["noresults"]}</div></td></tr>";
        } else {
            $nn = 0;
            while ( $CharData = $gdb[$sid]->fetch($sel) ) {
                $trClass = $nn++ % 2 ? "":"trRowA";
                $onlinetime = $controller->OnlineTime( $CharData["onlinetime"] );
                $sex = $CharData["sex"] ? "female":"male";
                $online = $CharData["online"] ? "<span class='l2online'>Online</span>":"<span class='l2offline'>Offline</span>";
                $_link = $_url.( ($l2cfg["mod_rewrite"]) ? "/clanview/s{$sid}/clan{$CharData["clan_id"]}":"&act=clanview&clan={$CharData["clan_id"]}&sid={$sid}" );
                $clan = $CharData["clan_name"] ? "<a href='{$_link}'>{$CharData["clan_name"]}</a>":$lang['stat_noclan'];

                $l2_content .= "
			<tr class='{$trClass}'>
				<td>{$nn}.</td>
				<td class='name'><span class='{$sex}'><b>".$CharData["char_name"]."</b></span><br />
					<small>{$CharData["ClassName"]}, {$CharData["level"]}</small>
				</td>
				<td>{$clan}</td>
				<td><span class='pvp'>{$CharData["pvpkills"]}</span> / <span class='pk'>{$CharData["pkkills"]}</span></td>
				<td>{$onlinetime}</td>
				<td>{$online}</td>
			</tr>";
            }
        }
        $l2_content .= "</table>";
        $tpl->SetResult( 'content', $l2_content );
        if ( $l2cfg["cache"]["enable"] and $l2cfg["cache"][$_act] ) {
            $controller->SetCache( "l2stat_{$_act}{$clanid}_s{$sid}", $tpl->GetResult('content'), $l2cfg["cache"][$_act] );
        }
    }

}
/**
 * ================================
 * 		Clan Top
 * ================================ 
 */
if ( $_act == "clan" ) {

    if ( !$l2cfg["gs"][$sid]["stat"]['clan'] or !$l2cfg["gs"][$sid]["stat"]["enable"] ) {
        $controller->redirect( $_url );
    }

    $data = $controller->GetCache( "l2stat_clan_s{$sid}" );

    if ( $data ) {
        $tpl->SetResult( 'content', $data );
    } else {

        stat_menu();

        $db->gdb( $sid );

        $sel_clan = $gdb[$sid]->SuperQuery( $qList[$vgs]["getTopClan"], array("limit" => $l2cfg["gs"][$sid]["stat"]["count"]) );

        $l2_content = "
			<div class='l2title'>..:: ".$l2cfg["gs"][$sid]["title"]." - {$lang["stat_top"]} ".$l2cfg["gs"][$sid]["stat"]["count"]." {$lang["stat_clans"]} ::..</div>
			<table id='l2top' cellpadding='0' cellspacing='0'>
			<thead>
			<tr>
				<th width='25px'></th>
				<th class='name' width=''>{$lang["stat_clan"]}</th>
				<th width='60px'>{$lang["stat_level"]}</th>
				<th width='60px'>{$lang["stat_castle"]}</th>
				<th width='60px'>{$lang["stat_players"]}</th>
				<th width='60px'>{$lang["stat_reputation"]}</th>
				<th width='100px'>{$lang["stat_alliance"]}</th>
			</tr>
			</thead>";

        if ( $gdb[$sid]->num_rows($sel_clan) == 0 ) {
            $l2_content .= "<tr><td colspan='6'><div class='error'>{$lang["noresults"]}</div></td></tr>";
        } else {
            $nn = 0;
            while ( $TopClan = $gdb[$sid]->fetch($sel_clan) ) {
                $trClass = $nn++ % 2 ? "":"trRowA";
                $clan_name = htmlspecialchars( $TopClan["clan_name"] );
                $castle = $controller->getCastleName( $TopClan["hasCastle"] );
                $TopClan["ally_name"] = ( $TopClan["ally_name"] == null ) ? "&nbsp":$TopClan["ally_name"];
                $_link = $_url.( ($l2cfg["mod_rewrite"]) ? "/clanview/s{$sid}/clan{$TopClan["clan_id"]}":"&act=clanview&clan={$TopClan["clan_id"]}&sid={$sid}" );
                $l2_content .= "
				<tr class='{$trClass}'>
					<td>{$nn}.</td>
					<td class='name'><a class='male' href='{$_link}'><b>{$clan_name}</b></a><br><small>{$lang["stat_leader"]}: {$TopClan["char_name"]}</small></td>
					<td>{$TopClan["clan_level"]}</td>
					<td>{$castle}</td>
					<td>{$TopClan["ccount"]}</td>
					<td>{$TopClan["reputation_score"]}</td>
					<td>{$TopClan["ally_name"]}</td>
				</tr>";
            }
        }
        $l2_content .= "</table>";
        $tpl->SetResult( 'content', $l2_content );
        if ( $l2cfg["cache"]["enable"] and $l2cfg["cache"]["clan"] ) {
            $controller->SetCache( "l2stat_clan_s{$sid}", $tpl->GetResult('content'), $l2cfg["cache"]["clan"] );
        }
    }
}
/**
 * ================================
 * 		Castle Status
 * ================================ 
 */
if ( $_act == "castles" ) {

    if ( !$l2cfg["gs"][$sid]["stat"]['castles'] or !$l2cfg["gs"][$sid]["stat"]["enable"] ) {
        $controller->redirect( $_url );
    }

    $data = $controller->GetCache( "l2stat_castles_s{$sid}" );

    if ( $data ) {
        $tpl->SetResult( 'content', $data );
    } else {

        stat_menu();

        $db->gdb( $sid );

        $sel_castles = $gdb[$sid]->SuperQuery( $qList[$vgs]["getCastles"] );

        $tpl->SetResult( 'content', "<div class='l2title'>..:: ".$l2cfg["gs"][$sid]["title"]." - {$lang["stat_castles"]} ::..</div>" );

        if ( $gdb[$sid]->num_rows($sel_castles) == 0 ) {
            $tpl->SetResult( 'content', "<div class='error'>{$lang["noresults"]}</div>" );
        } else {
            $castles = array();
            while ( $castle_data = $gdb[$sid]->fetch($sel_castles) ) {

                $Defenders = "";
                $Attackers = "";

                $siege = $gdb[$sid]->SuperQuery( $qList[$vgs]["getSiege"], array("castle" => $castle_data["id"]) );

                while ( $siege_data = $gdb[$sid]->fetch($siege) ) {
                    $_link = $_url.( ($l2cfg["mod_rewrite"]) ? "/clanview/s{$sid}/clan{$siege_data["clan_id"]}":"&act=clanview&clan={$siege_data["clan_id"]}&sid={$sid}" );
                    ${( $siege_data["type"] ) ? "Attackers":"Defenders"} .= "<a href='{$_link}'>".htmlspecialchars( $siege_data["clan_name"] )."</a> &nbsp ";
                }

                $_link = $_url.( ($l2cfg["mod_rewrite"]) ? "/clanview/s{$sid}/clan{$castle_data["clan_id"]}":"&act=clanview&clan={$castle_data["clan_id"]}&sid={$sid}" );
                $castles[strtolower( $castle_data['name'] )] = array( 'Tax' => intval($castle_data["taxPercent"]), 'SiegeDate' => $controller->DateFormat($castle_data["siegeDate"], TIMEZONE), 'Owner' => ($castle_data["clan_name"]) ? "<a href='{$_link}'>".htmlspecialchars($castle_data["clan_name"])."</a>":"NPC", 'Attackers' => $Attackers, 'Defenders' => $Defenders );
            }

            $tpl->LoadView( "statistic_castle" );
            foreach ( $castles as $key => $val ) {
                $tpl->Set( $key.'Tax', $val['Tax'] );
                $tpl->Set( $key.'SiegeDate', $val['SiegeDate'] );
                $tpl->Set( $key.'Owner', $val['Owner'] );
                $tpl->Set( $key.'Attackers', $val['Attackers'] );
                $tpl->Set( $key.'Defenders', $val['Defenders'] );
            }
            $tpl->Build( 'content' );
        }

        if ( $l2cfg["cache"]["enable"] and $l2cfg["cache"]["castle"] ) {
            $controller->SetCache( "l2stat_castles_s{$sid}", $tpl->GetResult('content'), $l2cfg["cache"]["castle"] );
        }
    }
}
/**
 * ================================
 * 		Raid/Epic Boss Status
 * ================================ 
 */
if ( $_act == "epic" or $_act == "raid" ) {

    if ( !$l2cfg["gs"][$sid]["stat"][$_act] or !$l2cfg["gs"][$sid]["stat"]["enable"] ) {
        $controller->redirect( $_url );
    }

    $data = $controller->GetCache( "l2stat_{$_act}_s{$sid}" );

    if ( $data ) {
        $tpl->SetResult( 'content', $data );
    } else {

        stat_menu();

        $db->gdb( $sid );

        if ( $_act == "epic" ) {
            $l2_content = "<div class='l2title'>..:: ".$l2cfg["gs"][$sid]["title"]." - {$lang["stat_epic"]}  ::..</div>";
            $sel_boss = $gdb[$sid]->SuperQuery( $qList[$vgs]["getEpicStatus"] );
        }
        if ( $_act == "raid" ) {
            $l2_content = "<div class='l2title'>..:: ".$l2cfg["gs"][$sid]["title"]." - {$lang["stat_raid"]}  ::..</div>";
            $sel_boss = $gdb[$sid]->SuperQuery( $qList[$vgs]["getRaidStatus"] );
        }

        $l2_content .= "
			<table id='l2top' cellpadding='0' cellspacing='0'>
			<thead>
			<tr>
				<th width='35'></th>
				<th class='name' width=''>{$lang["stat_boss"]}</th>
				<th width='60px'>{$lang["stat_level"]}</th>
				<th width='60px'>{$lang["stat_status"]}</th>
			</tr>
			</thead>";

        if ( $gdb[$sid]->num_rows($sel_boss) == 0 ) {
            $l2_content .= "<tr><td colspan='4'><div class='error'>{$lang["noresults"]}</div></td></tr>";
        } else {
            $nn = 0;
            while ( $BossStatus = $gdb[$sid]->fetch($sel_boss) ) {
                $trClass = $nn++ % 2 ? "":"trRowA";
                $status = ( $BossStatus["respawn_time"] == 0 ) ? $lang['stat_alive']:$lang['stat_dead'];
                $nameClass = ( $BossStatus["respawn_time"] == 0 ) ? "male":"female";
                $l2_content .= "
				<tr class='{$trClass}'>
					<td>{$nn}.</td>
					<td class='name'><span class='{$nameClass}'>{$BossStatus["name"]}</span></td>
					<td>{$BossStatus["level"]}</td>
					<td>{$status}</td>
				</tr>";
            }
        }
        $l2_content .= "</table>";
        $tpl->SetResult( 'content', $l2_content );
        if ( $l2cfg["cache"]["enable"] and $l2cfg["cache"][$_act] ) {
            $controller->SetCache( "l2stat_{$_act}_s{$sid}", $tpl->GetResult('content'), $l2cfg["cache"][$_act] );
        }
    }
}
/**
 * ================================
 * 		Olympiad Status
 * ================================ 
 */
if ( $_act == "olympiad" ) {

    if ( !$l2cfg["gs"][$sid]["stat"]["olympiad"] or !$l2cfg["gs"][$sid]["stat"]["enable"] ) {
        $controller->redirect( $_url );
    }

    $data = $controller->GetCache( "l2stat_olympiad_s{$sid}" );

    if ( $data ) {
        $tpl->SetResult( 'content', $data );
    } else {

        stat_menu();

        $db->gdb( $sid );

        $sel_oly = $gdb[$sid]->SuperQuery( $qList[$vgs]["getOlympiad"] );

        $l2_content = "
			<div class='l2title'>..:: ".$l2cfg["gs"][$sid]["title"]." - {$lang["stat_oly"]} ::..</div>
			<table id='l2top' cellpadding='0' cellspacing='0'>
			<thead>
			<tr>
				<th width='35'></th>
				<th class='name' width=''>{$lang["stat_nick"]}</th>
				<th width='60'>{$lang["stat_points"]}</th>
				<th width='100'>{$lang["stat_cdone"]}</th>
				<th width='150'>{$lang["stat_class"]}</th>
			</tr>
			</thead>";

        if ( $gdb[$sid]->num_rows($sel_oly) == 0 ) {
            $l2_content .= "<tr><td colspan='5'><div class='error'>{$lang["noresults"]}</div></td></tr>";
        } else {
            $nn = 0;
            while ( $olymp_data = $gdb[$sid]->fetch($sel_oly) ) {
                $trClass = $nn++ % 2 ? "":"trRowA";
                $sex = $olymp_data["sex"] ? "female":"male";
                $l2_content .= "
				<tr class='{$trClass}'>
					<td>{$nn}</td>
					<td class='name'><span class='{$sex}'><b>{$olymp_data["char_name"]}</b></span></td>
					<td>{$olymp_data["olympiad_points"]}</td>
					<td>{$olymp_data["competitions_done"]}</td>
					<td>{$olymp_data["ClassName"]}</td>
				</tr>";
            }
        }
        $l2_content .= "</table>";
        $tpl->SetResult( 'content', $l2_content );
        if ( $l2cfg["cache"]["enable"] and $l2cfg["cache"]["oly"] ) {
            $controller->SetCache( "l2stat_olympiad_s{$sid}", $tpl->GetResult('content'), $l2cfg["cache"]["oly"] );
        }
    }
}
/**
 * ================================
 * 		Top Rich 
 * ================================ 
 */
if ( $_act == "rich" ) {

    if ( !$l2cfg["gs"][$sid]["stat"]["rich"] or !$l2cfg["gs"][$sid]["stat"]["enable"] ) {
        $controller->redirect( $_url );
    }

    $data = $controller->GetCache( "l2stat_rich_s{$sid}" );

    if ( $data ) {
        $tpl->SetResult( 'content', $data );
    } else {

        stat_menu();

        $db->gdb( $sid );

        $l2_content = "<div class='l2title'>..:: ".$l2cfg["gs"][$sid]["title"]." - {$lang["stat_top"]} ".$l2cfg["gs"][$sid]["stat"]["count"]." {$lang["stat_rich"]} ::..</div>";
        $sel_rich = $gdb[$sid]->SuperQuery( $qList[$vgs]["getRich"], array("item_id" => 57, "limit" => $l2cfg["gs"][$sid]["stat"]["count"]) );

        $l2_content .= "
			<table id='l2top' cellpadding='0' cellspacing='0'>
			<thead>
			<tr>
				<th width='25px'></th>
				<th class='name'>{$lang["stat_nick"]}</th>
				<th>{$lang["stat_clan"]}</th>
				<th>Adena</th>
				<th>{$lang["stat_gametime"]}</th>
				<th>{$lang["stat_status"]}</th>
			</tr>
			</thead>";

        if ( $gdb[$sid]->num_rows($sel_rich) == 0 ) {
            $l2_content .= "<tr><td colspan='6'><div class='error'>{$lang["noresults"]}</div></td></tr>";
        } else {
            $nn = 0;
            while ( $CharData = $gdb[$sid]->fetch($sel_rich) ) {
                $trClass = $nn++ % 2 ? "":"trRowA";
                $onlinetime = $controller->OnlineTime( $CharData["onlinetime"] );
                $sex = $CharData["sex"] ? "female":"male";
                $online = $CharData["online"] ? "<span class='l2online'>Online</span>":"<span class='l2offline'>Offline</span>";
                $_link = $_url.( ($l2cfg["mod_rewrite"]) ? "/clanview/s{$sid}/clan{$CharData["clan_id"]}":"&act=clanview&clan={$CharData["clan_id"]}&sid={$sid}" );
                $clan = $CharData["clan_name"] ? "<a href='{$_link}'>{$CharData["clan_name"]}</a>":$lang['stat_noclan'];
                if ( $CharData["count"] > 1 )
                    $CharData["count"] = $controller->CountFormat( $CharData["count"] );

                $l2_content .= "
			<tr class='{$trClass}'>
				<td>{$nn}.</td>
				<td class='name'><span class='{$sex}'><b>".$CharData["char_name"]."</b></span><br />
					<small>{$CharData["ClassName"]}, {$CharData["level"]}</small>
				</td>
				<td>{$clan}</td>
				<td>{$CharData["count"]}</td>
				<td>{$onlinetime}</td>
				<td>{$online}</td>
			</tr>";
            }
        }
        $l2_content .= "</table>";
        $tpl->SetResult( 'content', $l2_content );
        if ( $l2cfg["cache"]["enable"] and $l2cfg["cache"]['rich'] ) {
            $controller->SetCache( "l2stat_rich_s{$sid}", $tpl->GetResult('content'), $l2cfg["cache"]['rich'] );
        }
    }
}
