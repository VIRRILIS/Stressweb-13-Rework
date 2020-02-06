<?php
/**
 * STRESS WEB
 * @author S.T.R.E.S.S.
 * @copyright 2008 - 2012 STRESS WEB
 * @version 13
 * @web http://stressweb.ru
 */
if ( !defined("STRESSWEB") )
    die( "Access denied..." );
if ( !$controller->isAdmin() or !defined('DEVELOP') ) {
    $controller->redirect( "index.php" );
}
$debug = false;
$LSarray = array();
for ( $i = 1; $i <= $l2cfg["ls"]["count"]; $i++ ) {
    $LSarray[$i] = "Login ".$i;
}
$select_server = $controller->select( "lid", $LSarray, $lid, 'style="width: 130px;" onchange="javascript: document.serv.submit(); return false;"' );
$l2_content = <<< HTML
<br /><table width="100%" border='0' cellpadding='0' cellspacing='0' style="border: 1px solid #AAA;" class='shadow'>
<tr>
    <td bgcolor="#EEEFEF" height="29" style="padding-left:10px;">Управление аккаунтами</td>
    <td bgcolor="#EEEFEF" height="29" style="padding-right:10px;" align="right" valign="middle">
		<form action="" method="GET" id="serv" name="serv">
		<input type="hidden" name="mod" value="accounts">
		Сервер: {$select_server}
		</form>
	</td>
</tr>
</table><br />
HTML;

$_act = isset( $_REQUEST["act"] ) ? $_REQUEST["act"]:"";
$db->ldb( $lid );
/*************************
* Send mail to selected accounts
*************************/
if ( isset($_POST['mail_send']) and !empty($_POST['sendaccounts']) ) {
    $sendaccounts = explode( ",", $_POST['sendaccounts'] );
    $sendaccounts = implode( "','", $sendaccounts );
    $mails_q = $ldb[$lid]->query( "SELECT l2email FROM accounts WHERE login IN ('{$sendaccounts}')" );
    $mails = array();
    while ( $mails_d = $ldb[$lid]->fetch($mails_q) ) {
        $mails[] = $mails_d['l2email'];
    }
    $mails = array_unique( $mails );
    $mails = implode( ", ", $mails );
    include DEVDIR.'class.mail.php';
    $mail = new Email( $l2cfg );
    $mail->send( $l2cfg['mail_admin'], $_POST['mail_subject'], $_POST['mail_text'], $mails );
    if ( $mail->send_error ) {
        $l2_content .= "<div class='error'>".$mail->smtp_msg."</div>";
    } else {
        $l2_content .= "<div class='no_error'>Письма отправлены</div>";
    }
}
/*************************
* Delete accounts
*************************/
if ( isset($_REQUEST["delete"]) ) {
    $list = $_REQUEST["delete"];
    $deleted_accounts = 0;
    foreach ( $list as $acc_del ) {
        $ldb[$lid]->SuperQuery( $qList[$vls]["delAccounts"], array("login" => $acc_del) );
        $deleted_accounts++;
    }
    $l2_content .= "<div class='error'> Удалено аккаунтов: {$deleted_accounts}</div><br>\n";
}
/*************************
* Search accounts
*************************/
$search_account = ( isset($_REQUEST["search_account"]) ) ? $ldb[$lid]->safe( $_REQUEST["search_account"] ):"";
$search_ip = ( isset($_REQUEST["search_ip"]) ) ? $ldb[$lid]->safe( $_REQUEST["search_ip"] ):"";
$search_access = ( isset($_REQUEST["search_access"]) ) ? $ldb[$lid]->safe( $_REQUEST["search_access"] ):"";
$where = array();
if ( $search_ip != "" ) {
    $where[] = "`lastIP` LIKE '%{$search_ip}%'";
}
if ( $search_account != "" ) {
    $where[] = "`login` LIKE '%{$search_account}%'";
}
if ( $search_access != "" ) {
    if ( $search_access == "less" ) {
        $where[] = "`{$qList["$vls"]["fields"]["accessLevel"]}`<'0'";
    } elseif ( $search_access == "more" ) {
        $where[] = "`{$qList["$vls"]["fields"]["accessLevel"]}`>'0'";
    }
}
if ( count($where) ) {
    $where = implode( " AND ", $where );
    $where = "WHERE ".$where;
} else
    $where = "";
/*************************
* List accounts
*************************/
$off_set = 100 * ( $page - 1 );
$sel_accounts = $ldb[$lid]->SuperQuery( $qList[$vls]['getAccounts'], array("where" => $where, "order" => "login", "limit" => "{$off_set},100") );
if ( $ldb[$lid]->num_rows($sel_accounts) == 0 ) {
    $l2_content .= "<div class='error'>Нет результатов данного запроса</div>";
} else {
    list( $count ) = $ldb[$lid]->SuperFetchArray( $qList[$vls]["getCountAccounts"], array("where" => $where) );
    $l2_content .= "<center>Всего аккаунтов <b>{$count}</b></center><br><a href=\"javascript:hide('search')\">Поиск</a> &nbsp; <a href=\"javascript:hide('mailsend')\">Рассылка писем</a><br><br>
		<div align='left' id='search' style='display:none; margin-bottom: 10px; padding:5px;'>
			<form action='' method='get'>
			<input type='hidden' name='mod' value='accounts'>
			<input type='hidden' name='lid' value='{$lid}'>
			<input type='hidden' name='act' value='search'>
			<table cellpadding='0' cellspacing='0' class='shadow'>
			<tr>
				<td>Поиск по аккаунтам</td>
				<td><input type='text' name='search_account' style='width: 150px; margin: 2px;'></td>
			</tr>
			<tr>
				<td>Поиск по IP</td>
				<td><input type='text' name='search_ip' style='width: 150px; margin: 2px;'></td>
			</tr>
			<tr>
				<td>Поиск по уровню доступа</td>
				<td>
					<select name='search_access' style='width: 50px; margin: 2px;'>
						<option value='null' selected> &nbsp;&nbsp; = &nbsp;&nbsp;</option>
						<option value='more'> &nbsp;&nbsp; > &nbsp;&nbsp; </option>
						<option value='less'> &nbsp;&nbsp; < &nbsp;&nbsp; </option>
					</select> 0
				</td>
			</tr>
			<tr>
				<td align='right' colspan='2'><input type='submit' class='button' value='Поиск'></td>
			</tr>
			</table>
			</form>
		</div>
		<div align='left' id='mailsend' style='display:none; margin-bottom: 10px; padding:5px;'>
			<script type='text/javascript'>
			$(document).ready(function(){
				$('input[name=\"delete[]\"], input[name=\"master_box\"]').click(function(){
					var sendacc = new Array();
					$.each($('input[name=\"delete[]\"]:checked'),function(){
						sendacc.push($(this).val());
					});
					$('#sendaccounts').val(sendacc);	
				});
			});
			</script>
			<form action='' method='post'>
			<input type='hidden' id='sendaccounts' value='' name='sendaccounts'>
			<table cellpadding='0' cellspacing='0' class='shadow'>
			<tr>
				<td>Введите тему письма:</td>
			</tr>
			<tr>
				<td><input type='text' name='mail_subject' style='width: 450px; margin: 2px;'></td>
			</tr>
			<tr>
				<td>Введите текст письма:</td>
			</tr>
			<tr>
				<td><textarea name='mail_text' style='width: 450px; margin: 2px;'></textarea></td>
			</tr>
			<tr>
				<td align='right'><input type='submit' class='button' name='mail_send' value='Отправить'></td>
			</tr>
			</table>
			</form>
		</div>";
    if ( !empty($_act) )
        $string = "&act=search&search_account={$search_account}&search_ip={$search_ip}&search_access={$search_access}";
    else
        $string = '';
    $l2_content .= "
			<center>
			<form name='accounts_list' method='post' action='{$_url}=accounts&lid={$lid}{$string}&page={$page}'>
			<table cellpadding='0' cellspacing='0' width='100%' id='List'>
			<tr>
				<td class='tdTitle'>Account</td>
				<td class='tdTitle'>Last IP</td>
				<td class='tdTitle'>Last Visit</td>
				<td class='tdTitle'>Access Level</td>
				<td align='center' class='tdTitle'><input type='checkbox' name='master_box' title='Выбрать все' onclick=\"javascript:check_uncheck_all(document.accounts_list)\">
				</td>
			</tr>";

    while ( list($account, $lastaccess, $accesslevel, $lastip) = $ldb[$lid]->fetch($sel_accounts) ) {
        if ( $accesslevel > 0 ) {
            $account_name = "<font color='green'><b>{$account}</b></font>";
            $trClass = "trRowA";
        } elseif ( $accesslevel < 0 ) {
            $account_name = "<font color='red'><b>{$account}</b></font>";
            $trClass = "trRowB";
        } else {
            $account_name = $account;
            $trClass = "trRowC";
        }
        $lastaccess = ( $lastaccess != "" ) ? date( 'H:i d.m.y', intval(substr($lastaccess, 0, 10) + $l2cfg["timezone"] * 60) ):"n/a";
        $lastip = ( $lastip != "" ) ? $lastip:"n/a";
        $l2_content .= "
				<tr class='{$trClass}' onmouseover=\"this.className='trRowOn'\" onmouseout=\"this.className='{$trClass}'\">
					<td><a href='{$_url}=chars&action=account&acc={$account}&lid={$lid}'>{$account_name}</a></td>
					<td>{$lastip}</td>
					<td>{$lastaccess}</td>
					<td>{$accesslevel}</td>
					<td align='center'><input type='checkbox' name='delete[]' value='{$account}'></td>
				</tr>";
    }
    $l2_content .= "</table><div style='text-align: right; padding-top: 5px;'><input type='submit' value='Удалить' class='swbutton2 aright'></div><div class='clear'></div></form></center>";
    if ( $count > 100 ) {
        $numpages = ceil( $count / 100 );
        $l2_content .= $controller->PageList( "{$_url}=accounts&lid={$lid}{$string}&page=", $numpages, $page );
    }
}

$tpl->SetResult( "content", $l2_content );
?>