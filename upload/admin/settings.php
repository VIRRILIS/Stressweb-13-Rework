<?php

if ( !defined("STRESSWEB") )
    die( "Access denied..." );

if ( !$controller->isAdmin() or !defined('DEVELOP') ) 
{
    $controller->redirect( "index.php" );
}

$conf_content = '';

/**
 * ==============================
 * Save config file
 * ============================== 
 */
if ( isset($_POST["act"]) and $_POST["act"] == "doSave" ) 
{
    $savedata = isset( $_POST["savedata"] ) ? $_POST["savedata"]:array();
    $fopen = fopen( CONFDIR.'config.l2cfg.php', "w" );
    if ( $fopen ) {
        fwrite( $fopen, "<?php\n" );
        fwrite( $fopen, "/**\n" );
        fwrite( $fopen, "* STRESS WEB\n" );
        fwrite( $fopen, "* @author S.T.R.E.S.S.\n" );
        fwrite( $fopen, "* @copyright 2008 - 2012 STRESS WEB\n" );
        fwrite( $fopen, "* @version 13\n" );
        fwrite( $fopen, "* @web http://stressweb.ru\n" );
        fwrite( $fopen, " */\n" );
        fwrite( $fopen, "if (!defined('STRESSWEB')) die ('Access denied...');\n" );
        $controller->cfgWrite( $fopen, $savedata, "\$l2cfg" );
        fwrite( $fopen, "\n?>" );
        fclose( $fopen );
        $errr = "Настройки успешно сохранены";
    } else
        $errr = "Ошибка записи";
    $conf_content .= "<center>{$errr}<br><a href='{$_url}=settings'>Назад</a></center>";
} 
else 
{
    $forumList = array( 'ipb' => 'IPB', 'phpbb' => 'phpBB', 'smf' => 'SMF', 'vbulletin' => 'vBulletin', 'xenforo' => 'XenFoRo', );
    $true_false = array( "false" => "Выкл", "true" => "Вкл" );
    /**
     * ==============================
     * Show config settings
     * ============================== 
     */

    ob_start();
    echo '
<br />
<table width="100%" border="0" cellpadding="0" cellspacing="0" id="List">
<tr>
    <td bgcolor="#EEEFEF" height="55" style="padding-left:10px; color: #c00;" align="left">
		<a href="javascript:ChangeOption(\'divGeneral\');"><img src="' . TPLDIR . '/general.png" title="Общие настройки"></a> &nbsp; &nbsp;  
		<a href="javascript:ChangeOption(\'divCode\');"><img src="' . TPLDIR . '/code.png" title="Безопасность"></a> &nbsp; &nbsp;  
		<a href="javascript:ChangeOption(\'divSMTP\');"><img src="' . TPLDIR . '/mail.png" title="Настройка E-Mail"></a> &nbsp; &nbsp;
		<a href="javascript:ChangeOption(\'divForum\');"><img src="' . TPLDIR . '/forum.png" title="Темы с Форума"></a> &nbsp; &nbsp; 
		<a href="javascript:ChangeOption(\'divCache\');"><img src="' . TPLDIR . '/cache.png" title="Настройки кеширования"></a> &nbsp; &nbsp; 
		<a href="javascript:ChangeOption(\'divLogin\');"><img src="' . TPLDIR . '/login.png" title="Логин Сервер"></a> &nbsp; &nbsp;
		<a href="javascript:ChangeOption(\'divGame\');"><img src="' . TPLDIR . '/game.png" title="Игровой Сервер"></a> &nbsp; &nbsp;
		<a href="javascript:ChangeOption(\'divL2Top\');"><img src="' . TPLDIR . '/l2top.png" title="L2Top"></a> &nbsp; &nbsp; 
		<a href="javascript:ChangeOption(\'divRobokassa\');"><img src="' . TPLDIR . '/rk.png" title="Robokassa"></a>
	</td>
</tr>
</table><br />
<form action="'.$_url.'=settings" method="post">
<table width="100%" cellpadding="0" cellspacing="0">
<tr id="divGeneral" style=""><td>
<table width="100%" border="0" cellpadding="0" cellspacing="0" style="border: 1px solid #AAA;" class="shadow">
<tr>
    <td bgcolor="#EEEFEF" height="29" style="padding-left:5px;" align="left">
		Общие настройки
	</td>
</tr>
</table><br />
<table width="100%" cellpadding="0" cellspacing="0" class="tab">';

    $controller->ShowTr( "Адрес сайта", "Указывать без слеша '/' в конце", "<input type='text' name='savedata[siteurl]' value='{$l2cfg["siteurl"]}' style='width: 245px;'>" );
    $controller->ShowTr( "Название сайта", "Будет отображаться в заголовке сайта", "<input type='text' name='savedata[title]' value='{$l2cfg["title"]}' style='width: 245px;'>" );
    $controller->ShowTr( "Сopyright @", "Будет отображаться при выводе копирайтов", "<input type='text' name='savedata[copy]' value='{$l2cfg["copy"]}' style='width: 245px;'>" );
    $controller->ShowTr( "Основной шаблон", "Выберите шаблон, который будет использоваться на сайте", $controller->select("savedata[template]", $controller->TemplatesList(), $l2cfg['template'], "style='width:150px;'") );
    $controller->ShowTr( "Секретное слово", "Используется для защиты данных.", "<input type='text' name='savedata[salt]' value='{$l2cfg["salt"]}' style='width: 150px;'>" );
    $controller->ShowTr( "Используемый язык", "Выберите язык, который будет использоваться при работе с системой", $controller->select("savedata[lang]", $langList, $l2cfg['lang'], "style='width:150px;'") );
    $controller->ShowTr( "Главная страница", "Новости / статическая страница.", $controller->select("savedata[main][page][static]", array("false" => "Новости", "true" => "Статическая"), $l2cfg["main"]["page"]["static"], "style='width:100px'") );
    $controller->ShowTr( "Название главной страницы", "Имя статической страницы. Указывается если главная страница статическая.", "<input type='text' name='savedata[main][page][name]' value='{$l2cfg["main"]["page"]["name"]}' style='width: 100px;'>" );
    $controller->ShowTr( "Количество новостей на страницу", "Кол-во кратких новостей, которое будет выводиться на страницу. По умолчанию: <b>5</b>.", "<input type='text' name='savedata[news][perpage]' value='{$l2cfg["news"]["perpage"]}' style='width: 50px;'>" );
    $controller->ShowTr( "Формат времени для Новостей", "Формат вывода даты новостей. По умолчанию: <b>d.m.y H:i</b>. Подробнее смотрите <a href='http://www.php.net/manual/en/function.date.php'>здесь</a>", "<input type='text' name='savedata[news][date]' value='{$l2cfg["news"]["date"]}' style='width: 100px;'>" );
    $controller->ShowTr( "Порядок сортировки новостей", "Выберите порядок сортировки новостей", $controller->select("savedata[news][sort]", array("DESC" => "По убыванию", "ASC" => "По возрастанию"), $l2cfg["news"]["sort"], "style='width:150px'") );
    $controller->ShowTr( "Коррекция временной зоны", "Используется для вывода статистики. Указывается в минутах. Пример: 60 = +1 час; -60 = -1 час", "<input type='text' name='savedata[timezone]' value='{$l2cfg["timezone"]}' style='width: 50px;'>" );
    $controller->ShowTr( "Регистрация новых пользователей", "Включена / Выключена", $controller->select("savedata[reg_enable]", $true_false, $l2cfg["reg_enable"], "style='width:100px'") );
    $controller->ShowTr( "Мультиаккаунт", " Позволить / запретить регистрировать на один E-Mail несколько аккаунтов", $controller->select("savedata[reg_multi]", $true_false, $l2cfg["reg_multi"], "style='width:100px'") );
    $controller->ShowTr( "Префикс при регистрации", "Будет добавляться к аккаунту", $controller->select("savedata[reg_prefix]", $true_false, $l2cfg["reg_prefix"], "style='width:100px'") );
    $controller->ShowTr( "Расширенная регистрация", "Активация аккаунта через E-Mail", $controller->select("savedata[reg_activate]", $true_false, $l2cfg["reg_activate"], "style='width:100px'") );
    $controller->ShowTr( "Расширенное восстановление пароля", "Если включено, то новый пароль будет отправляться на E-Mail. Выключено - новый пароль выводится на сайте", $controller->select("savedata[forget_activate]", $true_false, $l2cfg["forget_activate"], "style='width:100px'") );
    $controller->ShowTr( "Расширенная смена пароля", "Если включено, то на E-Mail отправляться письмо с активацией", $controller->select("savedata[chpass_activate]", $true_false, $l2cfg["chpass_activate"], "style='width:100px'") );
    $controller->ShowTr( "Расширенная смена e-mail", "Если включено, то на E-Mail отправляться письмо с активацией", $controller->select("savedata[chmail_activate]", $true_false, $l2cfg["chmail_activate"], "style='width:100px'") );
    $controller->ShowTr( "Количество серверов Авторизации", "По умолчанию: <b>1</b>", "<input type='text' name='savedata[ls][count]' value='{$l2cfg["ls"]["count"]}' style='width: 50px;'>" );
    $controller->ShowTr( "Количество Игровых серверов", "По умолчанию: <b>1</b>", "<input type='text' name='savedata[gs][count]' value='{$l2cfg["gs"]["count"]}' style='width: 50px;'>" );
    $controller->ShowTr( "Вывод статуса серверов", "Включить / Выключить", $controller->select("savedata[server][enable]", $true_false, $l2cfg["server"]["enable"], "style='width:100px'") );
    $controller->ShowTr( "Генерировать файл онлайна (online.txt)", "Включить / Выключить", $controller->select("savedata[txt][enable]", $true_false, $l2cfg["txt"]["enable"], "style='width:100px'") );
    $controller->ShowTr( "Сервер для генерации файла онлайна", "", $controller->select("savedata[txt][gs]", $cfgSList, $l2cfg["txt"]["gs"], "style='width:100px'") );
    $controller->ShowTr( "Обратная связь", "Включить / Выключить", $controller->select("savedata[support][enable]", $true_false, $l2cfg["support"]["enable"], "style='width:100px'") );
    $controller->ShowTr( "Выводить сообщения об ошибках MySQL", "По умолчанию: <b>выкл</b>", $controller->select("savedata[mysql][debug]", $true_false, $l2cfg["mysql"]["debug"], "style='width:100px'") );
    $controller->ShowTr( "Выключить сайт:", "Перевести сайт в состояние offline, для проведения технических работ", $controller->select("savedata[offline][enable]", array("true" => "Да", "false" => "Нет"), $l2cfg["offline"]["enable"], "style='width:100px'") );
    $controller->ShowTr( "Причина отключения сайта:", "Сообщение для отображения в режиме отключенного сайта", "<textarea style='width:250px; height:100px;' name='savedata[offline][reason]'>{$l2cfg["offline"]["reason"]}</textarea>" );

    echo '</table></td></tr>
	<tr id="divCode" style="display:none"><td>
	<table width="100%" border="0" cellpadding="0" cellspacing="0" style="border: 1px solid #AAA;" class="shadow">
	<tr>
	    <td bgcolor="#EEEFEF" height="29" style="padding-left:5px;" align="left">
			Настройки безопасности
		</td>
	</tr>
	</table><br />
	<table width="100%" cellpadding="0" cellspacing="0" class="tab">';

    $controller->ShowTr( "reCAPTCHA Public Key", "Требуется если используется reCAPTCHA (получить можно <a href='http://www.google.com/recaptcha/whyrecaptcha' target='_blank'>здесь</a>)", "<input type='text' name='savedata[captcha][publickey]' value='{$l2cfg["captcha"]["publickey"]}' style='width: 250px;'>" );
    $controller->ShowTr( "reCAPTCHA Private Key", "Требуется если используется reCAPTCHA", "<input type='text' name='savedata[captcha][privatekey]' value='{$l2cfg["captcha"]["privatekey"]}' style='width: 250px;'>" );
    $controller->ShowTr( "Код безопасности Вход в АдминПанель", "Выберите какую каптчу хотите использовать", $controller->select("savedata[captcha][admin_type]", array('sw' => 'Стандартная', 'recaptcha' => 'reCAPTCHA'), $l2cfg["captcha"]["admin_type"], "style='width:100px'") );
    $controller->ShowTr( "Код безопасности Регистрация", "Отображение кода безопасности при регистрации для защиты от автоматической регистрации", $controller->select("savedata[captcha][reg]", $true_false, $l2cfg["captcha"]["reg"], "style='width:100px'").' '.$controller->select("savedata[captcha][reg_type]", array('sw' => 'Стандартная', 'recaptcha' => 'reCAPTCHA'), $l2cfg["captcha"]["reg_type"], "style='width:100px'") );
    $controller->ShowTr( "Код безопасности Вход в Кабинет", "Отображение кода безопасности при входе в кабинет для защиты от подбора пароля", $controller->select("savedata[captcha][profile]", $true_false, $l2cfg["captcha"]["profile"], "style='width:100px'").' '.$controller->select("savedata[captcha][profile_type]", array('sw' => 'Стандартная', 'recaptcha' => 'reCAPTCHA'), $l2cfg["captcha"]["profile_type"], "style='width:100px'") );
    $controller->ShowTr( "Код безопасности Восстановление пароля", "Отображение кода безопасности при восстановлении пароля для защиты", $controller->select("savedata[captcha][repass]", $true_false, $l2cfg["captcha"]["repass"], "style='width:100px'").' '.$controller->select("savedata[captcha][repass_type]", array('sw' => 'Стандартная', 'recaptcha' => 'reCAPTCHA'), $l2cfg["captcha"]["repass_type"], "style='width:100px'") );
    $controller->ShowTr( "Код безопасности L2Top Bonus", "Отображение кода безопасности при получении бонусов L2Top", $controller->select("savedata[captcha][l2top]", $true_false, $l2cfg["captcha"]["l2top"], "style='width:100px'").' '.$controller->select("savedata[captcha][l2top_type]", array('sw' => 'Стандартная', 'recaptcha' => 'reCAPTCHA'), $l2cfg["captcha"]["l2top_type"], "style='width:100px'") );
    $controller->ShowTr( "Код безопасности MMOTop Bonus", "Отображение кода безопасности при получении бонусов MMOTOP", $controller->select("savedata[captcha][mmotop]", $true_false, $l2cfg["captcha"]["mmotop"], "style='width:100px'").' '.$controller->select("savedata[captcha][mmotop_type]", array('sw' => 'Стандартная', 'recaptcha' => 'reCAPTCHA'), $l2cfg["captcha"]["mmotop_type"], "style='width:100px'") );

    echo '</table></td></tr>
	<tr id="divSMTP" style="display:none"><td>
	<table width="100%" border="0" cellpadding="0" cellspacing="0" style="border: 1px solid #AAA;" class="shadow">
	<tr>
	    <td bgcolor="#EEEFEF" height="29" style="padding-left:5px;" align="left">
			Настройки E-Mail
		</td>
	</tr>
	</table><br />
	<table width="100%" cellpadding="0" cellspacing="0" class="tab">';

    $controller->ShowTr( "E-Mail адрес администратора", "Введите E-mail адрес администратора сайта", "<input type='text' name='savedata[mail_admin]' value='{$l2cfg["mail_admin"]}' style='width: 150px;'>" );
    $controller->ShowTr( "Метод отправки E-Mail", "Если функция PHP mail() недоступна, выберите метод SMTP", $controller->select("savedata[mail_method]", array("mail" => "PHP Mail()", "smtp" => "SMTP"), $l2cfg["mail_method"], "style='width:100px'") );
    $controller->ShowTr( "Кодировка сообщений", "По умолчанию: <b>utf-8</b>", "<input type='text' name='savedata[mail_charset]' value='{$l2cfg["mail_charset"]}' style='width: 150px;'>" );
    $controller->ShowTr( "Сервер исходящей почты SMTP", "Например: <b>smtp.gmail.com</b>", "<input type='text' name='savedata[mail_smtphost]' value='{$l2cfg["mail_smtphost"]}' style='width: 150px;'>" );
    $controller->ShowTr( "Порт сервера SMTP", "По умолчанию: <b>25</b>. В случае использования службы Gmail, порт 465.", "<input type='text' name='savedata[mail_smtpport]' value='{$l2cfg["mail_smtpport"]}' style='width: 50px;'>" );
    $controller->ShowTr( "Логин пользователя SMTP", "Например: <b>username@gmail.com</b>", "<input type='text' name='savedata[mail_smtpuser]' value='{$l2cfg["mail_smtpuser"]}' style='width: 150px;'>" );
    $controller->ShowTr( "Пароль пользователя SMTP", "", "<input type='password' name='savedata[mail_smtppass]' value='{$l2cfg["mail_smtppass"]}' style='width: 150px;'>" );
    $controller->ShowTr( "E-mail для авторизации на SMTP сервере в качестве отправителя", "Некоторые бесплатные почтовые сервисы требуют, чтобы в качестве E-mail адреса отправителя был указан именно адрес, зарегистрированный на их почтовом сервисе.", "<input type='text' name='savedata[mail_smtpmail]' value='{$l2cfg["mail_smtpmail"]}' style='width: 150px;'>" );
    $controller->ShowTr( "От кого", "По умолчанию: <b>Игровой сервер Lineage</b>", "<input type='text' name='savedata[mail_from]' value='{$l2cfg["mail_from"]}' style='width: 150px;'>" );

    echo '</table></td></tr>
	<tr id="divForum" style="display:none"><td>
	<table width="100%" border="0" cellpadding="0" cellspacing="0" style="border: 1px solid #AAA;" class="shadow">
	<tr>
	    <td bgcolor="#EEEFEF" height="29" style="padding-left:5px;" align="left">
			Настройки вывода тем с форума
		</td>
	</tr>
	</table><br />
	<table width="100%" cellpadding="0" cellspacing="0" class="tab">';

    $controller->ShowTr( "Модуль вывода последних тем форума", "Включить / выключить", $controller->select("savedata[forum][enable]", $true_false, $l2cfg["forum"]["enable"], "style='width:100px'") );
    $controller->ShowTr( "Версия форума", "", $controller->select("savedata[forum][version]", $forumList, $l2cfg["forum"]["version"], "style='width:100px'") );
    $controller->ShowTr( "Адрес форума", "Например: <b>http://site.com/forum</b> (без «/»  в конце)", "<input type='text' name='savedata[forum][url]' value='{$l2cfg["forum"]["url"]}' style='width: 150px;'>" );
    $controller->ShowTr( "Количество выводимых тем", "По умолчанию: <b>5</b>", "<input type='text' name='savedata[forum][count]' value='{$l2cfg["forum"]["count"]}' style='width: 50px;'>" );
    $controller->ShowTr( "Длина выводимой строки", "Если длина темы с форума превышает данное кол-во символов, строка будет обрезана. По умолчанию: <b>25</b>", "<input type='text' name='savedata[forum][length]' value='{$l2cfg["forum"]["length"]}' style='width: 50px;'>" );
    $controller->ShowTr( "Дата", "Формат вывода даты. По умолчанию: <b>d.m.y H:i</b>. Подробнее смотрите <a href='http://www.php.net/manual/en/function.date.php'>здесь</a>", "<input type='text' name='savedata[forum][date]' value='{$l2cfg["forum"]["date"]}' style='width: 100px;'>" );
    $controller->ShowTr( "IP адрес Базы Данных форума", "По умолчанию: <b>localhost</b>", "<input type='text' name='savedata[forum][dbhost]' value='{$l2cfg["forum"]["dbhost"]}' style='width: 100px;'>" );
    $controller->ShowTr( "Пользователь Базы Данных Форума", "По умолчанию: <b>root</b>", "<input type='text' name='savedata[forum][dbuser]' value='{$l2cfg["forum"]["dbuser"]}' style='width: 100px;'>" );
    $controller->ShowTr( "Пароль пользователя Базы Данных форума", "", "<input type='password' name='savedata[forum][dbpass]' value='{$l2cfg["forum"]["dbpass"]}' style='width: 100px;'>" );
    $controller->ShowTr( "Имя Базы Данных MySQL форума", "Не изменяйте параметр, если не знаете для чего он предназначен", "<input type='text' name='savedata[forum][dbname]' value='{$l2cfg["forum"]["dbname"]}' style='width: 100px;'>" );
    $controller->ShowTr( "Кодировка Базы Данных форума", "", "<input type='text' name='savedata[forum][dbcoll]' value='{$l2cfg["forum"]["dbcoll"]}' style='width: 100px;'>" );
    $controller->ShowTr( "Префикс", "Префикс таблиц форума. Например IPB использует: <b>ibf_</b>", "<input type='text' name='savedata[forum][prefix]' value='{$l2cfg["forum"]["prefix"]}' style='width: 50px;'>" );
    $controller->ShowTr( "Запрещеные форумы", "ID-список форумов с которых не выводить последние темы; Указывать через запятую;", "<input type='text' name='savedata[forum][deny]' value='{$l2cfg["forum"]["deny"]}' style='width: 150px;'>" );

    echo '</table></td></tr>
	<tr id="divCache" style="display:none"><td>
	<table width="100%" border="0" cellpadding="0" cellspacing="0" style="border: 1px solid #AAA;" class="shadow">
	<tr>
	    <td bgcolor="#EEEFEF" height="29" style="padding-left:5px;" align="left">
			Настройки кеширования
		</td>
	</tr>
	</table><br />
	<table width="100%" cellpadding="0" cellspacing="0" class="tab">';

    $controller->ShowTr( "Кэширование", "Включить / Выключить", $controller->select("savedata[cache][enable]", $true_false, $l2cfg["cache"]["enable"], "style='width:100px'") );
    $controller->ShowTr( "Интервал кэширования Темы с форума", "Задается в минутах. По умолчанию: <b>1</b>. Для отключения установите <b>0</b>", "<input type='text' name='savedata[cache][forum]' value='{$l2cfg["cache"]["forum"]}' style='width: 50px;'>" );
    $controller->ShowTr( "Интервал кэширования списка персонажей на аккаунте в ЛК", "Задается в минутах. По умолчанию: <b>1</b>. Для отключения установите <b>0</b>", "<input type='text' name='savedata[cache][login]' value='{$l2cfg["cache"]["login"]}' style='width: 50px;'>" );
    $controller->ShowTr( "Интервал кэширования просмотра инвентаря персонажа в ЛК", "Задается в минутах. По умолчанию: <b>1</b>. Для отключения установите <b>0</b>", "<input type='text' name='savedata[cache][char]' value='{$l2cfg["cache"]["char"]}' style='width: 50px;'>" );
    $controller->ShowTr( "Интервал кэширования Статус серверов", "Задается в минутах. По умолчанию: <b>1</b>. Для отключения установите <b>0</b>", "<input type='text' name='savedata[cache][sList]' value='{$l2cfg["cache"]["sList"]}' style='width: 50px;'>" );
    $controller->ShowTr( "Интервал кэширования статистики Общая", "Задается в минутах. По умолчанию: <b>1</b>. Для отключения установите <b>0</b>", "<input type='text' name='savedata[cache][stat]' value='{$l2cfg["cache"]["stat"]}' style='width: 50px;'>" );
    $controller->ShowTr( "Интервал кэширования статистики Онлайн", "Задается в минутах. По умолчанию: <b>1</b>. Для отключения установите <b>0</b>", "<input type='text' name='savedata[cache][online]' value='{$l2cfg["cache"]["online"]}' style='width: 50px;'>" );
    $controller->ShowTr( "Интервал кэширования статистики Топ", "Задается в минутах. По умолчанию: <b>1</b>. Для отключения установите <b>0</b>", "<input type='text' name='savedata[cache][top]' value='{$l2cfg["cache"]["top"]}' style='width: 50px;'>" );
    $controller->ShowTr( "Интервал кэширования статистики Топ PvP", "Задается в минутах. По умолчанию: <b>1</b>. Для отключения установите <b>0</b>", "<input type='text' name='savedata[cache][pvp]' value='{$l2cfg["cache"]["pvp"]}' style='width: 50px;'>" );
    $controller->ShowTr( "Интервал кэширования статистики Топ PK", "Задается в минутах. По умолчанию: <b>1</b>. Для отключения установите <b>0</b>", "<input type='text' name='savedata[cache][pk]' value='{$l2cfg["cache"]["pk"]}' style='width: 50px;'>" );
    $controller->ShowTr( "Интервал кэширования статистики Топ Клан", "Задается в минутах. По умолчанию: <b>1</b>. Для отключения установите <b>0</b>", "<input type='text' name='savedata[cache][clan]' value='{$l2cfg["cache"]["clan"]}' style='width: 50px;'>" );
    $controller->ShowTr( "Интервал кэширования данных по кланам", "Задается в минутах. По умолчанию: <b>1</b>. Для отключения установите <b>0</b>", "<input type='text' name='savedata[cache][clanview]' value='{$l2cfg["cache"]["clanview"]}' style='width: 50px;'>" );
    $controller->ShowTr( "Интервал кэширования данных по Замкам", "Задается в минутах. По умолчанию: <b>1</b>. Для отключения установите <b>0</b>", "<input type='text' name='savedata[cache][castle]' value='{$l2cfg["cache"]["castle"]}' style='width: 50px;'>" );
    $controller->ShowTr( "Интервал кэширования данных по RAIDBOSS'ам", "Задается в минутах. По умолчанию: <b>1</b>. Для отключения установите <b>0</b>", "<input type='text' name='savedata[cache][raid]' value='{$l2cfg["cache"]["raid"]}' style='width: 50px;'>" );
    $controller->ShowTr( "Интервал кэширования данных по EPICBOSS'ам", "Задается в минутах. По умолчанию: <b>1</b>. Для отключения установите <b>0</b>", "<input type='text' name='savedata[cache][epic]' value='{$l2cfg["cache"]["epic"]}' style='width: 50px;'>" );
    $controller->ShowTr( "Интервал кэширования статистики Олимпиады", "Задается в минутах. По умолчанию: <b>1</b>. Для отключения установите <b>0</b>", "<input type='text' name='savedata[cache][oly]' value='{$l2cfg["cache"]["oly"]}' style='width: 50px;'>" );
    $controller->ShowTr( "Интервал кэширования статистики Топ Богачей", "Задается в минутах. По умолчанию: <b>1</b>. Для отключения установите <b>0</b>", "<input type='text' name='savedata[cache][rich]' value='{$l2cfg["cache"]["rich"]}' style='width: 50px;'>" );

    echo '</table></td></tr>
	<tr id="divL2Top" style="display:none"><td>
	<table width="100%" border="0" cellpadding="0" cellspacing="0" style="border: 1px solid #AAA;" class="shadow">
	<tr>
	    <td bgcolor="#EEEFEF" height="29" style="padding-left:5px;" align="left">
			Настройки L2Top
		</td>
	</tr>
	</table><br />
	<table width="100%" cellpadding="0" cellspacing="0" class="tab">';

    $controller->ShowTr( "Состояние сервиса", "", $controller->select("savedata[l2top][enable]", $true_false, $l2cfg["l2top"]["enable"], "style='width:100px'") );
    $controller->ShowTr( "Ваш ID в L2Top", "", "<input type='text' name='savedata[l2top][id]' value='{$l2cfg["l2top"]["id"]}' style='width: 50px;'>" );
    $controller->ShowTr( "Ссылка на список последних 500 проголосовавших из админ-панели L2Top", "Получить ссылку можно в админ-панели http://l2top.ru", "<input type='text' name='savedata[l2top][url]' value='{$l2cfg["l2top"]["url"]}' style='width: 245px;'>" );

    echo '</table></td></tr>
	<tr id="divRobokassa" style="display:none"><td>
	<table width="100%" border="0" cellpadding="0" cellspacing="0" style="border: 1px solid #AAA;" class="shadow">
	<tr>
	    <td bgcolor="#EEEFEF" height="29" style="padding-left:5px;" align="left">
			Настройки RoboKassa
		</td>
	</tr>
	</table><br />
	<table width="100%" cellpadding="0" cellspacing="0" class="tab">';

    $controller->ShowTr( "Состояние сервиса", "", $controller->select("savedata[rb][enable]", $true_false, $l2cfg["rb"]["enable"], "style='width:100px'") );
    $controller->ShowTr( "Описание платежей", "Отображается в назначении платежа", "<input type='text' name='savedata[rb][invdesc]' value='{$l2cfg["rb"]["invdesc"]}' style='width: 245px;'>" );
    $controller->ShowTr( "Логин Продавца", "Ваш логин в системе ROBOKASSA", "<input type='text' name='savedata[rb][mrhlogin]' value='{$l2cfg["rb"]["mrhlogin"]}' style='width: 100px;'>" );
    $controller->ShowTr( "Пароль №1", "Указан в системе ROBOKASSA в разделе <b>Администрирование</b>", "<input type='password' name='savedata[rb][mrhpass1]' value='{$l2cfg["rb"]["mrhpass1"]}' style='width: 100px;'>" );
    $controller->ShowTr( "Пароль №2", "Указан в системе ROBOKASSA в разделе <b>Администрирование</b>", "<input type='password' name='savedata[rb][mrhpass2]' value='{$l2cfg["rb"]["mrhpass2"]}' style='width: 100px;'>" );

    echo "<tr><td colspan=2><br /><font color='#e00'>Внимание!!! Другие настройки перенесены в раздел Гейм Серверов</font></td></tr>";
    echo "</table></td></tr>";

    $LS_menu = "";
    $LSarray = array();
    for ( $i = 1; $i <= $l2cfg["ls"]["count"]; $i++ ) {
        $LS_menu .= " ..::<a href=\"javascript:ChangeLS('ls_{$i}');\">LoginServer [{$i}]</a>::.. ";
        $LSarray[$i] = "Login ".$i;
    }

    echo "
	<tr id='divLogin' style='display:none'><td>
	<table width='100%' border='0' cellpadding='0' cellspacing='0' style='border: 1px solid #AAA;' class='shadow'>
	<tr>
	    <td bgcolor='#EEEFEF' height='29' style='padding-left:5px; align='left' class='logins'>
			{$LS_menu}
		</td>
	</tr>
	</table><br />";

    $s = 1;
    for ( $i = 1; $i <= $l2cfg["ls"]["count"]; $i++ ) {
        $display = ( $s == 1 ) ? "":"display:none";
        echo '<div id="ls_'.$s.'" style="'.$display.'"><table width="100%" cellpadding="0" cellspacing="0" class="tab">';
        $controller->ShowTr( "Включить / Выключить", "", $controller->select("savedata[ls][$i][on]", $true_false, $l2cfg["ls"][$i]["on"], "style='width:100px'") );
        $controller->ShowTr( "Тип сборки сервера авторизации", "", $controller->select("savedata[ls][$i][version]", $vList, $l2cfg["ls"][$i]["version"], "style='width:100px'") );
        $controller->ShowTr( "IP адрес сервера авторизации", "По умолчанию: <b>127.0.0.1</b>", "<input type='text' name='savedata[ls][$i][host]' value='{$l2cfg["ls"][$i]["host"]}' style='width: 100px;'>" );
        $controller->ShowTr( "Порт сервера авторизации", "По умолчанию: <b>2106</b>", "<input type='text' name='savedata[ls][$i][port]' value='{$l2cfg["ls"][$i]["port"]}' style='width: 50px;'>" );
        $controller->ShowTr( "IP адрес Базы Данных MySQL сервера", "По умолчанию: <b>localhost</b>", "<input type='text' name='savedata[ls][$i][dbhost]' value='{$l2cfg["ls"][$i]["dbhost"]}' style='width: 100px;'>" );
        $controller->ShowTr( "Пользователь Базы Данных MySQL", "По умолчанию: <b>root</b>", "<input type='text' name='savedata[ls][$i][dbuser]' value='{$l2cfg["ls"][$i]["dbuser"]}' style='width: 100px;'>" );
        $controller->ShowTr( "Пароль пользователя Базы Данных MySQL", "", "<input type='password' name='savedata[ls][$i][dbpass]' value='{$l2cfg["ls"][$i]["dbpass"]}' style='width: 100px;'>" );
        $controller->ShowTr( "Имя Базы Данных", "По умолчанию: <b>l2jdb</b>", "<input type='text' name='savedata[ls][$i][dbname]' value='{$l2cfg["ls"][$i]["dbname"]}' style='width: 100px;'>" );
        $controller->ShowTr( "Тип шифрования пароля", "", $controller->select("savedata[ls][$i][encode]", array("sha1" => "SHA1", "whirlpool" => "WHIRLPOOL"), $l2cfg["ls"][$i]["encode"], "style='width:100px'") );
        echo '</table></div>';
        $s++;
    }
    echo "</td></tr>";

    $GS_menu = "";
    for ( $i = 1; $i <= $l2cfg["gs"]["count"]; $i++ ) {
        $GS_menu .= " ..::<a href=\"javascript:ChangeGS('gs_{$i}');\">GameServer [{$i}]</a>::.. ";
    }

    echo "
	<tr id='divGame' style='display:none'><td>
	<table width='100%' border='0' cellpadding='0' cellspacing='0' style='border: 1px solid #AAA;' class='shadow'>
	<tr>
	    <td bgcolor='#EEEFEF' height='29' style='padding-left:5px;' align='left' class='games'>
			{$GS_menu}
		</td>
	</tr>
	</table><br />";

    $s = 1;
    for ( $i = 1; $i <= $l2cfg["gs"]["count"]; $i++ ) {
        $display = ( $s == 1 ) ? "":"display:none";
        echo '<div id="gs_'.$s.'" style="'.$display.'"><table width="100%" cellpadding="0" cellspacing="0" class="tab">
		<tr>
		    <td bgcolor="#DEDFDF" colspan="2" height="25" style="padding-left:15px; color: #c00;" align="left">
				<b>Основные настройки</b>
			</td>
		</tr>';
		$controller->ShowTr( "Включить / выключить", "", $controller->select("savedata[gs][$i][on]", $true_false, $l2cfg["gs"][$i]["on"], "style='width:100px'") );
        $controller->ShowTr( "Тип сборки игрового сервера", "", $controller->select("savedata[gs][$i][version]", $vList, $l2cfg["gs"][$i]["version"], "style='width:100px'") );
        $controller->ShowTr( "Сервер авторизации используемый данным ГС", "", $controller->select("savedata[gs][$i][ls]", $LSarray, $l2cfg["gs"][$i]["ls"], "style='width:100px'") );
        $controller->ShowTr( "Название сервера", "", "<input type='text' name='savedata[gs][$i][title]' value='{$l2cfg["gs"][$i]["title"]}' style='width: 100px;'>" );
        $controller->ShowTr( "IP адрес сервера", "", "<input type='text' name='savedata[gs][$i][host]' value='{$l2cfg["gs"][$i]["host"]}' style='width: 100px;'>" );
        $controller->ShowTr( "Порт сервера", "", "<input type='text' name='savedata[gs][$i][port]' value='{$l2cfg["gs"][$i]["port"]}' style='width: 50px;'>" );
        $controller->ShowTr( "Хроники", "Используется в модуле 'Вывод статуса серверов'", "<input type='text' name='savedata[gs][$i][chronicle]' value='{$l2cfg["gs"][$i]["chronicle"]}' style='width: 150px;'>" );
        echo '
		<tr>
		    <td bgcolor="#DEDFDF" colspan="2" height="25" style="padding-left:15px; color: #c00;" align="left">
				<b>Рейты сервера</b>
			</td>
		</tr>';
        $controller->ShowTr( "Рейты EXP", "", "<input type='text' name='savedata[gs][$i][rates][exp]' value='{$l2cfg["gs"][$i]["rates"]["exp"]}' style='width: 50px;'>" );
        $controller->ShowTr( "Рейты SP", "", "<input type='text' name='savedata[gs][$i][rates][sp]' value='{$l2cfg["gs"][$i]["rates"]["sp"]}' style='width: 50px;'>" );
        $controller->ShowTr( "Рейты ADENA", "", "<input type='text' name='savedata[gs][$i][rates][adena]' value='{$l2cfg["gs"][$i]["rates"]["adena"]}' style='width: 50px;'>" );
        $controller->ShowTr( "Рейты ITEMS", "", "<input type='text' name='savedata[gs][$i][rates][items]' value='{$l2cfg["gs"][$i]["rates"]["items"]}' style='width: 50px;'>" );
        $controller->ShowTr( "Рейты SPOIL", "", "<input type='text' name='savedata[gs][$i][rates][spoil]' value='{$l2cfg["gs"][$i]["rates"]["spoil"]}' style='width: 50px;'>" );
        $controller->ShowTr( "Рейты QUEST", "", "<input type='text' name='savedata[gs][$i][rates][quest]' value='{$l2cfg["gs"][$i]["rates"]["quest"]}' style='width: 50px;'>" );
        echo '
		<tr>
		    <td bgcolor="#DEDFDF" colspan="2" height="25" style="padding-left:15px; color: #c00;" align="left">
				<b>База данных</b>
			</td>
		</tr>';
        $controller->ShowTr( "Адрес Базы Данных MySQL сервера", "По умолчанию <b>127.0.0.1</b>", "<input type='text' name='savedata[gs][$i][dbhost]' value='{$l2cfg["gs"][$i]["dbhost"]}' style='width: 100px;'>" );
        $controller->ShowTr( "Пользователь Базы Данных MySQL", "По умолчанию <b>root</b>", "<input type='text' name='savedata[gs][$i][dbuser]' value='{$l2cfg["gs"][$i]["dbuser"]}' style='width: 100px;'>" );
        $controller->ShowTr( "Пароль пользователя Базы Данных MySQL", "", "<input type='password' name='savedata[gs][$i][dbpass]' value='{$l2cfg["gs"][$i]["dbpass"]}' style='width: 100px;'>" );
        $controller->ShowTr( "Имя Базы Данных MySQL", "По умолчанию <b>l2jdb</b>", "<input type='text' name='savedata[gs][$i][dbname]' value='{$l2cfg["gs"][$i]["dbname"]}' style='width: 100px;'>" );
        echo '
		<tr>
		    <td bgcolor="#DEDFDF" colspan="2" height="25" style="padding-left:15px; color: #c00;" align="left">
				<b>Телнет</b>
			</td>
		</tr>';
        $controller->ShowTr( "Порт сервера Telnet", "По умолчанию: <b>12345</b>", "<input type='text' name='savedata[gs][$i][telnet][port]' value='{$l2cfg["gs"][$i]["telnet"]["port"]}' style='width: 50px;'>" );
        $controller->ShowTr( "Пароль сервера Telnet", "", "<input type='password' name='savedata[gs][$i][telnet][pass]' value='{$l2cfg["gs"][$i]["telnet"]["pass"]}' style='width: 100px;'>" );
        $controller->ShowTr( "Имя GM'а сервера Telnet", "Актуально только для L2jFree. При пустом значении, функция отключается. По умолчанию: <b>поле пустое</b>", "<input type='text' name='savedata[gs][$i][telnet][gmname]' value='{$l2cfg["gs"][$i]["telnet"]["gmname"]}' style='width: 100px;'>" );
        $controller->ShowTr( "Таймаут соединения с сервером Telnet", "Задается в секундах. По умолчанию: <b>2</b>", "<input type='text' name='savedata[gs][$i][telnet][timeout]' value='{$l2cfg["gs"][$i]["telnet"]["timeout"]}' style='width: 50px;'>" );
        echo '
		<tr>
		    <td bgcolor="#DEDFDF" colspan="2" height="25" style="padding-left:15px; color: #c00;" align="left">
				<b>Статистика</b>
			</td>
		</tr>';
        $controller->ShowTr( "Статистика", "", $controller->select("savedata[gs][$i][stat][enable]", $true_false, $l2cfg["gs"][$i]["stat"]["enable"], "style='width:100px'") );
        $controller->ShowTr( "Количество результатов", "", "<input type='text' name='savedata[gs][$i][stat][count]' value='{$l2cfg["gs"][$i]["stat"]["count"]}' style='width: 50px;'>" );
        $controller->ShowTr( "Статистика 'ОБЩАЯ'", "", $controller->select("savedata[gs][$i][stat][general]", $true_false, $l2cfg["gs"][$i]["stat"]["general"], "style='width:100px'") );
        $controller->ShowTr( "Статистика 'ОНЛАЙН'", "", $controller->select("savedata[gs][$i][stat][online]", $true_false, $l2cfg["gs"][$i]["stat"]["online"], "style='width:100px'") );
        $controller->ShowTr( "Статистика 'ТОП'", "", $controller->select("savedata[gs][$i][stat][top]", $true_false, $l2cfg["gs"][$i]["stat"]["top"], "style='width:100px'") );
        $controller->ShowTr( "Статистика 'ТОП PvP'", "", $controller->select("savedata[gs][$i][stat][pvp]", $true_false, $l2cfg["gs"][$i]["stat"]["pvp"], "style='width:100px'") );
        $controller->ShowTr( "Статистика 'ТОП PK'", "", $controller->select("savedata[gs][$i][stat][pk]", $true_false, $l2cfg["gs"][$i]["stat"]["pk"], "style='width:100px'") );
        $controller->ShowTr( "Статистика 'ТОП КЛАН'", "", $controller->select("savedata[gs][$i][stat][clan]", $true_false, $l2cfg["gs"][$i]["stat"]["clan"], "style='width:100px'") );
        $controller->ShowTr( "Статистика 'ПРОСМОТР КЛАНА'", "", $controller->select("savedata[gs][$i][stat][clanview]", $true_false, $l2cfg["gs"][$i]["stat"]["clanview"], "style='width:100px'") );
        $controller->ShowTr( "Статистика 'ЗАМКИ'", "", $controller->select("savedata[gs][$i][stat][castles]", $true_false, $l2cfg["gs"][$i]["stat"]["castles"], "style='width:100px'") );
        $controller->ShowTr( "Статистика 'РЕЙД БОССЫ'", "", $controller->select("savedata[gs][$i][stat][raid]", $true_false, $l2cfg["gs"][$i]["stat"]["raid"], "style='width:100px'") );
        $controller->ShowTr( "Статистика 'ЭПИК БОССЫ'", "", $controller->select("savedata[gs][$i][stat][epic]", $true_false, $l2cfg["gs"][$i]["stat"]["epic"], "style='width:100px'") );
        $controller->ShowTr( "Статистика 'ОЛИМПИАДА'", "", $controller->select("savedata[gs][$i][stat][olympiad]", $true_false, $l2cfg["gs"][$i]["stat"]["olympiad"], "style='width:100px'") );
        $controller->ShowTr( "Статистика 'ТОП БОГАЧЕЙ'", "", $controller->select("savedata[gs][$i][stat][rich]", $true_false, $l2cfg["gs"][$i]["stat"]["rich"], "style='width:100px'") );
        echo '
		<tr>
		    <td bgcolor="#DEDFDF" colspan="2" height="25" style="padding-left:15px; color: #c00;" align="left">
				<b>Личный кабинет</b>
			</td>
		</tr>';
        $controller->ShowTr( "Функция «Телепорт»", "Включить / выключить возможность телепорта в ближайший город из личного кабинете.", $controller->select("savedata[gs][$i][teleport][enable]", $true_false, $l2cfg["gs"][$i]["teleport"]["enable"], "style='width:100px'") );
        $controller->ShowTr( "Таймаут повторого использования функции «Телепорт»", "Задается в минутах", "<input type='text' name='savedata[gs][$i][teleport][time]' value='{$l2cfg["gs"][$i]["teleport"]["time"]}' style='width: 50px;'>" );
        echo '
		<tr>
		    <td bgcolor="#DEDFDF" colspan="2" height="25" style="padding-left:15px; color: #c00;" align="left">
				<b>Множитель значения онлайна</b>
			</td>
		</tr>';
        $controller->ShowTr( "Множитель значения", "Включить / выключить", $controller->select("savedata[gs][$i][fake][enable]", $true_false, $l2cfg["gs"][$i]["fake"]["enable"], "style='width:100px'") );
        $controller->ShowTr( "Коэффициент накрутки", "Задается в процентнах от настоящего онлайна", "<input type='text' name='savedata[gs][$i][fake][percent]' value='{$l2cfg["gs"][$i]["fake"]["percent"]}' style='width: 50px;'>" );
        echo '
		<tr>
		    <td bgcolor="#DEDFDF" colspan="2" height="25" style="padding-left:15px; color: #c00;" align="left">
				<b>L2Top Бонус</b>
			</td>
		</tr>';
        $controller->ShowTr( "Состояние сервиса для этого Игрового сервера", "", $controller->select("savedata[gs][$i][l2top][enable]", $true_false, $l2cfg["gs"][$i]["l2top"]["enable"], "style='width:100px'") );
        $controller->ShowTr( "Префикс", "Указывается только в случае использования более 1 Игрового сервера", "<input type='text' name='savedata[gs][$i][l2top][prefix]' value='{$l2cfg["gs"][$i]["l2top"]["prefix"]}' style='width: 100px;'>" );
        $controller->ShowTr( "Тип бонуса за голос", "Кредиты или предмет", $controller->select("savedata[gs][$i][l2top][bonus]", array("items" => "Предмет", "l2money" => "Кредиты", ), $l2cfg["gs"][$i]["l2top"]["bonus"], "style='width:100px'") );
        $controller->ShowTr( "Количество кредитов или предметов за голос", "По умолчанию: 1", "<input type='text' name='savedata[gs][$i][l2top][count]' value='{$l2cfg["gs"][$i]["l2top"]["count"]}' style='width: 50px;'>" );
        $controller->ShowTr( "ID предмета за голос", "Если установлен тип бонуса <b>предмет</b>. По умолчанию <b>4037 (Coin of luck)</b>", "<input type='text' name='savedata[gs][$i][l2top][item_id]' value='{$l2cfg["gs"][$i]["l2top"]["item_id"]}' style='width: 50px;'>" );
        $controller->ShowTr( "Таблица с предметами", "items, items_delayed, character_items", $controller->select("savedata[gs][$i][l2top][table]", array("items" => "items", "items_delayed" => "items_delayed", "character_items" => "character_items", ), $l2cfg["gs"][$i]["l2top"]["table"], "style='width:100px'") );
        $controller->ShowTr( "Способ выдачи бонусов", "По умолчанию: <b>запрос в MySQL</b>", $controller->select("savedata[gs][$i][l2top][method]", array('mysql' => 'MySQL', 'telnet' => 'Telnet', 'mysqltelnet' => 'MySQL+Telnet'), $l2cfg["gs"][$i]["l2top"]["method"], "style='width:100px'") );
        echo '
		<tr>
		    <td bgcolor="#DEDFDF" colspan="2" height="25" style="padding-left:15px; color: #c00;" align="left">
				<b>MMOTOP Бонус</b>
			</td>
		</tr>';
        $controller->ShowTr( "Состояние сервиса для этого Игрового сервера", "", $controller->select("savedata[gs][$i][mmotop][enable]", $true_false, $l2cfg["gs"][$i]["mmotop"]["enable"], "style='width:100px'") );
        $controller->ShowTr( "Ссылка на список последних проголосовавших MMOTop", "", "<input type='text' name='savedata[gs][$i][mmotop][url]' value='{$l2cfg["gs"][$i]["mmotop"]["url"]}' style='width: 245px;'>" );
        $controller->ShowTr( "Тип бонуса за голос", "Кредиты или предмет", $controller->select("savedata[gs][$i][mmotop][bonus]", array("items" => "Предмет", "l2money" => "Кредиты", ), $l2cfg["gs"][$i]["mmotop"]["bonus"], "style='width:100px'") );
        $controller->ShowTr( "Количество кредитов или предметов за голос", "По умолчанию: 1", "<input type='text' name='savedata[gs][$i][mmotop][count]' value='{$l2cfg["gs"][$i]["mmotop"]["count"]}' style='width: 50px;'>" );
        $controller->ShowTr( "ID предмета за голос", "Если установлен тип бонуса <b>предмет</b>. По умолчанию <b>4037 (Coin of luck)</b>", "<input type='text' name='savedata[gs][$i][mmotop][item_id]' value='{$l2cfg["gs"][$i]["mmotop"]["item_id"]}' style='width: 50px;'>" );
        $controller->ShowTr( "Таблица с предметами", "items, items_delayed, character_items", $controller->select("savedata[gs][$i][mmotop][table]", array("items" => "items", "items_delayed" => "items_delayed", "character_items" => "character_items", ), $l2cfg["gs"][$i]["mmotop"]["table"], "style='width:100px'") );
        $controller->ShowTr( "Способ выдачи бонусов", "По умолчанию: <b>запрос в MySQL</b>", $controller->select("savedata[gs][$i][mmotop][method]", array('mysql' => 'MySQL', 'telnet' => 'Telnet', 'mysqltelnet' => 'MySQL+Telnet'), $l2cfg["gs"][$i]["mmotop"]["method"], "style='width:100px'") );
        echo '
		<tr>
		    <td bgcolor="#DEDFDF" colspan="2" height="25" style="padding-left:15px; color: #c00;" align="left">
				<b>Смена пола персонажа</b>
			</td>
		</tr>';
        $controller->ShowTr( "Состояние сервиса для этого Игрового сервера", "Включить / Выключить", $controller->select("savedata[gs][$i][chsex][enable]", $true_false, $l2cfg["gs"][$i]["chsex"]["enable"], "style='width:100px'") );
        $controller->ShowTr( "Тип валюты", "Кредиты / Предмет инвентаря", $controller->select("savedata[gs][$i][chsex][money]", array("credits" => "Кредиты", "items" => "Предмет"), $l2cfg["gs"][$i]["chsex"]["money"], "style='width:100px'") );
        $controller->ShowTr( "Стоимость", "Количество кредитов или предметов за смену пола", "<input type='text' name='savedata[gs][{$i}][chsex][price]' value='{$l2cfg["gs"][$i]["chsex"]["price"]}' style='width:50px'>" );
        $controller->ShowTr( "Item ID", "ID предмета, если тип валюты 'Предмет инвентаря'", "<input type='text' name='savedata[gs][$i][chsex][item_id]' value='{$l2cfg["gs"][$i]["chsex"]["item_id"]}' style='width:50px'>" );
        $controller->ShowTr( "Название предмета", "Название предмета, если тип валюты 'Предмет инвентаря'", "<input type='text' name='savedata[gs][$i][chsex][item_name]' value='{$l2cfg["gs"][$i]["chsex"]["item_name"]}' style='width:150px'>" );
        echo '
		<tr>
		    <td bgcolor="#DEDFDF" colspan="2" height="25" style="padding-left:15px; color: #c00;" align="left">
				<b>Смена ника персонажа</b>
			</td>
		</tr>';
        $controller->ShowTr( "Состояние сервиса для этого Игрового сервера", "Включить / Выключить", $controller->select("savedata[gs][$i][chname][enable]", $true_false, $l2cfg["gs"][$i]["chname"]["enable"], "style='width:100px'") );
        $controller->ShowTr( "Тип валюты", "Кредиты / Предмет инвентаря", $controller->select("savedata[gs][$i][chname][money]", array("credits" => "Кредиты", "items" => "Предмет"), $l2cfg["gs"][$i]["chname"]["money"], "style='width:100px'") );
        $controller->ShowTr( "Стоимость", "Количество кредитов или предметов за смену пола", "<input type='text' name='savedata[gs][{$i}][chname][price]' value='{$l2cfg["gs"][$i]["chname"]["price"]}' style='width:50px'>" );
        $controller->ShowTr( "Item ID", "ID предмета, если тип валюты 'Предмет инвентаря'", "<input type='text' name='savedata[gs][$i][chname][item_id]' value='{$l2cfg["gs"][$i]["chname"]["item_id"]}' style='width:50px'>" );
        $controller->ShowTr( "Название предмета", "Название предмета, если тип валюты 'Предмет инвентаря'", "<input type='text' name='savedata[gs][$i][chname][item_name]' value='{$l2cfg["gs"][$i]["chname"]["item_name"]}' style='width:150px'>" );
        $controller->ShowTr( "Символы", "", "<input type='text' name='savedata[gs][$i][chname][letters]' value='{$l2cfg["gs"][$i]["chname"]["letters"]}' style='width:150px'>" );
        echo '
		<tr>
		    <td bgcolor="#DEDFDF" colspan="2" height="25" style="padding-left:15px; color: #c00;" align="left">
				<b>Обменник Кредиты->Предмет</b>
			</td>
		</tr>';
        $controller->ShowTr( "Состояние сервиса для этого Игрового сервера", "Включить / Выключить", $controller->select("savedata[gs][$i][changer][enable]", $true_false, $l2cfg["gs"][$i]["changer"]["enable"], "style='width:100px'") );
        $controller->ShowTr( "Стоимость", "Количество кредитов за предмет", "<input type='text' name='savedata[gs][{$i}][changer][price]' value='{$l2cfg["gs"][$i]["changer"]["price"]}' style='width:50px'>" );
        $controller->ShowTr( "Item ID", "ID предмета", "<input type='text' name='savedata[gs][$i][changer][item_id]' value='{$l2cfg["gs"][$i]["changer"]["item_id"]}' style='width:50px'>" );
        $controller->ShowTr( "Название предмета", "", "<input type='text' name='savedata[gs][$i][changer][item_name]' value='{$l2cfg["gs"][$i]["changer"]["item_name"]}' style='width:150px'>" );
        $controller->ShowTr( "Таблица с предметами", "items, items_delayed, character_items", $controller->select("savedata[gs][$i][changer][table]", array("items" => "items", "items_delayed" => "items_delayed", "character_items" => "character_items", ), $l2cfg["gs"][$i]["changer"]["table"], "style='width:100px'") );
        $controller->ShowTr( "Способ выдачи бонусов", "По умолчанию: <b>запрос в MySQL</b>", $controller->select("savedata[gs][$i][changer][method]", array('mysql' => 'MySQL', 'telnet' => 'Telnet', 'mysqltelnet' => 'MySQL+Telnet'), $l2cfg["gs"][$i]["changer"]["method"], "style='width:100px'") );

        echo '
		<tr>
		    <td bgcolor="#DEDFDF" colspan="2" height="25" style="padding-left:15px; color: #c00;" align="left">
				<b>Настройки Робокассы</b>
			</td>
		</tr>';
        $controller->ShowTr( "Тип бонуса за пожертвование", "Кредиты или предмет", $controller->select("savedata[gs][$i][rb][product]", array("items" => "Предмет", "l2money" => "Кредиты", ), $l2cfg["gs"][$i]["rb"]["product"], "style='width:100px'") );
        $controller->ShowTr( "ID предмета", "Если Тип бонуса 'предмет'. По умолчанию: <b>4037</b>", "<input type='text' name='savedata[gs][$i][rb][item_id]' value='{$l2cfg["gs"][$i]["rb"]["item_id"]}' style='width: 100px;'>" );
        $controller->ShowTr( "Название предмета или кредитов", "По умолчанию: <b>Coin of Luck</b>", "<input type='text' name='savedata[gs][$i][rb][money]' value='{$l2cfg["gs"][$i]["rb"]["money"]}' style='width: 100px;'>" );
        $controller->ShowTr( "Таблица с предметами", "Если Тип бонуса 'предмет'.  items, items_delayed, character_items", $controller->select("savedata[gs][$i][rb][table]", array("items" => "items", "items_delayed" => "items_delayed", "character_items" => "character_items", ), $l2cfg["gs"][$i]["rb"]["table"], "style='width:100px'") );
        $controller->ShowTr( "Валюта", "", $controller->select("savedata[gs][$i][rb][valuta]", array("WMRM" => "WMR", "WMZM" => "WMZ", "WMUM" => "WMU"), $l2cfg["gs"][$i]["rb"]["valuta"], "style='width:100px'") );
        $controller->ShowTr( "Стоимость предмета или кредита", "Указывается в зависимости от выбраной валюты", "<input type='text' name='savedata[gs][$i][rb][sum]' value='{$l2cfg["gs"][$i]["rb"]["sum"]}' style='width: 50px;'>" );

        echo '
		<tr>
		    <td bgcolor="#DEDFDF" colspan="2" height="25" style="padding-left:15px; color: #c00;" align="left">
				<b>Реферальная система</b>
			</td>
		</tr>';
        $controller->ShowTr( "Состояние сервиса для этого Игрового сервера", "Включить / Выключить", $controller->select("savedata[gs][$i][referal_enable]", $true_false, $l2cfg["gs"][$i]["referal_enable"], "style='width:100px'") );
        $controller->ShowTr( "Что нужно выполнить", "уровень/ноблесс", $controller->select("savedata[gs][$i][referal_type]", array('level' => 'Уровень'), $l2cfg["gs"][$i]["referal_type"], "style='width:100px'") );
        $controller->ShowTr( "Какие условия", "например: 20 уровень", "<input type='text' name='savedata[gs][$i][referal_condition]' value='{$l2cfg["gs"][$i]["referal_condition"]}' style='width: 50px;'>" );
        $controller->ShowTr( "Тип бонуса", "Кредиты или предмет", $controller->select("savedata[gs][$i][referal_bonus]", array("items" => "Предмет", "credits" => "Кредиты", ), $l2cfg["gs"][$i]["referal_bonus"], "style='width:100px'") );
        $controller->ShowTr( "Количество кредитов или предметов", "По умолчанию: 1", "<input type='text' name='savedata[gs][$i][referal_count]' value='{$l2cfg["gs"][$i]["referal_count"]}' style='width: 50px;'>" );
        $controller->ShowTr( "Название предмета или кредитов", "По умолчанию: <b>Coin of Luck</b>", "<input type='text' name='savedata[gs][$i][referal_item_name]' value='{$l2cfg["gs"][$i]["referal_item_name"]}' style='width: 100px;'>" );
        $controller->ShowTr( "ID предмета", "Если установлен тип бонуса <b>предмет</b>. По умолчанию <b>4037 (Coin of luck)</b>", "<input type='text' name='savedata[gs][$i][referal_item_id]' value='{$l2cfg["gs"][$i]["referal_item_id"]}' style='width: 50px;'>" );
        $controller->ShowTr( "Таблица с предметами", "items, items_delayed, character_items", $controller->select("savedata[gs][$i][referal_table]", array("items" => "items", "items_delayed" => "items_delayed", "character_items" => "character_items", ), $l2cfg["gs"][$i]["referal_table"], "style='width:100px'") );
        $controller->ShowTr( "Способ выдачи бонусов", "По умолчанию: <b>запрос в MySQL</b>", $controller->select("savedata[gs][$i][referal_method]", array('mysql' => 'MySQL', 'telnet' => 'Telnet', 'mysqltelnet' => 'MySQL+Telnet'), $l2cfg["gs"][$i]["referal_method"], "style='width:100px'") );
        //----------------------------
        echo '</table></div>';
        $s++;
    }
    echo "</td></tr>";

    echo "
	<tr><td>
	<div><input type='hidden' value='doSave' name='act'><input type='submit' value='Сохранить' class='swbutton2 aleft'></div>
	</td></tr></table>
	</form>";

    $conf_content .= ob_get_clean().'
<script type="text/javascript">
function ChangeOption(selectedOption) 
{
	document.getElementById(\'divGeneral\').style.display = "none";
	document.getElementById(\'divCode\').style.display = "none";
	document.getElementById(\'divSMTP\').style.display = "none";
	document.getElementById(\'divForum\').style.display = "none";
	document.getElementById(\'divCache\').style.display = "none";
	document.getElementById(\'divRobokassa\').style.display = "none";
	document.getElementById(\'divL2Top\').style.display = "none";
	document.getElementById(\'divLogin\').style.display = "none";
	document.getElementById(\'divGame\').style.display = "none";
	
	document.getElementById(selectedOption).style.display = "";
}
</script>
<script type="text/javascript">
function ChangeLS(selectedOption) 
{
	var i;
	for (i=1;i<='.$l2cfg["ls"]["count"].';i++)
	{
		document.getElementById(\'ls_\'+i).style.display = "none";
	}
	document.getElementById(selectedOption).style.display = "";
}
</script>
<script type="text/javascript">
function ChangeGS(selectedOption) 
{
	var i;
	for (i=1;i<='.$l2cfg["gs"]["count"].';i++)
	{
		document.getElementById(\'gs_\'+i).style.display = "none";
	}
	document.getElementById(selectedOption).style.display = "";
}
</script><br>';
}

$tpl->SetResult( "content", $conf_content );

?>