<div align='center'>
<form name="form" method="post" action="" onsubmit="return checkform(this)">
<table cellpadding="0" cellspacing="0" class="regForm">
<tr>
	<td colspan="2" align="center"><p>Account registration</p></td>
</tr>
[servers]
<tr>
	<td class="tdLeft">Server:</td>
  	<td class="tdRight"><select style="width: 100%;" name="sid">{servers}</select></td>
</tr>
[/servers]
[prefix]
<tr>
	<td class="tdLeft">Prefix:<br><span class="description">(will be added to account)</span></td>
  	<td class="tdRight"><select style="width: 100%;" name="l2prefix">{prefix}</select></td>
</tr>
[/prefix]
<tr>
	<td class="tdLeft">Account:<br><span class="description">(4-14 symbols)</span></td>
  	<td class="tdRight"><input type="text" name="l2account" maxlength="14" class="input"></td>
</tr>
<tr>
	<td class="tdLeft">Password:<br><span class="description">(6-16 symbols)</span></td>
  	<td class="tdRight"><input type="password" name="l2password1" maxlength="16" class="input"></td>
</tr>
<tr>
  	<td class="tdLeft">Repeat Password:</td>
  	<td class="tdRight"><input type="password" name="l2password2" maxlength="16" class="input"></td>
</tr>
<tr>
  	<td class="tdLeft">Email:<br><span class="description">(Enter valid e-mail)</span></td>
  	<td class="tdRight"><input type="text" name="l2email" maxlength="64" class="input"></td>
</tr>
[captcha]
<tr>
  	<td class="tdLeft">{l2sec_code}</td>
  	<td class="tdRight"><input type="text" name="l2sec_code" maxlength="10" class="input"></td>
</tr>
[/captcha]
[recaptcha]
<tr>
  	<td colspan="2">{code}</td>
</tr>
[/recaptcha]
[referal]
<tr>
	<td class="tdLeft">Friend name:<br><span class="description">(not necessarily)</span></td>
  	<td class="tdRight"><input type="text" name="l2friend" maxlength="16" class="input"></td>
</tr>
[/referal]
<tr>
  	<td colspan="2" style="text-align: center;"><input type="submit" name="register"  value="Registration" class="regbutton" /></td>
</tr>
</table>
</form>
</div>