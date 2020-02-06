[profile]
<div align="center">
<H3>~ Control Panel ~</H3>
<table width="90%" cellpadding="0" cellspacing="0" class="tabProfileMenu">
<tr>
	<td align="center" height="30">
		<a href="index.php?doExit=yes"> <u>Sign Out</u> </a>
	</td>
</tr>
<tr>
	<td align="center">
		<a href="{uCHARS}">::Chars::</a>
		<a href="{uCHPASS}">::Change password::</a>
		<a href="{uCHMAIL}">::Change E-Mail::</a>
		<a href="{uSUPPORT}">::Support::</a>
		<a href="{uROBO}">::Donate::</a>
		<a href="{uCHSEX}">::Sex change::</a>
		<a href="{uCHNAME}">::Nickname change::</a>
		<a href="{uCHANGER}">::Changer::</a>
		<a href="{uREFERAL}">::Invite Friend::</a>
	</td>
</tr>
</table>
{content}
</div>
[/profile]
[login]
<div align="center">
<form action="" method="post" name="do_login">
<input type="hidden" value="1" name="doLogin">
<table width="165" cellpadding="0" cellspacing="0" id="account">
<tr>
	<td colspan="2" class="title" height="30">Control Panel</td>
</tr>
[servers]
<tr>
	<td width="65" height="25" valign="top" align="left">Server:</td>
	<td width="100" valign="top">{servers}</td>
</tr>
[/servers]
<tr>
	<td width="65" height="25" valign="top" align="left">Login:</td>
	<td width="100" valign="top"><input type="text" name="sw_name" style="width: 80px;" maxlength="16"></td>
</tr>
<tr>
	<td width="65" height="25" valign="top" align="left">Password:</td>
	<td width="100" valign="top"><input type="password" name="sw_pass" style="width: 80px;" maxlength="16"></td>
</tr>
[captcha]
<tr>
	<td width="65" height="25" valign="top" align="left">{l2sec_code}</td>
  	<td width="100" valign="top"><input type="text" name="l2sec_code" maxlength="10" style="width: 80px;"></td>
</tr>
[/captcha]
[recaptcha]
<tr>
  	<td colspan="2">{code}</td>
</tr>
[/recaptcha]
<tr>
	<td colspan="2" align="center">
		<a href="{uFORGET}">Forget password?</a> &nbsp; <a href="/" onclick="javascript: document.do_login.submit(); return false;">Sign</a><br /><a href="{uREGISTER}">Register</a>
	</td>
</tr>
</table>
</form></div>
[/login]