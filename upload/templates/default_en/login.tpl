[logged]
<div id="cp">
Hello, <b>{user}</b><br />
<a href="{uCHARS}">Account</a><br />
<a href="{uCHPASS}">Change password</a><br />
<a href="{uCHMAIL}">Change E-mail</a><br />
<a href="{uSUPPORT}">Support</a><br />
<a href="{uROBO}">Donate</a><br />
<a href="{uCHSEX}">Sex change</a><br />
<a href="{uCHNAME}">Nickname change</a><br />
<a href="{uCHANGER}">Changer</a><br />
<a href="{uREFERAL}">Invite Friend</a><br />
<a href="{url}/index.php?doExit=yes">SignOut</a>
</div>
[/logged]
[login]
<div id="login">
<form action="" method="post" name="dologin">
<input type="hidden" value="1" name="doLogin">
<input type="submit" style="display:none">
<table width="170" cellpadding="0" cellspacing="0">
<tr>
	<td width="70" height="25" valign="top" align="left">Login:</td>
	<td width="100" valign="top"><input type="text" name="sw_name" style="width: 100px;" maxlength="16"></td>
</tr>
<tr>
	<td height="25" valign="top" align="left">Password:</td>
	<td valign="top"><input type="password" name="sw_pass" style="width: 100px;" maxlength="16"></td>
</tr>
[servers]
<tr>
	<td height="25" valign="top" align="left">Server:</td>
	<td valign="top">{servers}</td>
</tr>
[/servers]
[captcha]
<tr>
	<td height="25" valign="top" align="left">{l2sec_code}</td>
  	<td valign="top"><input type="text" name="l2sec_code" maxlength="10" style="width: 100px;"></td>
</tr>
[/captcha]
[recaptcha]
<tr>
	<td>{code}</td>
	<td>{field}</td>
</tr>
[/recaptcha]
<tr>
	<td colspan="2" align="center">
		<a href="{uFORGET}">Forget password?</a> &nbsp; <a href="/" onclick="javascript: document.dologin.submit(); return false;">Sign</a>		
	</td>
</tr>
</table>
</form>
</div>
[/login]