<?php
/*
 * Name: FreeLand v2
 * Author: Marko Murumaa
 * Created: July 2015
 * Website: www.freelandplay.eu
*/

$_lir = true;
require_once("core/init.php");

$transfer_line = null;
$bank_query = $inbox_query = DB::getInstance()->query("SELECT * FROM bank_transfers WHERE sender = ".$_USER->data()->id." or reciver = ".$_USER->data()->id." ORDER BY id DESC");
foreach ($bank_query->results() as $trasnfer) {

	if ($trasnfer->sender == $_USER->data()->id) {
		$user_i = new User($trasnfer->reciver);
		$username = '<a href="p.php?p=profile&user='.$user_i->data()->username.'">'.$user_i->data()->username.'</a>';
		$amount = '<font color="red">- '.$_GENERAL->format_number($trasnfer->amount).'</font>';
	} else if ($trasnfer->reciver == $_USER->data()->id) {
		$user_i = new User($trasnfer->sender);
		$username = '<a href="p.php?p=profile&user='.$user_i->data()->username.'">'.$user_i->data()->username.'</a>';
		$amount = '<font color="green">+ '.$_GENERAL->format_number($trasnfer->amount).'</font>';
	}

	$transfer_line .= '
			<tr>
				<td align="center">'.$trasnfer->time.'</td>
				<td>'.$trasnfer->desc.'</td>
				<td align="center">'.$username.'</td>
				<td align="center">'.$amount.'</td>
			</tr>
	';
}

include("includes/overall/header.php");
?>

<div id="page">
	<div class="page-title">Panga ülekannete ajalugu</div>
	<p>
		<table>
			<tr valign="top">
				<td width="20%"><img src="css/default/images/moneytransfer.png" width="100" height="100"></td>
				<td width="80%">
					Siin te näete kõiki ülekandeid mis teie olete teinud ja mis te olete saanud teistelt inimestelt.
				</td>
			</tr>
		</table>
	</p>
	<table>
		<tr>
			<th width="20%">Kellaaeg</th>
			<th width="45%">Kirjeldus</th>
			<th width="20%">Kasutajanimi</th>
			<th width="15%">Summa</th>
		</tr>
		<?php print($transfer_line);?>
	</table>
</div>

<?php
include("includes/overall/footer.php");
