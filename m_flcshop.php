<?php
/*
 * Name: FreeLand v2
 * Author: Marko Murumaa
 * Created: July 2015
 * Website: www.freelandplay.eu
*/

$_lir = true;
require_once("core/init.php");

if(Input::exists()) {
	if(Token::check(Input::get('token'), 'BUY_SPONSOR') ) {
		if ($_USER->data('data')->flc < 50) {
			$_GENERAL->addError("Teil ei ole piisavalt FLC ehk virtuaal krediiti. Seda saab osta Tasuliste teenuste alt.");
		}

		if ($_WORLD != 1) {
			$_GENERAL->addError("Toetaja liiget saab osta ainult esimeses maailmas.");
		}

		if (empty($_GENERAL->errors()) === true) {
			if ($_USER->data('data')->toetaja == 1) {
				$old_time = strtotime($_USER->data('data')->toetaja_time);
				$time = $old_time + 2592000;

				$_USER->update(array(
					'flc' => $_USER->data('data')->flc - 50,
					'toetaja_time' => date("Y-m-d H:i:s", $time),
					'toetaja' => 1
				),$_USER->data()->id, 'users_data');

				Session::flash('flcshop', 'Te pikendasite oma toetaja liikme aega.');
				Redirect::to('p.php?p=flcshop');
			} else {
				$time = time() + 2592000;
				$_USER->update(array(
					'flc' => $_USER->data('data')->flc - 50,
					'toetaja_time' => date("Y-m-d H:i:s", $time),
					'toetaja' => 1
				),$_USER->data()->id, 'users_data');

				Session::flash('flcshop', 'Te ostsite omale toetaja liikme.');
				Redirect::to('p.php?p=flcshop');
			}
		}
	} else if(Token::check(Input::get('token'), 'CHANGE_TURNS') ) {
		$value = round(Input::get('value'));
		$turns = $value * 35;
		if ($value < 1 or empty($value) === true) {
			$_GENERAL->addError("Sisestage mitu FLC te soovite vahetada käikudeks.");
		}

		if ($value > $_USER->data('data')->flc) {
			$_GENERAL->addError("Teil ei ole piisavalt FLC ehk virtuaal krediiti. Seda saab osta Tasuliste teenuste alt.");
		}

		if (empty($_GENERAL->errors()) === true) {
			$_USER->update(array(
				'flc' => $_USER->data('data')->flc - $value,
				'turns' => $_USER->data('data')->turns + $turns
			),$_USER->data()->id, 'users_data');

			Session::flash('flcshop', 'Te vahetasite FLC-d käikudeks.');
			Redirect::to('p.php?p=flcshop');
		}
	}
}


include("includes/overall/header.php");
?>

<div id="page">
	<div class="page-title">FLC pood</div>
	<p>
	<?php
	if (empty($_GENERAL->errors()) === false) {
		print($_GENERAL->output_errors());
	}

	if(Session::exists('flcshop')) {
		$_GENERAL->addOutSuccess(Session::flash('flcshop'));
		print($_GENERAL->output_success());
	}
	?>
	<table>
			<tr valign="top">
				<td width="20%"><img src="css/default/images/flcshop.png" width="100" height="100"></td>
				<td width="80%">Siin on võimalik vahetada FLC käikudeks.</td>
			</tr>
		</table>
	</p>
</div>
<?php
if ($_WORLD == 1) {
?>
<div id="page">
	<div class="page-title">Toetaja eelised</div>
	<p>
		<table>
			<tr>
				<th width="40%">Selgitus</th>
				<th width="30%">Tavaline liige</th>
				<th width="30%">Toetaja</th>
			</tr>
			<tr>
				<td>Maksimaalselt käike:</td>
				<td align="center">700</td>
				<td align="center">1500</td>
			</tr>
			<tr>
				<td>Käike tuleb iga 15 minut tagant:</td>
				<td align="center">15</td>
				<td align="center">20</td>
			</tr>
			<tr>
				<td>Haridust saab koolis:</td>
				<td align="center">2</td>
				<td align="center">3</td>
			</tr>
			<tr>
				<td>Kaitsesüsteemi soodustus:</td>
				<td align="center">-</td>
				<td align="center">15%</td>
			</tr>
			<tr>
				<td>Ründesüsteemi soodustus</td>
				<td align="center">-</td>
				<td align="center">15%</td>
			</tr>
			<tr>
				<td>Elamu hinna soodustus:</td>
				<td align="center">-</td>
				<td align="center">15%</td>
			</tr>
		</table>
	</p>
</div>
<?php
	if ($_USER->data('data')->toetaja == 1) {
		$sponsor_line = '<font color="green">Jah</font> Kuni '.$_USER->data('data')->toetaja_time;
	} else {
		$sponsor_line = '<font color="red">Ei</font>';
	}
?>
<div id="page">
	<div class="page-title">Toetaja</div>
	<p>
		<form action="p.php?p=flcshop" method="POST">
			<table>
				<tr>
					<td width="30%">Toetaja liige kestab:</td>
					<td width="70%">30 päeva</td>
				</tr>
				<tr>
					<td>Toetaja maksab:</td>
					<td>50 FLC</td>
				</tr>
				<tr>
					<td>Teie olete toetaja liige:</td>
					<td><?php print($sponsor_line);?></td>
				</tr>
				<tr>
				<td>Osta omale toetaja liikme staatus:</td>
				<td>
					<input type="hidden" name="token" value="<?php echo Token::generate('BUY_SPONSOR'); ?>">
					<input type="submit" value="Osta toetaja liige">
				</td>
			</tr>
			</table>
		</form>
	</p>
</div>
<?php
}
?>
<div id="page">
	<div class="page-title">Vaheta FLC-d Käikudeks</div>
	<p>
		<form action="p.php?p=flcshop" method="POST">
			<table>
				<tr>
					<td width="30%">1 FLC eest saad:</td>
					<td width="70%">35 käiku</td>
				</tr>
				<tr>
				<td>Vaheta FLC-d käikudeks:</td>
				<td>
					<input type="text" name="value" autocomplete="off">
					<input type="hidden" name="token" value="<?php echo Token::generate('CHANGE_TURNS'); ?>">
					<input type="submit" value="Vaheta">
				</td>
			</tr>
			</table>
		</form>
	</p>
</div>

<?php
include("includes/overall/footer.php");
