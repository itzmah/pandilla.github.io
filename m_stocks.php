<?php
/*
 * Name: FreeLand v2
 * Author: Marko Murumaa
 * Created: July 2015
 * Website: www.freelandplay.eu
*/

$_lir = true;
require_once("core/init.php");

	$job_i_query = DB::getInstance(1)->query("SELECT * FROM `job_list` WHERE `id` = ".$_USER->data('data')->job." ");
	$job_i = $job_i_query->first();

	$stock_price = $_GENERAL->settings('settings_game','STOCK_PRICE');
	$max_stock_calc = floor(round($_USER->data('data')->score / $job_i->stock_num) - $_USER->data('data')->stocks);
	$max_stock = ($max_stock_calc <= 0) ? 0 : $max_stock_calc;

	$max_stock_money = floor($_USER->data('data')->money / $stock_price);
	$max_stock_buy = ($max_stock_money < $max_stock) ? $max_stock_money : $max_stock;

	$fill_buy = $max_stock_buy;
	$fill_sell = $_USER->data('data')->stocks;

if(Input::exists()) {
	if(Token::check(Input::get('token'), 'BUY') ) {
		$value = round(Input::get('buy'));
		$price = $value * $stock_price;

		if ($value <= 0) {
			$_GENERAL->addError("Palun kirjutage kui palju aktsiaid te soovite osta.");
		}

		if ($price > $_USER->data('data')->money) {
			$_GENERAL->addError("Teil ei ole piisavalt sularaha.");
		}

		if ($value > $max_stock) {
			$_GENERAL->addError("Te ei saa osta niipalju aktsiad.");
		}

		if (empty($_GENERAL->errors()) === true) {
			$_USER->update(array(
					'money' => $_USER->data('data')->money - $price,
					'stocks' => $_USER->data('data')->stocks + $value
				),$_USER->data()->id, 'users_data');

			Session::flash('stocks', 'Te ostsite edukalt omale aktsiaid.');
			Redirect::to('p.php?p=stocks');
		}


	} else if(Token::check(Input::get('token'), 'SELL') ) {
		$value = round(Input::get('sell'));
		$price = $value * $stock_price;

		if ($value <= 0) {
			$_GENERAL->addError("Palun kirjutage kui palju aktsiaid te soovite müüa.");
		}

		if ($value > $_USER->data('data')->stocks) {
			$_GENERAL->addError("Teil ei ole piisavalt aktsiaid.");
		}

		if (empty($_GENERAL->errors()) === true) {
			$_USER->update(array(
					'money' => $_USER->data('data')->money + $price,
					'stocks' => $_USER->data('data')->stocks - $value
				),$_USER->data()->id, 'users_data');

			Session::flash('stocks', 'Te müüsite aktsiad edukalt maha ja saite raha '.$_GENERAL->format_number($price).'.');
			Redirect::to('p.php?p=stocks');
		}
	}
}

include("includes/overall/header.php");
?>

<div id="page">
	<div class="page-title">Aktsiad</div>
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

	if(Session::exists('stocks')) {
		$_GENERAL->addOutSuccess(Session::flash('stocks'));
		print($_GENERAL->output_success());
	}
	?>
		<table>
			<tr valign="top">
				<td width="20%"><img src="css/default/images/stocks.png" width="100" height="100"></td>
				<td width="80%">
					Aktsiad on üks hea moodus raha teenimiseks. <br>
					Aktsiaid tuleks osta siis kui hind on madal ja müüa siis kui hind on kõrge.
					Aktsia hind muutub iga 15 minuti tagant.<br>
					Teie töökohast sõltub kui palju aktsiaid te oma skoori kohta osta saate.
				</td>
			</tr>
		</table>
	</p>
	<table>
		<tr>
			<td width="20%">Praegune aktsia hind:</td>
			<td width="80%"><?php print($_GENERAL->format_number($stock_price));?></td>
		</tr>
		<tr>
			<td>Teil on aktsiaid:</td>
			<td><?php print($_GENERAL->format_number($_USER->data('data')->stocks));?></td>
		</tr>
		<tr>
			<td>Te saate osta:</td>
			<td><?php print($_GENERAL->format_number($max_stock_buy));?></td>
		</tr>
		<tr>
			<td>Osta aktsiad:</td>
			<td>
				<form action="p.php?p=stocks" method="POST">
					<input type="text" name="buy" id="buy" autocomplete="off">
					<img onclick="autoFill(buy, <?php print($fill_buy);?>)" src="css/default/images/icons/autofill.png" alt="autofill" width="16" height="16">
					<input type="hidden" name="token" value="<?php echo Token::generate('BUY'); ?>">
					<input type="submit" value="Osta">
				</form>
			</td>
		</tr>
		<tr>
			<td>Müü aktsiaid:</td>
			<td>
				<form action="p.php?p=stocks" method="POST">
					<input type="text" name="sell" id="sell" autocomplete="off">
					<img onclick="autoFill(sell, <?php print($fill_sell);?>)" src="css/default/images/icons/autofill.png" alt="autofill" width="16" height="16">
					<input type="hidden" name="token" value="<?php echo Token::generate('SELL'); ?>">
					<input type="submit" value="Müü">
				</form>
			</td>
		</tr>
	</table>
</div>

<?php
include("includes/overall/footer.php");
