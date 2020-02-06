[profile]
<div align="center">
<H3>~ Личный кабинет ~</H3>
<table width="90%" cellpadding="0" cellspacing="0" class="tabProfileMenu">
<tr>
	<td align="center" height="30">
		<a href="index.php?doExit=yes"> <u>Завершить сеанс</u> </a>
	</td>
</tr>
<tr>
	<td align="center">
		<a href="{uCHARS}">::Персонажи::</a>
		<a href="{uCHPASS}">::Сменить пароль::</a>
		<a href="{uCHMAIL}">::Сменить E-Mail::</a>
		<a href="{uSUPPORT}">::Поддержка::</a>
		<a href="{uROBO}">::Пожертвование::</a>
		<a href="{uCHSEX}">::Смена пола::</a>
		<a href="{uCHNAME}">::Смена ника::</a>
		<a href="{uCHANGER}">::Обменник::</a>
		<a href="{uREFERAL}">::Пригласи друга::</a>
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
	<td colspan="2" class="title" height="30">Вход в кабинет</td>
</tr>
[servers]
<tr>
	<td width="65" height="25" valign="top" align="left">Сервер:</td>
	<td width="100" valign="top">{servers}</td>
</tr>
[/servers]
<tr>
	<td width="65" height="25" valign="top" align="left">Логин:</td>
	<td width="100" valign="top"><input type="text" name="sw_name" style="width: 80px;" maxlength="16"></td>
</tr>
<tr>
	<td width="65" height="25" valign="top" align="left">Пароль:</td>
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
		<a href="{uFORGET}">Забыли пароль?</a> &nbsp; <a href="/" onclick="javascript: document.do_login.submit(); return false;">Войти</a><br /><a href="{uREGISTER}">Регистрация</a>
	</td>
</tr>
</table>
</form></div>
[/login]