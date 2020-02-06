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
    $_option = isset( $_REQUEST["opt"] ) ? $controller->SafeData( $_REQUEST["opt"], 1 ):"";
    $_lid = $controller->sess_get( 'lid' );
    $_vls = $vList[$l2cfg["ls"][$_lid]["version"]];
    $profile = "";

    switch ( $_option ) {
            //characters list
        case 'chars':
            {
                $cp = 'charlist';
                break;
            }
            //char info and inventory
        case 'charinfo':
            {
                $cp = 'charinfo';
                break;
            }
            //change password
        case 'chpass':
            {
                $cp = 'chpass';
                break;
            }
            //change email
        case 'chmail':
            {
                $cp = 'chmail';
                break;
            }
            //support system
        case 'support':
            {
                $cp = 'support';
                break;
            }
            //change char sex
        case 'chsex':
            {
                $cp = 'chsex';
                break;
            }
            //change char name
        case 'chname':
            {
                $cp = 'chname';
                break;
            }
            //donate robokassa
        case 'robo':
            {
                $cp = 'robokassa';
                break;
            }
            //change l2money to items
        case 'changer':
            {
                $cp = 'changer';
                break;
            }
            //auction
        case 'auction':
            {
                $cp = 'auction';
                break;
            }
            //referal system
        case 'referal':
            {
                $cp = 'referal';
                break;
            }
        default:
            $cp = 'charlist';
    }

    require APPDIR."account".DS.$cp.".php";
    /**
     * ------------------------------------------------------------
     */
    $tpl->LoadView( "profile" );
    $tpl->Block( 'profile' );
    $tpl->Block( 'login', false );
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
    $tpl->Set( "content", $profile );
    $tpl->Build( "content" );
} else {
    $tpl->LoadView( "profile" );
    if ( $l2cfg["captcha"]["profile"] and $l2cfg['captcha']['profile_type'] == 'sw' ) {
        $tpl->template = '<script type="text/javascript">//<![CDATA[
function reload () {
	var rndval = new Date().getTime(); 
	document.getElementById(\'sw-captcha\').innerHTML = \'<a onclick="reload(); return false;" href="#"><img src="'.HTTP_HOME_URL.'/module/antibot.php?rndval=\' + rndval + \'" border="0" title="'.$lang["reload"].'" alt="code" /></a>\';
};
//]]></script>'.$tpl->template;
        $tpl->Block( 'captcha' );
        $tpl->Set( 'l2sec_code', "<div id=\"sw-captcha\" class='captcha'><a onclick='reload(); return false;' href='#'><img src='".HTTP_HOME_URL."/module/antibot.php' alt='code' title='{$lang["reload"]}' border='0' /></a></div>" );
    } else
        $tpl->Block( 'captcha', false );
    if ( $l2cfg['captcha']['profile'] and $l2cfg['captcha']['profile_type'] == 'recaptcha' ) {
        $tpl->Set( 'code', '
            <script type="text/javascript">
 				var RecaptchaOptions = {
    				theme : \'white\'
 				};
 			</script>
			<script type="text/javascript"
		       src="http://www.google.com/recaptcha/api/challenge?k='.$l2cfg['captcha']['publickey'].'">
		    </script>
		    <noscript>
		       <iframe src="http://www.google.com/recaptcha/api/noscript?k='.$l2cfg['captcha']['publickey'].'"
		           height="300" width="500" frameborder="0"></iframe><br>
		       <textarea name="recaptcha_challenge_field" rows="3" cols="40">
		       </textarea>
		       <input type="hidden" name="recaptcha_response_field"
		           value="manual_challenge">
		    </noscript>' );
        $tpl->Block( 'recaptcha' );
    } else
        $tpl->Block( 'recaptcha', false );

    $tpl->Set( "uFORGET", HTTP_HOME_URL.(($l2cfg["mod_rewrite"]) ? "/forget":"/index.php?f=forget") );
    $tpl->Set( "uREGISTER", HTTP_HOME_URL.(($l2cfg["mod_rewrite"]) ? "/register":"/index.php?f=register") );
    $tpl->Block( 'profile', false );
    $tpl->Block( 'login' );
    if ( count($lsList) > 1 ) {
        $servList = "<select style='width: 100%;' name='sid'>";
        foreach ( $gsList as $i ) {
            $servList .= "<option value='{$i}'>{$l2cfg["gs"][$i]["title"]}</option>";
        }
        $servList .= "</select>";
        $tpl->Block( 'servers' );
        $tpl->Set( 'servers', $servList );
    } else
        $tpl->Block( 'servers', false );
    $tpl->Build( "content" );
}
?>