<?php
/*
 * Name: FreeLand v2
 * Author: Marko Murumaa
 * Created: July 2015
 * Website: www.freelandplay.eu
*/

$_lir = true;
require_once("core/init.php");

$new_resto_education = 16000;
$new_resto_money = 100000000;

$resto_bankrupt = -500000000;

if(Input::exists()) {
	if(Token::check(Input::get('token'), 'CREATE') ) {
		$resto_query = DB::getInstance()->query("SELECT * FROM `users_data_resto` WHERE `name` = ? AND `created` = 1", [Input::get('name')]);
		if (!$resto_query->count()) {
			if ($_USER->data('resto')->created == 1) {
				$_GENERAL->addError("Teil on juba restoran olemas.");
			}

			if (strlen(Input::get('name')) < 3) {
				$_GENERAL->addError("Restorani nimi peab olema vähemalt 3 sümbolit pikk.");
			}

			if (strlen(Input::get('name')) > 30) {
				$_GENERAL->addError("Restorani nimi võib olla kuni 30 sümbolit.");
			}

			if ($_USER->data('data')->education < $new_resto_education) {
				$_GENERAL->addError("Teie haridus ei ole piisavalt kõrge.");
			}

			if ($_USER->data('data')->money < $new_resto_money) {
				$_GENERAL->addError("Teil ei ole piisavalt raha");
			}

			if (empty($_GENERAL->errors()) === true) {
				$_USER->update(array(
						'money' => $_USER->data('data')->money - $new_resto_money
					),$_USER->data()->id, 'users_data');

				$_USER->update(array(
						'created' => 1,
						'name' => Input::get('name'),
						'created_time' => date("Y-m-d H:i:s"),
						'reputation' => 0,
						'money' => 0,
						'area' => 0,
						'area_total' => 20,
						'foods' => 0,
						'work_hours' => 8,
						'work_hours_time' => "2000-01-01 00:00:00",
						'waiter' => 0,
						'waiter_morale' => 0,
						'waiter_level' => 1,
						'waiter_salary' => 0,
						'waiter_salary_change' => "2000-01-01 00:00:00",
						'chef' => 0,
						'chef_morale' => 0,
						'chef_level' => 1,
						'chef_salary' => 0,
						'chef_salary_change' => "2000-01-01 00:00:00",
						'kitchen_level' => 1,
						'furniture_level' => 1,
						'income_today' => 0,
						'outcome_today' => 0,
						'food_make_today' => 0,
						'food_make_limit' => 0,
						'food_sell_today' => 0,
						'food_sell_limit' => 0,
						'food_1' => 0,
						'food_1_price' => 4000,
						'food_1_price_edit' => "2000-01-01 00:00:00",
						'food_1_orders' => 0,
						'food_1_start' => "2000-01-01 00:00:00",
						'food_1_end' => "2000-01-01 00:00:00",
						'food_1_amount' => 0,
						'food_2' => 0,
						'food_2_price' => 5600,
						'food_2_price_edit' => "2000-01-01 00:00:00",
						'food_2_orders' => 0,
						'food_2_start' => "2000-01-01 00:00:00",
						'food_2_end' => "2000-01-01 00:00:00",
						'food_2_amount' => 0,
						'food_3' => 0,
						'food_3_price' => 4400,
						'food_3_price_edit' => "2000-01-01 00:00:00",
						'food_3_orders' => 0,
						'food_3_start' => "2000-01-01 00:00:00",
						'food_3_end' => "2000-01-01 00:00:00",
						'food_3_amount' => 0,
						'food_4' => 0,
						'food_4_price' => 9200,
						'food_4_price_edit' => "2000-01-01 00:00:00",
						'food_4_orders' => 0,
						'food_4_start' => "2000-01-01 00:00:00",
						'food_4_end' => "2000-01-01 00:00:00",
						'food_4_amount' => 0,
						'food_5' => 0,
						'food_5_price' => 7200,
						'food_5_price_edit' => "2000-01-01 00:00:00",
						'food_5_orders' => 0,
						'food_5_start' => "2000-01-01 00:00:00",
						'food_5_end' => "2000-01-01 00:00:00",
						'food_5_amount' => 0
					),$_USER->data()->id, 'users_data_resto');

				Session::flash('restaurant', 'Teie restoran on edukalt loodud.');
				Redirect::to('p.php?p=restaurant');
			}
		} else {
			$_GENERAL->addError("See restorani nimi on juba kasutusel.");
		}
	}
}


include("includes/overall/header.php");

if ($_USER->data('resto')->created == 1) {

	$resto_menu = '
	<ul class="page-menu">
		<li><a href="p.php?p=restaurant">Restoran</a></li>
		<li><a href="p.php?p=restaurant&page=sales">Teenindussaal</a></li>
		<li><a href="p.php?p=restaurant&page=kitchen">Köök</a></li>
		<li><a href="p.php?p=restaurant&page=personal">Personal</a></li>
		<li><a href="p.php?p=restaurant&page=furniture">Sisustus</a></li>
		<li><a href="p.php?p=restaurant&page=transactions">Tehingud</a></li>
		<li><a href="p.php?p=restaurant&page=restaurants">Restoranide edetabel</a></li>
	</ul>
	';

	$chef_recommended_salary = 1000 * $_USER->data('resto')->chef_level * $_USER->data('resto')->kitchen_level;
	$chef_morale_per = round($_USER->data('resto')->chef_salary * 100 / $chef_recommended_salary);
	$chef_morale = ($chef_morale_per > 100) ? 100 : $chef_morale_per;

	$waiter_recommended_salary = 1000 * $_USER->data('resto')->waiter_level * $_USER->data('resto')->furniture_level;
	$waiter_morale_per = round($_USER->data('resto')->waiter_salary * 100 / $waiter_recommended_salary);
	$waiter_morale = ($waiter_morale_per > 100) ? 100 : $waiter_morale_per;

	$_USER->update(array(
			'chef_morale' => $chef_morale,
			'waiter_morale' => $waiter_morale
		),$_USER->data()->id, 'users_data_resto');

	if (Input::get('page') == 'sales') {
		include("m_resto_sales.php");
	} else if (Input::get('page') == 'kitchen') {
		include("m_resto_kitchen.php");
	} else if (Input::get('page') == 'personal') {
		include("m_resto_personal.php");
	} else if (Input::get('page') == 'furniture') {
		include("m_resto_furniture.php");
	} else if (Input::get('page') == 'transactions') {
		include("m_resto_transactions.php");
	} else if (Input::get('page') == 'restaurants') {
		include("m_resto_restaurants.php");
	} else {
		$profit_calc = $_USER->data('resto')->income_today - $_USER->data('resto')->outcome_today;
		$today_profit = ($profit_calc < 0) ? '<font color="red">'.$_GENERAL->format_number($profit_calc).'</font>' : '<font color="green">+'.$_GENERAL->format_number($profit_calc).'</font>';
		$resto_money = ($_USER->data('resto')->money < 0) ? '<font color="red">'.$_GENERAL->format_number($_USER->data('resto')->money).'</font>' : $_GENERAL->format_number($_USER->data('resto')->money);
		?>
	<div id="page">
		<div class="page-title">Restoran</div>
		<p>
		<?php 
		print($resto_menu);

		if (empty($_GENERAL->errors()) === false) {
			print($_GENERAL->output_errors());
		}

		if(Session::exists('restaurant')) {
			$_GENERAL->addOutSuccess(Session::flash('restaurant'));
			print($_GENERAL->output_success());
		}
		?>
			<table>
				<tr valign="top">
					<td width="20%"><img src="css/default/images/restaurant.png" width="100" height="100"></td>
					<td width="80%">
						Restoran on koht kus kohas te palgate omale töötajaid, et nad saaksid toite valmistada ja müüa.<br>
						Mida paremad on teie restorani tasemed seda rohkem teie töötajad teile raha toovad.<br>
						<br>
						Kui restoranil on raha vähem kui <b><?php print($_GENERAL->format_number($resto_bankrupt));?></b> siis kuulutatakse välja pankrot.<br></br>
						Töötajate palgad makstakse igapäev kell 21:00. Sellel hetkel peab teie restoranil olema raha, et palka maksta.
					</td>
				</tr>
			</table>
			<table>
				<tr valign="top">
					<td width="50%">
						<table>
							<tr>
								<th colspan="2">Üldised restorani andmed</th>
							</tr>
							<tr>
								<td width="40%">Restorani nimi:</td>
								<td width="60%"><?php print($_USER->data('resto')->name);?></td>
							</tr>
							<tr>
								<td>Restoran loodud:</td>
								<td><?php print($_USER->data('resto')->created_time);?></td>
							</tr>
							<tr>
								<td>Reputatsioon:</td>
								<td><?php print($_GENERAL->format_number($_USER->data('resto')->reputation));?></td>
							</tr>
							<tr>
								<td>Raha:</td>
								<td><?php print($resto_money);?></td>
							</tr>
							<tr>
								<td>Pindala kokku:</td>
								<td><?php print($_GENERAL->format_number($_USER->data('resto')->area_total));?> m<sup>2</sup></td>
							</tr>
							<tr>
								<td>Vaba pindala:</td>
								<td><?php print($_GENERAL->format_number($_USER->data('resto')->area));?> m<sup>2</sup></td>
							</tr>
							<tr>
								<th colspan="2">Toidud</th>
							</tr>
							<tr>
								<td>Toiduaineid:</td>
								<td><?php print($_GENERAL->format_number($_USER->data('resto')->foods));?></td>
							</tr>
							<tr>
								<td>Teed:</td>
								<td><?php print($_GENERAL->format_number($_USER->data('resto')->food_1));?></td>
							</tr>
							<tr>
								<td>Kohvi:</td>
								<td><?php print($_GENERAL->format_number($_USER->data('resto')->food_2));?></td>
							</tr>
							<tr>
								<td>Jäätist:</td>
								<td><?php print($_GENERAL->format_number($_USER->data('resto')->food_3));?></td>
							</tr>
							<tr>
								<td>Seapraade:</td>
								<td><?php print($_GENERAL->format_number($_USER->data('resto')->food_4));?></td>
							</tr>
							<tr>
								<td>Kartulipraade:</td>
								<td><?php print($_GENERAL->format_number($_USER->data('resto')->food_5));?></td>
							</tr>
						</table>
					</td>
					<td width="50%">
						<table>
							<tr>
								<th colspan="2">Personal ja tehnika</th>
							</tr>
							<tr>
								<td width="40%">Kokkasid:</td>
								<td width="60%"><?php print($_GENERAL->format_number($_USER->data('resto')->chef));?></td>
							</tr>
							<tr>
								<td>Kokkade tase:</td>
								<td><?php print($_GENERAL->format_number($_USER->data('resto')->chef_level));?></td>
							</tr>
							<tr>
								<td>Teenindajaid:</td>
								<td><?php print($_GENERAL->format_number($_USER->data('resto')->waiter));?></td>
							</tr>
							<tr>
								<td>Teenindajate tase:</td>
								<td><?php print($_GENERAL->format_number($_USER->data('resto')->waiter_level));?></td>
							</tr>
							<tr>
								<td>Köögi tehnika level:</td>
								<td><?php print($_GENERAL->format_number($_USER->data('resto')->kitchen_level));?></td>
							</tr>
							<tr>
								<td>Mööbli level:</td>
								<td><?php print($_GENERAL->format_number($_USER->data('resto')->furniture_level));?></td>
							</tr>
							<tr>
								<th colspan="2">Tulud / Kulud</th>
							</tr>
							<tr>
								<td>Tänased tulud:</td>
								<td><font color="green">+<?php print($_GENERAL->format_number($_USER->data('resto')->income_today));?></font></td>
							</tr>
							<tr>
								<td>Tänased kulud:</td>
								<td><font color="red">-<?php print($_GENERAL->format_number($_USER->data('resto')->outcome_today));?></font></td>
							</tr>
							<tr>
								<td>Tänane kasum:</td>
								<td><?php print($today_profit);?></td>
							</tr>
							<tr>
								<td>Täna toite müüdud:</td>
								<td><?php print($_GENERAL->format_number($_USER->data('resto')->food_sell_today));?></td>
							</tr>
							<tr>
								<td>Täna toite valmistatud:</td>
								<td><?php print($_GENERAL->format_number($_USER->data('resto')->food_make_today));?></td>
							</tr>
						</table>
					</td>
				</tr>
			</table>
		</p>
	</div>
		<?php
	}

} else {
?>

<div id="page">
	<div class="page-title">Restoran</div>
	<p>
	<?php
	if (empty($_GENERAL->errors()) === false) {
		print($_GENERAL->output_errors());
	}

	if(Session::exists('restaurant')) {
		$_GENERAL->addOutSuccess(Session::flash('restaurant'));
		print($_GENERAL->output_success());
	}
	?>
		<table>
			<tr valign="top">
				<td width="20%"><img src="css/default/images/restaurant.png" width="100" height="100"></td>
				<td width="80%">
					Restoran on koht kus kohas inimesed käivad õhtuti söömas.<br>
					Sul on ka võimalus omada restorani ja teenida sellega suuri summasid.<br>
					Restorani loomiseks sul vaja 16 000 haridust, 100 miljonit ja sobivat nime.<br><br>
					Restorani nimi võib olla kuni 30 sümbolit pikk.
				</td>
			</tr>
		</table>
	</p>
	<form action="p.php?p=restaurant" method="POST">
		<table>
			<tr>
				<td width="15%">Restorani nimi:</td>
				<td width="85%">
					<input type="text" name="name" autocomplete="off">
					<input type="hidden" name="token" value="<?php echo Token::generate('CREATE'); ?>">
					<input type="submit" value="Loo omale restoran">
				</td>
			</tr>
		</table>
	</form>
</div>
<?php
}
include("includes/overall/footer.php");
