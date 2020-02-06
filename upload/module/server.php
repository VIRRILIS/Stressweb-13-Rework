<?php

if ( !defined("STRESSWEB") )
{
    die( "Access denied..." );
}

if ( !$l2cfg["server"]["enable"] ) 
{
    $tpl->SetResult( 'server', "Статус серверов временно не доступен" );
} 
else 
{
    $mod_server = $controller->GetCache( 'mod_server' );
    if ( $mod_server )
        $tpl->SetResult( 'server', $mod_server );
    else 
	{
        $LS = array();
        $Total = 0;
        foreach ( $lsList as $_L ) 
		{
            $LS[$_L] = $controller->GetStatus( $l2cfg["ls"][$_L]["host"], $l2cfg["ls"][$_L]["port"] );
        }

        foreach ( $gsList as $_S ) 
		{
            $tpl->LoadView( "server" );
            $tpl->Block( 'main', false );
            $tpl->Block( 'item' );

            $GS = $controller->GetStatus( $l2cfg["gs"][$_S]["host"], $l2cfg["gs"][$_S]["port"] );

            $db->gdb( $_S );

            $Online = $gdb[$_S]->result( $gdb[$_S]->query("SELECT count(0) FROM `characters` WHERE `online`>'0'"), 0 );
            if ( $l2cfg["gs"][$_S]["fake"]["enable"] ) {
                $Online = intval( $Online * (1 + $l2cfg["gs"][$_S]["fake"]["percent"] / 100) );
            }
            $slink = ( $l2cfg["mod_rewrite"] ) ? "/stat/s{$_S}":"/index.php?f=stat&sid={$_S}";
            $tpl->Set( "nameLink", "<a href='".HTTP_HOME_URL."{$slink}'>{$l2cfg["gs"][$_S]["title"]}</a>" );
            $tpl->Set( "name", "{$l2cfg["gs"][$_S]["title"]}" );
            $tpl->Set( "online", $Online );
            $tpl->Set( "login", $LS[$l2cfg["gs"][$_S]["ls"]] );
            $tpl->Set( "game", $GS );
            $tpl->Set( "chronicle", $l2cfg["gs"][$_S]["chronicle"] );
            $tpl->Build( "server_item" );
            $Total += $Online;
            if ( $l2cfg["txt"]["enable"] and $l2cfg["txt"]["gs"] == $_S ) {
                $fopen = fopen( ROOT_DIR.'online.txt', "w" );
                if ( $fopen ) {
                    fwrite( $fopen, $Online );
                    fclose( $fopen );
                }
            }
        }
		
        $tpl->LoadView( "server" );
        $tpl->Block( 'main' );
        $tpl->Block( 'item', false );
        if ( isset($LS[0]) )
            $tpl->Set( "login", $LS[0] );
        $tpl->Set( 'item', $tpl->GetResult("server_item", true) );
        if ( count($gsList) > 1 ) {
            $tpl->Block( 'total' );
            $tpl->Set( "total", $Total );
        } else
            $tpl->Block( 'total', false );
        $tpl->Build( "server" );
        if ( $l2cfg["cache"]["enable"] and $l2cfg['cache']['sList'] ) {
            $controller->SetCache( "mod_server", $tpl->GetResult("server"), $l2cfg['cache']['sList'] );
        }
    }
}

?>