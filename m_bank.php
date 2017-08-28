<?php
/*
 * Name: FreeLand v2
 * Author: Marko Murumaa
 * Created: July 2015
 * Website: www.freelandplay.eu
*/

$_lir = true;
require_once("core/init.php");

	$bank_info_query = DB::getInstance(1)->query("SELECT * FROM `job_list` WHERE `id` = ".$_USER->data('data')->job." ");
	$bank_info = $bank_info_query->first();

	$max_money_bank = $bank_info->bank_limit;
	$max_money_send = 100000;
	$money_send_turns = 5;

	$loan_percent = $bank_info->loan_percent;

	if ($_USER->data('data')->loan > 0) {
		$loan_percent = 0;
	}

	$max_loan_money = round(($_USER->data('data')->money / 100) * $loan_percent);

	$fill_putmoney = ($max_money_bank - $_USER->data('data')->money_bank < $_USER->data('data')->money) ? $max_money_bank - $_USER->data('data')->money_bank : $_USER->data('data')->money;
	$fill_takemoney = $_USER->data('data')->money_bank;
	$fill_takeloan = $max_loan_money;
	$fill_payloan = ($_USER->data('data')->loan > $_USER->data('data')->money) ? $_USER->data('data')->money : $_USER->data('data')->loan;

if(Input::exists()) {
	if(Token::check(Input::get('token'), 'TAKE_MONEY') ) {
		$value = round(Input::get('takemoney'));

		if ($value < 1 or empty($value) === true) {
			$_GENERAL->addError("Palun kirjutage kui palju raha te tahate pangast valja võtta.");
		}

		if ($_USER->data('data')->money_bank < $value) {
			$_GENERAL->addError("Teil ei ole piisavalt raha pangas.");
		}

		if (empty($_GENERAL->errors()) === true) {
			$_USER->update(array(
					'money' => $_USER->data('data')->money + $value,
					'money_bank' => $_USER->data('data')->money_bank - $value
				),$_USER->data()->id, 'users_data');

			Session::flash('bank', 'Te võtsite edukalt pangast raha välja.');
			Redirect::to('p.php?p=bank');
		}

	} elseif(Token::check(Input::get('token'), 'PUT_MONEY') ) {
		$value = round(Input::get('putmoney'));

		if ($value < 1 or empty($value) === true) {
			$_GENERAL->addError("Palun kirjutage kui palju raha te tahate panka panna.");
		}

		if ($_USER->data('data')->money < $value) {
			$_GENERAL->addError("Teil ei ole piisavalt sularaha.");
		}

		if (($value + $_USER->data('data')->money_bank) > $max_money_bank) {
			$_GENERAL->addError("Teie panka ei mahu niipalju raha.");
		}

		if (empty($_GENERAL->errors()) === true) {
			$_USER->update(array(
					'money' => $_USER->data('data')->money - $value,
					'money_bank' => $_USER->data('data')->money_bank + $value
				),$_USER->data()->id, 'users_data');

			Session::flash('bank', 'Teie raha on edukalt pangas.');
			Redirect::to('p.php?p=bank');
		}

	} elseif(Token::check(Input::get('token'), 'TAKE_LOAN') ) {
		$value = round(Input::get('takeloan'));

		if ($value < 1 or empty($value) === true) {
			$_GENERAL->addError("Palun kirjutage kui palju laenu te tahate võtta.");
		}

		if ($max_loan_money < $value) {
			$_GENERAL->addError("Te ei saa niipalju laenu võtta.");
		}

		if ($_USER->data('data')->loan > 0) {
			$_GENERAL->addError("Te olete juba laenu võtnud.");
		}

		if (empty($_GENERAL->errors()) === true) {
			$_USER->update(array(
					'money' => $_USER->data('data')->money + $value,
					'loan' => $_USER->data('data')->loan + $value
				),$_USER->data()->id, 'users_data');

			Session::flash('bank', 'Te võtsite edukalt laenu.');
			Redirect::to('p.php?p=bank');
		}

	} elseif(Token::check(Input::get('token'), 'PAY_LOAN') ) {
		$value = round(Input::get('payloan'));

		if ($value < 1 or empty($value) === true) {
			$_GENERAL->addError("Palun kirjutage kui palju laenu te tahate tagasi maksata.");
		}

		if ($_USER->data('data')->loan < $value) {
			$_GENERAL->addError("Te ei ole niipalju pangale võlgu.");
		}

		if ($_USER->data('data')->money < $value) {
			$_GENERAL->addError("Teil ei ole piisavalt sularaha.");
		}

		if (empty($_GENERAL->errors()) === true) {
			$_USER->update(array(
					'money' => $_USER->data('data')->money - $value,
					'loan' => $_USER->data('data')->loan - $value
				),$_USER->data()->id, 'users_data');

			Session::flash('bank', 'Te maksite edukalt laenu pangale tagasi.');
			Redirect::to('p.php?p=bank');
		}

	} elseif(Token::check(Input::get('token'), 'SEND_MONEY') ) {
		$value = round(Input::get('amoney'));
		$desc = Input::get('desc');
		$account_number = (int)Input::get('anum');

		if (empty($account_number) === true) {
			$_GENERAL->addError("Te ei kirjutanud kellele te raha tahate saata.");
		}

		if (empty($value) === true) {
			$_GENERAL->addError("Te ei sisestanud kui palju raha te saata tahate.");
		}

		if (empty($desc) === true) {
			$_GENERAL->addError("Te ei kirjutanud ülekandele kirjeldust.");
		}

		if (empty($_GENERAL->errors()) === true) {
			$reciver_i = new User($account_number);
			if ($reciver_i->exists()) {
				if ($account_number == $_USER->data()->id) {
					$_GENERAL->addError("Te ei saa endale raha saata.");
				}

				if ($_USER->data('data')->money < $value) {
					$_GENERAL->addError("Teil ei ole piisavalt sularaha.");
				}

				if ($value > $max_money_send) {
					$_GENERAL->addError("Te ei saa niipalju raha korraga saata.");
				}

				if ($_USER->data('data')->turns < $money_send_turns) {
					$_GENERAL->addError("Teil ei ole piisavalt käike.");
				}

				if (strlen($desc) > 255) {
					$_GENERAL->addError("Description can\'t be longer than 255 characters.");
				}

				if (empty($_GENERAL->errors()) === true) {
					$_USER->update(array(
							'money' => $_USER->data('data')->money - $value,
							'turns' => $_USER->data('data')->turns - $money_send_turns
						),$_USER->data()->id, 'users_data');

					$reciver_i->update(array(
							'money' => $reciver_i->data('data')->money + $value
						),$reciver_i->data()->id, 'users_data');

					$fields_send = array(
						'sender' => $_USER->data()->id,
						'reciver' => $account_number,
						'amount' => $value,
						'desc' => $desc);

					DB::getInstance()->insert('bank_transfers', $fields_send);

					DB::getInstance()->insert('user_logs', array(
									'user_id' => $account_number,
									'type' => 2,
									'body' => '<font color="green">Teile saadeti raha!</font><br> Kasutaja <a href=\"p.php?p=profile&user='.$_USER->data()->username.'\">'.$_USER->data()->username.'</a> saatis teile '.$_GENERAL->format_number($value).'. <br>Kirjeldus: '.$desc,
									'active' => 1
									));
					Session::flash('bank', 'Ülekanne on edukalt sooritatud.');
					Redirect::to('p.php?p=bank');
				}

			} else {
				$_GENERAL->addError("Sellise kontonumbriga kasutajat ei eksisteeri.");
			}
		}
	}
}

include("includes/overall/header.php");
?>

<div id="page">
	<div class="page-title">Pank</div>
	<p>
		<ul class="page-menu">
			<li><a href="p.php?p=bank">Pank</a></li>
			<li><a href="p.php?p=stocks">Aktsiad (<?php print($_GENERAL->format_number($_GENERAL->settings('settings_game','STOCK_PRICE')));?>)</a></li>
			<li><a href="p.php?p=vault">Seif</a></li>
		</ul>
	<?php
	if (empty($_GENERAL->errors()) === false) {
		print($_GENERAL->output_errors());
	}

	if(Session::exists('bank')) {
		$_GENERAL->addOutSuccess(Session::flash('bank'));
		print($_GENERAL->output_success());
	}
	?>
		<table>
			<tr valign="top">
				<td width="20%"><img src="css/default/images/bank.png" width="100" height="100"></td>
				<td width="80%">
					Pangal on mängus väga tähtis osa. 
					Pangas hoiustate oma sularaha, et teised inimesed ei saaks teilt seda ära varastada.<br>
					Panga limiit ja laenu protsent sõltub teie töökohast.<br>
					Mida parem on teie töökoht seda rohkem raha teile panka mahub.
				</td>
			</tr>
		</table>
	</p>
	<table>
		<tr>
			<td width="20%">Sularaha:</td>
			<td width="80%"><?php print($_GENERAL->format_number($_USER->data('data')->money));?></td>
		</tr>
		<tr>
			<td>Raha pangas:</td>
			<td><?php print($_GENERAL->format_number($_USER->data('data')->money_bank));?></td>
		</tr>
		<tr>
			<td>Panga limiit:</td>
			<td><?php print($_GENERAL->format_number($max_money_bank));?></td>
		</tr>
		<tr>
			<td>&nbsp;</td>
			<td>&nbsp;</td>
		</tr>
		<tr>
			<td>Hetkel v&otildelgu:</td>
			<td width="60%"><?php print($_GENERAL->format_number($_USER->data('data')->loan));?></td>
		</tr>
		<tr>
			<td>Saad laenata:</td>
			<td><?php print($_GENERAL->format_number($max_loan_money));?></td>
		</tr>
	</table>
</div>
<div id="page">
	<div class="page-title">Tehingud</div>

	<table>
		<tr>
			<td width="20%">Pane raha panka:</td>
			<td width="80%">
				<form action="p.php?p=bank" method="POST">
					<input type="text" name="putmoney" id="putmoney" autocomplete="off">
					<img onclick="autoFill(putmoney, <?php print($fill_putmoney);?>)" src="css/default/images/icons/autofill.png" alt="autofill" width="16" height="16">
					<input type="hidden" name="token" value="<?php echo Token::generate('PUT_MONEY'); ?>">
					<input type="submit" value="Pane">
				</form>
			</td>
		</tr>
		<tr>
			<td>Võta raha pangast:</td>
			<td>
				<form action="p.php?p=bank" method="POST">
					<input type="text" name="takemoney" id="takemoney" autocomplete="off">
					<img onclick="autoFill(takemoney, <?php print($fill_takemoney);?>)" src="css/default/images/icons/autofill.png" alt="autofill" width="16" height="16">
					<input type="hidden" name="token" value="<?php echo Token::generate('TAKE_MONEY'); ?>">
					<input type="submit" value="Võta">
				</form>
			</td>
		</tr>
		<tr>
			<td>Võta laenu:</td>
			<td>
				<form action="p.php?p=bank" method="POST">
					<input type="text" name="takeloan" id="takeloan" autocomplete="off">
					<img onclick="autoFill(takeloan, <?php print($fill_takeloan);?>)" src="css/default/images/icons/autofill.png" alt="autofill" width="16" height="16">
					<input type="hidden" name="token" value="<?php echo Token::generate('TAKE_LOAN'); ?>">
					<input type="submit" value="Laena">
				</form>
			</td>
		</tr>
		<tr>
			<td>Maksa laenu:</td>
			<td>
				<form action="p.php?p=bank" method="POST">
					<input type="text" name="payloan" id="payloan" autocomplete="off">
					<img onclick="autoFill(payloan, <?php print($fill_payloan);?>)" src="css/default/images/icons/autofill.png" alt="autofill" width="16" height="16">
					<input type="hidden" name="token" value="<?php echo Token::generate('PAY_LOAN'); ?>">
					<input type="submit" value="Maksa">
				</form>
			</td>
		</tr>
	</table>
</div>
<div id="page">
	<div class="page-title">Saada raha</div>
	<p>
		<div align="right"><a href="p.php?p=bankhistory">Panga ülekannete ajalugu</a></div>
		Teie saate korraga raha saata <?php print($_GENERAL->format_number($max_money_send));?>. Raha saatmine võtab <?php print($_GENERAL->format_number($money_send_turns));?> käiku.
		<form action="p.php?p=bank" method="POST">
			<table>
				<tr>
					<td width="20%">Teie kontonumber:</td>
					<td width="80%"><?php print($_USER->data()->id);?></td>
				</tr>
				<tr>
					<td>Saaja kontonumber:</td>
					<td><input type="text" name="anum" autocomplete="off"></td>
				</tr>
				<tr>
					<td>Summa:</td>
					<td><input type="text" name="amoney" autocomplete="off"></td>
				</tr>
				<tr>
					<td>Makse kirjeldus:</td>
					<td><input type="text" name="desc" maxlength="255" style="width:300px" autocomplete="off"></td>
				</tr>
				<tr>
					<td></td>
					<td>
						<input type="hidden" name="token" value="<?php echo Token::generate('SEND_MONEY'); ?>">
						<input type="submit" value="Saada raha">
					</td>
				</tr>
			</table>
		</form>
	</p>
</div>

<?php
include("includes/overall/footer.php");
