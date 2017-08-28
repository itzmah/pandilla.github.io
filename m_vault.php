<?php
/*
 * Name: FreeLand v2
 * Author: Marko Murumaa
 * Created: August 2015
 * Website: www.freelandplay.eu
*/

$_lir = true;
require_once("core/init.php");

$current_vault_query = DB::getInstance(1)->query("SELECT * FROM `vault_levels` WHERE `id` = ".$_USER->data('data')->vault." ");
$current_vault = $current_vault_query->first();

$fill_putmoney = ($current_vault->max_money - $_USER->data('data')->vault_money < $_USER->data('data')->money) ? $current_vault->max_money - $_USER->data('data')->vault_money : $_USER->data('data')->money;
$fill_takemoney = $_USER->data('data')->vault_money;

if(Input::exists()) {
	if(Token::check(Input::get('token'), 'NEW_VAULT') ) {
		$vault_info_query = DB::getInstance(1)->query("SELECT * FROM `vault_levels` WHERE `id` = ".Input::get('vault')." ");
		if (!$vault_info_query->count()) {
			Redirect::to('p.php?p=vault');
		} else {
			$vault_i = $vault_info_query->first();
			if ($_USER->data('data')->education < $vault_i->education) {
				$_GENERAL->addError("Teie haridus ei ole piisavalt kõrge.");
			}

			if ($_USER->data('data')->money < $vault_i->money) {
				$_GENERAL->addError("Teil ei ole piisavalt sularaha.");
			}

			if ($_USER->data('data')->vault == $vault_i->id) {
				$_GENERAL->addError("Teil on juba see seifi level.");
			}

			if (empty($_GENERAL->errors()) === true) {
				$_USER->update(array(
						'vault' => $vault_i->id,
						'money' => $_USER->data('data')->money - $vault_i->money
					),$_USER->data()->id, 'users_data');

				Session::flash('vault', 'Te ostsite omale uue seifi.');
				Redirect::to('p.php?p=vault');
			}
		}
	} else if(Token::check(Input::get('token'), 'TAKE_MONEY') ) {
		$value = round(Input::get('value'));

		if ($value < 1 or empty($value) === true) {
			$_GENERAL->addError("Palun kirjutage kui palju raha te tahate seifist välja võtta.");
		}

		if ($_USER->data('data')->vault_money < $value) {
			$_GENERAL->addError("Teil ei ole piisavalt raha seifis.");
		}

		if (empty($_GENERAL->errors()) === true) {
			$_USER->update(array(
					'money' => $_USER->data('data')->money + $value,
					'vault_money' => $_USER->data('data')->vault_money - $value
				),$_USER->data()->id, 'users_data');

			Session::flash('vault', 'Te võtsite edukalt seifist raha välja.');
			Redirect::to('p.php?p=vault');
		}

	} elseif(Token::check(Input::get('token'), 'PUT_MONEY') ) {
		$value = round(Input::get('value'));

		if ($value < 1 or empty($value) === true) {
			$_GENERAL->addError("Palun kirjutage kui palju raha te tahate seifi panna.");
		}

		if ($_USER->data('data')->money < $value) {
			$_GENERAL->addError("Teil ei ole piisavalt sularaha.");
		}

		if (($value + $_USER->data('data')->vault_money) > $current_vault->max_money) {
			$_GENERAL->addError("Teie seifi ei mahu niipalju raha.");
		}

		if (empty($_GENERAL->errors()) === true) {
			$_USER->update(array(
					'money' => $_USER->data('data')->money - $value,
					'vault_money' => $_USER->data('data')->vault_money + $value
				),$_USER->data()->id, 'users_data');

			Session::flash('vault', 'Teie raha on edukalt seifis.');
			Redirect::to('p.php?p=vault');
		}

	}
}

include("includes/overall/header.php");
?>

<div id="page">
	<div class="page-title">Seif</div>
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

	if(Session::exists('vault')) {
		$_GENERAL->addOutSuccess(Session::flash('vault'));
		print($_GENERAL->output_success());
	}
	?>
		<table>
			<tr valign="top">
				<td width="20%"><img src="css/default/images/vault.png" width="100" height="100"></td>
				<td width="80%">
					Seif on koht kus kohas te saate hoida oma raha nagu pangas, aga seifis saab hoida suuremaid summasid.<br>
					Iga seifi leveli jaoks on vaja haridust.<br>
					Mida kõrgem on teie haridus seda parema seifi saate omale osta.
				</td>
			</tr>
		</table>
	</p>
	<?php
	if ($_USER->data('data')->vault != 0) {
		?>
	<table>
		<tr>
			<td width="20%">Teie seif:</td>
			<td width="80%"><?php print($current_vault->name);?></td>
		</tr>
		<tr>
			<td>Raha mahub seifi:</td>
			<td><?php print($_GENERAL->format_number($current_vault->max_money));?></td>
		</tr>
		<tr>
			<td>Raha on seifis:</td>
			<td><?php print($_GENERAL->format_number($_USER->data('data')->vault_money));?></td>
		</tr>
	</table>
	<table>
		<tr>
			<td width="20%">Pane raha seifi:</td>
			<td width="80%">
				<form action="p.php?p=vault" method="POST">
					<input type="text" name="value" id="putmoney" autocomplete="off">
					<img onclick="autoFill(putmoney, <?php print($fill_putmoney);?>)" src="css/default/images/icons/autofill.png" alt="autofill" width="16" height="16">
					<input type="hidden" name="token" value="<?php echo Token::generate('PUT_MONEY'); ?>">
					<input type="submit" value="Pane">
				</form>
			</td>
		</tr>
		<tr>
			<td>Võta raha seifist:</td>
			<td>
				<form action="p.php?p=vault" method="POST">
					<input type="text" name="value" id="takemoney" autocomplete="off">
					<img onclick="autoFill(takemoney, <?php print($fill_takemoney);?>)" src="css/default/images/icons/autofill.png" alt="autofill" width="16" height="16">
					<input type="hidden" name="token" value="<?php echo Token::generate('TAKE_MONEY'); ?>">
					<input type="submit" value="Võta">
				</form>
			</td>
		</tr>
	</table>
		<?php
	} else {
		$_GENERAL->addOutInfo("Teil ei ole seifi.");
		print($_GENERAL->output_info());
	}
	?>
</div>
<?php

$vault_query = DB::getInstance(1)->query("SELECT * FROM `vault_levels`");
foreach ($vault_query->results() as $vault) {
	$selected = '';
	if(Input::exists()) {
		if (Input::get('vault') == $vault->id) {
			$selected = ' selected';
		}
	} else {
		$vault_id = ($_USER->data('data')->vault == 0) ? 1 : $_USER->data('data')->vault;
		if ($vault->id == $vault_id) {
			$selected = ' selected';
		}
	}

	$output_line .= '<option'.$selected.' value="'.$vault->id.'">'.$vault->name.'</option>';
}


if(Input::exists()) {
	$vault_i_query = DB::getInstance(1)->query("SELECT * FROM `vault_levels` WHERE `id` = ".Input::get('vault')." ");
	if (!$vault_i_query->count()) {
		Redirect::to('p.php?p=vault');
	}
} else {
	$vault_id = ($_USER->data('data')->vault == 0) ? 1 : $_USER->data('data')->vault;
	$vault_i_query = DB::getInstance(1)->query("SELECT * FROM `vault_levels` WHERE `id` = ".$vault_id." ");
}
	$vault_data = $vault_i_query->first();
?>
<div id="page">
	<div class="page-title">Ostke omale seif</div>
	<p>
		Seifi leveleid ei pea ostma järjest.
		<table>
		<tr>
			<td width="20%">Seifi nimetus:</td>
			<td width="80%">
				<form action="p.php?p=vault" method="POST">
					<select name="vault" onchange="this.form.submit();">
						<?php print($output_line);?>
					</select>
				</form>
			</td>
		</tr>
		<tr>
			<td>Seifi mahub:</td>
			<td><?php print($_GENERAL->format_number($vault_data->max_money));?></td>
		</tr>
		<tr>
			<td>Haridust vaja:</td>
			<td><?php print($_GENERAL->format_number($vault_data->education));?></td>
		</tr>
		<tr>
			<td>Hind:</td>
			<td><?php print($_GENERAL->format_number($vault_data->money));?></td>
		</tr>
		<tr>
			<td></td>
			<td>
				<form action="p.php?p=vault" method="POST">
					<input type="hidden" name="vault" value="<?php print($_GENERAL->format_number($vault_data->id));?>">
					<input type="hidden" name="token" value="<?php echo Token::generate('NEW_VAULT'); ?>">
					<input type="submit" value="Osta omale uus seif">
				</form>
			</td>
		</tr>
	</table>
	</p>
</div>

<?php
include("includes/overall/footer.php");
