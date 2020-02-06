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

/*** Modules list to Load ***/
$SWMODULES = array( 'copyright', 'login', 'poll', 'server', 'pvptop', 'pktop', 'forum' );

/*** Servers list ***/
$vList = array( 1 => "DuoTM", 2 => "EmuRT", 3 => "FirstTeam", 4 => "L2DC", 5 => "L2Dream", 6 => "L2EmuEnterprise", 7 => "L2Evo", 8 => "L2jFree", 9 => "L2jFrozen", 10 => "L2jServer", 11 => "L2NextGen", 12 => "L2Open", 13 => "L2OpenFreya", 14 => "La2Base", 15 => "Phoenix19xxx", 16 => "Phoenix", 17 => "RT", 18 => "Scoria", 19 => "Lucera");

/*** Race list ***/
$raceList = array( 0 => "Человек", 1 => "Светлый эльф", 2 => "Темный эльф", 3 => "Орк", 4 => "Гном", 5 => "Камаэль" );

/*** Language list ***/
$langList = array( "ru" => "Русский", "en" => "Английский" );

function DefVal( &$key, $value )
{
    if ( isset($key) == false )
        $key = $value;
}

// основной конфиг
DefVal( $l2cfg["siteurl"], 'http://'.$_SERVER['HTTP_HOST'] );
DefVal( $l2cfg["title"], "STRESS WEB R13" );
DefVal( $l2cfg["copy"], "ServerName.Ru" );
DefVal( $l2cfg["template"], "default" );
DefVal( $l2cfg["salt"], "secretword" );
DefVal( $l2cfg["lang"], "ru" );
DefVal( $l2cfg["main"]["page"]["static"], false );
DefVal( $l2cfg["main"]["page"]["name"], "index" );
DefVal( $l2cfg["news"]["perpage"], 5 );
DefVal( $l2cfg["news"]["date"], "d.m.y H:i" );
DefVal( $l2cfg["news"]["sort"], "DESC" );
DefVal( $l2cfg["timezone"], 0 );
DefVal( $l2cfg["reg_enable"], true );
DefVal( $l2cfg["reg_multi"], false );
DefVal( $l2cfg["reg_prefix"], false );
DefVal( $l2cfg["reg_activate"], false );
DefVal( $l2cfg["forget_activate"], false );
DefVal( $l2cfg["chpass_activate"], false );
DefVal( $l2cfg["chmail_activate"], false );
DefVal( $l2cfg["server"]["enable"], true );
DefVal( $l2cfg["txt"]["enable"], false );
DefVal( $l2cfg["txt"]["gs"], 1 );
DefVal( $l2cfg["support"]["enable"], false );
DefVal( $l2cfg["mysql"]["debug"], false );
DefVal( $l2cfg["offline"]["enable"], false );
DefVal( $l2cfg["offline"]["reason"], "Сайт находится на текущей реконструкции, после завершения всех работ сайт будет открыт. Приносим вам свои извинения за доставленные неудобства." );
//Код безопасности
DefVal( $l2cfg["captcha"]["admin_type"], 'sw' );
DefVal( $l2cfg["captcha"]["publickey"], '6Lfzms8SAAAAAJP0p0kDViBqNFfyXlGroKaaNE_c' );
DefVal( $l2cfg["captcha"]["privatekey"], '6Lfzms8SAAAAABlkVaBQPY0vM2bipQJMVEhR0Ylx' );
DefVal( $l2cfg["captcha"]["reg"], true );
DefVal( $l2cfg["captcha"]["reg_type"], 'sw' );
DefVal( $l2cfg["captcha"]["profile"], true );
DefVal( $l2cfg["captcha"]["profile_type"], 'sw' );
DefVal( $l2cfg["captcha"]["repass"], true );
DefVal( $l2cfg["captcha"]["repass_type"], 'sw' );
DefVal( $l2cfg["captcha"]["l2top"], true );
DefVal( $l2cfg["captcha"]["l2top_type"], 'sw' );
DefVal( $l2cfg["captcha"]["mmotop"], true );
DefVal( $l2cfg["captcha"]["mmotop_type"], 'sw' );

//Mail
DefVal( $l2cfg["mail_admin"], "admin@lineage2.com" );
DefVal( $l2cfg["mail_method"], "mail" );
DefVal( $l2cfg["mail_smtphost"], "smtp.gmail.com" );
DefVal( $l2cfg["mail_smtpport"], 25 );
DefVal( $l2cfg["mail_charset"], "utf-8" );
DefVal( $l2cfg["mail_smtpuser"], "username@gmail.com" );
DefVal( $l2cfg["mail_smtppass"], "password" );
DefVal( $l2cfg["mail_smtpmail"], "username@gmail.com" );
DefVal( $l2cfg["mail_from"], "Игровой сервер Lineage" );
//кеширование
DefVal( $l2cfg["cache"]["enable"], false );
DefVal( $l2cfg["cache"]["forum"], 1 );
DefVal( $l2cfg["cache"]["sList"], 1 );
DefVal( $l2cfg["cache"]["stat"], 1 );
DefVal( $l2cfg["cache"]["online"], 1 );
DefVal( $l2cfg["cache"]["pvp"], 1 );
DefVal( $l2cfg["cache"]["pk"], 1 );
DefVal( $l2cfg["cache"]["top"], 1 );
DefVal( $l2cfg["cache"]["clan"], 1 );
DefVal( $l2cfg["cache"]["clanview"], 1 );
DefVal( $l2cfg["cache"]["castle"], 1 );
DefVal( $l2cfg["cache"]["raid"], 1 );
DefVal( $l2cfg["cache"]["epic"], 1 );
DefVal( $l2cfg["cache"]["oly"], 1 );
DefVal( $l2cfg["cache"]["rich"], 1 );
DefVal( $l2cfg["cache"]["login"], 1 );
DefVal( $l2cfg["cache"]["char"], 1 );
//робокасса
DefVal( $l2cfg["rb"]["enable"], false );
DefVal( $l2cfg["rb"]["invdesc"], "Добровольное пожертвование" );
DefVal( $l2cfg["rb"]["mrhlogin"], "rblogin" );
DefVal( $l2cfg["rb"]["mrhpass1"], "password1" );
DefVal( $l2cfg["rb"]["mrhpass2"], "password2" );
//темы с форума
DefVal( $l2cfg["forum"]["enable"], false );
DefVal( $l2cfg["forum"]["url"], "http://forum" );
DefVal( $l2cfg["forum"]["count"], 5 );
DefVal( $l2cfg["forum"]["length"], 25 );
DefVal( $l2cfg["forum"]["date"], "d.m.y H:i" );
DefVal( $l2cfg["forum"]["prefix"], "ibf_" );
DefVal( $l2cfg["forum"]["dbhost"], "localhost" );
DefVal( $l2cfg["forum"]["dbuser"], "root" );
DefVal( $l2cfg["forum"]["dbpass"], "" );
DefVal( $l2cfg["forum"]["dbname"], "forumdb" );
DefVal( $l2cfg["forum"]["dbcoll"], "utf8" );
DefVal( $l2cfg["forum"]["version"], "ipb" );
DefVal( $l2cfg["forum"]["deny"], "" );
//л2топ бонус
DefVal( $l2cfg["l2top"]["enable"], false );
DefVal( $l2cfg["l2top"]["id"], 1111 );
DefVal( $l2cfg["l2top"]["url"], "" );

DefVal( $l2cfg["mmotop"]["enable"], false );

// Login server
DefVal( $l2cfg["ls"]["count"], 1 );
for ( $i = 1; $i <= $l2cfg["ls"]["count"]; $i++ ) {
    DefVal( $l2cfg["ls"][$i]["on"], true );
    DefVal( $l2cfg["ls"][$i]["version"], 1 );
    DefVal( $l2cfg["ls"][$i]["host"], "127.0.0.1" );
    DefVal( $l2cfg["ls"][$i]["port"], 2106 );
    DefVal( $l2cfg["ls"][$i]["dbhost"], "localhost" );
    DefVal( $l2cfg["ls"][$i]["dbuser"], "root" );
    DefVal( $l2cfg["ls"][$i]["dbpass"], "" );
    DefVal( $l2cfg["ls"][$i]["dbname"], "l2jdb" );
    DefVal( $l2cfg["ls"][$i]["encode"], "sha1" );
}
// Game server
DefVal( $l2cfg["gs"]["count"], 1 );
for ( $i = 1; $i <= $l2cfg["gs"]["count"]; $i++ ) {
    DefVal( $l2cfg["gs"][$i]["on"], true );
    DefVal( $l2cfg["gs"][$i]["version"], 1 );
    DefVal( $l2cfg["gs"][$i]["ls"], 1 );
    DefVal( $l2cfg["gs"][$i]["title"], "Server".$i );
    DefVal( $l2cfg["gs"][$i]["host"], "127.0.0.1" );
    DefVal( $l2cfg["gs"][$i]["port"], 7777 );
    DefVal( $l2cfg["gs"][$i]["chronicle"], "Interlude" );

    DefVal( $l2cfg["gs"][$i]["rates"]["exp"], 1 );
    DefVal( $l2cfg["gs"][$i]["rates"]["sp"], 1 );
    DefVal( $l2cfg["gs"][$i]["rates"]["adena"], 1 );
    DefVal( $l2cfg["gs"][$i]["rates"]["items"], 1 );
    DefVal( $l2cfg["gs"][$i]["rates"]["spoil"], 1 );
    DefVal( $l2cfg["gs"][$i]["rates"]["quest"], 1 );

    DefVal( $l2cfg["gs"][$i]["dbhost"], "localhost" );
    DefVal( $l2cfg["gs"][$i]["dbuser"], "root" );
    DefVal( $l2cfg["gs"][$i]["dbpass"], "" );
    DefVal( $l2cfg["gs"][$i]["dbname"], "l2jdb" );

    DefVal( $l2cfg["gs"][$i]["telnet"]["port"], 12345 );
    DefVal( $l2cfg["gs"][$i]["telnet"]["pass"], "admin" );
    DefVal( $l2cfg["gs"][$i]["telnet"]["gmname"], "" );
    DefVal( $l2cfg["gs"][$i]["telnet"]["timeout"], 2 );

    DefVal( $l2cfg["gs"][$i]["stat"]["enable"], true );
    DefVal( $l2cfg["gs"][$i]["stat"]["count"], 10 );
    DefVal( $l2cfg["gs"][$i]["stat"]["general"], true );
    DefVal( $l2cfg["gs"][$i]["stat"]["online"], true );
    DefVal( $l2cfg["gs"][$i]["stat"]["top"], true );
    DefVal( $l2cfg["gs"][$i]["stat"]["pvp"], true );
    DefVal( $l2cfg["gs"][$i]["stat"]["pk"], true );
    DefVal( $l2cfg["gs"][$i]["stat"]["castles"], true );
    DefVal( $l2cfg["gs"][$i]["stat"]["clan"], true );
    DefVal( $l2cfg["gs"][$i]["stat"]["epic"], true );
    DefVal( $l2cfg["gs"][$i]["stat"]["raid"], true );
    DefVal( $l2cfg["gs"][$i]["stat"]["clanview"], true );
    DefVal( $l2cfg["gs"][$i]["stat"]["olympiad"], true );
    DefVal( $l2cfg["gs"][$i]["stat"]["rich"], true );

    DefVal( $l2cfg["gs"][$i]["teleport"]["enable"], true );
    DefVal( $l2cfg["gs"][$i]["teleport"]["time"], 60 );

    DefVal( $l2cfg["gs"][$i]["fake"]["enable"], false );
    DefVal( $l2cfg["gs"][$i]["fake"]["percent"], 10 );

    DefVal( $l2cfg["gs"][$i]["l2top"]["enable"], false );
    DefVal( $l2cfg["gs"][$i]["l2top"]["prefix"], "" );
    DefVal( $l2cfg["gs"][$i]["l2top"]["bonus"], "items" );
    DefVal( $l2cfg["gs"][$i]["l2top"]["count"], 1 );
    DefVal( $l2cfg["gs"][$i]["l2top"]["item_id"], 4037 );
    DefVal( $l2cfg["gs"][$i]["l2top"]["table"], "items" );
    DefVal( $l2cfg["gs"][$i]["l2top"]["method"], "mysql" );

    DefVal( $l2cfg["gs"][$i]["mmotop"]["enable"], false );
    DefVal( $l2cfg["gs"][$i]["mmotop"]["url"], "" );
    DefVal( $l2cfg["gs"][$i]["mmotop"]["bonus"], "items" );
    DefVal( $l2cfg["gs"][$i]["mmotop"]["count"], 1 );
    DefVal( $l2cfg["gs"][$i]["mmotop"]["item_id"], 4037 );
    DefVal( $l2cfg["gs"][$i]["mmotop"]["table"], "items" );
    DefVal( $l2cfg["gs"][$i]["mmotop"]["method"], "mysql" );

    DefVal( $l2cfg["gs"][$i]["chsex"]["enable"], false );
    DefVal( $l2cfg["gs"][$i]["chsex"]["money"], "items" );
    DefVal( $l2cfg["gs"][$i]["chsex"]["price"], 1 );
    DefVal( $l2cfg["gs"][$i]["chsex"]["item_id"], 4037 );
    DefVal( $l2cfg["gs"][$i]["chsex"]["item_name"], "Coin of Luck" );

    DefVal( $l2cfg["gs"][$i]["chname"]["enable"], false );
    DefVal( $l2cfg["gs"][$i]["chname"]["money"], "items" );
    DefVal( $l2cfg["gs"][$i]["chname"]["price"], 1 );
    DefVal( $l2cfg["gs"][$i]["chname"]["item_id"], 4037 );
    DefVal( $l2cfg["gs"][$i]["chname"]["item_name"], "Coin of Luck" );
    DefVal( $l2cfg["gs"][$i]["chname"]["letters"], "a,b,c,d,e,f,g,h,i,j,k,l,m,n,o,p,q,r,s,t,u,v,w,x,y,z,A,B,C,D,E,F,G,H,I,J,K,L,M,N,O,P,Q,R,S,T,U,V,W,X,Y,Z,#,$,%,!,*,-,=" );

    DefVal( $l2cfg["gs"][$i]["changer"]["enable"], false );
    DefVal( $l2cfg["gs"][$i]["changer"]["price"], 1 );
    DefVal( $l2cfg["gs"][$i]["changer"]["item_id"], 4037 );
    DefVal( $l2cfg["gs"][$i]["changer"]["item_name"], "Coin of Luck" );
    DefVal( $l2cfg["gs"][$i]["changer"]["table"], "items" );
    DefVal( $l2cfg["gs"][$i]["changer"]["method"], "mysql" );

    DefVal( $l2cfg["gs"][$i]["rb"]["product"], "items" );
    DefVal( $l2cfg["gs"][$i]["rb"]["item_id"], 4037 );
    DefVal( $l2cfg["gs"][$i]["rb"]["money"], "Coin of Luck" );
    DefVal( $l2cfg["gs"][$i]["rb"]["table"], "items" );
    DefVal( $l2cfg["gs"][$i]["rb"]["valuta"], "WMRM" );
    DefVal( $l2cfg["gs"][$i]["rb"]["sum"], 30 );

    DefVal( $l2cfg["gs"][$i]["referal_enable"], false );
    DefVal( $l2cfg["gs"][$i]["referal_type"], 'level' );
    DefVal( $l2cfg["gs"][$i]["referal_condition"], 20 );
    DefVal( $l2cfg["gs"][$i]["referal_bonus"], "items" );
    DefVal( $l2cfg["gs"][$i]["referal_item_id"], 4037 );
    DefVal( $l2cfg["gs"][$i]["referal_item_name"], "Coin of Luck" );
    DefVal( $l2cfg["gs"][$i]["referal_count"], 1 );
    DefVal( $l2cfg["gs"][$i]["referal_method"], "mysql" );
    DefVal( $l2cfg["gs"][$i]["referal_table"], "items" );
}
DefVal( $l2cfg["mod_rewrite"], false );
DefVal( $l2cfg["nocopy"], false );
$cfgSList = array();
for ( $i = 1; $i <= $l2cfg["gs"]["count"]; $i++ ) {
    if ( $l2cfg["gs"][$i]["on"] ) {
        $cfgSList[$i] = $l2cfg["gs"][$i]["title"];
    }
}
?>