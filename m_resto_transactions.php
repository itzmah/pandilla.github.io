<?php

$take_money_turns = 200;
$take_money_limit = 10000000000;

$put_money_turns = 150;
$put_money_limit = 500000000;

if(Input::exists()) {
	if(Token::check(Input::get('token'), 'PUT_MONEY') ) {
		$value = round(Input::get('value'));
		if ($value < 1) {
			$_GENERAL->addError("Palun sisestage kui palju raha te tahate investeerida.");
		}

		if ($value > $put_money_limit) {
			$_GENERAL->addError("Maksimaalselt saab korraga investeerida ".$_GENERAL->format_number($put_money_limit).".");
		}

		if ($_USER->data('data')->turns < $put_money_turns) {
			$_GENERAL->addError("Teil ei ole piisavalt käike.");
		}

		if ($_USER->data('data')->money < $value) {
			$_GENERAL->addError("Teil ei ole piisavalt sularaha.");
		}

		if (empty($_GENERAL->errors()) === true) {
			$_USER->update(array(
					'money' => $_USER->data('data')->money - $value,
					'turns' => $_USER->data('data')->turns - $put_money_turns
				),$_USER->data()->id, 'users_data');

			$_USER->update(array(
					'money' => $_USER->data('resto')->money + $value,
					'income_today' => $_USER->data('resto')->income_today + $value
				),$_USER->data()->id, 'users_data_resto');

			Session::flash('restaurant', 'Te investeerisite oma restoranile raha.');
			Redirect::to('p.php?p=restaurant&page=transactions');
		}
	} else if(Token::check(Input::get('token'), 'TAKE_MONEY') ) {
		$value = round(Input::get('value'));
		if ($value < 1) {
			$_GENERAL->addError("Palun sisestage kui palju raha te tahate välja võtta.");
		}

		if ($value > $take_money_limit) {
			$_GENERAL->addError("Maksimaalselt saab korraga raha välja võtta ".$_GENERAL->format_number($put_money_limit).".");
		}

		if ($_USER->data('data')->turns < $take_money_turns) {
			$_GENERAL->addError("Teil ei ole piisavalt käike.");
		}

		if ($_USER->data('resto')->money < $value) {
			$_GENERAL->addError("Restoranil ei ole piisavalt raha.");
		}

		if (empty($_GENERAL->errors()) === true) {
			$_USER->update(array(
					'money' => $_USER->data('data')->money + $value,
					'turns' => $_USER->data('data')->turns - $take_money_turns
				),$_USER->data()->id, 'users_data');

			$_USER->update(array(
					'money' => $_USER->data('resto')->money - $value,
					'outcome_today' => $_USER->data('resto')->outcome_today + $value
				),$_USER->data()->id, 'users_data_resto');

			Session::flash('restaurant', 'Te võtsite oma restoranist raha välja.');
			Redirect::to('p.php?p=restaurant&page=transactions');
		}
	}
}

?>
	<div id="page">
		<div class="page-title">Restorani majandus</div>
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
					<td width="20%"><img src="css/default/images/transactions.png" width="100" height="100"></td>
					<td width="80%">
						Siin on sul võimalik investeerida restorani raha ja võtta restorani kasumit välja.<br>
						Restorani investeerimine võtab <?php print($_GENERAL->format_number($put_money_turns));?> käiku ja maksimaalselt saab investeerida <?php print($_GENERAL->format_number($put_money_limit));?>.<br>
						Restorani kasumi välja võtmiseks on vaja <?php print($_GENERAL->format_number($take_money_turns));?> käiku ja maksimaalselt saab <br>välja võtta <?php print($_GENERAL->format_number($take_money_limit));?>.
					</td>
				</tr>
			</table>
		</p>
	</div>

	<div id="page">
		<div class="page-title">Investeeri restoranile raha</div>
		<p>
			<form action="p.php?p=restaurant&page=transactions" method="POST">
				<table>
					<tr>
						<td width="30%">Käike vaja:</td>
						<td width="70%"><?php print($_GENERAL->format_number($put_money_turns));?></td>
					</tr>
					<tr>
						<td>Maksimaalselt saad investeerida:</td>
						<td><?php print($_GENERAL->format_number($put_money_limit));?></td>
					</tr>
					<tr>
						<td>Kui palju raha investeerid:</td>
						<td>
							<input type="text" name="value" autocomplete="off">
							<input type="hidden" name="token" value="<?php echo Token::generate('PUT_MONEY'); ?>">
							<input type="submit" value="Investeeri">
						</td>
					</tr>
				</table>
			</form>
		</p>
	</div>

	<div id="page">
		<div class="page-title">Võta restorani kasum välja</div>
		<p>
			<form action="p.php?p=restaurant&page=transactions" method="POST">
				<table>
					<tr>
						<td width="30%">Käike vaja:</td>
						<td width="70%"><?php print($_GENERAL->format_number($take_money_turns));?></td>
					</tr>
					<tr>
						<td>Restoranil raha:</td>
						<td><?php print($_GENERAL->format_number($_USER->data('resto')->money));?></td>
					</tr>
					<tr>
						<td>Maksimaalselt saad raha võtta:</td>
						<td><?php print($_GENERAL->format_number($take_money_limit));?></td>
					</tr>
					<tr>
						<td>Kui palju raha võtad:</td>
						<td>
							<input type="text" name="value" autocomplete="off">
							<input type="hidden" name="token" value="<?php echo Token::generate('TAKE_MONEY'); ?>">
							<input type="submit" value="Võta raha">
						</td>
					</tr>
				</table>
			</form>
		</p>
	</div>
