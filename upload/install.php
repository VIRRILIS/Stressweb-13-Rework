<?php
/**
 * STRESS WEB
 * @author S.T.R.E.S.S.
 * @copyright 2008 - 2012 STRESS WEB
 * @version 1.0
 * @web http://stressweb.ru
 */
error_reporting(E_ALL);
define("SW", "13");
define("STRESSWEB", true);
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html>
	<head>
        <meta http-equiv="Content-Type" content="text/html; charset=utf-8">
		<title>Инсталляция CMS Stress Web Lineage</title>
		<link rel="stylesheet" type="text/css" href="install/css.css" />
		<script type="text/javascript" src="install/jquery-1.4.2.min.js"></script>
<script type="text/javascript">
	$(document).ready(function(){ 
		$("#testmysql").click(function(){
			var mshost = $("#dbhost").val();
			var msuser = $("#dbuser").val();
			var mspass = $("#dbpass").val();
			var msname = $("#dbname").val();
			var data = "s=1&mshost="+mshost+"&msuser="+msuser+"&mspass="+mspass+"&msname="+msname;
			$.ajax({			
	  			type: "POST",
	  			url: "install/jqinstall.php",
				data: data,
				dataType: "html",
				success: function(msg){
					$("#mysql_mess").html(msg);
				}
			});
			return false;
		});
		$("#writecfg").click(function(){
			var mshost = $("#dbhost").val();
			var msuser = $("#dbuser").val();
			var mspass = $("#dbpass").val();
			var msname = $("#dbname").val();
			var mscol = $("#dbcollate").val();
			var data = "s=2&mshost="+mshost+"&msuser="+msuser+"&mspass="+mspass+"&msname="+msname+"&mscol="+mscol;
			$.ajax({			
	  			type: "POST",
	  			url: "install/jqinstall.php",
				data: data,
				dataType: "html",
				success: function(msg){
					if(msg == 'OK'){
						location.href='install.php?step=5';
					}
					else{
						alert('Файл config/config.db.php: ошибка записи');
					}
				}
			});
			return false;
		});
		$("#admincrt").click(function(){
			var admname = $("#admname").val();
			var admpass1 = $("#admpass1").val();
			var admpass2 = $("#admpass2").val();
			var admnick = $("#admnick").val();
			if(admname=='' || admpass1 == '' || admpass2 == '' || admnick == ''){
				alert('Заполните все поля');
				return false;
			}
			if(admpass1 != admpass2){
				alert('Пароли не совпадают');
				return false;
			}
			var data = "s=3&name="+admname+"&pass="+admpass1+"&nick="+admnick;
			$.ajax({			
	  			type: "POST",
	  			url: "install/jqinstall.php",
				data: data,
				dataType: "html",
				success: function(msg){
					if(msg == 'ok'){
						location.href='install.php?step=8';
					}
					if(msg == 'conn'){
						alert('Нет связи с БД');
					}
					if(msg == 'db'){
						alert('БД не выбрана');
					}
					if(msg == 'sql'){
						alert('Невозможно выполнить запрос');
					}
				}
			});
		});
 });
</script>
<script type="text/javascript">
function checK()
{
    if (document.install.license_true.checked){
    	location.href='install.php?step=3';
    	return true;		
	}
	else{
		alert("Перед установкой необходимо принять Пользовательское соглашение!");
		return false;		
	}
}
</script>
	</head>
<body><br /><br />
<?php
/**
 * =========================
 * 		Template
 * =========================
 */
function build_template($action, $description, $content, $button1, $button2, $status = "")
{
    echo '
<center>
<form action="" method="get" name="install" >
<table width="620" cellpadding="0" cellspacing="0" class="ins_tab">
<tr>
	<td class="header"><center>Инсталлятор CMS Stress Web Lineage</center></td>
</tr>
<tr>
	<td>
	<table width="100%" cellpadding="0" cellspacing="0" class="cont_tab">
<tr>
	<td width="160" valign="top"><div style="text-align: center; color: white;"><br><br><b>'.$action.'</b><div></td>
	<td width="450" valign="top">
	<div class="content1"><img src="install/images/sw.png" align="left"> <h2>Установка <a href="http://www.stressweb.ru" target="_blank" style="text-decoration:none; color: green;">STRESS WEB</a> '.SW.'</h2><hr><br></div>
	<div class="content2"><center><font color=blue><b>'.$description.'</b></font></center>
	<div class="content3">'.$content.'</div>'.$status.'
	</div>
	</td>
</tr>
<tr>
	<td valign="top">'.$button1.'</td>
	<td align="right" valign="top">'.$button2.'<br><br> <a href="http://stressweb.ru"><span style="font-size: 9px; color: #fff;">© 2008-2013 STRESS WEB</span></a></td>
</tr>
</table>
	</td>
</tr>
</table>
</form>
</center><br>
';
}


/**
 * =========================
 * 		Already Installed???
 * =========================
 */
//if (file_exists(dirname(__file__).DIRECTORY_SEPARATOR."config".DIRECTORY_SEPARATOR."lock.php"))
  //  die('<div>ВНИМАНИЕ! Инсталляция STRESS WEB LIENAGE уже была произведена ранее. Для повторной инсталляции необходимо удалить файл <b>config/lock.php</b> используя FTP клиент.<br /><br /><a href="install.php">Обновить</a></div>');


$step = (isset($_GET['step'])) ? intval($_GET['step']):1;
/**
 * =========================
 * 		Start Page
 * =========================
 */
if ($step == 1)
{
    $_action = "Этап - 1 из 8<br><br>Технические требования";
    $_description = "Добро пожаловать в инсталлятор CMS Stress Web Lineage.<br>
	Cкрипт произведет установку продукта на ваш веб-сайт.";
    $_content = "<br><br>
	<b>Минимальные технические требования:</b><br>
	Веб-сервер Apache версии 2.2 (<a href='http://httpd.apache.org/' target='_blank'>Сайт разработчика</a>)<br>
	База данных MySQL версии 5.2.x (<a href='http://dev.mysql.com/downloads/mysql/' target='_blank'>Сайт разработчика</a>)<br>
	Поддержка PHP версии 5.2 и выше (<a href='http://php.net/' target='_blank'>Сайт разработчика</a>)<br>
	Модуль GD 2.0 и выше<br>
	ionCube Loader 4.0.x (<a href='http://www.ioncube.com/loaders.php' target='_blank'>Сайт разработчика</a>)<br>";
    $_button1 = '<input type="button" value="Прекратить" onclick="location.href=\'install.php\'" style="background: transparent; border: 1px solid; color: white;">';
    $_button2 = '<input type="button" value="Начать установку" onclick="location.href=\'install.php?step=2\'" style="background: transparent; border: 1px solid; color: white;">';
    build_template($_action, $_description, $_content, $_button1, $_button2);
}
/**
 * =========================
 * 		License
 * =========================
 */
if ($step == 2)
{
    $_action = "Этап - 2 из 8<br><br>Пользовательское соглашение";
    $_description = "Условия соглашения обязательны к выполнению";
    ob_start();
    echo "<div style='background: #999999;'>".file_get_contents("install/license.lic")."</div>";
    $_content = ob_get_contents();
    ob_end_clean();
    $_status = "<input type='checkbox' name='license_true'> <b>Принимаю условия Пользовательского соглашения</b>";
    $_button1 = '<input type="button" value="Прекратить" onclick="location.href=\'install.php\'" style="background: transparent; border: 1px solid; color: white;">';
    $_button2 = '<input type="button" value="Перейти к следующему этапу" onclick="javascript: checK();" style="background: transparent; border: 1px solid; color: white;">';
    build_template($_action, $_description, $_content, $_button1, $_button2, $_status);
}
/**
 * =========================
 * 		CHMOD
 * =========================
 */
if ($step == 3)
{
    $important_files = array('./online.txt', './cache/', './config/', './config/config.l2cfg.php', './config/config.db.php', );

    $_action = "Этап - 3 из 8<br><br>Установка прав CHMOD";
    $_description = "Проверка прав на чтение/запись";
    $_button1 = '<input type="button" value="Прекратить" onclick="location.href=\'install.php\'" style="background: transparent; border: 1px solid; color: white;">';
    $_button2 = '<input type="button" value="Перейти к следующему этапу" onclick="location.href=\'install.php?step=4\'" style="background: transparent; border: 1px solid; color: white;">';
    $_content = "<table width='100%'>
	<tr>
<td height='25'>&nbsp;<b>Папка/Файл</b></td>
<td width='60'>&nbsp;<b>CHMOD</b></td>
<td width='100'>&nbsp;<b>Статус</b></td>
	</tr>";
    $chmod_errors = 0;
    $not_found_errors = 0;
    foreach ($important_files as $file)
    {
        if (!file_exists($file))
        {
            $file_status = "<font color=red>не найден!</font>";
            $not_found_errors++;
        } elseif (is_writable($file))
        {
            $file_status = "<font color=green>разрешено</font>";
        }
        else
        {
            @chmod($file, 0777);
            if (is_writable($file))
            {
                $file_status = "<font color=green>разрешено</font>";
            }
            else
            {
                @chmod("$file", 0755);
                if (is_writable($file))
                {
                    $file_status = "<font color=green>разрешено</font>";
                }
                else
                {
                    $file_status = "<font color=red>запрещено</font>";
                    $chmod_errors++;
                }
            }
        }
        $chmod_value = @decoct(@fileperms($file)) % 1000;

        $_content .= "
	<tr>
         <td>&nbsp;$file</td>
         <td>&nbsp; $chmod_value</td>
         <td>&nbsp; $file_status</td>
    </tr>";
    }
    if ($chmod_errors == 0 and $not_found_errors == 0)
    {
        $status_report = '<span style="color: green;">Проверка успешно завершена! Можно продолжать установку!</span>';
    }
    else
    {
        if ($chmod_errors > 0)
        {
            $status_report = "<font color=red>ВНИМАНИЕ! УВАГА! ATTENTION!</font><br /><br />Обнаружены ошибки: <b>$chmod_errors</b>. Нет прав на запись.<br />Необходимо изменить значение CHMOD на 777 для директорий и CHMOD 666 для файлов.<br /><br /><font color=red><b>Дальнейшая установка будет невозможна пока не будут правильно выставлены значения CHMOD.</b><br /></font>После правки значений нажмите F5.<br />";
        }
        if ($not_found_errors > 0)
        {
            $status_report = "<font color=red>Внимание!!!</font><br />Во время проверки обнаружены ошибки. <i>Файлов не найдено:</i> <b>$not_found_errors</b><br /><br /><font color=red><b>Не рекомендуется</b></font> продолжать установку, пока не будут произведены изменения.<br />";
        }
    }
    $_content .= "
<tr>
	<td colspan=3>&nbsp;&nbsp;Состояние проверки</td>
</tr>
<tr>
	<td style='padding: 5px' colspan='3'>$status_report</td>
</tr>    
</table>";
    build_template($_action, $_description, $_content, $_button1, $_button2);
}
/**
 * =========================
 * 		Config Form
 * =========================
 */
if ($step == 4)
{
    $_action = "Этап - 4 из 8<br><br>Настройка соединения с базой данных";
    $_description = "Если данные для доступа в базу данных вам неизвестны, необходимо обратиться к вашему хостинг-провайдеру";
    $_button1 = '<input type="button" value="Прекратить" onclick="location.href=\'install.php\'" style="background: transparent; border: 1px solid; color: white;">';
    $_button2 = '<input type="button" value="Перейти к следующему этапу" id="writecfg" style="background: transparent; border: 1px solid; color: white;">';
    $_content = '
<input type="hidden" name="step" value="5">
<table>
<tr><td colspan="2" height="25" width="430">&nbsp;&nbsp;<b>Внимательно заполните все поля для подключения к MySQL Серверу</b></td></tr>

<tr><td style="padding: 5px;" width="200">Адрес сервера:</td><td style="padding: 5px;"><input type=text size="28" name="dbhost" value="localhost" id="dbhost"></td></tr>

<tr><td style="padding: 5px;">Имя базы:</td><td style="padding: 5px;"><input type=text size="28" name="dbname" value="sw13" id="dbname"></td></tr>

<tr><td style="padding: 5px;">Пользователь базы:</td><td style="padding: 5px;"><input type=text size="28" name="dbuser" value="root" id="dbuser"></td></tr>

<tr><td style="padding: 5px;">Пароль пользователя:</td><td style="padding: 5px;"><input type=password size="28" name="dbpass" id="dbpass"></td></tr>

<tr><td style="padding: 5px;">Кодировка MySQL:<br><span style="font-size: 9px; color: red;">Не изменяйте параметр, если не знаете для чего он предназначен</span></td><td style="padding: 5px;"><input type=text size="28" name="dbcollate" id="dbcollate" value="utf8"></td></tr>

</table>
<div id="mysql_mess"></div>
<input type="button" name="mysql_test" value="Проверить соединение" id="testmysql">';
    build_template($_action, $_description, $_content, $_button1, $_button2);
}
/**
 * =========================
 * 		Install Databases 1
 * =========================
 */
if ($step == 5)
{
    $_action = "Этап - 5 из 8<br><br>Установка базы данных";
    $_description = "Установка базы данных";
    $_button1 = '<input type="button" value="Прекратить" onclick="location.href=\'install.php\'" style="background: transparent; border: 1px solid; color: white;">';
    $_button2 = '<input type="button" value="Продолжить" onclick="location.href=\'install.php?step=6\'" style="background: transparent; border: 1px solid; color: white;">';
    $_content = 'Данные для подключения к MySQL Серверу успешно записаны.<br />Нажмите <b>Продолжить</b> для установки таблиц в базу данных.<br><br><font color=red>Вниманние! Все таблицы в базе данных будут удалены.</font>';
    build_template($_action, $_description, $_content, $_button1, $_button2);
}
/**
 * =========================
 * 		Install Databases 2
 * =========================
 */
if ($step == 6)
{
    $_action = "Этап - 6 из 8<br><br>Установка базы данных";
    $_description = "";
    $_button1 = '<input type="button" value="Прекратить" onclick="location.href=\'install.php\'" style="background: transparent; border: 1px solid; color: white;">';
    $_button2 = '<input type="button" value="Продолжить" onclick="location.href=\'install.php?step=7\'" style="background: transparent; border: 1px solid; color: white;">';

    $table = array('admin', 'l2top_bonus', 'mmotop', 'news', 'poll', 'poll_logs', 'static', 'ticket_section', 'ticket_task', 'ticket_comments');
    $sql = array();

    //-- ----------------------------
    //-- Table structure for stress_admin
    //-- ----------------------------
    $sql['admin'] = "CREATE TABLE `stress_admin` (
  `id` int(11) NOT NULL auto_increment,
  `login` varchar(255) NOT NULL default '',
  `password` varchar(255) NOT NULL default '',
  `nick` varchar(255) NOT NULL default '',
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;";

    //-- ----------------------------
    //-- Table structure for stress_l2top_bonus
    //-- ----------------------------
    $sql['l2top_bonus'] = "CREATE TABLE `stress_l2top_bonus` (
  `id` int(10) NOT NULL auto_increment,
  `date` timestamp NOT NULL default CURRENT_TIMESTAMP,
  `char` varchar(30) NOT NULL,
  `prefix` varchar(50) NOT NULL,
  `give` enum('0','1') NOT NULL default '0',
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;";

	//-- ----------------------------
    //-- Table structure for stress_mmotop
    //-- ----------------------------
    $sql['mmotop'] = "CREATE TABLE `stress_mmotop` (
  `id` int(10) NOT NULL auto_increment,
  `mmoid` int(10) NOT NULL DEFAULT '0',
  `account_name` varchar(100) NOT NULL,
  `charid` int(255) DEFAULT NULL,
  `charname` varchar(255) DEFAULT NULL,
  `ip` varchar(25) DEFAULT NULL,
  `date` timestamp NOT NULL default '0000-00-00 00:00:00',
  `deliver` enum('0','1') NOT NULL default '0',
  `date_deliver` timestamp NOT NULL default '0000-00-00 00:00:00',
  `sid` int(3) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;";

    //-- ----------------------------
    //-- Table structure for stress_news
    //-- ----------------------------
    $sql['news'] = "CREATE TABLE `stress_news` (
  `nid` int(11) NOT NULL auto_increment,
  `author` varchar(50) NOT NULL default '',
  `date` int(11) NOT NULL default '0',
  `title` varchar(255) NOT NULL default '',
  `content` text NOT NULL,
  `full` text NOT NULL,
  `title_en` varchar(255) NOT NULL default '',
  `content_en` text NOT NULL,
  `full_en` text NOT NULL,
  `flink` text NOT NULL,
  `img` text NOT NULL,
  PRIMARY KEY  (`nid`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;";

    //-- ----------------------------
    //-- Table structure for stress_poll
    //-- ----------------------------
    $sql['poll'] = "CREATE TABLE `stress_poll` (
  `id` int(11) NOT NULL auto_increment,
  `title` varchar(255) NOT NULL,
  `title_en` varchar(255) NOT NULL,
  `date` int(11) NOT NULL,
  `poll_num` int(11) NOT NULL default '0',
  `body` text NOT NULL,
  `body_en` text NOT NULL,
  `status` int(11) NOT NULL default '0',
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;";

    //-- ----------------------------
    //-- Table structure for stress_poll_logs
    //-- ----------------------------
    $sql['poll_logs'] = "CREATE TABLE `stress_poll_logs` (
  `id` int(11) NOT NULL auto_increment,
  `pid` int(11) NOT NULL,
  `answ` int(11) NOT NULL default '0',
  `name` varchar(255) NOT NULL,
  `ip` varchar(25) NOT NULL default '0.0.0.0',
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;";

    //-- ----------------------------
    //-- Table structure for stress_static
    //-- ----------------------------
    $sql['static'] = "CREATE TABLE `stress_static` (
  `sid` int(11) NOT NULL auto_increment,
  `s_name` varchar(100) NOT NULL,
  `s_title` varchar(255) NOT NULL default 'title',
  `s_content` text NOT NULL,
  `s_title_en` varchar(255) NOT NULL default 'title',
  `s_content_en` text NOT NULL,
  PRIMARY KEY  (`sid`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;";

    //-- ----------------------------
    //-- Table structure for stress_ticket_section
    //-- ----------------------------
    $sql['ticket_section'] = "CREATE TABLE `stress_ticket_section` (
  `id` int(11) NOT NULL auto_increment,
  `section` varchar(255) NOT NULL default '',
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;";

    //-- ----------------------------
    //-- Table structure for stress_ticket_task
    //-- ----------------------------
    $sql['ticket_task'] = "CREATE TABLE `stress_ticket_task` (
  `id` int(11) NOT NULL auto_increment,
  `sid` int(11) NOT NULL default '0',
  `server` int(11) NOT NULL default '0',
  `title` varchar(255) NOT NULL default '',
  `task` text NOT NULL,
  `severity` int(11) NOT NULL default '0',
  `status` int(11) NOT NULL default '0',
  `name` varchar(255) NOT NULL default '',
  `login` varchar(255) NOT NULL default '',
  `date` int(11) NOT NULL default '0',
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;";

    //-- ----------------------------
    //-- Table structure for stress_ticket_comments
    //-- ----------------------------
    $sql['ticket_comments'] = "CREATE TABLE `stress_ticket_comments` (
  `id` int(11) NOT NULL auto_increment,
  `tid` int(11) NOT NULL default '0',
  `author` varchar(255) NOT NULL default '',
  `comment` text NOT NULL,
  `date` int(11) NOT NULL default '0',
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;";

    //-- ----------------------------
    //-- RECORDS
    //-- ----------------------------
    $table1['news'] = "INSERT INTO `stress_news` VALUES (1, 'S.T.R.E.S.S.', '".time()."', 'STRESS WEB LINEAGE R".SW."', 'Если Вы видите эту страницу значит Вам удалось установить сайт.<br /><br />Для дальнейшей работы перейдите в админ панель по адресу http://".$_SERVER["HTTP_HOST"]."/admin.php и настройте работу сайта по своим требованиям.<br /><br /><br /><br /><i>Спасибо за выбор обвязки STRESS WEB.</i><br /><br /><br /><font color=\"#ff0000\"><b>Для безопасности удалите папку <b>install/</b> и файл <i>install.php</i></b></font><br /><br />', 'Это текст полной новости<br />', 'STRESS WEB LINEAGE R".SW."', 'If you see this page means you have managed to establish a website.<br /><br />For further work, go to the admin panel at http://".$_SERVER["HTTP_HOST"]."/admin.php<br /><br /><br /><br /><i>Thank you for your choice of STRESS WEB.</i><br /><br /><br /><font color=\"#ff0000\"><b>For security remove the folder <b>install/</b> and file <i>install.php</i></b></font><br />', '', '', '');";

    $table1['static'] = "INSERT INTO `stress_static` VALUES (1, 'files', 'Файлы', '<div style=\"text-align: center;\">Патч <a href=\"patch.zip\">скачать</a><br>Клиент <a href=\"client.rar\">скачать</a></div>', 'Files', '<div style=\"text-align: center;\">Patch <a href=\"patch.zip\">download</a><br>Client <a href=\"client.rar\">download</a></div>');";

    $table1['poll'] = "INSERT INTO `stress_poll` VALUES ('1','Как вам новый двиг?', 'How do you like the new web?', '1329713000', '0', '|Очень хорошо|Намного лучше|Хорошо|Не очень|Плохо', '|Very good|Much better|Good|Not very|Bad', '1');";

    $table1['ticket_section'] = "INSERT INTO `stress_ticket_section` VALUES ('1','Технические проблемы');";

    require_once dirname(__file__).DIRECTORY_SEPARATOR."config".DIRECTORY_SEPARATOR."config.db.php";
    $link = @mysqli_connect(DBHOST, DBUSER, DBPASS);
    $db = @mysqli_select_db($link, DBNAME);
    if (!$link or !$db)
    {
        $_content = '<font color=red><b>Невозможно подключиться к MySQL серверу или выбрать базу данных MySQL.</b></font>';
        build_template($_action, $_description, $_content, $_button1, $_button2);
        exit;
    }
    @mysqli_query($link,"/*!40101 SET NAMES '".COLLATE."' */");
    $_content = 'Удаление таблиц:<br>';
    foreach ($table as $tableName)
    {
        $_content .= '<font color=red>Удаление</font> <b>`stress_'.$tableName.'`...</b>';
        if ( mysqli_query($link,"drop table if exists stress_".$tableName ) )
            $_content .= "<font color=green><b>OK</b></font><br>";
        else
            $_content .= @mysqli_error($link)."<br>";
    }
    $_content .= "<br>Создание таблиц:<br>";
    foreach ($table as $tableName)
    {
        $_content .= '<font color=green>Установка</font> <b>`stress_'.$tableName.'`...</b>';
        if (@mysqli_query($link, $sql[$tableName]))
            $_content .= "<font color=green><b>OK</b></font><br>";
        else
            $_content .= @mysqli_error($link)."<br>";
    }
    $_content .= "<br>Запись данных:<br>";
    foreach ($table1 as $key => $var)
    {
        $_content .= '<font color=green>Запись в таблицу</font> <b>`stress_'.$key.'`...</b>';
        if (@mysqli_query($link, $var))
            $_content .= "<font color=green><b>OK</b></font><br>";
        else
            $_content .= @mysqli_error($link)."<br>";
    }
    @mysqli_close($link);
    build_template($_action, $_description, $_content, $_button1, $_button2);
}
/**
 * =========================
 * 		Administrator
 * =========================
 */
if ($step == 7)
{
    $_action = "Этап - 7 из 8<br><br>Создание учетной записи Администратора";
    $_description = "Учетная запись Администратора";
    $_button1 = '<input type="button" value="Прекратить" onclick="location.href=\'install.php\'" style="background: transparent; border: 1px solid; color: white;">';
    $_button2 = '<input type="button" value="Продолжить" id="admincrt" style="background: transparent; border: 1px solid; color: white;">';
    $_content = '
<table>
<tr><td colspan="2" height="40" width="430">&nbsp;&nbsp;<b>Внимательно заполните поля и запомните пароль</b></td></tr>

<tr><td style="padding: 5px;" width="200">Имя пользователя:</td><td style="padding: 5px;"><input type=text size="28" name="admlogin" id="admname" ></td></tr>

<tr><td style="padding: 5px;" width="200">Пароль:</td><td style="padding: 5px;"><input type=password size="28" name="admpass" id="admpass1" ></td></tr>

<tr><td style="padding: 5px;" width="200">Повторите Пароль:</td><td style="padding: 5px;"><input type=password size="28" name="admpass2" id="admpass2" ></td></tr>

<tr><td style="padding: 5px;" width="200">Псевдоним для публикации новостей:</td><td style="padding: 5px;"><input type=text size="28" name="admnick" id="admnick" ></td></tr>

</table>';
    build_template($_action, $_description, $_content, $_button1, $_button2);
}
/**
 * =========================
 * 		Finish
 * =========================
 */
if ($step == 8)
{
    //$lock = fopen(dirname(__file__).DIRECTORY_SEPARATOR."config".DIRECTORY_SEPARATOR."lock.php", "w+");
    //fwrite($lock, "LOCKED");
    //fclose($lock);
    //@chmod(dirname(__file__).DIRECTORY_SEPARATOR."config".DIRECTORY_SEPARATOR."lock.php", 0666);
    $_action = "Этап 8 из 8<br><br>Установка";
    $_description = "Завершение установки";
    $_button1 = "";
    $_button2 = "";
    $_content = '<font color=green><b>Поздравляем! Установка полностью завершена.</b></font><br><br><br><b><font color=red>Для дальнейшей работы Вам необходимо войти в Панель Администратора и настроить сайт. Так же не забудьте удалить файл <font color=blue>install.php</font> и директорию</font> <font color=blue>install</font></b><br><br>';
    $_content .= '<center><a href="/">Главная сайта</a><br><a href="/?admin.php">Админ панель</a></center>';
	
	build_template($_action, $_description, $_content, $_button1, $_button2);
}
?>
</body>
</html>