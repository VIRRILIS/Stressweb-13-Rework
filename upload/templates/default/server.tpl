[main]
<!-- Модуль Статус Серверов -->
<table cellpadding="0" cellspacing="0" id="server">
<tr>
	<th>name</th>
	<th colspan="2">status</th>
	<th></th>
</tr>
<tr>
	<th>chronicle</th>
	<th>login</th>
	<th>game</th>
	<th>online</th>
</tr>
{item}
[total]
<tr>
	<td colspan="4"><small>Суммарный онлайн: {total}</small></td>
</tr>
[/total]
</table>
[/main]

[item]
<tr>
	<td class="name">{nameLink}<br /><small>{chronicle}</small></td>
	<td><img src="{template}/images/ico-{login}.png" alt="{login}"></td>
	<td><img src="{template}/images/ico-{game}.png" alt="{game}"></td>
	<td>{online}</td>
</tr>
[/item]