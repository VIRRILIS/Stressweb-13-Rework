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

/******************************
* НАСТРОЙКА МОДУЛЯ 
******************************/
$T_ENABLE = true; // вкл/выкл модуль
$T_COUNT = 5; // количество результатов
$T_SID = 1; // ID сервера с которого выводить результаты
$T_CACHE = 5; // время кеширования в минутах, 0 - отключить
/******************************
* ВЫВОД РЕЗУЛЬТАТОВ 
******************************/
if ( !$T_ENABLE )
    $tpl->SetResult( 'pvptop' );
else {
    if ( $pvptop = $controller->GetCache( 'mod_pvptop' ) )
        $tpl->SetResult( 'pvptop', $pvptop );
    else 
	{

        $db->gdb( $T_SID );

        $T_SEL = $gdb[$T_SID]->query( "SELECT `char_name`,`pvpkills`,`pkkills` FROM `characters` ORDER BY `pvpkills` DESC, `pkkills` DESC LIMIT {$T_COUNT}" );
        if ( $gdb[$T_SID]->num_rows($T_SEL) > 0 ) 
		{
            $T_N = 1;
            while ( $T_RESULT = $gdb[$T_SID]->fetch($T_SEL) ) {
                $tpl->LoadView( 'pvptop' );
                $tpl->Block( 'main', false );
                $tpl->Block( 'item' );
                $tpl->Set( 'n', $T_N );
                $tpl->Set( 'char_name', $T_RESULT['char_name'] );
                $tpl->Set( 'pvp', $T_RESULT['pvpkills'] );
                $tpl->Set( 'pk', $T_RESULT['pkkills'] );
                $tpl->Build( 'pvptop_item' );
                $T_N++;
            }
            $tpl->LoadView( 'pvptop' );
            $tpl->Block( 'item', false );
            $tpl->Block( 'main' );
            $tpl->Set( 'item', $tpl->GetResult('pvptop_item', true) );
            $tpl->Build( 'pvptop' );
        } else
            $tpl->SetResult( 'pvptop' );
        if ( $T_CACHE > 0 ) 
		{
            $controller->SetCache( 'mod_pvptop', $tpl->GetResult('pvptop'), $T_CACHE );
        }
    }
}

?>