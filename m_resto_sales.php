<?php

$buy_foods_money = 10;

$sell_foods_today = (10 * $_USER->data('resto')->waiter * $_USER->data('resto')->waiter_level * $_USER->data('resto')->work_hours);
$sell_foods_morale = round($sell_foods_today * $_USER->data('resto')->waiter_morale / 100);
$sell_foods_check =  $sell_foods_morale - $_USER->data('resto')->food_sell_limit;
$sell_foods_total = ($sell_foods_check < 0) ? 0 : $sell_foods_check;

if(Input::exists()) {
	if(Token::check(Input::get('token'), 'BUY_FOODS') ) {
		$value = round(Input::get('value'));
		$money_total = ($value * $buy_foods_money);

		if ($value < 1) {
			$_GENERAL->addError("Sisestage kui palju toiduained tahate osta.");
		}

		if ($_USER->data('resto')->money < $money_total) {
			$_GENERAL->addError("Restoranil ei ole piisavalt raha.");
		}

		if (empty($_GENERAL->errors()) === true) {
			$_USER->update(array(
					'foods' => $_USER->data('resto')->foods + $value,
					'money' => $_USER->data('resto')->money - $money_total,
					'outcome_today' => $_USER->data('resto')->outcome_today + $money_total
				),$_USER->data()->id, 'users_data_resto');

			Session::flash('restaurant', 'Te ostsite restoranile toiduained.');
			Redirect::to('p.php?p=restaurant&page=sales');
		}
	} else if(Token::check(Input::get('token'), 'SELL') ) {
		$product = round(Input::get('product'));
		if ($product == 1) {
			$value = round(Input::get('value'));
			if (empty(Input::get('sell')) === false) {
				$money_total = $value * $_USER->data('resto')->food_1_price;

				if ($value < 1) {
					$_GENERAL->addError("Sisestage kui palju te seda toitu müüte.");
				}

				if ($_USER->data('resto')->food_1_orders < $value) {
					$_GENERAL->addError("Sellel tootel ei ole niipalju tellimusi.");
				}

				if ($_USER->data('resto')->food_1 < $value) {
					$_GENERAL->addError("Restoranil ei ole piisavalt seda toodet.");
				}

				if ($sell_foods_total < $value) {
					$_GENERAL->addError("Teie teenindajad ei saa täna niipalju müüa.");
				}

				if (empty($_GENERAL->errors()) === true) {
					$_USER->update(array(
							'food_1' => $_USER->data('resto')->food_1 - $value,
							'food_1_orders' => $_USER->data('resto')->food_1_orders - $value,
							'money' => $_USER->data('resto')->money + $money_total,
							'food_sell_today' => $_USER->data('resto')->food_sell_today + $value,
							'food_sell_limit' => $_USER->data('resto')->food_sell_limit + $value,
							'income_today' => $_USER->data('resto')->income_today + $money_total,
							'reputation' => $_USER->data('resto')->reputation + $value
						),$_USER->data()->id, 'users_data_resto');

					Session::flash('restaurant', 'Te müüsite restorani toite.');
					Redirect::to('p.php?p=restaurant&page=sales');
				}

			} else if (empty(Input::get('edit')) === false) {
				if ($value < 1) {
					$_GENERAL->addError("Sisestage hind.");
				}

				if (strtotime($_USER->data('resto')->food_1_price_edit) > time()) {
					$_GENERAL->addError("Söögi hinda saab muuta ühe korra 15 minuti jooksul.");
				}

				if (empty($_GENERAL->errors()) === true) {
					$price_new_time = time() + 60 * 15;
					$_USER->update(array(
							'food_1_price' => $value,
							'food_1_price_edit' => date("Y-m-d H:i:s", $price_new_time),
							'food_1_orders' => 0
						),$_USER->data()->id, 'users_data_resto');

					Session::flash('restaurant', 'Te muutsite söögi hinda.');
					Redirect::to('p.php?p=restaurant&page=sales');
				}
			}
		} else if ($product == 2) {
			$value = round(Input::get('value'));
			if (empty(Input::get('sell')) === false) {
				$money_total = $value * $_USER->data('resto')->food_2_price;

				if ($value < 1) {
					$_GENERAL->addError("Sisestage kui palju te seda toitu müüte.");
				}

				if ($_USER->data('resto')->food_2_orders < $value) {
					$_GENERAL->addError("Sellel tootel ei ole niipalju tellimusi.");
				}

				if ($_USER->data('resto')->food_2 < $value) {
					$_GENERAL->addError("Restoranil ei ole piisavalt seda toodet.");
				}

				if ($sell_foods_total < $value) {
					$_GENERAL->addError("Teie teenindajad ei saa täna niipalju müüa.");
				}

				if (empty($_GENERAL->errors()) === true) {
					$_USER->update(array(
							'food_2' => $_USER->data('resto')->food_2 - $value,
							'food_2_orders' => $_USER->data('resto')->food_2_orders - $value,
							'money' => $_USER->data('resto')->money + $money_total,
							'food_sell_today' => $_USER->data('resto')->food_sell_today + $value,
							'food_sell_limit' => $_USER->data('resto')->food_sell_limit + $value,
							'income_today' => $_USER->data('resto')->income_today + $money_total,
							'reputation' => $_USER->data('resto')->reputation + $value
						),$_USER->data()->id, 'users_data_resto');

					Session::flash('restaurant', 'Te müüsite restorani toite.');
					Redirect::to('p.php?p=restaurant&page=sales');
				}

			} else if (empty(Input::get('edit')) === false) {
				if ($value < 1) {
					$_GENERAL->addError("Sisestage hind.");
				}

				if (strtotime($_USER->data('resto')->food_2_price_edit) > time()) {
					$_GENERAL->addError("Söögi hinda saab muuta ühe korra 15 minuti jooksul.");
				}

				if (empty($_GENERAL->errors()) === true) {
					$price_new_time = time() + 60 * 15;
					$_USER->update(array(
							'food_2_price' => $value,
							'food_2_price_edit' => date("Y-m-d H:i:s", $price_new_time),
							'food_2_orders' => 0
						),$_USER->data()->id, 'users_data_resto');

					Session::flash('restaurant', 'Te muutsite söögi hinda.');
					Redirect::to('p.php?p=restaurant&page=sales');
				}
			}
		} else if ($product == 3) {
			$value = round(Input::get('value'));
			if (empty(Input::get('sell')) === false) {
				$money_total = $value * $_USER->data('resto')->food_3_price;

				if ($value < 1) {
					$_GENERAL->addError("Sisestage kui palju te seda toitu müüte.");
				}

				if ($_USER->data('resto')->food_3_orders < $value) {
					$_GENERAL->addError("Sellel tootel ei ole niipalju tellimusi.");
				}

				if ($_USER->data('resto')->food_3 < $value) {
					$_GENERAL->addError("Restoranil ei ole piisavalt seda toodet.");
				}

				if ($sell_foods_total < $value) {
					$_GENERAL->addError("Teie teenindajad ei saa täna niipalju müüa.");
				}

				if (empty($_GENERAL->errors()) === true) {
					$_USER->update(array(
							'food_3' => $_USER->data('resto')->food_3 - $value,
							'food_3_orders' => $_USER->data('resto')->food_3_orders - $value,
							'money' => $_USER->data('resto')->money + $money_total,
							'food_sell_today' => $_USER->data('resto')->food_sell_today + $value,
							'food_sell_limit' => $_USER->data('resto')->food_sell_limit + $value,
							'income_today' => $_USER->data('resto')->income_today + $money_total,
							'reputation' => $_USER->data('resto')->reputation + $value
						),$_USER->data()->id, 'users_data_resto');

					Session::flash('restaurant', 'Te müüsite restorani toite.');
					Redirect::to('p.php?p=restaurant&page=sales');
				}

			} else if (empty(Input::get('edit')) === false) {
				if ($value < 1) {
					$_GENERAL->addError("Sisestage hind.");
				}

				if (strtotime($_USER->data('resto')->food_3_price_edit) > time()) {
					$_GENERAL->addError("Söögi hinda saab muuta ühe korra 15 minuti jooksul.");
				}

				if (empty($_GENERAL->errors()) === true) {
					$price_new_time = time() + 60 * 15;
					$_USER->update(array(
							'food_3_price' => $value,
							'food_3_price_edit' => date("Y-m-d H:i:s", $price_new_time),
							'food_3_orders' => 0
						),$_USER->data()->id, 'users_data_resto');

					Session::flash('restaurant', 'Te muutsite söögi hinda.');
					Redirect::to('p.php?p=restaurant&page=sales');
				}
			}
		} else if ($product == 4) {
			$value = round(Input::get('value'));
			if (empty(Input::get('sell')) === false) {
				$money_total = $value * $_USER->data('resto')->food_4_price;

				if ($value < 1) {
					$_GENERAL->addError("Sisestage kui palju te seda toitu müüte.");
				}

				if ($_USER->data('resto')->food_4_orders < $value) {
					$_GENERAL->addError("Sellel tootel ei ole niipalju tellimusi.");
				}

				if ($_USER->data('resto')->food_4 < $value) {
					$_GENERAL->addError("Restoranil ei ole piisavalt seda toodet.");
				}

				if ($sell_foods_total < $value) {
					$_GENERAL->addError("Teie teenindajad ei saa täna niipalju müüa.");
				}

				if (empty($_GENERAL->errors()) === true) {
					$_USER->update(array(
							'food_4' => $_USER->data('resto')->food_4 - $value,
							'food_4_orders' => $_USER->data('resto')->food_4_orders - $value,
							'money' => $_USER->data('resto')->money + $money_total,
							'food_sell_today' => $_USER->data('resto')->food_sell_today + $value,
							'food_sell_limit' => $_USER->data('resto')->food_sell_limit + $value,
							'income_today' => $_USER->data('resto')->income_today + $money_total,
							'reputation' => $_USER->data('resto')->reputation + $value
						),$_USER->data()->id, 'users_data_resto');

					Session::flash('restaurant', 'Te müüsite restorani toite.');
					Redirect::to('p.php?p=restaurant&page=sales');
				}

			} else if (empty(Input::get('edit')) === false) {
				if ($value < 1) {
					$_GENERAL->addError("Sisestage hind.");
				}

				if (strtotime($_USER->data('resto')->food_4_price_edit) > time()) {
					$_GENERAL->addError("Söögi hinda saab muuta ühe korra 15 minuti jooksul.");
				}

				if (empty($_GENERAL->errors()) === true) {
					$price_new_time = time() + 60 * 15;
					$_USER->update(array(
							'food_4_price' => $value,
							'food_4_price_edit' => date("Y-m-d H:i:s", $price_new_time),
							'food_4_orders' => 0
						),$_USER->data()->id, 'users_data_resto');

					Session::flash('restaurant', 'Te muutsite söögi hinda.');
					Redirect::to('p.php?p=restaurant&page=sales');
				}
			}
		} else if ($product == 5) {
			$value = round(Input::get('value'));
			if (empty(Input::get('sell')) === false) {
				$money_total = $value * $_USER->data('resto')->food_5_price;

				if ($value < 1) {
					$_GENERAL->addError("Sisestage kui palju te seda toitu müüte.");
				}

				if ($_USER->data('resto')->food_5_orders < $value) {
					$_GENERAL->addError("Sellel tootel ei ole niipalju tellimusi.");
				}

				if ($_USER->data('resto')->food_5 < $value) {
					$_GENERAL->addError("Restoranil ei ole piisavalt seda toodet.");
				}

				if ($sell_foods_total < $value) {
					$_GENERAL->addError("Teie teenindajad ei saa täna niipalju müüa.");
				}

				if (empty($_GENERAL->errors()) === true) {
					$_USER->update(array(
							'food_5' => $_USER->data('resto')->food_5 - $value,
							'food_5_orders' => $_USER->data('resto')->food_5_orders - $value,
							'money' => $_USER->data('resto')->money + $money_total,
							'food_sell_today' => $_USER->data('resto')->food_sell_today + $value,
							'food_sell_limit' => $_USER->data('resto')->food_sell_limit + $value,
							'income_today' => $_USER->data('resto')->income_today + $money_total,
							'reputation' => $_USER->data('resto')->reputation + $value
						),$_USER->data()->id, 'users_data_resto');

					Session::flash('restaurant', 'Te müüsite restorani toite.');
					Redirect::to('p.php?p=restaurant&page=sales');
				}

			} else if (empty(Input::get('edit')) === false) {
				if ($value < 1) {
					$_GENERAL->addError("Sisestage hind.");
				}

				if (strtotime($_USER->data('resto')->food_5_price_edit) > time()) {
					$_GENERAL->addError("Söögi hinda saab muuta ühe korra 15 minuti jooksul.");
				}

				if (empty($_GENERAL->errors()) === true) {
					$price_new_time = time() + 60 * 15;
					$_USER->update(array(
							'food_5_price' => $value,
							'food_5_price_edit' => date("Y-m-d H:i:s", $price_new_time),
							'food_5_orders' => 0
						),$_USER->data()->id, 'users_data_resto');

					Session::flash('restaurant', 'Te muutsite söögi hinda.');
					Redirect::to('p.php?p=restaurant&page=sales');
				}
			}
		} else {
			$_GENERAL->addError("Palun valige toode.");
		}
	}
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
					<td width="20%"><img src="css/default/images/waiter.png" width="100" height="100"></td>
					<td width="80%">
						Siin müüvad restorani teenindajad teie kokkade valmistatud toite.<br>
						Tellimuste arv sõltub teenindussaali mööbli levelist ja kui kallid on teie toidud.<br>
						<br>
						Teenindajad saavad täna müüa veel <b><?php print($_GENERAL->format_number($sell_foods_total));?></b> toitu.
					</td>
				</tr>
			</table>
		</p>
	</div>

	<div id="page">
		<div class="page-title">Müü restorani toite</div>
		<p>
			<form action="p.php?p=restaurant&page=sales" method="POST">
				<table>
					<tr>
						<th width="30%">Nimi</th>
						<th width="20%">Restoranil on</th>
						<th width="20%">Hind</th>
						<th width="20%">Tellimusi</th>
						<th width="10%">#</th>
					</tr>
					<tr>
						<td>Tee</td>
						<td align="center"><?php print($_GENERAL->format_number($_USER->data('resto')->food_1));?></td>
						<td align="center"><?php print($_GENERAL->format_number($_USER->data('resto')->food_1_price));?></td>
						<td align="center"><?php print($_GENERAL->format_number($_USER->data('resto')->food_1_orders));?></td>
						<td align="center">
							<input type="radio" name="product" value="1">
						</td>
					</tr>
					<tr>
						<td>Kohv</td>
						<td align="center"><?php print($_GENERAL->format_number($_USER->data('resto')->food_2));?></td>
						<td align="center"><?php print($_GENERAL->format_number($_USER->data('resto')->food_2_price));?></td>
						<td align="center"><?php print($_GENERAL->format_number($_USER->data('resto')->food_2_orders));?></td>
						<td align="center">
							<input type="radio" name="product" value="2">
						</td>
					</tr>
					<tr>
						<td>Jäätis</td>
						<td align="center"><?php print($_GENERAL->format_number($_USER->data('resto')->food_3));?></td>
						<td align="center"><?php print($_GENERAL->format_number($_USER->data('resto')->food_3_price));?></td>
						<td align="center"><?php print($_GENERAL->format_number($_USER->data('resto')->food_3_orders));?></td>
						<td align="center">
							<input type="radio" name="product" value="3">
						</td>
					</tr>
					<tr>
						<td>Seapraad</td>
						<td align="center"><?php print($_GENERAL->format_number($_USER->data('resto')->food_4));?></td>
						<td align="center"><?php print($_GENERAL->format_number($_USER->data('resto')->food_4_price));?></td>
						<td align="center"><?php print($_GENERAL->format_number($_USER->data('resto')->food_4_orders));?></td>
						<td align="center">
							<input type="radio" name="product" value="4">
						</td>
					</tr>
					<tr>
						<td>Kartulipraad</td>
						<td align="center"><?php print($_GENERAL->format_number($_USER->data('resto')->food_5));?></td>
						<td align="center"><?php print($_GENERAL->format_number($_USER->data('resto')->food_5_price));?></td>
						<td align="center"><?php print($_GENERAL->format_number($_USER->data('resto')->food_5_orders));?></td>
						<td align="center">
							<input type="radio" name="product" value="5">
						</td>
					</tr>
				</table>
				<table>
					<tr>
						<td width="65%" align="right"><input type="text" name="value" placeholder="Kogus / Hind" autocomplete="off"></td>
						<td width="10%" align="center"><input type="submit" name="sell" value="Müü"></td>
						<td width="5%" align="center">või</td>
						<td width="20%" align="center">
							<input type="hidden" name="token" value="<?php echo Token::generate('SELL'); ?>">
							<input type="submit" name="edit" value="Muuda hinda">
						</td>
					</tr>
				</table>
			</form>
		</p>
	</div>

	<div id="page">
		<div class="page-title">Osta restoranile asju</div>
		<p>
			<form action="p.php?p=restaurant&page=sales" method="POST">
				<table>
					<tr>
						<th width="30%">Nimi</th>
						<th width="25%">Restoranil on</th>
						<th width="10%">Hind</th>
						<th width="25%">Kogus</th>
						<th width="10%">#</th>
					</tr>
					<tr>
						<td>Toiduained</td>
						<td align="center"><?php print($_GENERAL->format_number($_USER->data('resto')->foods));?></td>
						<td align="center"><?php print($_GENERAL->format_number($buy_foods_money));?></td>
						<td align="center"><input type="text" name="value"></td>
						<td align="center">
							<input type="hidden" name="token" value="<?php echo Token::generate('BUY_FOODS'); ?>">
							<input type="submit" value="Osta">
						</td>
					</tr>
				</table>
			</form>
		</p>
	</div>