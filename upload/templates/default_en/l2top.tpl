<div align="center">
	<div class="voteBlock">
		<center><b><u>How to get bonus?</u></b></center>
 		<br>1. You must have a char on our server
    	<br>2. Click l2top banner (or this <a href="http://l2top.ru/vote/{voteid}/" target="_blank">link</a>) and vote for us
    	<br>3. Below this enter char name and select server
		<br>4. Click Get Bonus
    	<br>
    	<br><center><u>Attention: You may vote only for 1 vote from 1 IP address in 24 hours.</u></center>
    	<br><br>
	</div>
	[vote]
	<div class="voteBlock">
		<script type="text/javascript">//<![CDATA[
function reload () {
	var rndval = new Date().getTime(); 
	document.getElementById('sw-captcha').innerHTML = '<a onclick="reload(); return false;" href="#"><img src="{url}/module/antibot.php?rndval=' + rndval + '" border="0"></a>';
};
//]]></script>
		<center><b><u>Voted? Get bonus</u></b>
		<br>Type char name<br /></center>
		<form action="" method="post">
		<input type="hidden" name="act" value="get">
		<table border="0" cellpadding="0" cellspacing="0" width="100%" id="voteTab1">
        <tr>
        	<td align="right">«Char name» &nbsp;&nbsp;</td>
        	<td><input type="text" name="char_name" maxlength="16" class="input"></td>
        </tr>
        <tr>
        	<td align="right">«Server» &nbsp;&nbsp;</td>
            <td>{servers}</td>
        </tr>
        [captcha]
        <tr>
        	<td align="right">{l2sec_code} </td>
        	<td valign="top"><input type="text" name="l2sec_code" maxlength="5" class="input"></td>
        </tr>
        [/captcha]
        [recaptcha]
		<tr>
		  	<td colspan="2">{code}</td>
		</tr>
		[/recaptcha]
        <tr>
        	<td colspan="2" align="center"><input type="submit" value="Get Bonus" class="button"></td>
		</tr>
        </table>
		</form>
	</div>
	[/vote]
	[error]
	<div class="voteBlockErr">
		{error}
	</div>
	[/error]
</div>