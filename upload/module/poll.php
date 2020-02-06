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
$_lang = ( isset($_COOKIE['swlang']) and $_COOKIE['swlang'] == 'en' ) ? '_en':'';
$sel_poll = $db->query( "SELECT * FROM `stress_poll` WHERE `status`='1' LIMIT 1" );
if ( $db->num_rows($sel_poll) > 0 ) {
    $poll_data = $db->fetch( $sel_poll );
    if ( empty($poll_data['title_en']) )
        $poll_data['title_en'] = $poll_data['title'];
    if ( empty($poll_data['body_en']) )
        $poll_data['body_en'] = $poll_data['body'];
    
	$answers = explode( "|", $poll_data['body'.$_lang] );

    $HTTP = HTTP_HOME_URL;
    $p_content = <<< HTML
<script language="javascript" type="text/javascript">
function doVote( task, pid ){
	
	var val = 0;

    if(task=='vote'){
		val = $("form[name=pollsw] input[name=vote]:checked").val();
	}
	if(task=='results'){
		val = 0;
	}
	$("#pbusy").show();
	$.ajax({
		type: "POST",
		url: "{$HTTP}/ajax/aj.poll.php",
		data: ( {id : pid, val : val} ),
		dataType: "html",
		success: function(msg){
			$("#pbusy").hide();
			$("#pvars").html(msg);
		}
	});

}
</script>
HTML;
    $p_content .= "
    
	<div id='poll'>
		<div class='ptitle'>".$poll_data['title'.$_lang]."</div>
		<div id='pbusy' style='display:none'><img src='{$HTTP}/sysimg/loading.gif' alt='loading...' /></div>
		<div id='pvars'>
		<form metho='post' name='pollsw'>";

    for ( $i = 1; $i < count($answers); $i++ ) {
        $checked = ( $i == 1 ) ? " checked":"";
        $p_content .= "
			<div class='pradio' align='left'><input type='radio' name='vote' value='".$i."'{$checked}> ".$answers[$i]."</div>\n";
    }
    $p_content .= "
		<div class='pbuttons'>
			<a href='#' onclick=\"doVote('vote','{$poll_data['id']}'); return false;\" class='pvote'>{$lang["vote"]}</a>
			<a href='#' onclick=\"doVote('results','{$poll_data['id']}'); return false;\" class='presult'>{$lang["results"]}</a>
		</div>
		</form>
		</div>
	</div>";
} else {
    $p_content = "";
}
$tpl->SetResult( 'poll', $p_content );
?>