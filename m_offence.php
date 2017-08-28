<?php
/*
 * Name: FreeLand v2
 * Author: Marko Murumaa
 * Created: July 2015
 * Website: www.freelandplay.eu
*/

$_lir = true;
require_once("core/init.php");

	$number = 800 + (500 * $_USER->data('house')->offence_level);
	$offence_level_money = $_USER->data('house')->offence_level * ($number * $_USER->data('house')->offence_level) * $_USER->data('house')->offence_level;
	$offence_level_max = 100;

	$offence_man_money = $_GENERAL->settings('settings_game','OFEMAN_MONEY');
	$offence_man_food = $_GENERAL->settings('settings_game','OFEMAN_FOOD');
	$offence_man_turns = $_GENERAL->settings('settings_game','OFEMAN_TURNS');

	if ($_USER->data('data')->toetaja == 1) {
		$offence_level_money = $_GENERAL->discount($offence_level_money, 15);
		$offence_man_money = $_GENERAL->discount($offence_man_money, 15);
		$offence_man_food = $_GENERAL->discount($offence_man_food, 15);
	}

	$points_gang = $_USER->user_offence_i('gang');
	$points_gym = $_USER->user_offence_i('gym');
	$points_weapons = $_USER->user_offence_i('weapons');
	$points_mens = $_USER->user_offence_i('self');
	$points_total = $_USER->user_offence_i();

if(Input::exists()) {
	if(Token::check(Input::get('token'), 'HIRE') ) {
		$amount = round(Input::get('value'));
		if ($amount < 1) {
			$_GENERAL->addError("Palun sisestage kui palju te soovite ründajaid palgata.");
		}

		if (($offence_man_turns*$amount) > $_USER->data('data')->turns) {
			$_GENERAL->addError("Teil ei ole piisavalt käike.");
		}

		if (($offence_man_money*$amount) > $_USER->data('data')->money) {
			$_GENERAL->addError("Teil ei ole piisavalt sularaha.");
		}

		if (($offence_man_food*$amount) > $_USER->data('data')->food) {
			$_GENERAL->addError("Teil ei ole piisavalt toitu.");
		}

		if (empty($_GENERAL->errors()) === true) {
			$_USER->update(array(
					'money' => $_USER->data('data')->money - ($offence_man_money*$amount),
					'food' => $_USER->data('data')->food - ($offence_man_food*$amount),
					'turns' => $_USER->data('data')->turns - ($offence_man_turns*$amount)
				), $_USER->data()->id, 'users_data');

			$_USER->update(array(
					'offence_man' => $_USER->data('house')->offence_man + $amount
				), $_USER->data()->id, 'users_data_house');

			Session::flash('offence', 'Te palkasite omale edukalt ründajaid.');
			Redirect::to('p.php?p=offence');
		}
	} else if(Token::check(Input::get('token'), 'UPDATE') ) {
		if (Input::get('level') != 1) {
			$_GENERAL->addError("Palun valige ründe level.");
		} else {
			if ($offence_level_money > $_USER->data('data')->money) {
				$_GENERAL->addError("Teil ei ole piisavalt sularaha.");
			}

			if ($_USER->data('house')->offence_level >= $offence_level_max) {
				$_GENERAL->addError("Teil on juba maksimaalne ründe level.");
			}

			if (empty($_GENERAL->errors()) === true) {
				$_USER->update(array(
						'money' => $_USER->data('data')->money - $offence_level_money
					), $_USER->data()->id, 'users_data');

				$_USER->update(array(
						'offence_level' => $_USER->data('house')->offence_level + 1
					), $_USER->data()->id, 'users_data_house');

				Session::flash('offence', 'Te täiustasite edukalt oma ründe levelit.');
				Redirect::to('p.php?p=offence');
			}
		}
	}
}



include("includes/overall/header.php");
?>

<div id="page">
	<div class="page-title">Ründeüsteem</div>
	<p>
	<?php
	if (empty($_GENERAL->errors()) === false) {
		print($_GENERAL->output_errors());
	}

	if(Session::exists('offence')) {
		$_GENERAL->addOutSuccess(Session::flash('offence'));
		print($_GENERAL->output_success());
	}
	?>
		<table>
			<tr valign="top">
				<td width="20%"><img src="css/default/images/offence.png" width="100" height="100"></td>
				<td width="80%">
					Ründesüsteemis saate palgata ründajaid ja täiustada ründe levelit.
					Mida suurem on teie rünne seda suurem on tõenäosus, et te saate teistelt inimestelt raha röövida. 
					Iga ründaja saab kanda kõikidest relvadest kaks tükki.<br>
					Ühe ründaja palkamiseks läheb teil vaja <?php print($_GENERAL->format_number($offence_man_money));?> raha, 
					<?php print($_GENERAL->format_number($offence_man_food));?> toitu ja vajab 
					<?php print($_GENERAL->format_number($offence_man_turns));?> käiku.<br>
					Üks ründe level on 1% kamba rünnet. Kui teie ründe level on 100 siis te saate kambast 100% kamba rünnet.
				</td>
			</tr>
		</table>
	</p>
	<table>
		<tr>
			<td width="20%">Palka ründajaid:</td>
			<td width="80%">
				<form action="p.php?p=offence" method="POST">
					<input type="text" name="value" autocomplete="off">
					<input type="hidden" name="token" value="<?php echo Token::generate('HIRE'); ?>">
					<input type="submit" value="Palka">
				</form>
			</td>
		</tr>
		<tr>
			<td>Täiusta ründe levelit</td>
			<td>
				<form action="p.php?p=offence" method="POST">
					<select name="level">
						<option value="0">Valige ründe level</option>
						<option value="1">Level: <?php print($_GENERAL->format_number($_USER->data('house')->offence_level + 1));?> | Hind: <?php print($_GENERAL->format_number($offence_level_money));?></option>
					</select>
					<input type="hidden" name="token" value="<?php echo Token::generate('UPDATE'); ?>">
					<input type="submit" value="Täiusta">
				</form>
			</td>
		</tr>
	</table>
</div>
<div id="page">
	<div class="page-title">Ründeüsteemi info</div>
	<table>
		<tr valign="top">
			<td width="50%">
				<table>
					<tr>
						<td width="40%">Punkte kokku:</td>
						<td width="60%"><?php print($_GENERAL->format_number($points_total));?></td>
					</tr>
					<tr>
						<td>Punkte kambast:</td>
						<td><?php print($_GENERAL->format_number($points_gang));?></td>
					</tr>
					<tr>
						<td>Punkte ründajatest:</td>
						<td><?php print($_GENERAL->format_number($points_mens));?></td>
					</tr>
					<tr>
						<td>Punkte relvadest:</td>
						<td><?php print($_GENERAL->format_number($points_weapons));?></td>
					</tr>
					<tr>
						<td>Punkte jõusaalist:</td>
						<td><?php print($_GENERAL->format_number($points_gym));?></td>
					</tr>
				</table>
			</td>
			<td width="50%">
				<table>
					<tr>
						<td width="40%">Ründe level:</td>
						<td width="60%"><?php print($_GENERAL->format_number($_USER->data('house')->offence_level));?></td>
					</tr>
					<tr>
						<td>Ründajaid:</td>
						<td><?php print($_GENERAL->format_number($_USER->data('house')->offence_man));?></td>
					</tr>
				</table>
			</td>
		</tr>
	</table>
</div>

<?php
include("includes/overall/footer.php");
