<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html dir="ltr" xmlns="http://www.w3.org/1999/xhtml" lang="en" xml:lang="en">
<head>
	<title>{title}</title>
	<link rel="shortcut icon" href="{url}/favicon.ico">
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
	<meta name="Description" content="stress, lineage2, la2, stressweb.ru">
	<meta name="Keywords" content="stress, lineage2, la2, stressweb.ru">
	<link href="{template}/css/style.css" rel="stylesheet" type="text/css" />
	<link href="{template}/css/engine.css" rel="stylesheet" type="text/css" />
	{headers}
</head>
<body>

<div id="wrapper">
	
	<div id="container">
		
		<div id="main">
			<div id="head">
				<div class="langs">
					<a href="{url}/index.php?f=ru"><img src="{url}/sysimg/ru.png" alt="ru" /></a>
					<a href="{url}/index.php?f=en"><img src="{url}/sysimg/en.png" alt="en" /></a>
				</div>
			</div>
			<!-- вывод основных страниц -->
			<div id="content">{info}{content}</div>
		</div><!-- #main-->
		
		<div id="sidebar">
			<div id="stitle">&raquo; <span>S</span>erver status</div>
			{server}
			<div id="stitle">&raquo; <span>C</span>ontrol panel</div>
			{login}
			<div id="stitle">&raquo; <span>N</span>avigation</div>
			<div id="menu">
			<ul>
				<li><a href="{url}">Home</a></li>
				<li><a href="{url}/index.php?f=register">Register</a></li>
				<li><a href="{url}/index.php?f=stat">Statistic</a></li>
				<li><a href="{url}/index.php?f=files">Download</a></li>
				<li><a href="{url}/index.php?f=mmotop">MMOTOP Bonus</a></li>
				<li><a href="{url}/index.php?f=l2top">L2Top Bonus</a></li>
			</ul>
			</div>
			<div id="stitle">&raquo; <span>F</span>orum news</div>
			{forum}
			<div id="stitle">&raquo; <span>V</span>ote</div>
			{poll}
			<div id="stitle">&raquo; <span>T</span>op 10 PVP</div>
			{pvptop}
			<div id="stitle">&raquo; <span>T</span>op 10 PK</div>
			{pktop}
		</div><!-- #sidebar-->
		
	</div><!-- #container-->
</div><!-- #wrapper-->

<div id="footer">
	{copyright}<br />Page was generated in {timer} seconds
</div><!-- #footer -->

</body>
</html>