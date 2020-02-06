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
    $tpl->SetResult( 'pktop' );
else {
    $pktop = $controller->GetCache( 'mod_pktop' );
    if ( $pktop )
        $tpl->SetResult( 'pktop', $pktop );
    else {

        $db->gdb( $T_SID );

        $T_SEL = $gdb[$T_SID]->query( "SELECT `char_name`,`pkkills` FROM `characters` ORDER BY `pkkills` DESC LIMIT {$T_COUNT}" );
        if ( $gdb[$T_SID]->num_rows($T_SEL) > 0 ) {
            $T_N = 1;
            while ( $T_RESULT = $gdb[$T_SID]->fetch($T_SEL) ) {
                $tpl->LoadView( 'pktop' );
                $tpl->Block( 'main', false );
                $tpl->Block( 'item' );
                $tpl->Set( 'n', $T_N );
                $tpl->Set( 'char_name', $T_RESULT['char_name'] );
                $tpl->Set( 'pk', $T_RESULT['pkkills'] );
                $tpl->Build( 'pktop_item' );
                $T_N++;
            }
            $tpl->LoadView( 'pktop' );
            $tpl->Block( 'main' );
            $tpl->Block( 'item', false );
            $tpl->Set( 'item', $tpl->GetResult('pktop_item', true) );
            $tpl->Build( 'pktop' );
        } else
            $tpl->SetResult( 'pktop' );
        if ( $T_CACHE > 0 ) {
            $controller->SetCache( 'mod_pktop', $tpl->GetResult('pktop'), $T_CACHE );
        }
    }
}
?>