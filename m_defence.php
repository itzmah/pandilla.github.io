<?php
/*
 * Name: FreeLand v2
 * Author: Marko Murumaa
 * Created: July 2015
 * Website: www.freelandplay.eu
*/

$_lir = true;
require_once("core/init.php");

	$number = 1000 + (500 * $_USER->data('house')->defence_level);
	$defence_level_money = $_USER->data('house')->defence_level * ($number * $_USER->data('house')->defence_level) * $_USER->data('house')->defence_level;
	$defence_level_max = 100;

	$defence_man_money = $_GENERAL->settings('settings_game','DEFMAN_MONEY');
	$defence_man_food = $_GENERAL->settings('settings_game','DEFMAN_FOOD');
	$defence_man_turns = $_GENERAL->settings('settings_game','DEFMAN_TURNS');

	if ($_USER->data('data')->toetaja == 1) {
		$defence_level_money = $_GENERAL->discount($defence_level_money, 15);
		$defence_man_money = $_GENERAL->discount($defence_man_money, 15);
		$defence_man_food = $_GENERAL->discount($defence_man_food, 15);
	}

	$man_defenced = $_USER->data('house')->defence_level * $_USER->data('house')->defence_level * $_GENERAL->settings('settings_game','ROB_DEFMAN_PROTECTED'); 
	$man_defend = ($man_defenced > $_USER->data('house')->defence_man) ? $_USER->data('house')->defence_man : $man_defenced;

	$points_gang = $_USER->user_defence_i('gang');
	$points_gym = $_USER->user_defence_i('gym');
	$points_weapons = $_USER->user_defence_i('weapons');
	$points_mens = $_USER->user_defence_i('self');
	$points_total = $_USER->user_defence_i();

if(Input::exists()) {
	if(Token::check(Input::get('token'), 'HIRE') ) {
		$amount = round(Input::get('value'));
		if ($amount < 1) {
			$_GENERAL->addError("Palun sisestage kui palju te soovite kaitsjaid palgata.");
		}

		if (($defence_man_turns*$amount) > $_USER->data('data')->turns) {
			$_GENERAL->addError("Teil ei ole piisavalt käike.");
		}

		if (($defence_man_money*$amount) > $_USER->data('data')->money) {
			$_GENERAL->addError("Teil ei ole piisavalt sularaha.");
		}

		if (($defence_man_food*$amount) > $_USER->data('data')->food) {
			$_GENERAL->addError("Teil ei ole piisavalt toitu.");
		}

		if (empty($_GENERAL->errors()) === true) {
			$_USER->update(array(
					'money' => $_USER->data('data')->money - ($defence_man_money*$amount),
					'food' => $_USER->data('data')->food - ($defence_man_food*$amount),
					'turns' => $_USER->data('data')->turns - ($defence_man_turns*$amount)
				), $_USER->data()->id, 'users_data');

			$_USER->update(array(
					'defence_man' => $_USER->data('house')->defence_man + $amount
				), $_USER->data()->id, 'users_data_house');

			Session::flash('defence', 'Te palkasite omale edukalt kaitsjaid.');
			Redirect::to('p.php?p=defence');
		}
	} else if(Token::check(Input::get('token'), 'UPDATE') ) {
		if (Input::get('level') != 1) {
			$_GENERAL->addError("Palun valige kaitse level.");
		} else {
			if ($defence_level_money > $_USER->data('data')->money) {
				$_GENERAL->addError("Teil ei ole piisavalt sularaha.");
			}

			if ($_USER->data('house')->defence_level >= $defence_level_max) {
				$_GENERAL->addError("Teil on juba maksimaalne kaitse level.");
			}

			if (empty($_GENERAL->errors()) === true) {
				$_USER->update(array(
						'money' => $_USER->data('data')->money - $defence_level_money
					), $_USER->data()->id, 'users_data');

				$_USER->update(array(
						'defence_level' => $_USER->data('house')->defence_level + 1
					), $_USER->data()->id, 'users_data_house');

				Session::flash('defence', 'Te täiustasite edukalt oma kaitse levelit.');
				Redirect::to('p.php?p=defence');
			}
		}
	}
}



include("includes/overall/header.php");
?>

<div id="page">
	<div class="page-title">Kaitsesüsteem</div>
	<p>
	<?php
	if (empty($_GENERAL->errors()) === false) {
		print($_GENERAL->output_errors());
	}

	if(Session::exists('defence')) {
		$_GENERAL->addOutSuccess(Session::flash('defence'));
		print($_GENERAL->output_success());
	}
	?>
		<table>
			<tr valign="top">
				<td width="20%"><img src="css/default/images/defence.png" width="100" height="100"></td>
				<td width="80%">
					Kaitsesüsteem on teile mängu jooksul väga oluline asi.
					Mida suurem on teie kaitse seda väiksem on tõenäosus, et teie raha röövitakse ära.<br>
					Kaitsjad kaitsevad teid relvadega ja iga kaitsja saab kanda kõikidest relvadest kaks tükki.<br>
					ühe kaitsja palkamiseks läheb teil vaja <?php print($_GENERAL->format_number($defence_man_money));?> raha, 
					<?php print($_GENERAL->format_number($defence_man_food));?> toitu ja vajab
					<?php print($_GENERAL->format_number($defence_man_turns));?> käiku.<br>
					Üks kaitse level on 1% kamba kaitset. Kui teie kaitse level on 100 siis te saate kambast 100% kamba kaitset.
				</td>
			</tr>
		</table>
	</p>
	<table>
		<tr>
			<td width="20%">Palka kaitsjaid:</td>
			<td width="80%">
				<form action="p.php?p=defence" method="POST">
					<input type="text" name="value" autocomplete="off">
					<input type="hidden" name="token" value="<?php echo Token::generate('HIRE'); ?>">
					<input type="submit" value="Palka">
				</form>
			</td>
		</tr>
		<tr>
			<td>Täiusta kaitse levelit</td>
			<td>
				<form action="p.php?p=defence" method="POST">
					<select name="level">
						<option value="0">Valige kaitse level</option>
						<option value="1">Level: <?php print($_GENERAL->format_number($_USER->data('house')->defence_level + 1));?> | Hind: <?php print($_GENERAL->format_number($defence_level_money));?></option>
					</select>
					<input type="hidden" name="token" value="<?php echo Token::generate('UPDATE'); ?>">
					<input type="submit" value="Täiusta">
				</form>
			</td>
		</tr>
	</table>
</div>
<div id="page">
	<div class="page-title">Kaitsesüsteemi info</div>
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
						<td>Punkte kaitsjatest:</td>
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
						<td width="40%">Kaitse level:</td>
						<td width="60%"><?php print($_GENERAL->format_number($_USER->data('house')->defence_level));?></td>
					</tr>
					<tr>
						<td>Kaitsjaid:</td>
						<td><?php print($_GENERAL->format_number($_USER->data('house')->defence_man));?></td>
					</tr>
					<tr>
						<td>Kaitsjaid kaitse all:</td>
						<td><?php print($_GENERAL->format_number($man_defend));?> / <?php print($_GENERAL->format_number($man_defenced));?></td>
					</tr>
				</table>
			</td>
		</tr>
	</table>
</div>

<?php
include("includes/overall/footer.php");
