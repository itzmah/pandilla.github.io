<?php

$food_make_turns = 10;

$make_foods_hour = (3 * $_USER->data('resto')->chef * $_USER->data('resto')->chef_level);

$make_foods_today = ($make_foods_hour * $_USER->data('resto')->work_hours);
$make_foods_morale = round($make_foods_today * $_USER->data('resto')->chef_morale / 100);
$make_foods_check =  $make_foods_morale - $_USER->data('resto')->food_make_today;
$max_food = ($make_foods_check < 0) ? 0 : $make_foods_check;

$make_foods_hour_morale = round($make_foods_hour * $_USER->data('resto')->chef_morale / 100);
$make_foods_hour_check =  $make_foods_hour_morale - $_USER->data('resto')->food_make_limit;
$max_food_hour = ($make_foods_hour_check < 0) ? 0 : $make_foods_hour_check;
if ($max_food < $max_food_hour) {
	$max_food_hour = $max_food;
}


$one_food_time = 61 - $_USER->data('resto')->chef_level - $_USER->data('resto')->kitchen_level;

$food_1_foods = 10;
$food_2_foods = 14;
$food_3_foods = 11;
$food_4_foods = 23;
$food_5_foods = 18;

if(Input::exists()) {
	if(Token::check(Input::get('token'), 'COOK_1') ) {
		$value = round(Input::get('value'));
		$foods_total = ($value * $food_1_foods);

		if ($value < 1) {
			$_GENERAL->addError("Sisestage kui palju sööke tahate teha.");
		}

		if ($_USER->data('data')->turns < $food_make_turns) {
			$_GENERAL->addError("Teil ei ole piisavalt käike.");
		}

		if ($_USER->data('resto')->foods < $foods_total) {
			$_GENERAL->addError("Restoranil ei ole piisavalt toiduaineid.");
		}

		if ($max_food_hour < $value) {
			$_GENERAL->addError("Te ei saa selles tunnis niipalju sööke teha.");
		}

		if (empty($_GENERAL->errors()) === true) {
			$food_make_time = time() + ($value * $one_food_time);
			$_USER->update(array(
					'turns' => $_USER->data('data')->turns - $food_make_turns
				),$_USER->data()->id, 'users_data');

			$_USER->update(array(
					'food_1_amount' => $value,
					'food_1_start' => date("Y-m-d H:i:s"),
					'food_1_end' => date("Y-m-d H:i:s", $food_make_time),
					'food_make_limit' => $_USER->data('resto')->food_make_limit + $value,
					'food_make_today' => $_USER->data('resto')->food_make_today + $value,
					'reputation' => $_USER->data('resto')->reputation + $value,
					'foods' => $_USER->data('resto')->foods - $foods_total
				),$_USER->data()->id, 'users_data_resto');

			Session::flash('restaurant', 'Teie restorani kokad hakkasid teed tegema.');
			Redirect::to('p.php?p=restaurant&page=kitchen');
		}
	} else if(Token::check(Input::get('token'), 'COOK_2') ) {
		$value = round(Input::get('value'));
		$foods_total = ($value * $food_2_foods);

		if ($value < 1) {
			$_GENERAL->addError("Sisestage kui palju sööke tahate teha.");
		}

		if ($_USER->data('data')->turns < $food_make_turns) {
			$_GENERAL->addError("Teil ei ole piisavalt käike.");
		}

		if ($_USER->data('resto')->foods < $foods_total) {
			$_GENERAL->addError("Restoranil ei ole piisavalt toiduaineid.");
		}

		if ($max_food_hour < $value) {
			$_GENERAL->addError("Te ei saa selles tunnis niipalju sööke teha.");
		}

		if (empty($_GENERAL->errors()) === true) {
			$food_make_time = time() + ($value * $one_food_time);

			$_USER->update(array(
					'turns' => $_USER->data('data')->turns - $food_make_turns
				),$_USER->data()->id, 'users_data');

			$_USER->update(array(
					'food_2_amount' => $value,
					'food_2_start' => date("Y-m-d H:i:s"),
					'food_2_end' => date("Y-m-d H:i:s", $food_make_time),
					'food_make_limit' => $_USER->data('resto')->food_make_limit + $value,
					'food_make_today' => $_USER->data('resto')->food_make_today + $value,
					'reputation' => $_USER->data('resto')->reputation + $value,
					'foods' => $_USER->data('resto')->foods - $foods_total
				),$_USER->data()->id, 'users_data_resto');

			Session::flash('restaurant', 'Teie restorani kokad hakkasid kohvi tegema.');
			Redirect::to('p.php?p=restaurant&page=kitchen');
		}
	} else if(Token::check(Input::get('token'), 'COOK_3') ) {
		$value = round(Input::get('value'));
		$foods_total = ($value * $food_3_foods);

		if ($value < 1) {
			$_GENERAL->addError("Sisestage kui palju sööke tahate teha.");
		}

		if ($_USER->data('data')->turns < $food_make_turns) {
			$_GENERAL->addError("Teil ei ole piisavalt käike.");
		}

		if ($_USER->data('resto')->foods < $foods_total) {
			$_GENERAL->addError("Restoranil ei ole piisavalt toiduaineid.");
		}

		if ($max_food_hour < $value) {
			$_GENERAL->addError("Te ei saa selles tunnis niipalju sööke teha.");
		}

		if (empty($_GENERAL->errors()) === true) {
			$food_make_time = time() + ($value * $one_food_time);

			$_USER->update(array(
					'turns' => $_USER->data('data')->turns - $food_make_turns
				),$_USER->data()->id, 'users_data');


			$_USER->update(array(
					'food_3_amount' => $value,
					'food_3_start' => date("Y-m-d H:i:s"),
					'food_3_end' => date("Y-m-d H:i:s", $food_make_time),
					'food_make_limit' => $_USER->data('resto')->food_make_limit + $value,
					'food_make_today' => $_USER->data('resto')->food_make_today + $value,
					'reputation' => $_USER->data('resto')->reputation + $value,
					'foods' => $_USER->data('resto')->foods - $foods_total
				),$_USER->data()->id, 'users_data_resto');

			Session::flash('restaurant', 'Teie restorani kokad hakkasid jäätist tegema.');
			Redirect::to('p.php?p=restaurant&page=kitchen');
		}
	} else if(Token::check(Input::get('token'), 'COOK_4') ) {
		$value = round(Input::get('value'));
		$foods_total = ($value * $food_4_foods);

		if ($value < 1) {
			$_GENERAL->addError("Sisestage kui palju sööke tahate teha.");
		}

		if ($_USER->data('data')->turns < $food_make_turns) {
			$_GENERAL->addError("Teil ei ole piisavalt käike.");
		}

		if ($_USER->data('resto')->foods < $foods_total) {
			$_GENERAL->addError("Restoranil ei ole piisavalt toiduaineid.");
		}

		if ($max_food_hour < $value) {
			$_GENERAL->addError("Te ei saa selles tunnis niipalju sööke teha.");
		}

		if (empty($_GENERAL->errors()) === true) {
			$food_make_time = time() + ($value * $one_food_time);

			$_USER->update(array(
					'turns' => $_USER->data('data')->turns - $food_make_turns
				),$_USER->data()->id, 'users_data');

			$_USER->update(array(
					'food_4_amount' => $value,
					'food_4_start' => date("Y-m-d H:i:s"),
					'food_4_end' => date("Y-m-d H:i:s", $food_make_time),
					'food_make_limit' => $_USER->data('resto')->food_make_limit + $value,
					'food_make_today' => $_USER->data('resto')->food_make_today + $value,
					'reputation' => $_USER->data('resto')->reputation + $value,
					'foods' => $_USER->data('resto')->foods - $foods_total
				),$_USER->data()->id, 'users_data_resto');

			Session::flash('restaurant', 'Teie restorani kokad hakkasid seapraade tegema.');
			Redirect::to('p.php?p=restaurant&page=kitchen');
		}
	} else if(Token::check(Input::get('token'), 'COOK_5') ) {
		$value = round(Input::get('value'));
		$foods_total = ($value * $food_5_foods);

		if ($value < 1) {
			$_GENERAL->addError("Sisestage kui palju sööke tahate teha.");
		}

		if ($_USER->data('data')->turns < $food_make_turns) {
			$_GENERAL->addError("Teil ei ole piisavalt käike.");
		}

		if ($_USER->data('resto')->foods < $foods_total) {
			$_GENERAL->addError("Restoranil ei ole piisavalt toiduaineid.");
		}

		if ($max_food_hour < $value) {
			$_GENERAL->addError("Te ei saa selles tunnis niipalju sööke teha.");
		}

		if (empty($_GENERAL->errors()) === true) {
			$food_make_time = time() + ($value * $one_food_time);

			$_USER->update(array(
					'turns' => $_USER->data('data')->turns - $food_make_turns
				),$_USER->data()->id, 'users_data');

			$_USER->update(array(
					'food_5_amount' => $value,
					'food_5_start' => date("Y-m-d H:i:s"),
					'food_5_end' => date("Y-m-d H:i:s", $food_make_time),
					'food_make_limit' => $_USER->data('resto')->food_make_limit + $value,
					'food_make_today' => $_USER->data('resto')->food_make_today + $value,
					'reputation' => $_USER->data('resto')->reputation + $value,
					'foods' => $_USER->data('resto')->foods - $foods_total
				),$_USER->data()->id, 'users_data_resto');

			Session::flash('restaurant', 'Teie restorani kokad hakkasid kartulipraade tegema.');
			Redirect::to('p.php?p=restaurant&page=kitchen');
		}
	}
}


if (strtotime($_USER->data('resto')->food_1_end) <= time()) {
	if ($_USER->data('resto')->food_1_amount != 0) {
		$_USER->update(array(
			'food_1_amount' => 0,
			'food_1' => $_USER->data('resto')->food_1 + $_USER->data('resto')->food_1_amount
		),$_USER->data()->id, 'users_data_resto');
		$food_ready = true;
	}
}

if (strtotime($_USER->data('resto')->food_2_end) <= time()) {
	if ($_USER->data('resto')->food_2_amount != 0) {
		$_USER->update(array(
			'food_2_amount' => 0,
			'food_2' => $_USER->data('resto')->food_2 + $_USER->data('resto')->food_2_amount
		),$_USER->data()->id, 'users_data_resto');
		$food_ready = true;
	}
}

if (strtotime($_USER->data('resto')->food_3_end) <= time()) {
	if ($_USER->data('resto')->food_3_amount != 0) {
		$_USER->update(array(
			'food_3_amount' => 0,
			'food_3' => $_USER->data('resto')->food_3 + $_USER->data('resto')->food_3_amount
		),$_USER->data()->id, 'users_data_resto');
		$food_ready = true;
	}
}

if (strtotime($_USER->data('resto')->food_4_end) <= time()) {
	if ($_USER->data('resto')->food_4_amount != 0) {
		$_USER->update(array(
			'food_4_amount' => 0,
			'food_4' => $_USER->data('resto')->food_4 + $_USER->data('resto')->food_4_amount,
		),$_USER->data()->id, 'users_data_resto');
		$food_ready = true;
	}
}

if (strtotime($_USER->data('resto')->food_5_end) <= time()) {
	if ($_USER->data('resto')->food_5_amount != 0) {
		$_USER->update(array(
			'food_5_amount' => 0,
			'food_5' => $_USER->data('resto')->food_5 + $_USER->data('resto')->food_5_amount
		),$_USER->data()->id, 'users_data_resto');
		$food_ready = true;
	}
}

if ($food_ready === true) {
	Session::flash('restaurant', 'Teie restorani kokkadel said söögid valmis.');
	Redirect::to('p.php?p=restaurant&page=kitchen');
}

?>
	<div id="page">
		<div class="page-title">Restorani köök</div>
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
					<td width="20%"><img src="css/default/images/chef.png" width="100" height="100"></td>
					<td width="80%">
						Siin valmistavad kokad teie restoranile toite.<br>
						Toitude valmistamise kogus ja kiirus sõltub kokkade tasemest ja köögi tehnika levelist.<br>
						Toitude valmistamine võtab <?php print($_GENERAL->format_number($food_make_turns));?> käiku sõltumata kogusest.<br>
						<br>
						Tänasel päeval saavad kokad veel valmistada <b><?php print($_GENERAL->format_number($max_food));?></b> toitu.<br>
						<br>
						Selles tunnis saate valmistada veel <b><?php print($_GENERAL->format_number($max_food_hour));?></b> toitu.
					</td>
				</tr>
			</table>
		</p>
	</div>

	<div id="page">
		<div class="page-title">Tee</div>
		<p>
			<?php
			if (strtotime($_USER->data('resto')->food_1_end) > time()) {
				$food_1_percent_cur = (strtotime($_USER->data('resto')->food_1_end) - time());
				$food_1_percent_total = (strtotime($_USER->data('resto')->food_1_end) - strtotime($_USER->data('resto')->food_1_start));
				$food_1_percent = 100 - ceil($food_1_percent_cur * 100 / $food_1_percent_total);
			?>
			<table>
				<tr>
					<td width="25%">Teed valmis:</td>
					<td width="75%">
						<div class="resto-p-bar">
							<span style="width: <?php print($food_1_percent);?>%;"></span>
						</div>
					</td>
				</tr>
				<tr>
					<td>Teed valmistamisel:</td>
					<td><?php print($_GENERAL->format_number($_USER->data('resto')->food_1_amount));?></td>
				</tr>
				<tr>
					<td>Tee saab valmis:</td>
					<td><?php print($_USER->data('resto')->food_1_end);?></td>
				</tr>
			</table>
			<?php
			} else {
			?>
			<form action="p.php?p=restaurant&page=kitchen" method="POST">
				<table>
					<tr>
						<td width="25%">Restoranil teed:</td>
						<td width="75%"><?php print($_GENERAL->format_number($_USER->data('resto')->food_1));?></td>
					</tr>
					<tr>
						<td>Toiduaineid vaja:</td>
						<td><?php print($_GENERAL->format_number($food_1_foods));?></td>
					</tr>
					<tr>
						<td>Kui palju teed teed:</td>
						<td>
							<input type="text" name="value" autocomplete="off">
							<input type="hidden" name="token" value="<?php echo Token::generate('COOK_1'); ?>">
							<input type="submit" value="Valmista teed">
						</td>
					</tr>
				</table>
			</form>
			<?php
			}
			?>
		</p>
	</div>

	<div id="page">
		<div class="page-title">Kohv</div>
		<p>
			<?php
			if (strtotime($_USER->data('resto')->food_2_end) > time()) {
				$food_2_percent_cur = (strtotime($_USER->data('resto')->food_2_end) - time());
				$food_2_percent_total = (strtotime($_USER->data('resto')->food_2_end) - strtotime($_USER->data('resto')->food_2_start));
				$food_2_percent = 100 - ceil($food_2_percent_cur * 100 / $food_2_percent_total);
			?>
			<table>
				<tr>
					<td width="25%">Kohvi valmis:</td>
					<td width="75%">
						<div class="resto-p-bar">
							<span style="width: <?php print($food_2_percent);?>%;"></span>
						</div>
					</td>
				</tr>
				<tr>
					<td>Kohvi valmistamisel:</td>
					<td><?php print($_GENERAL->format_number($_USER->data('resto')->food_2_amount));?></td>
				</tr>
				<tr>
					<td>Kohv saab valmis:</td>
					<td><?php print($_USER->data('resto')->food_2_end);?></td>
				</tr>
			</table>
			<?php
			} else {
			?>
			<form action="p.php?p=restaurant&page=kitchen" method="POST">
				<table>
					<tr>
						<td width="25%">Restoranil kohvi:</td>
						<td width="75%"><?php print($_GENERAL->format_number($_USER->data('resto')->food_2));?></td>
					</tr>
					<tr>
						<td>Toiduaineid vaja:</td>
						<td><?php print($_GENERAL->format_number($food_2_foods));?></td>
					</tr>
					<tr>
						<td>Kui palju kohvi teed:</td>
						<td>
							<input type="text" name="value" autocomplete="off">
							<input type="hidden" name="token" value="<?php echo Token::generate('COOK_2'); ?>">
							<input type="submit" value="Valmista kohvi">
						</td>
					</tr>
				</table>
			</form>
			<?php
			}
			?>
		</p>
	</div>

	<div id="page">
		<div class="page-title">Jäätis</div>
		<p>
			<?php
			if (strtotime($_USER->data('resto')->food_3_end) > time()) {
				$food_3_percent_cur = (strtotime($_USER->data('resto')->food_3_end) - time());
				$food_3_percent_total = (strtotime($_USER->data('resto')->food_3_end) - strtotime($_USER->data('resto')->food_3_start));
				$food_3_percent = 100 - ceil($food_3_percent_cur * 100 / $food_3_percent_total);
			?>
			<table>
				<tr>
					<td width="25%">Jäätist valmis:</td>
					<td width="75%">
						<div class="resto-p-bar">
							<span style="width: <?php print($food_3_percent);?>%;"></span>
						</div>
					</td>
				</tr>
				<tr>
					<td>Jäätist valmistamisel:</td>
					<td><?php print($_GENERAL->format_number($_USER->data('resto')->food_3_amount));?></td>
				</tr>
				<tr>
					<td>Jäätis saab valmis:</td>
					<td><?php print($_USER->data('resto')->food_3_end);?></td>
				</tr>
			</table>
			<?php
			} else {
			?>
			<form action="p.php?p=restaurant&page=kitchen" method="POST">
				<table>
					<tr>
						<td width="25%">Restoranil jäätist:</td>
						<td width="75%"><?php print($_GENERAL->format_number($_USER->data('resto')->food_3));?></td>
					</tr>
					<tr>
						<td>Toiduaineid vaja:</td>
						<td><?php print($_GENERAL->format_number($food_3_foods));?></td>
					</tr>
					<tr>
						<td>Kui palju seapraade teed:</td>
						<td>
							<input type="text" name="value" autocomplete="off">
							<input type="hidden" name="token" value="<?php echo Token::generate('COOK_3'); ?>">
							<input type="submit" value="Valmista jäätist">
						</td>
					</tr>
				</table>
			</form>
			<?php
			}
			?>
		</p>
	</div>

	<div id="page">
		<div class="page-title">Seapraad</div>
		<p>
			<?php
			if (strtotime($_USER->data('resto')->food_4_end) > time()) {
				$food_4_percent_cur = (strtotime($_USER->data('resto')->food_4_end) - time());
				$food_4_percent_total = (strtotime($_USER->data('resto')->food_4_end) - strtotime($_USER->data('resto')->food_4_start));
				$food_4_percent = 100 - ceil($food_4_percent_cur * 100 / $food_4_percent_total);
			?>
			<table>
				<tr>
					<td width="25%">Seapraade valmis:</td>
					<td width="75%">
						<div class="resto-p-bar">
							<span style="width: <?php print($food_4_percent);?>%;"></span>
						</div>
					</td>
				</tr>
				<tr>
					<td>Seapraade valmistamisel:</td>
					<td><?php print($_GENERAL->format_number($_USER->data('resto')->food_4_amount));?></td>
				</tr>
				<tr>
					<td>Toit saab valmis:</td>
					<td><?php print($_USER->data('resto')->food_4_end);?></td>
				</tr>
			</table>
			<?php
			} else {
			?>
			<form action="p.php?p=restaurant&page=kitchen" method="POST">
				<table>
					<tr>
						<td width="25%">Restoranil seapraade:</td>
						<td width="75%"><?php print($_GENERAL->format_number($_USER->data('resto')->food_4));?></td>
					</tr>
					<tr>
						<td>Toiduaineid vaja:</td>
						<td><?php print($_GENERAL->format_number($food_4_foods));?></td>
					</tr>
					<tr>
						<td>Kui palju seapraade teed:</td>
						<td>
							<input type="text" name="value" autocomplete="off">
							<input type="hidden" name="token" value="<?php echo Token::generate('COOK_4'); ?>">
							<input type="submit" value="Valmista seapraade">
						</td>
					</tr>
				</table>
			</form>
			<?php
			}
			?>
		</p>
	</div>

	<div id="page">
		<div class="page-title">Kartulipraad</div>
		<p>
			<?php
			if (strtotime($_USER->data('resto')->food_5_end) > time()) {
				$food_5_percent_cur = (strtotime($_USER->data('resto')->food_5_end) - time());
				$food_5_percent_total = (strtotime($_USER->data('resto')->food_5_end) - strtotime($_USER->data('resto')->food_5_start));
				$food_5_percent = 100 - ceil($food_5_percent_cur * 100 / $food_5_percent_total);
			?>
			<table>
				<tr>
					<td width="25%">Kartulipraade valmis:</td>
					<td width="75%">
						<div class="resto-p-bar">
							<span style="width: <?php print($food_5_percent);?>%;"></span>
						</div>
					</td>
				</tr>
				<tr>
					<td>Kartulipraade valmistamisel:</td>
					<td><?php print($_GENERAL->format_number($_USER->data('resto')->food_5_amount));?></td>
				</tr>
				<tr>
					<td>Toit saab valmis:</td>
					<td><?php print($_USER->data('resto')->food_5_end);?></td>
				</tr>
			</table>
			<?php
			} else {
			?>
			<form action="p.php?p=restaurant&page=kitchen" method="POST">
				<table>
					<tr>
						<td width="25%">Restoranil kartulipraade:</td>
						<td width="75%"><?php print($_GENERAL->format_number($_USER->data('resto')->food_5));?></td>
					</tr>
					<tr>
						<td>Toiduaineid vaja:</td>
						<td><?php print($_GENERAL->format_number($food_5_foods));?></td>
					</tr>
					<tr>
						<td>Kui palju kartulipraade teed:</td>
						<td>
							<input type="text" name="value" autocomplete="off">
							<input type="hidden" name="token" value="<?php echo Token::generate('COOK_5'); ?>">
							<input type="submit" value="Valmista kartulipraade">
						</td>
					</tr>
				</table>
			</form>
			<?php
			}
			?>
		</p>
	</div>