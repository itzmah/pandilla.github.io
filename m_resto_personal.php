<?php

$buy_chef_money = 10000;
$buy_chef_area = 5;
$buy_chef_turns = 4;

$buy_waiter_money = 7000;
$buy_waiter_area = 4;
$buy_waiter_turns = 3;

$edit_work_time_money = 50000000;

if ($_USER->data('resto')->chef_morale > 80) {
	$chef_morale_out = '<font color="green">'.$_USER->data('resto')->chef_morale.'%</font>';
} else if ($_USER->data('resto')->chef_morale >= 50) {
	$chef_morale_out = '<font color="orange">'.$_USER->data('resto')->chef_morale.'%</font>';
} else {
	$chef_morale_out = '<font color="red">'.$_USER->data('resto')->chef_morale.'%</font>';
}

if ($_USER->data('resto')->waiter_morale >= 80) {
	$waiter_morale_out = '<font color="green">'.$_USER->data('resto')->waiter_morale.'%</font>';
} else if ($_USER->data('resto')->waiter_morale >= 50) {
	$waiter_morale_out = '<font color="orange">'.$_USER->data('resto')->waiter_morale.'%</font>';
} else {
	$waiter_morale_out = '<font color="red">'.$_USER->data('resto')->waiter_morale.'%</font>';
}

if(Input::exists()) {
	if(Token::check(Input::get('token'), 'EDIT_CHEF') ) {
		$value = round(Input::get('value'));
		$chef_salary_change = time() - 60*60*24;

		if ($value < 1) {
			$_GENERAL->addError("Sisestage kui suure palga te kokkadele määrate.");
		}

		if (strtotime($_USER->data('resto')->chef_salary_change) > $chef_salary_change) {
			$_GENERAL->addError("Kokkade palka saab muuta iga 24 tunni tagant.");
		}

		if ($value > (5 * $chef_recommended_salary)) {
			$_GENERAL->addError("Kokkade palk ei saa olla 5 korda suurem kui on soovitatav palk.");
		}

		if (empty($_GENERAL->errors()) === true) {
			$_USER->update(array(
					'chef_salary' => $value,
					'chef_salary_change' => date("Y-m-d H:i:s")
				),$_USER->data()->id, 'users_data_resto');

			Session::flash('restaurant', 'Te määrasite kokkadele uue palga.');
			Redirect::to('p.php?p=restaurant&page=personal');
		}
	} else if(Token::check(Input::get('token'), 'BUY_CHEF') ) {
		$value = round(Input::get('value'));
		$money_total = $value * $buy_chef_money;
		$turns_total = $value * $buy_chef_turns;
		$area_total = $value * $buy_chef_area;

		if ($value < 1) {
			$_GENERAL->addError("Sisestage mitu kokka te palkate oma restoranile.");
		}

		if ($_USER->data('resto')->money < $money_total) {
			$_GENERAL->addError("Restoranil ei ole piisavalt raha.");
		}

		if ($_USER->data('data')->turns < $turns_total) {
			$_GENERAL->addError("Teil ei ole piisavalt käike.");
		}

		if ($_USER->data('resto')->area < $area_total) {
			$_GENERAL->addError("Restoranil ei ole piisavalt vaba pindala.");
		}

		if (empty($_GENERAL->errors()) === true) {
			$_USER->update(array(
					'turns' => $_USER->data('data')->turns - $turns_total
				),$_USER->data()->id, 'users_data');

			$_USER->update(array(
					'money' => $_USER->data('resto')->money - $money_total,
					'area' => $_USER->data('resto')->area - $area_total,
					'chef' => $_USER->data('resto')->chef + $value,
					'outcome_today' => $_USER->data('resto')->outcome_today + $money_total
				),$_USER->data()->id, 'users_data_resto');

			Session::flash('restaurant', 'Te palkasite restoranile kokkasid juurde.');
			Redirect::to('p.php?p=restaurant&page=personal');
		}
	} else if(Token::check(Input::get('token'), 'CHEF_LEVEL') ) {
		$chef_lvl_info_query = DB::getInstance(1)->query("SELECT * FROM `resto_levels` WHERE `id` = ".Input::get('chef_level')." ");
		if (!$chef_lvl_info_query->count()) {
			Redirect::to('p.php?p=restaurant&page=personal');
		} else {
			$chef_lvl_i = $chef_lvl_info_query->first();
			if ($_USER->data('data')->education < $chef_lvl_i->education) {
				$_GENERAL->addError("Teil ei ole piisavalt kõrge haridus.");
			}

			if ($_USER->data('resto')->chef_level >= $chef_lvl_i->id) {
				$_GENERAL->addError("Teie kokkadel on juba see tase olemas.");
			}

			if (($_USER->data('resto')->chef_level + 1) < $chef_lvl_i->id) {
				$_GENERAL->addError("Kokkasid tuleb koolitada järjest.");
			}

			if ($_USER->data('resto')->money < $chef_lvl_i->money) {
				$_GENERAL->addError("Restoranil ei ole piisavalt raha.");
			}

			if (empty($_GENERAL->errors()) === true) {
				$_USER->update(array(
						'money' => $_USER->data('resto')->money - $chef_lvl_i->money,
						'chef_level' => $chef_lvl_i->id,
					'outcome_today' => $_USER->data('resto')->outcome_today + $chef_lvl_i->money
					),$_USER->data()->id, 'users_data_resto');

				Session::flash('restaurant', 'Te koolitasite oma restorani kokkasid.');
				Redirect::to('p.php?p=restaurant&page=personal');
			}
		}
	} else if(Token::check(Input::get('token'), 'EDIT_WAITER') ) {
		$value = round(Input::get('value'));
		$waiter_salary_change = time() - 60*60*24;
		
		if ($value < 1) {
			$_GENERAL->addError("Sisestage kui suure palga te teenindajatele määrate.");
		}

		if (strtotime($_USER->data('resto')->waiter_salary_change) > $waiter_salary_change) {
			$_GENERAL->addError("Teenindajate palka saab muuta iga 24 tunni tagant.");
		}

		if ($value > (5 * $waiter_recommended_salary)) {
			$_GENERAL->addError("Teenindajate palk ei saa olla 5 korda suurem kui on soovitatav palk.");
		}

		if (empty($_GENERAL->errors()) === true) {
			$_USER->update(array(
					'waiter_salary' => $value,
					'waiter_salary_change' => date("Y-m-d H:i:s")
				),$_USER->data()->id, 'users_data_resto');

			Session::flash('restaurant', 'Te määrasite teenindajatele uue palga.');
			Redirect::to('p.php?p=restaurant&page=personal');
		}
	} else if(Token::check(Input::get('token'), 'BUY_WATER') ) {
		$value = round(Input::get('value'));
		$money_total = $value * $buy_waiter_money;
		$turns_total = $value * $buy_waiter_turns;
		$area_total = $value * $buy_waiter_area;

		if ($value < 1) {
			$_GENERAL->addError("Sisestage mitu teenindajat te palkate oma restoranile.");
		}

		if ($_USER->data('resto')->money < $money_total) {
			$_GENERAL->addError("Restoranil ei ole piisavalt raha.");
		}

		if ($_USER->data('data')->turns < $turns_total) {
			$_GENERAL->addError("Teil ei ole piisavalt käike.");
		}

		if ($_USER->data('resto')->area < $area_total) {
			$_GENERAL->addError("Restoranil ei ole piisavalt vaba pindala.");
		}

		if (empty($_GENERAL->errors()) === true) {
			$_USER->update(array(
					'turns' => $_USER->data('data')->turns - $turns_total
				),$_USER->data()->id, 'users_data');

			$_USER->update(array(
					'money' => $_USER->data('resto')->money - $money_total,
					'area' => $_USER->data('resto')->area - $area_total,
					'waiter' => $_USER->data('resto')->waiter + $value,
					'outcome_today' => $_USER->data('resto')->outcome_today + $money_total
				),$_USER->data()->id, 'users_data_resto');

			Session::flash('restaurant', 'Te palkasite restoranile teenindjaid juurde.');
			Redirect::to('p.php?p=restaurant&page=personal');
		}
	} else if(Token::check(Input::get('token'), 'WAITER_LEVEL') ) {
		$waiter_lvl_info_query = DB::getInstance(1)->query("SELECT * FROM `resto_levels` WHERE `id` = ".Input::get('waiter_level')." ");
		if (!$waiter_lvl_info_query->count()) {
			Redirect::to('p.php?p=restaurant&page=personal');
		} else {
			$waiter_lvl_i = $waiter_lvl_info_query->first();
			if ($_USER->data('data')->education < $waiter_lvl_i->education) {
				$_GENERAL->addError("Teil ei ole piisavalt kõrge haridus.");
			}

			if ($_USER->data('resto')->waiter_level >= $waiter_lvl_i->id) {
				$_GENERAL->addError("Teie teenindajatel on juba see tase olemas.");
			}

			if (($_USER->data('resto')->waiter_level + 1) < $waiter_lvl_i->id) {
				$_GENERAL->addError("Teenindajaid tuleb koolitada järjest.");
			}

			if ($_USER->data('resto')->money < $waiter_lvl_i->money) {
				$_GENERAL->addError("Restoranil ei ole piisavalt raha.");
			}

			if (empty($_GENERAL->errors()) === true) {
				$_USER->update(array(
						'money' => $_USER->data('resto')->money - $waiter_lvl_i->money,
						'waiter_level' => $waiter_lvl_i->id,
						'outcome_today' => $_USER->data('resto')->outcome_today + $waiter_lvl_i->money
					),$_USER->data()->id, 'users_data_resto');

				Session::flash('restaurant', 'Te koolitasite oma restorani teenindajasid.');
				Redirect::to('p.php?p=restaurant&page=personal');
			}
		}
	} else if(Token::check(Input::get('token'), 'EDIT_WORK_TIME') ) {
		$value = round(Input::get('work_hours'));
		if ($value < 0 or $value > 24) {
			$_GENERAL->addError("Valitud tööpäeva pikkus on vigane.");
		}

		if (strtotime($_USER->data('resto')->work_hours_time) > time()) {
			$_GENERAL->addError("Tööpäeva pikkust saab muuta iga 24 tunni tagant.");
		}

		if ($_USER->data('resto')->work_hours == $value) {
			$_GENERAL->addError("Restoranil on juba see tööpäeva pikkus.");
		}

		if ($value == 0) {
			if ($_USER->data('resto')->money < $edit_work_time_money) {
				$_GENERAL->addError("Restoranil ei ole piisavalt raha, et töötajaid puhkusele saata.");
			}
		}

		if (empty($_GENERAL->errors()) === true) {
			$work_time_edit = time() + 60*60*24;
			if ($value == 0) {
				$_USER->update(array(
					'money' => $_USER->data('resto')->money - $edit_work_time_money,
					'work_hours' => $value,
					'work_hours_time' => date("Y-m-d H:i:s", $work_time_edit),
					'outcome_today' => $_USER->data('resto')->outcome_today + $edit_work_time_money
				),$_USER->data()->id, 'users_data_resto');

				Session::flash('restaurant', 'Te saatsite restorani töötajad puhkusele.');
			} else {
				$_USER->update(array(
					'work_hours_time' => date("Y-m-d H:i:s", $work_time_edit),
					'work_hours' => $value
				),$_USER->data()->id, 'users_data_resto');

				Session::flash('restaurant', 'Te  muutsite restorani tööpäeva pikkust.');
			}
			Redirect::to('p.php?p=restaurant&page=personal');
		}
	}
}

for ($i=0; $i < 25; $i++) { 
	if ($_USER->data('resto')->work_hours == $i) {
		$work_time_selected[$i] = ' selected';
	} else {
		$work_time_selected[$i] = '';
	}
}

?>
	<div id="page">
		<div class="page-title">Restorani personal</div>
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
					<td width="20%"><img src="css/default/images/personal.png" width="100" height="100"></td>
					<td width="80%">
						Siin on võimalik palgata ja koolitada kokkasid ja teenindajaid.<br>
						Kui töötajate moraal langeb alla 50% siis nad hakkavad lahkuma töölt. Moraali võiks hoida üle 50 protsendi.<br>
						Töötajate palka võiks tõsta siis kui koolitate töötajaid või uuendate sisustuse leveleid.
					</td>
				</tr>
				<tr>
					<td></td>
					<td>
						Tööpäeva pikkusest sõltub kui palju teie töötajad tööd teevad. <br>
						Kui panete tööpäeva pikkuseks 0 tundi siis on töötajad puhkusel.<br>
						Puhkusele saatmine maksab <?php print($_GENERAL->format_number($edit_work_time_money));?>.<br>
						Tööpäeva pikkust saab muuta korra 24 tunni jooksul.
					</td>
				</tr>
				<form action="p.php?p=restaurant&page=personal" method="POST">
					<tr>
						<td></td>
						<td>Tööpäeva pikkus: 
							<select name="work_hours">
								<option value="0"<?php print($work_time_selected[0]);?>>0 tundi</option>
								<option value="1"<?php print($work_time_selected[1]);?>>1 tund</option>
								<option value="2"<?php print($work_time_selected[2]);?>>2 tundi</option>
								<option value="3"<?php print($work_time_selected[3]);?>>3 tundi</option>
								<option value="4"<?php print($work_time_selected[4]);?>>4 tundi</option>
								<option value="5"<?php print($work_time_selected[5]);?>>5 tundi</option>
								<option value="6"<?php print($work_time_selected[6]);?>>6 tundi</option>
								<option value="7"<?php print($work_time_selected[7]);?>>7 tundi</option>
								<option value="8"<?php print($work_time_selected[8]);?>>8 tundi</option>
								<option value="9"<?php print($work_time_selected[9]);?>>9 tundi</option>
								<option value="10"<?php print($work_time_selected[10]);?>>10 tundi</option>
								<option value="11"<?php print($work_time_selected[11]);?>>11 tundi</option>
								<option value="12"<?php print($work_time_selected[12]);?>>12 tundi</option>
								<option value="13"<?php print($work_time_selected[13]);?>>13 tundi</option>
								<option value="14"<?php print($work_time_selected[14]);?>>14 tundi</option>
								<option value="15"<?php print($work_time_selected[15]);?>>15 tundi</option>
								<option value="16"<?php print($work_time_selected[16]);?>>16 tundi</option>
								<option value="17"<?php print($work_time_selected[17]);?>>17 tundi</option>
								<option value="18"<?php print($work_time_selected[18]);?>>18 tundi</option>
								<option value="19"<?php print($work_time_selected[19]);?>>19 tundi</option>
								<option value="20"<?php print($work_time_selected[20]);?>>20 tundi</option>
								<option value="21"<?php print($work_time_selected[21]);?>>21 tundi</option>
								<option value="22"<?php print($work_time_selected[22]);?>>22 tundi</option>
								<option value="23"<?php print($work_time_selected[23]);?>>23 tundi</option>
								<option value="24"<?php print($work_time_selected[24]);?>>24 tundi</option>
							</select>
							<input type="hidden" name="token" value="<?php echo Token::generate('EDIT_WORK_TIME'); ?>">
							<input type="submit" value="Muuda tööpäeva pikkust">
						</td>
					</tr>
				</form>
			</table>
		</p>
	</div>
<?php

$chef_level_query = DB::getInstance(1)->query("SELECT * FROM `resto_levels`");
foreach ($chef_level_query->results() as $lvl) {
	$selected = '';
	if ($lvl->id == $_USER->data('resto')->chef_level) {
		$selected = ' selected';
	}
	$output_line_chef .= '<option'.$selected.' value="'.$lvl->id.'">'.$lvl->name.' | Hind: '.$_GENERAL->format_number($lvl->money).' | Haridust: '.$_GENERAL->format_number($lvl->education).'</option>';
}
?>
	<div id="page">
		<div class="page-title">Restorani kokad</div>
		<p>
			<table>
				<tr>
					<td width="30%">Restoranil kokkasid:</td>
					<td width="70%"><?php print($_GENERAL->format_number($_USER->data('resto')->chef));?></td>
				</tr>
				<tr>
					<td>Kokkade moraal:</td>
					<td><?php print($chef_morale_out);?></td>
				</tr>
			</table>
			<table>
				<form action="p.php?p=restaurant&page=personal" method="POST">
					<tr>
						<td width="20%">Kokkade tase:</td>
						<td width="80%">
							<select name="chef_level">
								<?php print($output_line_chef);?>
							</select>
							<input type="hidden" name="token" value="<?php echo Token::generate('CHEF_LEVEL'); ?>">
							<input type="submit" value="Koolita">
						</td>
					</tr>
				</form>
			</table>
			<table>
				<form action="p.php?p=restaurant&page=personal" method="POST">
					<tr>
						<td width="30%">Ühe koka palk:</td>
						<td width="70%">
							<input type="text" name="value" value="<?php print($_USER->data('resto')->chef_salary);?>" autocomplete="off">
							<input type="hidden" name="token" value="<?php echo Token::generate('EDIT_CHEF'); ?>">
							<input type="submit" value="Muuda palka">
							(Soovitatav palk: <?php print($_GENERAL->format_number($chef_recommended_salary));?>)
						</td>
					</tr>
				</form>
			</table>
			<table>
				<tr>
					<td width="30%">Üks kokk vajab käike:</td>
					<td width="70%"><?php print($_GENERAL->format_number($buy_chef_turns));?></td>
				</tr>
				<tr>
					<td>Üks kokk vajab pindala:</td>
					<td><?php print($_GENERAL->format_number($buy_chef_area));?> m<sup>2</sup></td>
				</tr>
				<tr>
					<td>Üks kokk maksab:</td>
					<td><?php print($_GENERAL->format_number($buy_chef_money));?></td>
				</tr>
				<form action="p.php?p=restaurant&page=personal" method="POST">
					<tr>
						<td>Mitu kokka palkad:</td>
						<td>
							<input type="text" name="value" autocomplete="off">
							<input type="hidden" name="token" value="<?php echo Token::generate('BUY_CHEF'); ?>">
							<input type="submit" value="Palka kokkasid">
						</td>
					</tr>
				</form>
			</table>
		</p>
	</div>
<?php

$waiter_level_query = DB::getInstance(1)->query("SELECT * FROM `resto_levels`");
foreach ($waiter_level_query->results() as $lvl) {
	$selected = '';
	if ($lvl->id == $_USER->data('resto')->waiter_level) {
		$selected = ' selected';
	}
	$output_line_waiter .= '<option'.$selected.' value="'.$lvl->id.'">'.$lvl->name.' | Hind: '.$_GENERAL->format_number($lvl->money).' | Haridust: '.$_GENERAL->format_number($lvl->education).'</option>';
}
?>
	<div id="page">
		<div class="page-title">Restorani teenindajad</div>
		<p>
			<table>
				<tr>
					<td width="30%">Restoranil teenindajaid:</td>
					<td width="70%"><?php print($_GENERAL->format_number($_USER->data('resto')->waiter));?></td>
				</tr>
				<tr>
					<td>Teenindajate moraal:</td>
					<td><?php print($waiter_morale_out);?></td>
				</tr>
			</table>
			<table>
				<form action="p.php?p=restaurant&page=personal" method="POST">
					<tr>
						<td width="20%">Teenindajate tase:</td>
						<td width="80%">
							<select name="waiter_level">
								<?php print($output_line_waiter);?>
							</select>
							<input type="hidden" name="token" value="<?php echo Token::generate('WAITER_LEVEL'); ?>">
							<input type="submit" value="Koolita">
						</td>
					</tr>
				</form>
			</table>
			<table>
				<form action="p.php?p=restaurant&page=personal" method="POST">
					<tr>
						<td width="30%">Ühe teenindaja palk:</td>
						<td width="70%">
							<input type="text" name="value" value="<?php print($_USER->data('resto')->waiter_salary);?>" autocomplete="off">
							<input type="hidden" name="token" value="<?php echo Token::generate('EDIT_WAITER'); ?>">
							<input type="submit" value="Muuda palka">
							(Soovitatav palk: <?php print($_GENERAL->format_number($waiter_recommended_salary));?>)
						</td>
					</tr>
				</form>
			</table>
			<table>
				<tr>
					<td width="30%">Üks teenindaja vajab käike:</td>
					<td width="70%"><?php print($_GENERAL->format_number($buy_waiter_turns));?></td>
				</tr>
				<tr>
					<td>Üks teenindaja vajab pindala:</td>
					<td><?php print($_GENERAL->format_number($buy_waiter_area));?> m<sup>2</sup></td>
				</tr>
				<tr>
					<td>Üks teenindaja maksab:</td>
					<td><?php print($_GENERAL->format_number($buy_waiter_money));?></td>
				</tr>
				<form action="p.php?p=restaurant&page=personal" method="POST">
					<tr>
						<td>Mitu teenindajat palkad:</td>
						<td>
							<input type="text" name="value" autocomplete="off">
							<input type="hidden" name="token" value="<?php echo Token::generate('BUY_WATER'); ?>">
							<input type="submit" value="Palka teenindajaid">
						</td>
					</tr>
				</form>
			</table>
		</p>
	</div>