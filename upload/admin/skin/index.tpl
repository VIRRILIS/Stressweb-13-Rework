<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html dir="ltr" xmlns="http://www.w3.org/1999/xhtml" lang="en" xml:lang="en">
<head>
	<title>Admin Panel</title>
	<link rel="stylesheet" type="text/css" href="{template}/css.css" media="all" />
	<script language="JavaScript" type="text/javascript" src="{template}/js.js"></script>
	<script language="JavaScript" type="text/javascript" src="{template}/jquery-1.4.2.min.js"></script>
<script type="text/javascript">
$(document).ready(function(){
	$('.games a:first, .logins a:first').addClass('active');
	$('.games a').click(function(){
		$('.games a').removeClass('active');
		$(this).addClass('active');
	});
	$('.logins a').click(function(){
		$('.logins a').removeClass('active');
		$(this).addClass('active');
	});
});
</script>
</head>
<body>
<div id="wrapper">
	
	<div id="navig">
	<ul>
		<li><a href='/{index}'>Главная</a></li>
		<li><a href='/{index}?get=license'>Лицензионная информация</a></li>				
		<li><a href='/' target='_blank'>На сайт</a></li>
		<li><a href='/{index}?exit=yes'>Выход</a></li>
	</ul>
	</div>
	
	<div class="swlogo"><a href="http://stressweb.ru" target="_blank"></a></div>
	
	<div id="acc">Вы вошли как: <b>{login}</b></div>
	
	<div class="pagetitle">{title}</div>
	
	<div class="content">					
		{content}<br>
	</div>
	
</div>
<div id="footbar">
	<div class="footbar">
		<div id="nav">
		<ul id="navigation">
			<li class="home"><a href="/{index}?mod=settings"><img src="{template}/settings.png" title="Настройки"></a></li>
	        <li class="news"><a href="/{index}?mod=news"><img src="{template}/news.png" title="Новости"></a></li>
	        <li class="static"><a href="/{index}?mod=static"><img src="{template}/static.png" title="Статические страницы"></a></li>
	        <li class="votes"><a href="/{index}?mod=polls"><img src="{template}/polls.png" title="Опросы"></a></li>
	        <li class="admins"><a href="/{index}?mod=admins"><img src="{template}/admins.png" title="Администраторы"></a></li>
	        <li class="accounts"><a href="/{index}?mod=support"><img src="{template}/support.png" title="Тех. поддержка"></a></li>
	        <li class="accounts"><a href="/{index}?mod=accounts"><img src="{template}/accounts.png" title="Аккаунты"></a></li>
	        <li class="players"><a href="/{index}?mod=chars"><img src="{template}/chars.png" title="Персонажи"></a></li>
	        <li class="telnet"><a href="/{index}?mod=telnet"><img src="{template}/telnet.png" title="Telnet"></a></li>
	    </ul>
	    </div>
		{copyright}
	</div>
</div>
</body>
</html>