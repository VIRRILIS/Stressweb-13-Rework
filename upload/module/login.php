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

$tpl->LoadView( "login" );
if ( $controller->isLogged() ) {
    $tpl->Block( 'login', false );
    $tpl->Block( 'logged' );
    $tpl->Set( "uCHARS", HTTP_HOME_URL.(($l2cfg["mod_rewrite"]) ? "/cp/chars":"/index.php?f=cp&opt=chars") );
    $tpl->Set( "uCHPASS", HTTP_HOME_URL.(($l2cfg["mod_rewrite"]) ? "/cp/chpass":"/index.php?f=cp&opt=chpass") );
    $tpl->Set( "uCHMAIL", HTTP_HOME_URL.(($l2cfg["mod_rewrite"]) ? "/cp/chmail":"/index.php?f=cp&opt=chmail") );
    $tpl->Set( "uSUPPORT", HTTP_HOME_URL.(($l2cfg["mod_rewrite"]) ? "/cp/support":"/index.php?f=cp&opt=support") );
    $tpl->Set( "uROBO", HTTP_HOME_URL.(($l2cfg["mod_rewrite"]) ? "/cp/robo":"/index.php?f=cp&opt=robo") );
    $tpl->Set( "uCHSEX", HTTP_HOME_URL.(($l2cfg["mod_rewrite"]) ? "/cp/chsex":"/index.php?f=cp&opt=chsex") );
    $tpl->Set( "uCHNAME", HTTP_HOME_URL.(($l2cfg["mod_rewrite"]) ? "/cp/chname":"/index.php?f=cp&opt=chname") );
    $tpl->Set( "uCHANGER", HTTP_HOME_URL.(($l2cfg["mod_rewrite"]) ? "/cp/changer":"/index.php?f=cp&opt=changer") );
    $tpl->Set( "uAUCTION", HTTP_HOME_URL.(($l2cfg["mod_rewrite"]) ? "/cp/auction":"/index.php?f=cp&opt=auction") );
    $tpl->Set( "uREFERAL", HTTP_HOME_URL.(($l2cfg["mod_rewrite"]) ? "/cp/referal":"/index.php?f=cp&opt=referal") );
    $tpl->Set( 'user', $controller->GetName() );
} else {

    $tpl->Block( 'login' );
    $tpl->Block( 'logged', false );
    $tpl->Set( 'uFORGET', ($l2cfg["mod_rewrite"]) ? HTTP_HOME_URL."/forget":HTTP_HOME_URL."/index.php?f=forget" );
    $tpl->Set( 'uREGISTER', ($l2cfg["mod_rewrite"]) ? HTTP_HOME_URL."/register":HTTP_HOME_URL."/index.php?f=register" );
    if ( $l2cfg["captcha"]["profile"] and $l2cfg["captcha"]["profile_type"] == "sw" ) {
        $tpl->template = '<script type="text/javascript">//<![CDATA[
function reload1 () {
	var rndval = new Date().getTime(); 
	document.getElementById(\'sw1-captcha\').innerHTML = \'<a onclick="reload1(); return false;" href="#"><img src="'.HTTP_HOME_URL.'/module/antibot.php?rndval=\' + rndval + \'" border="0" alt="code" title="'.$lang["reload"].'" /></a>\';
};
//]]></script>'.$tpl->template;
        $tpl->Block( 'captcha' );
        $tpl->Set( 'l2sec_code', "<div id='sw1-captcha' class='captcha'><a onclick=\"reload1(); return false;\" href='#'><img src='".HTTP_HOME_URL."/module/antibot.php' alt='code' title='{$lang["reload"]}' border='0' /></a></div>" );
    } else {
        $tpl->Block( 'captcha', false );
    }
    if ( $l2cfg["captcha"]["profile"] and $l2cfg["captcha"]["profile_type"] == "recaptcha" ) {
        $tpl->template = '
        <style>
        	#recaptcha_window {display:none; width: 300px; height: 57px; position: fixed; left: 40%; top: 15%; margin: 0 auto; padding: 20px; background: #fff; -webkit-border-radius: 7px; -moz-border-radius: 7px; border-radius: 7px; border: 1px solid; z-index: 999;}
        	.recaptcha_close {background: url('.HTTP_HOME_URL.'/sysimg/close.png) 0 0 no-repeat; width: 30px; height: 30px; position: absolute; top: -10px; right: -10px; cursor: pointer;}
        </style>
        <script type="text/javascript" src="http://www.google.com/recaptcha/api/js/recaptcha_ajax.js"></script>
		<script type="text/javascript">
		$(document).ready(function(){
			$(".recaptcha_close").click(function(){
				$("#recaptcha_window").hide();
			});
		});
         function showRecaptcha(element) {
         	$("#recaptcha_window").show();
           Recaptcha.create("'.$l2cfg["captcha"]["publickey"].'", element, {
             theme: "custom",
             lang : \'ru\',
             custom_theme_widget: \'recaptcha_div\',
             callback: Recaptcha.focus_response_field});
         }
      	</script>'.$tpl->template;
        $tpl->Block( 'recaptcha' );
        $tpl->Set( 'field', "
		<div id='recaptcha_div'>
				<div id='recaptcha_window'>
					<div class='recaptcha_close'></div>
					<div id='recaptcha_image'></div>
				</div>
			<input type='text' id='recaptcha_response_field' name='recaptcha_response_field'>
		</div>" );
        $tpl->Set( 'code', "<div id='sw1-captcha' class='captcha'><a onclick=\"showRecaptcha('recaptcha_div'); return false;\" href='#'><img src='".HTTP_HOME_URL."/sysimg/click.jpg' alt='code' title='{$lang["reload"]}' border='0' /></a></div>" );
    } else {
        $tpl->Block( 'recaptcha', false );
    }
    if ( count($lsList) > 1 ) {
        $servList = "<select style='width: 100%;' name='sid'>";
        foreach ( $gsList as $key ) {
            $servList .= "<option value='{$key}'>{$l2cfg["gs"][$key]["title"]}</option>";
        }
        $servList .= "</select>";
        $tpl->Block( 'servers' );
        $tpl->Set( 'servers', $servList );
    } else
        $tpl->Block( 'servers', false );
}
$tpl->Build( 'login' );
?>