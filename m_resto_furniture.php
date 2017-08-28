<?php

$buy_area_money = 50000;
$buy_area_turns = 5;

if(Input::exists()) {
	if(Token::check(Input::get('token'), 'BUY_AREA') ) {
		$value = round(Input::get('value'));
		$money_total = $value * $buy_area_money;
		$turns_total = $value * $buy_area_turns;

		if ($value < 1) {
			$_GENERAL->addError("Sisestage kui palju pindala tahate restoranile osta.");
		}

		if ($_USER->data('resto')->money < $money_total) {
			$_GENERAL->addError("Restoranil ei ole piisavalt raha.");
		}

		if ($_USER->data('data')->turns < $turns_total) {
			$_GENERAL->addError("Teil ei ole piisavalt käike.");
		}

		if (empty($_GENERAL->errors()) === true) {
			$_USER->update(array(
					'turns' => $_USER->data('data')->turns - $turns_total
				),$_USER->data()->id, 'users_data');

			$_USER->update(array(
					'money' => $_USER->data('resto')->money - $money_total,
					'area' => $_USER->data('resto')->area + $value,
					'area_total' => $_USER->data('resto')->area_total + $value,
					'outcome_today' => $_USER->data('resto')->outcome_today + $money_total
				),$_USER->data()->id, 'users_data_resto');

			Session::flash('restaurant', 'Te ostsite edukalt restoranile pindala juurde.');
			Redirect::to('p.php?p=restaurant&page=furniture');
		}
	} else if(Token::check(Input::get('token'), 'UPGRADE_KITCHEN') ) {
		$kitchen_lvl_info_query = DB::getInstance(1)->query("SELECT * FROM `resto_levels` WHERE `id` = ".Input::get('level')." ");
		if (!$kitchen_lvl_info_query->count()) {
			Redirect::to('p.php?p=restaurant&page=furniture');
		} else {
			$kitchen_lvl_i = $kitchen_lvl_info_query->first();
			if ($_USER->data('data')->education < $kitchen_lvl_i->education) {
				$_GENERAL->addError("Teil ei ole piisavalt kõrge haridus.");
			}

			if ($_USER->data('resto')->kitchen_level >= $kitchen_lvl_i->id) {
				$_GENERAL->addError("Restoranil on juba see köögi tehnika level olemas.");
			}

			if (($_USER->data('resto')->kitchen_level + 1) < $kitchen_lvl_i->id) {
				$_GENERAL->addError("Köögi tehinka leveleid peab uuendama järjest.");
			}

			if ($_USER->data('resto')->money < $kitchen_lvl_i->money) {
				$_GENERAL->addError("Restoranil ei ole piisavalt raha.");
			}

			if ($_USER->data('resto')->area < $kitchen_lvl_i->area) {
				$_GENERAL->addError("Restoranil ei ole piisavalt vaba pindala.");
			}

			if (empty($_GENERAL->errors()) === true) {
				$_USER->update(array(
						'money' => $_USER->data('resto')->money - $kitchen_lvl_i->money,
						'area' => $_USER->data('resto')->area - $kitchen_lvl_i->area,
						'kitchen_level' => $kitchen_lvl_i->id,
						'outcome_today' => $_USER->data('resto')->outcome_today + $kitchen_lvl_i->money
					),$_USER->data()->id, 'users_data_resto');

				Session::flash('restaurant', 'Te uuendasite restorani köögi tehnika levelit.');
				Redirect::to('p.php?p=restaurant&page=furniture');
			}
		}
	} else if(Token::check(Input::get('token'), 'UPGRADE_FURNTIURE') ) {
		$furniture_lvl_info_query = DB::getInstance(1)->query("SELECT * FROM `resto_levels` WHERE `id` = ".Input::get('level')." ");
		if (!$furniture_lvl_info_query->count()) {
			Redirect::to('p.php?p=restaurant&page=furniture');
		} else {
			$furniture_lvl_i = $furniture_lvl_info_query->first();
			if ($_USER->data('data')->education < $furniture_lvl_i->education) {
				$_GENERAL->addError("Teil ei ole piisavalt kõrge haridus.");
			}

			if ($_USER->data('resto')->furniture_level >= $furniture_lvl_i->id) {
				$_GENERAL->addError("Restoranil on juba see mööbli level olemas.");
			}

			if (($_USER->data('resto')->furniture_level + 1) < $furniture_lvl_i->id) {
				$_GENERAL->addError("Mööbli leveleid peab uuendama järjest.");
			}

			if ($_USER->data('resto')->money < $furniture_lvl_i->money) {
				$_GENERAL->addError("Restoranil ei ole piisavalt raha.");
			}

			if ($_USER->data('resto')->area < $furniture_lvl_i->area) {
				$_GENERAL->addError("Restoranil ei ole piisavalt vaba pindala.");
			}

			if (empty($_GENERAL->errors()) === true) {
				$_USER->update(array(
						'money' => $_USER->data('resto')->money - $furniture_lvl_i->money,
						'area' => $_USER->data('resto')->area - $furniture_lvl_i->area,
						'furniture_level' => $furniture_lvl_i->id,
						'outcome_today' => $_USER->data('resto')->outcome_today + $furniture_lvl_i->money
					),$_USER->data()->id, 'users_data_resto');

				Session::flash('restaurant', 'Te uuendasite restorani mööbli levelit.');
				Redirect::to('p.php?p=restaurant&page=furniture');
			}
		}
	}
}

?>
	<div id="page">
		<div class="page-title">Restorani sisustus</div>
		<p>
		<?php 
		print($resto_menu);
		
		if (empty($_GENERAL->errors()) === false) {
			print("<br>");
			print($_GENERAL->output_errors());
		}

		if(Session::exists('restaurant')) {
			$_GENERAL->addOutSuccess(Session::flash('restaurant'));
			print("<br>");
			print($_GENERAL->output_success());
		}
		?>
			<table>
				<tr valign="top">
					<td width="20%"><img src="css/default/images/furniture.png" width="100" height="100"></td>
					<td width="80%">
						Restorani sisustusest sõltub restorani külastajate arv ja kui palju saavad kokkad süüa teha.<br>
						Köögi tehnika levelist sõltub kui palju saavad kokkad süüa teha ühes tunnis.<br>
						Teenindussaali mööbli levelist sõltub klientide arv ühe tunni kohta.<br>
						Siin on ka võimalik osta oma restoranile rohkem pindala, et uuendada leveleid ja palgata personali.
					</td>
				</tr>
			</table>
		</p>
	</div>
<?php

$tech_level_query = DB::getInstance(1)->query("SELECT * FROM `resto_levels`");
foreach ($tech_level_query->results() as $lvl) {
	$selected = '';
	if(empty(Input::get('kitchen_level')) === false) {
		if (Input::get('kitchen_level') == $lvl->id) {
			$selected = ' selected';
		}
	} else {
		if ($lvl->id == $_USER->data('resto')->kitchen_level) {
			$selected = ' selected';
		}
	}
	$output_line_kitchen .= '<option'.$selected.' value="'.$lvl->id.'">Tehnika level '.$lvl->id.'</option>';
}


if(empty(Input::get('kitchen_level')) === false) {
	$tech_level_i_query = DB::getInstance(1)->query("SELECT * FROM `resto_levels` WHERE `id` = ".Input::get('kitchen_level')." ");
	if (!$tech_level_i_query->count()) {
		Redirect::to('p.php?p=restaurant&page=furniture');
	}
} else {
	$tech_level_i_query = DB::getInstance(1)->query("SELECT * FROM `resto_levels` WHERE `id` = ".$_USER->data('resto')->kitchen_level." ");
}
	$tech_level_data = $tech_level_i_query->first();
?>
	<div id="page">
		<div class="page-title">Köögi tehnika level</div>
		<p>
			<table>
				<tr>
					<td width="20%">Tehnika level:</td>
					<td width="80%">
						<form action="p.php?p=restaurant&page=furniture" method="POST">
							<select name="kitchen_level" onchange="this.form.submit();">
								<?php print($output_line_kitchen);?>
							</select>
						</form>
					</td>
				</tr>
				<tr>
					<td>Vaba pindala vaja:</td>
					<td><?php print($_GENERAL->format_number($tech_level_data->area));?> m<sup>2</sup></td>
				</tr>
				<tr>
					<td>Haridust vaja:</td>
					<td><?php print($_GENERAL->format_number($tech_level_data->education));?></td>
				</tr>
				<tr>
					<td>Hind:</td>
					<td><?php print($_GENERAL->format_number($tech_level_data->money));?> </td>
				</tr>
				<form action="p.php?p=restaurant&page=furniture" method="POST">
					<tr>
						<td></td>
						<td>
							<input type="hidden" name="level" value="<?php print($tech_level_data->id);?> " autocomplete="off">
							<input type="hidden" name="token" value="<?php echo Token::generate('UPGRADE_KITCHEN'); ?>">
							<input type="submit" value="Uuenda tehnika levelit">
						</td>
					</tr>
				</form>
			</table>
		</p>
	</div>
<?php

$furniture_level_query = DB::getInstance(1)->query("SELECT * FROM `resto_levels`");
foreach ($furniture_level_query->results() as $lvl) {
	$selected = '';
	if(empty(Input::get('furniture_level')) === false) {
		if (Input::get('furniture_level') == $lvl->id) {
			$selected = ' selected';
		}
	} else {
		if ($lvl->id == $_USER->data('resto')->furniture_level) {
			$selected = ' selected';
		}
	}
	$output_line_furniture .= '<option'.$selected.' value="'.$lvl->id.'">Mööbli level '.$lvl->id.'</option>';
}

if (empty(Input::get('furniture_level')) === false) {
	$furniture_level_i_query = DB::getInstance(1)->query("SELECT * FROM `resto_levels` WHERE `id` = ".Input::get('furniture_level')." ");
	if (!$furniture_level_i_query->count()) {
		Redirect::to('p.php?p=restaurant&page=furniture');
	}
} else {
	$furniture_level_i_query = DB::getInstance(1)->query("SELECT * FROM `resto_levels` WHERE `id` = ".$_USER->data('resto')->furniture_level." ");
}
	$furniture_level_data = $furniture_level_i_query->first();
?>
	<div id="page">
		<div class="page-title">Teenindussaali mööbli level</div>
		<p>
			<table>
				<tr>
					<td width="20%">Mööbli level:</td>
					<td width="80%">
						<form action="p.php?p=restaurant&page=furniture" method="POST">
							<select name="furniture_level" onchange="this.form.submit();">
								<?php print($output_line_furniture);?>
							</select>
						</form>
					</td>
				</tr>
				<tr>
					<td>Vaba pindala vaja:</td>
					<td><?php print($_GENERAL->format_number($furniture_level_data->area));?> m<sup>2</sup></td>
				</tr>
				<tr>
					<td>Haridust vaja:</td>
					<td><?php print($_GENERAL->format_number($furniture_level_data->education));?></td>
				</tr>
				<tr>
					<td>Hind:</td>
					<td><?php print($_GENERAL->format_number($furniture_level_data->money));?> </td>
				</tr>
				<form action="p.php?p=restaurant&page=furniture" method="POST">
					<tr>
						<td></td>
						<td>
							<input type="hidden" name="level" value="<?php print($furniture_level_data->id);?> " autocomplete="off">
							<input type="hidden" name="token" value="<?php echo Token::generate('UPGRADE_FURNTIURE'); ?>">
							<input type="submit" value="Uuenda mööbli levelit">
						</td>
					</tr>
				</form>
			</table>
		</p>
	</div>
	<div id="page">
		<div class="page-title">Restorani pindala</div>
		<p>
			<form action="p.php?p=restaurant&page=furniture" method="POST">
				<table>
					<tr>
						<td width="25%">Restoranil on vaba pindala:</td>
						<td width="75%"><?php print($_GENERAL->format_number($_USER->data('resto')->area));?> m<sup>2</sup></td>
					</tr>
					<tr>
						<td>1 m<sup>2</sup> jaoks käike vaja:</td>
						<td><?php print($_GENERAL->format_number($buy_area_turns));?></td>
					</tr>
					<tr>
						<td>1 m<sup>2</sup> maksab:</td>
						<td><?php print($_GENERAL->format_number($buy_area_money));?></td>
					</tr>
					<tr>
						<td>Palju pindala ostad</td>
						<td>
							<input type="text" name="value" autocomplete="off">
							<input type="hidden" name="token" value="<?php echo Token::generate('BUY_AREA'); ?>">
							<input type="submit" value="Osta restoranile pindala">
						</td>
					</tr>
				</table>
			</form>
		</p>
	</div>