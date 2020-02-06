<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html dir="ltr" xmlns="http://www.w3.org/1999/xhtml" lang="en" xml:lang="en">
<head>
<title>Панель Администратора - STRESS WEB</title>
<style>
* {margin: 0; padding: 0;}
html, body {margin: 0; padding: 0; background: #D9D8DD; width: 100%;}
label {
	font-size: 12px;
	font-family: "Lucida Sans Unicode", "Lucida Grande", sans-serif;
}
.button {
	background: transparent;
	border: 1px solid black;
	font-size: 12px;
}
#box {width: 350px; margin: 100px auto 0;}
#box .title {
	background: #1C354C;
	font: 16px/40px Verdana;
	color: #fff;
	text-indent: 20px;
	font-weight: bold;
	-moz-border-radius-topleft: 5px;
	-moz-border-radius-topright: 5px;
	-moz-border-radius-bottomright: 0px;
	-moz-border-radius-bottomleft: 0px;
	-webkit-border-radius: 5px 5px 0px 0px;
	border-radius: 5px 5px 0px 0px; 
}
#box .formbox {
	padding: 5px 15px;
	background: #EDEFF1;
}
#box .inp {
	width: 120px;
	background: #CED0D1;
	-webkit-border-radius: 2px;
	-moz-border-radius: 2px;
	border-radius: 2px;
	border: 1px solid #fff;
	height: 25px;
	font: 11px/18px Tahoma;
	color: #555;
	text-align: center;
}
#box .submit {
	background: #183049;
	font: 13px/18px Tahoma;
	height: 25px;
	border: 1px solid #ddd;
	-webkit-border-radius: 5px;
	-moz-border-radius: 5px;
	border-radius: 5px;
	color: #fff;
	cursor: pointer;
}
#box .foot {
	background:#3E4753;
	height: 40px;
	font: 11px/35px Arial;
	font-weight: bold;
	color: #ddd;
	text-align: center;
	-moz-border-radius-topleft: 0px;
	-moz-border-radius-topright: 0px;
	-moz-border-radius-bottomright: 5px;
	-moz-border-radius-bottomleft: 5px;
	-webkit-border-radius: 0px 0px 5px 5px;
	border-radius: 0px 0px 5px 5px;
}
#box .foot a {color: #fff;}
</style>
</head>
<body>

<div id="box">
	<div class="title">Вход в Админ Панель</div>
	<div class="formbox">
		<form action="" method="post">
			<table cellpadding="0" cellspacing="2" border="0">
			<tr>
				<td width="150" height="25" align="left"><label>Аккаунт: </label></td>
				<td width="150" align="right"><input type="text" name="acp_login" class="inp"></td>
			</tr>
			<tr>
				<td height="25" align="left"><label>Пароль: </label></td>
				<td align="right"><input type="password" name="acp_pass" class="inp"></td>
			</tr>
			[code]
			<tr>
				<td height="40" align="left"><label>Код:</label><img src="{url}/module/antibot.php?rndval=2012" align="absmiddle" /></td>
			    <td align="right"><input type="text" name="sec_code" class="inp"></td>
			</tr>
			[/code]
			[recode]
			<tr>
				<td align="center" colspan="2">{recaptcha}</td>
			</tr>
			[/recode]			
			<tr>
				<td colspan="2" align="right" class='logoimg'>
					<input type="hidden" name="doLogin" value="1">
					<input type="submit" value="Login" class="submit">
				</td>
			</tr>
			</table>
		</form>
	</div>
	<div class="foot">2008-2012 &copy; <a href="http://stressweb.ru" target="_blank">STRESSWEB</a></div>
</div>
</body>
</html>