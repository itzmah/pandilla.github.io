<?php
/*
 * Name: FreeLand v2
 * Author: Marko Murumaa
 * Created: July 2015
 * Website: www.freelandplay.eu
*/

	if ($GANG_ACCESS[$gang_member->rank_id]['market_buy'] == 0 and $GANG_ACCESS[$gang_member->rank_id]['market_sell'] == 0) {
		$_GENERAL->addError("Teil ei ole õiguseid turul olla.");
	}

$wep1_price = $_GENERAL->settings('settings_game','WEP_1_MONEY');
$wep1_def = $_GENERAL->settings('settings_game','WEP_1_DEF');
$wep1_ofe = $_GENERAL->settings('settings_game','WEP_1_OFE');

$wep2_price = $_GENERAL->settings('settings_game','WEP_2_MONEY');
$wep2_def = $_GENERAL->settings('settings_game','WEP_2_DEF');
$wep2_ofe = $_GENERAL->settings('settings_game','WEP_2_OFE');

$wep3_price = $_GENERAL->settings('settings_game','WEP_3_MONEY');
$wep3_def = $_GENERAL->settings('settings_game','WEP_3_DEF');
$wep3_ofe = $_GENERAL->settings('settings_game','WEP_3_OFE');

$wep4_price = $_GENERAL->settings('settings_game','WEP_4_MONEY');
$wep4_def = $_GENERAL->settings('settings_game','WEP_4_DEF');
$wep4_ofe = $_GENERAL->settings('settings_game','WEP_4_OFE');

$wep5_price = $_GENERAL->settings('settings_game','WEP_5_MONEY');
$wep5_def = $_GENERAL->settings('settings_game','WEP_5_DEF');
$wep5_ofe = $_GENERAL->settings('settings_game','WEP_5_OFE');

$wep6_price = $_GENERAL->settings('settings_game','WEP_6_MONEY');
$wep6_def = $_GENERAL->settings('settings_game','WEP_6_DEF');
$wep6_ofe = $_GENERAL->settings('settings_game','WEP_6_OFE');


if(Input::exists()) {
	if(Token::check(Input::get('token'), 'BUY_WEAPONS') ) {
		$product = round(Input::get('product'));
		$value = round(Input::get('value'));
		if ($product == 1) {

			$price = $wep1_price * $value;
			if (empty(Input::get('buy')) === false) {
				if ($value < 1 or empty($value) === true) {
					$_GENERAL->addError("Palun sisestage kui palju te soovite osta.");
				}

				if ($GANG_ACCESS[$gang_member->rank_id]['market_buy'] == 0) {
					$_GENERAL->addError("Teil ei ole õigusi kamba turul tooteid osta.");
				}

				if ($gang_info->points < $value) {
					$_GENERAL->addError("Kambal ei ole piisavalt punkte.");
				}

				if ($gang_info->money < $price) {
					$_GENERAL->addError("Kambal ei ole piisavalt raha.");
				}

				if (empty($_GENERAL->errors()) === true) {
					DB::getInstance()->update('gang', $gang_info->id, array(
						'money' => $gang_info->money - $price,
						'points' => $gang_info->points - $value,
						'wep_1' => $gang_info->wep_1 + $value
						));

					Session::flash('gang', 'Te ostsite kambale kaitse prille.');
					Redirect::to('p.php?p=gang&page=market');
				}
			} else if (empty(Input::get('sell')) === false) {
				if ($value < 1 or empty($value) === true) {
					$_GENERAL->addError("Palun sisestage kui palju te soovite müüa.");
				}

				if ($GANG_ACCESS[$gang_member->rank_id]['market_sell'] == 0) {
					$_GENERAL->addError("Teil ei ole õigusi kamba turul tooteid müüa.");
				}

				if ($gang_info->wep_1 < $value) {
					$_GENERAL->addError("Kambal ei ole piisavalt kaitse prille.");
				}

				if (empty($_GENERAL->errors()) === true) {
					DB::getInstance()->update('gang', $gang_info->id, array(
						'money' => $gang_info->money + $price,
						'wep_1' => $gang_info->wep_1 - $value
						));

					Session::flash('gang', 'Te müüstie kambal kaitse prille.');
					Redirect::to('p.php?p=gang&page=market');
				}
			}
		} else if ($product == 2) {

			$price = $wep2_price * $value;
			if (empty(Input::get('buy')) === false) {
				if ($value < 1 or empty($value) === true) {
					$_GENERAL->addError("Palun sisestage kui palju te soovite osta.");
				}

				if ($GANG_ACCESS[$gang_member->rank_id]['market_buy'] == 0) {
					$_GENERAL->addError("Teil ei ole õigusi kamba turul tooteid osta.");
				}

				if ($gang_info->points < $value) {
					$_GENERAL->addError("Kambal ei ole piisavalt punkte.");
				}

				if ($gang_info->money < $price) {
					$_GENERAL->addError("Kambal ei ole piisavalt raha.");
				}

				if (empty($_GENERAL->errors()) === true) {
					DB::getInstance()->update('gang', $gang_info->id, array(
						'money' => $gang_info->money - $price,
						'points' => $gang_info->points - $value,
						'wep_2' => $gang_info->wep_2 + $value
						));

					Session::flash('gang', 'Te ostsite kambale nukke.');
					Redirect::to('p.php?p=gang&page=market');
				}
			} else if (empty(Input::get('sell')) === false) {
				if ($value < 1 or empty($value) === true) {
					$_GENERAL->addError("Palun sisestage kui palju te soovite müüa.");
				}

				if ($GANG_ACCESS[$gang_member->rank_id]['market_sell'] == 0) {
					$_GENERAL->addError("Teil ei ole õigusi kamba turul tooteid müüa.");
				}

				if ($gang_info->wep_2 < $value) {
					$_GENERAL->addError("Kambal ei ole piisavalt nukke.");
				}

				if (empty($_GENERAL->errors()) === true) {
					DB::getInstance()->update('gang', $gang_info->id, array(
						'money' => $gang_info->money + $price,
						'wep_2' => $gang_info->wep_2 - $value
						));

					Session::flash('gang', 'Te müüstie kambal nukke.');
					Redirect::to('p.php?p=gang&page=market');
				}
			}
		} else if ($product == 3) {

			$price = $wep3_price * $value;
			if (empty(Input::get('buy')) === false) {
				if ($value < 1 or empty($value) === true) {
					$_GENERAL->addError("Palun sisestage kui palju te soovite osta.");
				}

				if ($GANG_ACCESS[$gang_member->rank_id]['market_buy'] == 0) {
					$_GENERAL->addError("Teil ei ole õigusi kamba turul tooteid osta.");
				}

				if ($gang_info->points < $value) {
					$_GENERAL->addError("Kambal ei ole piisavalt punkte.");
				}

				if ($gang_info->money < $price) {
					$_GENERAL->addError("Kambal ei ole piisavalt raha.");
				}

				if (empty($_GENERAL->errors()) === true) {
					DB::getInstance()->update('gang', $gang_info->id, array(
						'money' => $gang_info->money - $price,
						'points' => $gang_info->points - $value,
						'wep_3' => $gang_info->wep_3 + $value
						));

					Session::flash('gang', 'Te ostsite kambale kuuliveste.');
					Redirect::to('p.php?p=gang&page=market');
				}
			} else if (empty(Input::get('sell')) === false) {
				if ($value < 1 or empty($value) === true) {
					$_GENERAL->addError("Palun sisestage kui palju te soovite müüa.");
				}

				if ($GANG_ACCESS[$gang_member->rank_id]['market_sell'] == 0) {
					$_GENERAL->addError("Teil ei ole õigusi kamba turul tooteid müüa.");
				}

				if ($gang_info->wep_3 < $value) {
					$_GENERAL->addError("Kambal ei ole piisavalt kuuliveste.");
				}

				if (empty($_GENERAL->errors()) === true) {
					DB::getInstance()->update('gang', $gang_info->id, array(
						'money' => $gang_info->money + $price,
						'wep_3' => $gang_info->wep_3 - $value
						));

					Session::flash('gang', 'Te müüstie kambal kuuliveste.');
					Redirect::to('p.php?p=gang&page=market');
				}
			}
		} else if ($product == 4) {

			$price = $wep4_price * $value;
			if (empty(Input::get('buy')) === false) {
				if ($value < 1 or empty($value) === true) {
					$_GENERAL->addError("Palun sisestage kui palju te soovite osta.");
				}

				if ($GANG_ACCESS[$gang_member->rank_id]['market_buy'] == 0) {
					$_GENERAL->addError("Teil ei ole õigusi kamba turul tooteid osta.");
				}

				if ($gang_info->points < $value) {
					$_GENERAL->addError("Kambal ei ole piisavalt punkte.");
				}

				if ($gang_info->money < $price) {
					$_GENERAL->addError("Kambal ei ole piisavalt raha.");
				}

				if (empty($_GENERAL->errors()) === true) {
					DB::getInstance()->update('gang', $gang_info->id, array(
						'money' => $gang_info->money - $price,
						'points' => $gang_info->points - $value,
						'wep_4' => $gang_info->wep_4 + $value
						));

					Session::flash('gang', 'Te ostsite kambale tavalisi relvi.');
					Redirect::to('p.php?p=gang&page=market');
				}
			} else if (empty(Input::get('sell')) === false) {
				if ($value < 1 or empty($value) === true) {
					$_GENERAL->addError("Palun sisestage kui palju te soovite müüa.");
				}

				if ($GANG_ACCESS[$gang_member->rank_id]['market_sell'] == 0) {
					$_GENERAL->addError("Teil ei ole õigusi kamba turul tooteid müüa.");
				}

				if ($gang_info->wep_4 < $value) {
					$_GENERAL->addError("Kambal ei ole piisavalt tavalisi relvi.");
				}

				if (empty($_GENERAL->errors()) === true) {
					DB::getInstance()->update('gang', $gang_info->id, array(
						'money' => $gang_info->money + $price,
						'wep_4' => $gang_info->wep_4 - $value
						));

					Session::flash('gang', 'Te müüstie kambal tavalisi relvi.');
					Redirect::to('p.php?p=gang&page=market');
				}
			}
		} else if ($product == 5) {

			$price = $wep5_price * $value;
			if (empty(Input::get('buy')) === false) {
				if ($value < 1 or empty($value) === true) {
					$_GENERAL->addError("Palun sisestage kui palju te soovite osta.");
				}

				if ($GANG_ACCESS[$gang_member->rank_id]['market_buy'] == 0) {
					$_GENERAL->addError("Teil ei ole õigusi kamba turul tooteid osta.");
				}

				if ($gang_info->points < $value) {
					$_GENERAL->addError("Kambal ei ole piisavalt punkte.");
				}

				if ($gang_info->money < $price) {
					$_GENERAL->addError("Kambal ei ole piisavalt raha.");
				}

				if (empty($_GENERAL->errors()) === true) {
					DB::getInstance()->update('gang', $gang_info->id, array(
						'money' => $gang_info->money - $price,
						'points' => $gang_info->points - $value,
						'wep_5' => $gang_info->wep_5 + $value
						));

					Session::flash('gang', 'Te ostsite kambale kilpe.');
					Redirect::to('p.php?p=gang&page=market');
				}
			} else if (empty(Input::get('sell')) === false) {
				if ($value < 1 or empty($value) === true) {
					$_GENERAL->addError("Palun sisestage kui palju te soovite müüa.");
				}

				if ($GANG_ACCESS[$gang_member->rank_id]['market_sell'] == 0) {
					$_GENERAL->addError("Teil ei ole õigusi kamba turul tooteid müüa.");
				}

				if ($gang_info->wep_5 < $value) {
					$_GENERAL->addError("Kambal ei ole piisavalt kilpe.");
				}

				if (empty($_GENERAL->errors()) === true) {
					DB::getInstance()->update('gang', $gang_info->id, array(
						'money' => $gang_info->money + $price,
						'wep_5' => $gang_info->wep_5 - $value
						));

					Session::flash('gang', 'Te müüstie kambal kilpe.');
					Redirect::to('p.php?p=gang&page=market');
				}
			}
		} else if ($product == 6) {

			$price = $wep6_price * $value;
			if (empty(Input::get('buy')) === false) {
				if ($value < 1 or empty($value) === true) {
					$_GENERAL->addError("Palun sisestage kui palju te soovite osta.");
				}

				if ($GANG_ACCESS[$gang_member->rank_id]['market_buy'] == 0) {
					$_GENERAL->addError("Teil ei ole õigusi kamba turul tooteid osta.");
				}

				if ($gang_info->points < $value) {
					$_GENERAL->addError("Kambal ei ole piisavalt punkte.");
				}

				if ($gang_info->money < $price) {
					$_GENERAL->addError("Kambal ei ole piisavalt raha.");
				}

				if (empty($_GENERAL->errors()) === true) {
					DB::getInstance()->update('gang', $gang_info->id, array(
						'money' => $gang_info->money - $price,
						'points' => $gang_info->points - $value,
						'wep_6' => $gang_info->wep_6 + $value
						));

					Session::flash('gang', 'Te ostsite kambale automaat relvi.');
					Redirect::to('p.php?p=gang&page=market');
				}
			} else if (empty(Input::get('sell')) === false) {
				if ($value < 1 or empty($value) === true) {
					$_GENERAL->addError("Palun sisestage kui palju te soovite müüa.");
				}

				if ($GANG_ACCESS[$gang_member->rank_id]['market_sell'] == 0) {
					$_GENERAL->addError("Teil ei ole õigusi kamba turul tooteid müüa.");
				}

				if ($gang_info->wep_6 < $value) {
					$_GENERAL->addError("Kambal ei ole piisavalt automaat relvi.");
				}

				if (empty($_GENERAL->errors()) === true) {
					DB::getInstance()->update('gang', $gang_info->id, array(
						'money' => $gang_info->money + $price,
						'wep_6' => $gang_info->wep_6 - $value
						));

					Session::flash('gang', 'Te müüstie automaat relvi.');
					Redirect::to('p.php?p=gang&page=market');
				}
			}
		} else {
			$_GENERAL->addError("Sellist relva ei leitud meie turult.");
		}
	}
}
?>
<div id="page">
	<div class="page-title">Kamp</div>
	<p>
	<?php
	print($gang_menu);
	if (empty($_GENERAL->errors()) === false) {
		print($_GENERAL->output_errors());
	}

	if(Session::exists('gang')) {
		$_GENERAL->addOutSuccess(Session::flash('gang'));
		print($_GENERAL->output_success());
	}
	?>
</div>
<?php 
if ($GANG_ACCESS[$gang_member->rank_id]['market_buy'] == 1 or $GANG_ACCESS[$gang_member->rank_id]['market_sell'] == 1) {
?>
<div id="page">
	<div class="page-title">Kamba turg</div>
	<p>
		<table>
			<tr valign="top">
				<td width="20%"><img src="css/default/images/weapons.png" width="100" height="100"></td>
				<td width="80%">
					Kambas on kõige olulisemad asjad relvad.
					Relvad annavad kambale kaitset, rünnet ja skoori.<br>
					Mida rohkem on teil kambas relvi seda tugevam kamp teil on.<br>
					Üks relv vajab ühte kamba punkti.
				</td>
			</tr>
			<tr>
				<td>Kambal punkte:</td>
				<td><?php print($_GENERAL->format_number($gang_info->points));?></td>
			</tr>
		</table>
	</p>
	<form action="p.php?p=gang&page=market" method="POST">
		<table>
			<tr>
				<th width="35%">Relva nimi</th>
				<th width="15%">Kambal on</th>
				<th width="15%">Hind</th>
				<th width="15%">Kaitse</th>
				<th width="15%">Rünne</th>
				<th width="5%">#</th>
			</tr>
			<tr>
				<td>Kaitse prillid</td>
				<td align="center"><?php print($_GENERAL->format_number($gang_info->wep_1));?></td>
				<td align="center"><?php print($_GENERAL->format_number($wep1_price));?></td>
				<td align="center"><?php print($_GENERAL->format_number($wep1_def));?>p</td>
				<td align="center"><?php print($_GENERAL->format_number($wep1_ofe));?>p</td>
				<td align="center"><input type="radio" name="product" value="1"></td>
			</tr>
			<tr>
				<td>Nukid</td>
				<td align="center"><?php print($_GENERAL->format_number($gang_info->wep_2));?></td>
				<td align="center"><?php print($_GENERAL->format_number($wep2_price));?></td>
				<td align="center"><?php print($_GENERAL->format_number($wep2_def));?>p</td>
				<td align="center"><?php print($_GENERAL->format_number($wep2_ofe));?>p</td>
				<td align="center"><input type="radio" name="product" value="2"></td>
			</tr>
			<tr>
				<td>Kuulivest</td>
				<td align="center"><?php print($_GENERAL->format_number($gang_info->wep_3));?></td>
				<td align="center"><?php print($_GENERAL->format_number($wep3_price));?></td>
				<td align="center"><?php print($_GENERAL->format_number($wep3_def));?>p</td>
				<td align="center"><?php print($_GENERAL->format_number($wep3_ofe));?>p</td>
				<td align="center"><input type="radio" name="product" value="3"></td>
			</tr>
			<tr>
				<td>Tavaline relv</td>
				<td align="center"><?php print($_GENERAL->format_number($gang_info->wep_4));?></td>
				<td align="center"><?php print($_GENERAL->format_number($wep4_price));?></td>
				<td align="center"><?php print($_GENERAL->format_number($wep4_def));?>p</td>
				<td align="center"><?php print($_GENERAL->format_number($wep4_ofe));?>p</td>
				<td align="center"><input type="radio" name="product" value="4"></td>
			</tr>
			<tr>
				<td>Kilp</td>
				<td align="center"><?php print($_GENERAL->format_number($gang_info->wep_5));?></td>
				<td align="center"><?php print($_GENERAL->format_number($wep5_price));?></td>
				<td align="center"><?php print($_GENERAL->format_number($wep5_def));?>p</td>
				<td align="center"><?php print($_GENERAL->format_number($wep5_ofe));?>p</td>
				<td align="center"><input type="radio" name="product" value="5"></td>
			</tr>
			<tr>
				<td>Automaat relv</td>
				<td align="center"><?php print($_GENERAL->format_number($gang_info->wep_6));?></td>
				<td align="center"><?php print($_GENERAL->format_number($wep6_price));?></td>
				<td align="center"><?php print($_GENERAL->format_number($wep6_def));?>p</td>
				<td align="center"><?php print($_GENERAL->format_number($wep6_ofe));?>p</td>
				<td align="center"><input type="radio" name="product" value="6"></td>
			</tr>
		</table>
		<table>
			<tr>
				<td width="75%" align="right"><input type="text" name="value" placeholder="Kogus" autocomplete="off"></td>
				<td width="10%" align="center"><input type="submit" name="buy" value="Osta"></td>
				<td width="5%" align="center">Või</td>
				<td width="10%" align="center">
					<input type="hidden" name="token" value="<?php echo Token::generate('BUY_WEAPONS'); ?>">
					<input type="submit" name="sell" value="Müü">
				</td>
			</tr>
		</table>
	</form>
</div>
<?php
}
