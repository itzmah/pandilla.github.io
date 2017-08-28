<?php
/*
 * Name: FreeLand v2
 * Author: Marko Murumaa
 * Created: July 2015
 * Website: www.freelandplay.eu
*/

$_lir = true;
require_once("core/init.php");

$food_price_buy = $_GENERAL->settings('settings_game','FOOD_BUY_MONEY');
$food_price_sell = $_GENERAL->settings('settings_game','FOOD_SELL_MONEY');

$weed_price_buy = $_GENERAL->settings('settings_game','WEED_BUY_MONEY');
$weed_price_sell = $_GENERAL->settings('settings_game','WEED_SELL_MONEY');

$seed_price_buy = $_GENERAL->settings('settings_game','SEED_BUY_MONEY');
$seed_price_sell = $_GENERAL->settings('settings_game','SEED_SELL_MONEY');

if ($_WORLD == 1) {
	$wep_turns = 1;
} else {
	$wep_turns = 2;
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
	if(Token::check(Input::get('token'), 'FIRST') ) {
		$product = round(Input::get('product'));
		$value = round(Input::get('value'));
		if ($product == 1) {
			if (empty(Input::get('buy')) === false) {
				$price = $food_price_buy * $value;
				if ($value < 1 or empty($value) === true) {
					$_GENERAL->addError("Palun sisestage kui palju te soovite osta.");
				}

				if ($_USER->data('data')->money < $price) {
					$_GENERAL->addError("Teil ei ole piisavalt sularaha.");
				}

				if (empty($_GENERAL->errors()) === true) {
					$_USER->update(array(
							'money' => $_USER->data('data')->money - $price,
							'food' => $_USER->data('data')->food + $value
						),$_USER->data()->id, 'users_data');

					Session::flash('market', 'Te ostsite omale toitu.');
					Redirect::to('p.php?p=market');
				}
			} else if (empty(Input::get('sell')) === false) {
				$price = $food_price_sell * $value;
				if ($value < 1 or empty($value) === true) {
					$_GENERAL->addError("Palun sisestage kui palju te soovite müüa.");
				}

				if ($_USER->data('data')->food < $value) {
					$_GENERAL->addError("Teil ei ole piisavalt toitu.");
				}

				if (empty($_GENERAL->errors()) === true) {
					$_USER->update(array(
							'money' => $_USER->data('data')->money + $price,
							'food' => $_USER->data('data')->food - $value
						),$_USER->data()->id, 'users_data');

					Session::flash('market', 'Te müüsite toitu.');
					Redirect::to('p.php?p=market');
				}
			}
		} else if ($product == 2) {
			if (empty(Input::get('buy')) === false) {
				$_GENERAL->addError("Seda toodet ei saa osta.");
			} else if (empty(Input::get('sell')) === false) {
				$price = $weed_price_sell * $value;
				if ($value < 1 or empty($value) === true) {
					$_GENERAL->addError("Palun sisestage kui palju te soovite müüa.");
				}

				if ($_USER->data('house')->weed < $value) {
					$_GENERAL->addError("Teil ei ole piisavalt kanepit.");
				}

				if (empty($_GENERAL->errors()) === true) {
					$_USER->update(array(
							'money' => $_USER->data('data')->money + $price
						),$_USER->data()->id, 'users_data');
					$_USER->update(array(
							'weed' => $_USER->data('house')->weed - $value
						),$_USER->data()->id, 'users_data_house');

					Session::flash('market', 'Te müüstie kanepit.');
					Redirect::to('p.php?p=market');
				}
			}
		} else if ($product == 3) {
			if (empty(Input::get('buy')) === false) {
				$price = $seed_price_buy * $value;
				if ($value < 1 or empty($value) === true) {
					$_GENERAL->addError("Palun sisestage kui palju te soovite osta.");
				}

				if ($_USER->data('data')->money < $price) {
					$_GENERAL->addError("Teil ei ole piisavalt sularaha.");
				}

				if (empty($_GENERAL->errors()) === true) {
					$_USER->update(array(
							'money' => $_USER->data('data')->money - $price
						),$_USER->data()->id, 'users_data');

					$_USER->update(array(
							'seed' => $_USER->data('house')->seed + $value
						),$_USER->data()->id, 'users_data_house');

					Session::flash('market', 'Te ostsite omale seemneid.');
					Redirect::to('p.php?p=market');
				}
			} else if (empty(Input::get('sell')) === false) {
				$price = $seed_price_sell * $value;
				if ($value < 1 or empty($value) === true) {
					$_GENERAL->addError("Palun sisestage kui palju te soovite müüa.");
				}

				if ($_USER->data('house')->seed < $value) {
					$_GENERAL->addError("Teil ei ole piisavalt seemneid.");
				}

				if (empty($_GENERAL->errors()) === true) {
					$_USER->update(array(
							'money' => $_USER->data('data')->money + $price
						),$_USER->data()->id, 'users_data');

					$_USER->update(array(
							'seed' => $_USER->data('house')->seed - $value
						),$_USER->data()->id, 'users_data_house');


					Session::flash('market', 'Te müüstie seemneid.');
					Redirect::to('p.php?p=market');
				}
			}
		} else {
			$_GENERAL->addError("Sellist toodet ei leitud meie turult.");
		}
	} else if(Token::check(Input::get('token'), 'WEAPONS') ) {
		$product = round(Input::get('product'));
		$value = round(Input::get('value'));
		$turns = $value * $wep_turns;

		if ($product == 1) {

			$price = $wep1_price * $value;
			if (empty(Input::get('buy')) === false) {
				if ($value < 1 or empty($value) === true) {
					$_GENERAL->addError("Palun sisestage kui palju te soovite osta.");
				}

				if ($_USER->data('data')->turns < $turns) {
					$_GENERAL->addError("Teil ei ole piisavalt käike.");
				}

				if ($_USER->data('data')->money < $price) {
					$_GENERAL->addError("Teil ei ole piisavalt sularaha.");
				}

				if (empty($_GENERAL->errors()) === true) {
					$_USER->update(array(
							'money' => $_USER->data('data')->money - $price,
							'turns' => $_USER->data('data')->turns - $turns
						),$_USER->data()->id, 'users_data');

					$_USER->update(array(
							'wep_1' => $_USER->data('house')->wep_1 + $value
						),$_USER->data()->id, 'users_data_house');

					Session::flash('market', 'Te ostsite omale kaitse prille.');
					Redirect::to('p.php?p=market');
				}
			} else if (empty(Input::get('sell')) === false) {
				if ($value < 1 or empty($value) === true) {
					$_GENERAL->addError("Palun sisestage kui palju te soovite müüa.");
				}

				if ($_USER->data('house')->wep_1 < $value) {
					$_GENERAL->addError("Teil ei ole piisavalt kaitse prille.");
				}

				if (empty($_GENERAL->errors()) === true) {
					$_USER->update(array(
							'money' => $_USER->data('data')->money + $price
						),$_USER->data()->id, 'users_data');

					$_USER->update(array(
							'wep_1' => $_USER->data('house')->wep_1 - $value
						),$_USER->data()->id, 'users_data_house');

					Session::flash('market', 'Te müüstie kaitse prille.');
					Redirect::to('p.php?p=market');
				}
			}
		} else if ($product == 2) {

			$price = $wep2_price * $value;
			if (empty(Input::get('buy')) === false) {
				if ($value < 1 or empty($value) === true) {
					$_GENERAL->addError("Palun sisestage kui palju te soovite osta.");
				}

				if ($_USER->data('data')->turns < $turns) {
					$_GENERAL->addError("Teil ei ole piisavalt käike.");
				}

				if ($_USER->data('data')->money < $price) {
					$_GENERAL->addError("Teil ei ole piisavalt sularaha.");
				}

				if (empty($_GENERAL->errors()) === true) {
					$_USER->update(array(
							'money' => $_USER->data('data')->money - $price,
							'turns' => $_USER->data('data')->turns - $turns
						),$_USER->data()->id, 'users_data');

					$_USER->update(array(
							'wep_2' => $_USER->data('house')->wep_2 + $value
						),$_USER->data()->id, 'users_data_house');

					Session::flash('market', 'Te ostsite omale nukke.');
					Redirect::to('p.php?p=market');
				}
			} else if (empty(Input::get('sell')) === false) {
				if ($value < 1 or empty($value) === true) {
					$_GENERAL->addError("Palun sisestage kui palju te soovite müüa.");
				}

				if ($_USER->data('house')->wep_2 < $value) {
					$_GENERAL->addError("Teil ei ole piisavalt nukke.");
				}

				if (empty($_GENERAL->errors()) === true) {
					$_USER->update(array(
							'money' => $_USER->data('data')->money + $price
						),$_USER->data()->id, 'users_data');

					$_USER->update(array(
							'wep_2' => $_USER->data('house')->wep_2 - $value
						),$_USER->data()->id, 'users_data_house');

					Session::flash('market', 'Te müüstie nukke.');
					Redirect::to('p.php?p=market');
				}
			}
		} else if ($product == 3) {

			$price = $wep3_price * $value;
			if (empty(Input::get('buy')) === false) {
				if ($value < 1 or empty($value) === true) {
					$_GENERAL->addError("Palun sisestage kui palju te soovite osta.");
				}

				if ($_USER->data('data')->turns < $turns) {
					$_GENERAL->addError("Teil ei ole piisavalt käike.");
				}

				if ($_USER->data('data')->money < $price) {
					$_GENERAL->addError("Teil ei ole piisavalt sularaha.");
				}

				if (empty($_GENERAL->errors()) === true) {
					$_USER->update(array(
							'money' => $_USER->data('data')->money - $price,
							'turns' => $_USER->data('data')->turns - $turns
						),$_USER->data()->id, 'users_data');

					$_USER->update(array(
							'wep_3' => $_USER->data('house')->wep_3 + $value
						),$_USER->data()->id, 'users_data_house');

					Session::flash('market', 'Te ostsite omale kuuliveste.');
					Redirect::to('p.php?p=market');
				}
			} else if (empty(Input::get('sell')) === false) {
				if ($value < 1 or empty($value) === true) {
					$_GENERAL->addError("Palun sisestage kui palju te soovite müüa.");
				}

				if ($_USER->data('house')->wep_3 < $value) {
					$_GENERAL->addError("Teil ei ole piisavalt kuuliveste.");
				}

				if (empty($_GENERAL->errors()) === true) {
					$_USER->update(array(
							'money' => $_USER->data('data')->money + $price
						),$_USER->data()->id, 'users_data');

					$_USER->update(array(
							'wep_3' => $_USER->data('house')->wep_3 - $value
						),$_USER->data()->id, 'users_data_house');

					Session::flash('market', 'Te müüstie kuuliveste.');
					Redirect::to('p.php?p=market');
				}
			}
		} else if ($product == 4) {

			$price = $wep4_price * $value;
			if (empty(Input::get('buy')) === false) {
				if ($value < 1 or empty($value) === true) {
					$_GENERAL->addError("Palun sisestage kui palju te soovite osta.");
				}

				if ($_USER->data('data')->turns < $turns) {
					$_GENERAL->addError("Teil ei ole piisavalt käike.");
				}

				if ($_USER->data('data')->money < $price) {
					$_GENERAL->addError("Teil ei ole piisavalt sularaha.");
				}

				if (empty($_GENERAL->errors()) === true) {
					$_USER->update(array(
							'money' => $_USER->data('data')->money - $price,
							'turns' => $_USER->data('data')->turns - $turns
						),$_USER->data()->id, 'users_data');

					$_USER->update(array(
							'wep_4' => $_USER->data('house')->wep_4 + $value
						),$_USER->data()->id, 'users_data_house');

					Session::flash('market', 'Te ostsite omale tavalisi relvi.');
					Redirect::to('p.php?p=market');
				}
			} else if (empty(Input::get('sell')) === false) {
				if ($value < 1 or empty($value) === true) {
					$_GENERAL->addError("Palun sisestage kui palju te soovite müüa.");
				}

				if ($_USER->data('house')->wep_4 < $value) {
					$_GENERAL->addError("Teil ei ole piisavalt tavalisi relvi.");
				}

				if (empty($_GENERAL->errors()) === true) {
					$_USER->update(array(
							'money' => $_USER->data('data')->money + $price
						),$_USER->data()->id, 'users_data');

					$_USER->update(array(
							'wep_4' => $_USER->data('house')->wep_4 - $value
						),$_USER->data()->id, 'users_data_house');

					Session::flash('market', 'Te müüstie tavalisi relvi.');
					Redirect::to('p.php?p=market');
				}
			}
		} else if ($product == 5) {

			$price = $wep5_price * $value;
			if (empty(Input::get('buy')) === false) {
				if ($value < 1 or empty($value) === true) {
					$_GENERAL->addError("Palun sisestage kui palju te soovite osta.");
				}

				if ($_USER->data('data')->turns < $turns) {
					$_GENERAL->addError("Teil ei ole piisavalt käike.");
				}

				if ($_USER->data('data')->money < $price) {
					$_GENERAL->addError("Teil ei ole piisavalt sularaha.");
				}

				if (empty($_GENERAL->errors()) === true) {
					$_USER->update(array(
							'money' => $_USER->data('data')->money - $price,
							'turns' => $_USER->data('data')->turns - $turns
						),$_USER->data()->id, 'users_data');

					$_USER->update(array(
							'wep_5' => $_USER->data('house')->wep_5 + $value
						),$_USER->data()->id, 'users_data_house');

					Session::flash('market', 'Te ostsite omale kilpe.');
					Redirect::to('p.php?p=market');
				}
			} else if (empty(Input::get('sell')) === false) {
				if ($value < 1 or empty($value) === true) {
					$_GENERAL->addError("Palun sisestage kui palju te soovite müüa.");
				}

				if ($_USER->data('house')->wep_5 < $value) {
					$_GENERAL->addError("Teil ei ole piisavalt kilpe.");
				}

				if (empty($_GENERAL->errors()) === true) {
					$_USER->update(array(
							'money' => $_USER->data('data')->money + $price
						),$_USER->data()->id, 'users_data');

					$_USER->update(array(
							'wep_5' => $_USER->data('house')->wep_5 - $value
						),$_USER->data()->id, 'users_data_house');

					Session::flash('market', 'Te müüstie kilpe.');
					Redirect::to('p.php?p=market');
				}
			}
		} else if ($product == 6) {

			$price = $wep6_price * $value;
			if (empty(Input::get('buy')) === false) {
				if ($value < 1 or empty($value) === true) {
					$_GENERAL->addError("Palun sisestage kui palju te soovite osta.");
				}

				if ($_USER->data('data')->turns < $turns) {
					$_GENERAL->addError("Teil ei ole piisavalt käike.");
				}

				if ($_USER->data('data')->money < $price) {
					$_GENERAL->addError("Teil ei ole piisavalt sularaha.");
				}

				if (empty($_GENERAL->errors()) === true) {
					$_USER->update(array(
							'money' => $_USER->data('data')->money - $price,
							'turns' => $_USER->data('data')->turns - $turns
						),$_USER->data()->id, 'users_data');

					$_USER->update(array(
							'wep_6' => $_USER->data('house')->wep_6 + $value
						),$_USER->data()->id, 'users_data_house');

					Session::flash('market', 'Te ostsite omale automaat relvi.');
					Redirect::to('p.php?p=market');
				}
			} else if (empty(Input::get('sell')) === false) {
				if ($value < 1 or empty($value) === true) {
					$_GENERAL->addError("Palun sisestage kui palju te soovite müüa.");
				}

				if ($_USER->data('house')->wep_6 < $value) {
					$_GENERAL->addError("Teil ei ole piisavalt automaat relvi.");
				}

				if (empty($_GENERAL->errors()) === true) {
					$_USER->update(array(
							'money' => $_USER->data('data')->money + $price
						),$_USER->data()->id, 'users_data');

					$_USER->update(array(
							'wep_6' => $_USER->data('house')->wep_6 - $value
						),$_USER->data()->id, 'users_data_house');

					Session::flash('market', 'Te müüstie automaat relvi.');
					Redirect::to('p.php?p=market');
				}
			}
		} else {
			$_GENERAL->addError("Sellist relva ei leitud meie turult.");
		}
	}
}

include("includes/overall/header.php");
?>

<div id="page">
	<div class="page-title">Turg</div>
	<p>
	<?php
	if (empty($_GENERAL->errors()) === false) {
		print($_GENERAL->output_errors());
	}

	if(Session::exists('market')) {
		$_GENERAL->addOutSuccess(Session::flash('market'));
		print($_GENERAL->output_success());
	}
	?>
		<table>
			<tr valign="top">
				<td width="20%"><img src="css/default/images/market.png" width="100" height="100"></td>
				<td width="80%">
					Turg on koht kus kohas inimesed käivad omale asju ostmas või neid maha müümas.<br>
					Siin saate osta omale asju ja müüa oma asju.
				</td>
			</tr>
		</table>
	</p>
	<form action="p.php?p=market" method="POST">
		<table>
			<tr>
				<th width="35%">Toote nimi</th>
				<th width="25%">Teil on</th>
				<th width="30%">Ostu / Müügi hind</th>
				<th width="10%">#</th>
			</tr>
			<tr>
				<td>Toit</td>
				<td align="center"><?php print($_GENERAL->format_number($_USER->data('data')->food));?></td>
				<td align="center"><?php print($_GENERAL->format_number($food_price_buy));?> / <?php print($_GENERAL->format_number($food_price_sell));?></td>
				<td align="center"><input type="radio" name="product" value="1"></td>
			</tr>
			<tr>
				<td>Kanep</td>
				<td align="center"><?php print($_GENERAL->format_number($_USER->data('house')->weed));?></td>
				<td align="center"><?php print($_GENERAL->format_number($weed_price_buy));?> / <?php print($_GENERAL->format_number($weed_price_sell));?></td>
				<td align="center"><input type="radio" name="product" value="2"></td>
			</tr>
			<tr>
				<td>Seemned</td>
				<td align="center"><?php print($_GENERAL->format_number($_USER->data('house')->seed));?></td>
				<td align="center"><?php print($_GENERAL->format_number($seed_price_buy));?> / <?php print($_GENERAL->format_number($seed_price_sell));?></td>
				<td align="center"><input type="radio" name="product" value="3"></td>
			</tr>
		</table>
		<table>
			<tr>
				<td width="75%" align="right"><input type="text" name="value" placeholder="Kogus" autocomplete="off"></td>
				<td width="10%" align="center"><input type="submit" name="buy" value="Osta"></td>
				<td width="5%" align="center">Või</td>
				<td width="10%" align="center">
					<input type="hidden" name="token" value="<?php echo Token::generate('FIRST'); ?>">
					<input type="submit" name="sell" value="Müü">
				</td>
			</tr>
		</table>
	</form>
</div>
<div id="page">
	<div class="page-title">Relvad</div>
	<p>
		<table>
			<tr valign="top">
				<td width="20%"><img src="css/default/images/weapons.png" width="100" height="100"></td>
				<td width="80%">
					Relvad on kaitse ja ründe juurdes tähtsad asjad. 
					Iga kaitsja ja ründaja saab kanda igast relvast kaks tükki.<br>
					Ühe relva ostmiseks läheb vaja <?php print($_GENERAL->format_number($wep_turns));?> käiku.
				</td>
			</tr>
		</table>
	</p>
	<form action="p.php?p=market" method="POST">
		<table>
			<tr>
				<th width="35%">Relva nimi</th>
				<th width="15%">Teil on</th>
				<th width="15%">Hind</th>
				<th width="15%">Kaitse</th>
				<th width="15%">Rünne</th>
				<th width="5%">#</th>
			</tr>
			<tr>
				<td>Kaitse prillid</td>
				<td align="center"><?php print($_GENERAL->format_number($_USER->data('house')->wep_1));?></td>
				<td align="center"><?php print($_GENERAL->format_number($wep1_price));?></td>
				<td align="center"><?php print($_GENERAL->format_number($wep1_def));?>p</td>
				<td align="center"><?php print($_GENERAL->format_number($wep1_ofe));?>p</td>
				<td align="center"><input type="radio" name="product" value="1"></td>
			</tr>
			<tr>
				<td>Nukid</td>
				<td align="center"><?php print($_GENERAL->format_number($_USER->data('house')->wep_2));?></td>
				<td align="center"><?php print($_GENERAL->format_number($wep2_price));?></td>
				<td align="center"><?php print($_GENERAL->format_number($wep2_def));?>p</td>
				<td align="center"><?php print($_GENERAL->format_number($wep2_ofe));?>p</td>
				<td align="center"><input type="radio" name="product" value="2"></td>
			</tr>
			<tr>
				<td>Kuulivest</td>
				<td align="center"><?php print($_GENERAL->format_number($_USER->data('house')->wep_3));?></td>
				<td align="center"><?php print($_GENERAL->format_number($wep3_price));?></td>
				<td align="center"><?php print($_GENERAL->format_number($wep3_def));?>p</td>
				<td align="center"><?php print($_GENERAL->format_number($wep3_ofe));?>p</td>
				<td align="center"><input type="radio" name="product" value="3"></td>
			</tr>
			<tr>
				<td>Tavaline relv</td>
				<td align="center"><?php print($_GENERAL->format_number($_USER->data('house')->wep_4));?></td>
				<td align="center"><?php print($_GENERAL->format_number($wep4_price));?></td>
				<td align="center"><?php print($_GENERAL->format_number($wep4_def));?>p</td>
				<td align="center"><?php print($_GENERAL->format_number($wep4_ofe));?>p</td>
				<td align="center"><input type="radio" name="product" value="4"></td>
			</tr>
			<tr>
				<td>Kilp</td>
				<td align="center"><?php print($_GENERAL->format_number($_USER->data('house')->wep_5));?></td>
				<td align="center"><?php print($_GENERAL->format_number($wep5_price));?></td>
				<td align="center"><?php print($_GENERAL->format_number($wep5_def));?>p</td>
				<td align="center"><?php print($_GENERAL->format_number($wep5_ofe));?>p</td>
				<td align="center"><input type="radio" name="product" value="5"></td>
			</tr>
			<tr>
				<td>Automaat relv</td>
				<td align="center"><?php print($_GENERAL->format_number($_USER->data('house')->wep_6));?></td>
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
					<input type="hidden" name="token" value="<?php echo Token::generate('WEAPONS'); ?>">
					<input type="submit" name="sell" value="Müü">
				</td>
			</tr>
		</table>
	</form>
</div>

<?php
include("includes/overall/footer.php");
