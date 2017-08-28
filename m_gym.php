<?php
/*
 * Name: FreeLand v2
 * Author: Marko Murumaa
 * Created: July 2015
 * Website: www.freelandplay.eu
*/

$_lir = true;
require_once("core/init.php");

$gym_money = $_GENERAL->settings('settings_game','GYM_MONEY');
$gym_food = $_GENERAL->settings('settings_game','GYM_FOOD');

$gym_1_def = $_GENERAL->settings('settings_game','GYM_1_DEF');
$gym_1_ofe = $_GENERAL->settings('settings_game','GYM_1_OFE');

$gym_2_def = $_GENERAL->settings('settings_game','GYM_2_DEF');
$gym_2_ofe = $_GENERAL->settings('settings_game','GYM_2_OFE');

$gym_3_def = $_GENERAL->settings('settings_game','GYM_3_DEF');
$gym_3_ofe = $_GENERAL->settings('settings_game','GYM_2_OFE');

$activty_i[0]['point'] = 5;
$activty_i[0]['limit'] = 0;

$activty_i[1]['point'] = 7;
$activty_i[1]['limit'] = 1500;

$activty_i[2]['point'] = 9;
$activty_i[2]['limit'] = 4500;

$activty_i[3]['point'] = 11;
$activty_i[3]['limit'] = 8000;

$activty_i[4]['point'] = 13;
$activty_i[4]['limit'] = 13000;

$activty_i[5]['point'] = 15;
$activty_i[5]['limit'] = 19000;

$activty_i[6]['point'] = 17;
$activty_i[6]['limit'] = 27000;

$activty_i[7]['point'] = 19;
$activty_i[7]['limit'] = 39000;

$activty_i[8]['point'] = 21;
$activty_i[8]['limit'] = 50000;

$activty_i[9]['point'] = 22;
$activty_i[9]['limit'] = 65000;

if(Input::exists()) {
	if(Token::check(Input::get('token'), 'TRAIN_1') ) {
		$time_limit = Input::get('time');
		$activity = Input::get('activity'); 

		if ($_USER->data('data')->gym != 0) {
			$_GENERAL->addError("Te olete juba jõusaalis treenimas.");
		} else {
			if ($time_limit < 1 or $time_limit > 8) {
				$_GENERAL->addError("Te olete valinud vales vahemikus oleva aja.");
			} else {
				if ($activity < 0 or $activity > 9) {
					$_GENERAL->addError("Sellist treening võimalust ei ole.");
				} else {
					$money_total = $time_limit * $gym_money;
					$food_total = $time_limit * $gym_food;

					if ($activty_i[$activity]['limit'] > $_USER->data('data')->speed) {
						$_GENERAL->addError("Selle treening taseme jaoks on vaja kiiruse punkte ".$_GENERAL->format_number($activty_i[$activity]['limit']).".");
					}

					if ($_USER->data('data')->money < $money_total) {
						$_GENERAL->addError("Teil ei ole piisavalt sularaha.");
					}

					if ($_USER->data('data')->food < $food_total) {
						$_GENERAL->addError("Teil ei ole piisavalt toitu.");
					}

					if (empty($_GENERAL->errors()) === true) {
						$train_time = time() + $time_limit * 3600;
						$train_points = $time_limit * $activty_i[$activity]['point'];

						$_USER->update(array(
							'money' => $_USER->data('data')->money - $money_total,
							'food' => $_USER->data('data')->food - $food_total,
							'gym' =>  1,
							'gym_time' => $train_time,
							'gym_points' => $train_points
						),$_USER->data()->id, 'users_data');

						Session::flash('gym', 'Te olete nüüd jõusaalis.');
						Redirect::to('p.php?p=gym');
					}
				}
			}
		}
	} else if(Token::check(Input::get('token'), 'TRAIN_2') ) {
		$time_limit = Input::get('time');
		$activity = Input::get('activity'); 

		if ($_USER->data('data')->gym != 0) {
			$_GENERAL->addError("Te olete juba jõusaalis treenimas.");
		} else {
			if ($time_limit < 1 or $time_limit > 8) {
				$_GENERAL->addError("Te olete valinud vales vahemikus oleva aja.");
			} else {
				if ($activity < 0 or $activity > 9) {
					$_GENERAL->addError("Sellist treening võimalust ei ole.");
				} else {
					$money_total = $time_limit * $gym_money;
					$food_total = $time_limit * $gym_food;

					if ($activty_i[$activity]['limit'] > $_USER->data('data')->strength) {
						$_GENERAL->addError("Selle treening taseme jaoks on vaja tugevuse punkte ".$_GENERAL->format_number($activty_i[$activity]['limit']).".");
					}

					if ($_USER->data('data')->money < $money_total) {
						$_GENERAL->addError("Teil ei ole piisavalt sularaha.");
					}

					if ($_USER->data('data')->food < $food_total) {
						$_GENERAL->addError("Teil ei ole piisavalt toitu.");
					}

					if (empty($_GENERAL->errors()) === true) {
						$train_time = time() + $time_limit * 3600;
						$train_points = $time_limit * $activty_i[$activity]['point'];

						$_USER->update(array(
							'money' => $_USER->data('data')->money - $money_total,
							'food' => $_USER->data('data')->food - $food_total,
							'gym' =>  2,
							'gym_time' => $train_time,
							'gym_points' => $train_points
						),$_USER->data()->id, 'users_data');

						Session::flash('gym', 'Te olete nüüd jõusaalis.');
						Redirect::to('p.php?p=gym');
					}
				}
			}
		}
	} else if(Token::check(Input::get('token'), 'TRAIN_3') ) {
		$time_limit = Input::get('time');
		$activity = Input::get('activity'); 

		if ($_USER->data('data')->gym != 0) {
			$_GENERAL->addError("Te olete juba jõusaalis treenimas.");
		} else {
			if ($time_limit < 1 or $time_limit > 8) {
				$_GENERAL->addError("Te olete valinud vales vahemikus oleva aja.");
			} else {
				if ($activity < 0 or $activity > 9) {
					$_GENERAL->addError("Sellist treening võimalust ei ole.");
				} else {
					$money_total = $time_limit * $gym_money;
					$food_total = $time_limit * $gym_food;

					if ($activty_i[$activity]['limit'] > $_USER->data('data')->stamina) {
						$_GENERAL->addError("Selle treening taseme jaoks on vaja vastupidavuse punkte ".$_GENERAL->format_number($activty_i[$activity]['limit']).".");
					}

					if ($_USER->data('data')->money < $money_total) {
						$_GENERAL->addError("Teil ei ole piisavalt sularaha.");
					}

					if ($_USER->data('data')->food < $food_total) {
						$_GENERAL->addError("Teil ei ole piisavalt toitu.");
					}

					if (empty($_GENERAL->errors()) === true) {
						$train_time = time() + $time_limit * 3600;
						$train_points = $time_limit * $activty_i[$activity]['point'];

						$_USER->update(array(
							'money' => $_USER->data('data')->money - $money_total,
							'food' => $_USER->data('data')->food - $food_total,
							'gym' =>  3,
							'gym_time' => $train_time,
							'gym_points' => $train_points
						),$_USER->data()->id, 'users_data');

						Session::flash('gym', 'Te olete nüüd jõusaalis.');
						Redirect::to('p.php?p=gym');
					}
				}
			}
		}
	} else if(Token::check(Input::get('token'), 'END') ) {
		if ($_USER->data('data')->gym == 0) {
			$_GENERAL->addError("Te ei treeni hetkel.");
		} else {
			$gym_speed = 0;
			$gym_strength = 0;
			$gym_stamina = 0;

			if ($_USER->data('data')->gym == 1) {
				$gym_speed = $_USER->data('data')->gym_points;
			} else if ($_USER->data('data')->gym == 2) {
				$gym_strength = $_USER->data('data')->gym_points;
			} else if ($_USER->data('data')->gym == 3) {
				$gym_stamina = $_USER->data('data')->gym_points;
			}

			$_USER->update(array(
				'speed' => $_USER->data('data')->speed + $gym_speed,
				'strength' => $_USER->data('data')->strength + $gym_strength,
				'stamina' => $_USER->data('data')->stamina + $gym_stamina,
				'gym' =>  0,
				'gym_time' => 0,
				'gym_points' => 0
			),$_USER->data()->id, 'users_data');

			Session::flash('gym', 'Te olete jõusaali edukalt lõpetanud.');
			Redirect::to('p.php?p=gym');
		}
	}
}
	$c = 0;
foreach ($activty_i as $selection) {
	if ($_USER->data('data')->speed > $selection['limit']) {
		$activity_1_selected[$c - 1] = "";
		$activity_1_selected[$c] = 'selected';
	}

	if ($_USER->data('data')->strength > $selection['limit']) {
		$activity_2_selected[$c - 1] = "";
		$activity_2_selected[$c] = 'selected';
	} 

	if ($_USER->data('data')->stamina > $selection['limit']) {
		$activity_3_selected[$c - 1] = "";
		$activity_3_selected[$c] = 'selected';
	} 
	$c++;
}

include("includes/overall/header.php");
?>

<div id="page">
	<div class="page-title">Jõusaal</div>
	<p>
	<?php
	if (empty($_GENERAL->errors()) === false) {
		print($_GENERAL->output_errors());
	}

	if(Session::exists('gym')) {
		$_GENERAL->addOutSuccess(Session::flash('gym'));
		print($_GENERAL->output_success());
	}
	?>
		<table>
			<tr valign="top">
				<td width="20%"><img src="css/default/images/gym.png" width="100" height="100"></td>
				<td width="80%">
					Jõusaalis saate treenida ennast. Treenides saate juurde punkte mis annavad teile kaitset ja rünnet.<br>
					Üks tund jõusaalis vajab <?php print($_GENERAL->format_number($gym_money));?> raha ja <?php print($_GENERAL->format_number($gym_food));?> toitu.
				</td>
			</tr>
		</table>
	</p>
</div>
<div id="page">
	<div class="page-title">Treenimine</div>
	<table>
		<tr>
			<td width="30%">
				<table>
					<tr>
						<td width="50%">Kiirus:</td>
						<td width="50%"><?php print($_GENERAL->format_number($_USER->data('data')->speed));?>p</td>
					</tr>
					<tr>
						<td>Jõud:</td>
						<td><?php print($_GENERAL->format_number($_USER->data('data')->strength));?>p</td>
					</tr>
					<tr>
						<td>Vastupidavus:</td>
						<td><?php print($_GENERAL->format_number($_USER->data('data')->stamina));?>p</td>
					</tr>
					<tr>
						<td>Punkte kokku:</td>
						<td><?php print($_GENERAL->format_number($_USER->data('data')->speed + $_USER->data('data')->strength +$_USER->data('data')->stamina ));?>p</td>
					</tr>
				</table>
			</td>
			<td width="80%" align="center">
				<?php
				if ($_USER->data('data')->gym == 0) {
					?>
					Te ei treeni hetkel
					<?php
				} else {
					if ($_USER->data('data')->gym_time < time()) {
						?>
					<form action="p.php?p=gym" method="POST">
						<input type="hidden" name="token" value="<?php echo Token::generate('END'); ?>">
						<input type="submit" value="Lõpeta treenimine">
					</form>
						<?php
					} else {
						if ($_USER->data('data')->gym == 1) {
							$training_name = 'Jooksmine';
						} else if ($_USER->data('data')->gym == 2) {
							$training_name = 'Kangi tõstmine';
						} else if ($_USER->data('data')->gym == 3) {
							$training_name = 'Rattaga sõitmine';
						}
						$training_time_remaining = $_USER->data('data')->gym_time - time();
					?>
					<ul>
						<li>Tegevus: <?php print($training_name);?></b></li>
						<li>Treenimine lõppeb <b><?php print($_GENERAL->time_ends($training_time_remaining));?></b> pärast</li>
						<li>(<?php print(date("d.m.Y H:i:s", $_USER->data('data')->gym_time));?>)</li>
					</ul>
				<?php
					}
				}
				?>
			</td>
		</tr>
	</table>
	<table>
		<tr>
			<th width="40%">Tegevus</th>
			<th width="15%">Aeg</th>
			<th width="15%">Kaitse</th>
			<th width="15%">Rünne</th>
			<th width="15%">#</th>
		</tr>
		<form action="p.php?p=gym" method="POST">
			<tr>
				<td>
					<select name="activity">
						<option value="0" <?php print($activity_1_selected[0]);?>>Metsa rada 1km [1 tund = 5 kiirust]</option>
						<option value="1" <?php print($activity_1_selected[1]);?>>Metsa rada 2km [1 tund = 7 kiirust]</option>
						<option value="2" <?php print($activity_1_selected[2]);?>>Metsa rada 3km [1 tund = 9 kiirust]</option>
						<option value="3" <?php print($activity_1_selected[3]);?>>Metsa rada 4km [1 tund = 11 kiirust]</option>
						<option value="4" <?php print($activity_1_selected[4]);?>>Metsa rada 5km [1 tund = 13 kiirust]</option>
						<option value="5" <?php print($activity_1_selected[5]);?>>Metsa rada 6km [1 tund = 15 kiirust]</option>
						<option value="6" <?php print($activity_1_selected[6]);?>>Metsa rada 7km [1 tund = 17 kiirust]</option>
						<option value="7" <?php print($activity_1_selected[7]);?>>Metsa rada 8km [1 tund = 19 kiirust]</option>
						<option value="8" <?php print($activity_1_selected[8]);?>>Metsa rada 9km [1 tund = 21 kiirust]</option>
						<option value="9" <?php print($activity_1_selected[9]);?>>Metsa rada 10km [1 tund = 23 kiirust]</option>
					</select>
				</td>
				<td align="center">
					<select name="time">
						<option value="1">1 tund</option>
						<option value="2">2 tundi</option>
						<option value="3">3 tundi</option>
						<option value="4">4 tundi</option>
						<option value="5">5 tundi</option>
						<option value="6">6 tundi</option>
						<option value="7">7 tundi</option>
						<option value="8">8 tundi</option>
					</select>
				</td>
				<td align="center"><?php print($_GENERAL->format_number($gym_1_def));?>p</td>
				<td align="center"><?php print($_GENERAL->format_number($gym_1_ofe));?>p</td>
				<td align="center">
					<input type="hidden" name="token" value="<?php echo Token::generate('TRAIN_1'); ?>">
					<input type="submit" value="Jookse">
				</td>
			</tr>
		</form>
		<form action="p.php?p=gym" method="POST">
			<tr>
				<td>
					<select name="activity">
						<option value="0" <?php print($activity_2_selected[0]);?>>Kang 10kg [1 tund = 5 jõudu]</option>
						<option value="1" <?php print($activity_2_selected[1]);?>>Kang 20kg [1 tund = 7 jõudu]</option>
						<option value="2" <?php print($activity_2_selected[2]);?>>Kang 30kg [1 tund = 9 jõudu]</option>
						<option value="3" <?php print($activity_2_selected[3]);?>>Kang 40kg [1 tund = 11 jõudu]</option>
						<option value="4" <?php print($activity_2_selected[4]);?>>Kang 50kg [1 tund = 13 jõudu]</option>
						<option value="5" <?php print($activity_2_selected[5]);?>>Kang 60kg [1 tund = 15 jõudu]</option>
						<option value="6" <?php print($activity_2_selected[6]);?>>Kang 70kg [1 tund = 17 jõudu]</option>
						<option value="7" <?php print($activity_2_selected[7]);?>>Kang 80kg [1 tund = 19 jõudu]</option>
						<option value="8" <?php print($activity_2_selected[8]);?>>Kang 90kg [1 tund = 21 jõudu]</option>
						<option value="9" <?php print($activity_2_selected[9]);?>>Kang 100kg [1 tund = 23 jõudu]</option>
					</select>
				</td>
				<td align="center">
					<select name="time">
						<option value="1">1 tund</option>
						<option value="2">2 tundi</option>
						<option value="3">3 tundi</option>
						<option value="4">4 tundi</option>
						<option value="5">5 tundi</option>
						<option value="6">6 tundi</option>
						<option value="7">7 tundi</option>
						<option value="8">8 tundi</option>
					</select>
				</td>
				<td align="center"><?php print($_GENERAL->format_number($gym_2_def));?>p</td>
				<td align="center"><?php print($_GENERAL->format_number($gym_2_ofe));?>p</td>
				<td align="center">
					<input type="hidden" name="token" value="<?php echo Token::generate('TRAIN_2'); ?>">
					<input type="submit" value="Tõsta kangi">
				</td>
			</tr>
		</form>
		<form action="p.php?p=gym" method="POST">
			<tr>
				<td>
					<select name="activity">
						<option value="0" <?php print($activity_3_selected[0]);?>>Maantee 5km [1 tund = 5 vastupidavust]</option>
						<option value="1" <?php print($activity_3_selected[1]);?>>Maantee 10km [1 tund = 7 vastupidavust]</option>
						<option value="2" <?php print($activity_3_selected[2]);?>>Maantee 15km [1 tund = 9 vastupidavust]</option>
						<option value="3" <?php print($activity_3_selected[3]);?>>Maantee 20km [1 tund = 11 vastupidavust]</option>
						<option value="4" <?php print($activity_3_selected[4]);?>>Maantee 25km [1 tund = 13 vastupidavust]</option>
						<option value="5" <?php print($activity_3_selected[5]);?>>Maantee 30km [1 tund = 15 vastupidavust]</option>
						<option value="6" <?php print($activity_3_selected[6]);?>>Maantee 35km [1 tund = 17 vastupidavust]</option>
						<option value="7" <?php print($activity_3_selected[7]);?>>Maantee 40km [1 tund = 19 vastupidavust]</option>
						<option value="8" <?php print($activity_3_selected[8]);?>>Maantee 45km [1 tund = 21 vastupidavust]</option>
						<option value="9" <?php print($activity_3_selected[9]);?>>Maantee 50km [1 tund = 23 vastupidavust]</option>
					</select>
				</td>
				<td align="center">
					<select name="time">
						<option value="1">1 tund</option>
						<option value="2">2 tundi</option>
						<option value="3">3 tundi</option>
						<option value="4">4 tundi</option>
						<option value="5">5 tundi</option>
						<option value="6">6 tundi</option>
						<option value="7">7 tundi</option>
						<option value="8">8 tundi</option>
					</select>
				</td>
				<td align="center"><?php print($_GENERAL->format_number($gym_3_def));?>p</td>
				<td align="center"><?php print($_GENERAL->format_number($gym_3_ofe));?>p</td>
				<td align="center">
					<input type="hidden" name="token" value="<?php echo Token::generate('TRAIN_3'); ?>">
					<input type="submit" value="Sõida rattaga">
				</td>
			</tr>
		</form>
	</table>
</div>

<?php
include("includes/overall/footer.php");
