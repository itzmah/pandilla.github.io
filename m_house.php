<?php
/*
 * Name: FreeLand v2
 * Author: Marko Murumaa
 * Created: July 2015
 * Website: www.freelandplay.eu
*/

$_lir = true;
require_once("core/init.php");

$house_land_money = $_GENERAL->settings('settings_game','HOUSE_LAND_MONEY');
$house_land_turns = $_GENERAL->settings('settings_game','HOUSE_LAND_TURNS');

$house_weed_seed = $_GENERAL->settings('settings_game','HOUSE_WEED_SEED');
$house_weed_turns = $_GENERAL->settings('settings_game','HOUSE_WEED_TURNS');

$house_food_turns = $_GENERAL->settings('settings_game','HOUSE_FOOD_TURNS');
$house_food_foods = $_GENERAL->settings('settings_game','HOUSE_FOOD_FOODS');
$house_grow_limit = $_GENERAL->settings('settings_game','HOUSE_FOOD_LIMIT');


	$cur_house_lvl_query = DB::getInstance()->query("SELECT * FROM `house_levels` WHERE `id` = " . $_USER->data('house')->house_level);
	$cur_house_data = $cur_house_lvl_query->first();
	$house_items = json_decode($_USER->data('house')->items, true);

if(Input::exists()) {
	if(Token::check(Input::get('token'), 'UPGRADE_HOUSE') ) {
		$house_lvl = (int)Input::get('house');
		$house_info_query = DB::getInstance()->query("SELECT * FROM `house_levels` WHERE `id` = " . $house_lvl);

		if (!$house_info_query->count()) {
			Redirect::to('p.php?p=house');
		} else {
			$house_info = $house_info_query->first();
			if ($_USER->data('data')->toetaja == 1) {
				$house_money = $_GENERAL->discount($house_info->money, 15);
			} else {
				$house_money = $house_info->money;
			}

			if ($_USER->data('data')->money < $house_money) {
				$_GENERAL->addError("Teil ei ole piisavalt sularaha.");
			}

			if ($_USER->data('house')->land < $house_info->land) {
				$_GENERAL->addError("Teil ei ole piisavalt vaba maad.");
			}

			if ($_USER->data('house')->house_level >= $house_info->id) {
				$_GENERAL->addError("Teil on juba see elamu.");
			}

			if (($_USER->data('house')->house_level + 1) < $house_info->id ) {
				$_GENERAL->addError("Elamut tuleb täiustada järjest.");
			}

			if (empty($_GENERAL->errors()) === true) {
				$_USER->update(array(
						'money' => $_USER->data('data')->money - $house_money
					),$_USER->data()->id, 'users_data');

				$_USER->update(array(
						'land' => $_USER->data('house')->land - $house_info->land,
						'house_level' => $house_info->id
					),$_USER->data()->id, 'users_data_house');

				Session::flash('house', 'Te täiustasite oma elamut.');
				Redirect::to('p.php?p=house');
			}
		}
	} else if(Token::check(Input::get('token'), 'BUY_LAND') ) {
		$value = round(Input::get('value'));
		$price = $value * $house_land_money;
		$turns = $value * $house_land_turns;

		if ($value < 1 or empty($value) === true) {
			$_GENERAL->addError("Palun sisestage kui palju te soovite maad osta.");
		}

		if ($_USER->data('data')->money < $price) {
			$_GENERAL->addError("Teil ei ole piisavalt sularaha.");
		}

		if ($_USER->data('data')->turns < $turns) {
			$_GENERAL->addError("Teil ei ole piisavalt käike.");
		}

		if (empty($_GENERAL->errors()) === true) {
			$_USER->update(array(
					'money' => $_USER->data('data')->money - $price,
					'turns' => $_USER->data('data')->turns - $turns
				),$_USER->data()->id, 'users_data');

			$_USER->update(array(
					'land' => $_USER->data('house')->land + $value
				),$_USER->data()->id, 'users_data_house');

			Session::flash('house', 'Te ostsite omale maad.');
			Redirect::to('p.php?p=house&page=land');
		}
	} else if(Token::check(Input::get('token'), 'GROW_WEED') ) {
		$value = round(Input::get('value'));
		$seed = $value * $house_weed_seed;
		$turns = $house_weed_turns;

		if ($value < 1 or empty($value) === true) {
			$_GENERAL->addError("Palun sisestage kui palju te soovite kanepit kasvatada.");
		}

		if ($_USER->data('house')->seed < $seed) {
			$_GENERAL->addError("Teil ei ole piisavalt seemneid.");
		}

		if ($_USER->data('data')->turns < $turns) {
			$_GENERAL->addError("Teil ei ole piisavalt käike.");
		}

		if ($cur_house_data->weed_limit < $value) {
			$_GENERAL->addError("Te ei saa niipalju kanepit korraga kasvatada.");
		}

		if (empty($_GENERAL->errors()) === true) {
			$_USER->update(array(
					'turns' => $_USER->data('data')->turns - $turns
				),$_USER->data()->id, 'users_data');

			$_USER->update(array(
					'seed' => $_USER->data('house')->seed - $seed,
					'weed' => $_USER->data('house')->weed + $value
				),$_USER->data()->id, 'users_data_house');

			Session::flash('house', 'Te kasvatasite kanepit.');
			Redirect::to('p.php?p=house&page=weed');
		}
	} else if(Token::check(Input::get('token'), 'BUY_INTERIOR') ) {
		$item = (int)Input::get('item');
		$value = round(Input::get('value'));
		$item_query = DB::getInstance()->query("SELECT * FROM `house_interior` WHERE `id` = " . $item);

		if (empty($item) === true or !$item_query->count()) {
			$_GENERAL->addError("Palun valige millist maja eset te soovite osta.");
		} else {
			$item_data = $item_query->first();
			$price = $value * $item_data->money;
			$item_max = ($item_data->limit * $_USER->data('house')->house_level) - $house_items['item_'.$item_data->id];

			if ($value < 1 or empty($value) === true) {
				$_GENERAL->addError("Palun sisestage kui palju te soovite eset osta.");
			}

			if ($_USER->data('data')->money < $price) {
				$_GENERAL->addError("Teil ei ole piisavalt raha.");
			}

			if ($_USER->data('data')->education < $item_data->education) {
				$_GENERAL->addError("Teie haridus ei ole piisavalt kõrge.");
			}

			if ($item_max < $value) {
				$_GENERAL->addError("Te ei saa seda eset niipalju osta.");
			}

			if (empty($_GENERAL->errors()) === true) {
				$_USER->update(array(
						'money' => $_USER->data('data')->money - $price
					),$_USER->data()->id, 'users_data');

				$new_items .= '{';
				$new_items_query = DB::getInstance()->query("SELECT * FROM `house_interior`");
				$items_count = $new_items_query->count();
				foreach ($new_items_query->results() as $it) {
					$i++;
					$comma = ($i == $items_count) ? '' : ', ';
					if ($item == $it->id) {
						$items_value = $house_items['item_'.$it->id] + $value;
					} else {
						if (empty($house_items['item_'.$it->id]) === true) {
							$items_value = 0;
						} else {
							$items_value = $house_items['item_'.$it->id];
						}
					}
					$new_items .= '"item_'.$it->id.'":'.$items_value.$comma;
				}
				$new_items .= '}';

				$_USER->update(array(
						'items' => $new_items
					),$_USER->data()->id, 'users_data_house');

				Session::flash('house', 'Te ostsite oma majale esemeid.');
				Redirect::to('p.php?p=house&page=interior');
			}
		}
	} else if(Token::check(Input::get('token'), 'GROW_FOOD') ) {
		$value = round(Input::get('value'));
		$turns = $house_food_turns;
		$food_grow_limit = $cur_house_data->greenhouse_land * $house_grow_limit;

		if ($value < 1 or empty($value) === true) {
			$_GENERAL->addError("Palun sisestage kui palju te soovite mitu taime te kasvama panete.");
		}

		if ($_USER->data('house')->seed < $value) {
			$_GENERAL->addError("Teil ei ole piisavalt seemneid.");
		}

		if ($_USER->data('data')->turns < $turns) {
			$_GENERAL->addError("Teil ei ole piisavalt käike.");
		}

		if ($food_grow_limit < $value) {
			$_GENERAL->addError("Te ei saa niipalju taimi kasvama panna.");
		}

		if (empty($_GENERAL->errors()) === true) {
			for ($i=0; $i < $value; $i++) { 
				$foods_get += mt_rand(3,9);
			}

			$_USER->update(array(
					'turns' => $_USER->data('data')->turns - $turns
				),$_USER->data()->id, 'users_data');

			$_USER->update(array(
					'seed' => $_USER->data('house')->seed - $value,
					'foods' => $_USER->data('house')->foods + $foods_get
				),$_USER->data()->id, 'users_data_house');

			Session::flash('house', 'Te panite taimed kasvama ja saite neilt '.$_GENERAL->format_number($foods_get).' toiduainet.');
			Redirect::to('p.php?p=house&page=greenhouse');
		}
	} else if(Token::check(Input::get('token'), 'MAKE_FOOD') ) {
		$value = round(Input::get('value'));
		$turns = $house_food_turns;
		$foods = $house_food_foods * $value;

		if ($value < 1 or empty($value) === true) {
			$_GENERAL->addError("Palun sisestage kui palju toitu te valmistate.");
		}

		if ($_USER->data('house')->foods < $foods) {
			$_GENERAL->addError("Teil ei ole piisavalt toiduaineid.");
		}

		if ($_USER->data('data')->turns < $turns) {
			$_GENERAL->addError("Teil ei ole piisavalt käike.");
		}

		if (empty($_GENERAL->errors()) === true) {
			$_USER->update(array(
					'turns' => $_USER->data('data')->turns - $turns,
					'food' => $_USER->data('data')->food + $value
				),$_USER->data()->id, 'users_data');

			$_USER->update(array(
					'foods' => $_USER->data('house')->foods - $foods
				),$_USER->data()->id, 'users_data_house');

			Session::flash('house', 'Te valmistasite toitu.');
			Redirect::to('p.php?p=house&page=food');
		}
	}
}

include("includes/overall/header.php");

if (Input::get('page') == 'interior') {

	$interior_query = DB::getInstance()->query("SELECT * FROM `house_interior`");
	foreach ($interior_query->results() as $interior) {
		$max_item = $interior->limit * $_USER->data('house')->house_level;
		$score_total = $interior->score * $_USER->data('house')->house_level * $house_items['item_'.$interior->id];
		$output_line .= '
			<tr>
				<td>'.$interior->name.'</td>
				<td align="center">'.$_GENERAL->format_number($house_items['item_'.$interior->id]).' / '.$_GENERAL->format_number($max_item).'</td>
				<td align="center">'.$_GENERAL->format_number($interior->money).'</td>
				<td align="center">'.$_GENERAL->format_number($interior->education).'</td>
				<td align="center"><font color="green">+ '.$_GENERAL->format_number($score_total).'</font></td>
				<td align="center"><input type="radio" name="item" value="'.$interior->id.'"></td>
			</tr>
		';
	}

?>
	<div id="page">
		<div class="page-title">Maja esemed</div>
		<p>
		<?php
		if (empty($_GENERAL->errors()) === false) {
			print($_GENERAL->output_errors());
		}

		if(Session::exists('house')) {
			$_GENERAL->addOutSuccess(Session::flash('house'));
			print($_GENERAL->output_success());
		}
		?>
			<table>
				<tr valign="top">
					<td width="20%"><img src="css/default/images/interior.png" width="100" height="100"></td>
					<td width="80%">
						Elamu esemed annavad teile skoori juurde.<br>
						Mida suurem on teie elamu seda rohkem esemeid saate osta.
					</td>
				</tr>
			</table>
		</p>
		<form action="p.php?p=house&page=interior" method="POST">
			<table>
				<tr>
					<th width="25%">Nimi</th>
					<th width="20%">Teil on</th>
					<th width="15%">Hind</th>
					<th width="15%">Haridust vaja</th>
					<th width="20%">Skoori saad</th>
					<th width="5%">#</th>
				</tr>
				<?php print($output_line);?>
			</table>
			<table>
				<tr>
					<td width="90%" align="right"><input type="text" name="value" placeholder="Kogus" autocomplete="off"></td>
					<td width="10%" align="right">
						<input type="hidden" name="token" value="<?php echo Token::generate('BUY_INTERIOR'); ?>">
						<input type="submit" value="Osta">
					</td>
				</tr>
			</table>
		</form>
	</div>
<?php

} else if (Input::get('page') == 'land') {

?>
	<div id="page">
		<div class="page-title">Maakler - Osta maad</div>
		<p>
		<?php
		if (empty($_GENERAL->errors()) === false) {
			print($_GENERAL->output_errors());
		}

		if(Session::exists('house')) {
			$_GENERAL->addOutSuccess(Session::flash('house'));
			print($_GENERAL->output_success());
		}
		?>
			<table>
				<tr valign="top">
					<td width="20%"><img src="css/default/images/land.png" width="100" height="100"></td>
					<td width="80%">
						Maa ostmine on kasulik asi kui te tahate uuendada oma elamut.<br>
						1 m<sup>2</sup> maad maksab <?php print($_GENERAL->format_number($house_land_money));?> ja käike on vaja <?php print($_GENERAL->format_number($house_land_turns));?>.
					</td>
				</tr>
			</table>
		</p>
		<form action="p.php?p=house&page=land" method="POST">
			<table>
				<tr>
					<td width="20%">Teil on vaba maad: </td>
					<td width="80%"><?php print($_GENERAL->format_number($_USER->data('house')->land));?> m<sup>2</sup></td>
				</tr>
				<tr>
					<td>Palju maad ostad</td>
					<td>
						<input type="text" name="value" autocomplete="off">
						<input type="hidden" name="token" value="<?php echo Token::generate('BUY_LAND'); ?>">
						<input type="submit" value="Osta maad">
					</td>
				</tr>
			</table>
		</form>
	</div>
<?php

} else if (Input::get('page') == 'weed') {

	if ($_USER->data('data')->turns < $house_weed_turns) {
		$max_weed_grow = 0;
	} else {
		$calc1 = $_USER->data('house')->seed / $house_weed_seed;
		if ($calc1 >= $cur_house_data->weed_limit) {
			$max_weed_grow = $cur_house_data->weed_limit;
		} else if ($calc1 < $cur_house_data->weed_limit) {
			$max_weed_grow = floor($calc1);
		}
	}

?>
	<div id="page">
		<div class="page-title">Kuur - Kasvata kanepit</div>
		<p>
		<?php
		if (empty($_GENERAL->errors()) === false) {
			print($_GENERAL->output_errors());
		}

		if(Session::exists('house')) {
			$_GENERAL->addOutSuccess(Session::flash('house'));
			print($_GENERAL->output_success());
		}
		?>
			<table>
				<tr valign="top">
					<td width="20%"><img src="css/default/images/weed.png" width="100" height="100"></td>
					<td width="80%">
						Kanepi kasvatusega teenib kiiresti suuri summasid.<br>
						Üks valmis kanep vajab <?php print($_GENERAL->format_number($house_weed_seed));?> seemet ja kasvatamine võtab <?php print($_GENERAL->format_number($house_weed_turns));?> käiku.
					</td>
				</tr>
			</table>
		</p>
		<table>
			<tr>
				<td width="25%">Seemneid:</td>
				<td width="75%"><?php print($_GENERAL->format_number($_USER->data('house')->seed));?></td>
			</tr>
			<tr>
				<td>Valmis kanepit:</td>
				<td><?php print($_GENERAL->format_number($_USER->data('house')->weed));?></td>
			</tr>
			<tr>
				<td>Kanepi valmistamise limiit:</td>
				<td><?php print($_GENERAL->format_number($cur_house_data->weed_limit));?></td>
			</tr>
			<tr>
				<td>Palju kanepit kasvatad:</td>
				<td>
					<form action="p.php?p=house&page=weed" method="POST">
						<input type="text" name="value" value="<?php print($max_weed_grow);?>" autocomplete="off">
						<input type="hidden" name="token" value="<?php echo Token::generate('GROW_WEED'); ?>">
						<input type="submit" value="Kasvata kanepit">
					</form>
				</td>
			</tr>
		</table>
	</div>
<?php
} else if (Input::get('page') == 'food') {

	if ($_USER->data('data')->turns < $house_food_turns) {
		$max_food_make = 0;
	} else {
		$calc1 = $_USER->data('house')->foods / $house_food_foods;
		$max_food_make = floor($calc1);
	}

?>
	<div id="page">
		<div class="page-title">Köök - Valmista toitu</div>
		<p>
		<?php
		if (empty($_GENERAL->errors()) === false) {
			print($_GENERAL->output_errors());
		}

		if(Session::exists('house')) {
			$_GENERAL->addOutSuccess(Session::flash('house'));
			print($_GENERAL->output_success());
		}
		?>
			<table>
				<tr valign="top">
					<td width="20%"><img src="css/default/images/food.png" width="100" height="100"></td>
					<td width="80%">
						Toidu valmistamisega saate omale valmistada toitu, et seda ei peaks turult ostma.<br>
						Toidu valmistamiseks on teil vaja toiduaineid mida saate kasvuhoonest kasvatades.<br>
						Ühe toidu valmistamiseks on vaja <?php print($_GENERAL->format_number($house_food_foods));?> toiduainet.<br>
						Toidu valmistamiseks läheb vaja <?php print($_GENERAL->format_number($house_food_turns));?> käiku sõltumata kogusest.
					</td>
				</tr>
			</table>
		</p>
		<table>
			<tr>
				<td width="25%">Teil on toiduaineid:</td>
				<td width="75%"><?php print($_GENERAL->format_number($_USER->data('house')->foods));?></td>
			</tr>
			<tr>
				<td>Mitu toitu valmistad:</td>
				<td>
					<form action="p.php?p=house&page=food" method="POST">
						<input type="text" name="value" value="<?php print($max_food_make);?>" autocomplete="off">
						<input type="hidden" name="token" value="<?php echo Token::generate('MAKE_FOOD'); ?>">
						<input type="submit" value="Valmista toitu">
					</form>
				</td>
			</tr>
		</table>
	</div>
	</div>
<?php
} else if (Input::get('page') == 'greenhouse') {

	if ($_USER->data('data')->turns < $house_food_turns) {
		$max_food_grow = 0;
	} else {
		if ($_USER->data('house')->seed > ($cur_house_data->greenhouse_land * $house_grow_limit) ) {
			$max_food_grow = $cur_house_data->greenhouse_land * $house_grow_limit;
		} else {
			$max_food_grow = $_USER->data('house')->seed;
		}
	}
?>
	<div id="page">
		<div class="page-title">Kasvuhoone - Kasvata toiduaineid</div>
		<p>
		<?php
		if (empty($_GENERAL->errors()) === false) {
			print($_GENERAL->output_errors());
		}

		if(Session::exists('house')) {
			$_GENERAL->addOutSuccess(Session::flash('house'));
			print($_GENERAL->output_success());
		}
		?>
			<table>
				<tr valign="top">
					<td width="20%"><img src="css/default/images/greenhouse.png" width="100" height="100"></td>
					<td width="80%">
						Siin on teil võimalik saada omale toiduaineid, et te saaks valmistada toitu.<br>
						Iga elamu tasemega suureneb teil kasvuhoone pindala, et saaksite kasvatada rohkem taimi.
						Ühele kasvuhoone ruutmeetrile mahub <?php print($_GENERAL->format_number($house_grow_limit));?> taime.<br>
						Ühest seemnest saate ühe taime ja taimedest saate toiduaineid.<br>
						Taimede kasvatamine võtab sõltumata kogusest <?php print($_GENERAL->format_number($house_food_turns));?> käiku.
					</td>
				</tr>
			</table>
		</p>
		<table>
			<tr>
				<td width="25%">Teil on seemneid:</td>
				<td width="75%"><?php print($_GENERAL->format_number($_USER->data('house')->seed));?></td>
			</tr>
			<tr>
				<td>Taimi saate kasvatada:</td>
				<td><?php print($_GENERAL->format_number($cur_house_data->greenhouse_land * $house_grow_limit));?></td>
			</tr>
			<tr>
				<td>Mitu taime kasvatad:</td>
				<td>
					<form action="p.php?p=house&page=greenhouse" method="POST">
						<input type="text" name="value" value="<?php print($max_food_grow);?>" autocomplete="off">
						<input type="hidden" name="token" value="<?php echo Token::generate('GROW_FOOD'); ?>">
						<input type="submit" value="Kasvata taimi">
					</form>
				</td>
			</tr>
		</table>
	</div>
<?php

} else {

	$house_level_query = DB::getInstance()->query("SELECT * FROM `house_levels` ORDER BY `id` ASC");
	foreach ($house_level_query->results() as $h_lvl) {
		$selected = '';
		if(Input::exists()) {
			if (Input::get('house') == $h_lvl->id) {
				$selected = ' selected';
			}
		} else {
			if ($h_lvl->id == $_USER->data('house')->house_level) {
				$selected = ' selected';
			}
		}

		$output_line .= '<option'.$selected.' value="'.$h_lvl->id.'">'.$h_lvl->name.'</option>';
	}

	if(Input::exists()) {
		$house_lvl_i_query = DB::getInstance()->query("SELECT * FROM `house_levels` WHERE `id` = " . (int)Input::get('house'));
		if (!$house_lvl_i_query->count()) {
			Redirect::to('p.php?p=house');
		}
	} else {
		$house_lvl_i_query = DB::getInstance()->query("SELECT * FROM `house_levels` WHERE `id` = " . $_USER->data('house')->house_level);
	}

	$house_lvl_data = $house_lvl_i_query->first();

	if ($_USER->data('data')->toetaja == 1) {
		$house_price = $_GENERAL->discount($house_lvl_data->money, 15);
	} else {
		$house_price = $house_lvl_data->money;
	}
?>
	<div id="page">
		<div class="page-title">Elamu</div>
		<p>
		<?php
		if (empty($_GENERAL->errors()) === false) {
			print($_GENERAL->output_errors());
		}

		if(Session::exists('house')) {
			$_GENERAL->addOutSuccess(Session::flash('house'));
			print($_GENERAL->output_success());
		}
		?>
			<table>
				<tr valign="top">
					<td width="20%"><img src="css/default/images/house.png" width="100" height="100"></td>
					<td width="80%">
						Elamu on sinu kodu. Siin saad sa kasvatada kanepit, osta majale uusi esemeid.<br>
						Elamust sõltub kui palju sa esemeid osta saad ja kui palju kanepit saad sa korraga teha.
					</td>
				</tr>
			</table>
		</p>
		<table>
			<tr valign="top">
				<td width="50%">
					<ul>
						<li><b>Teie elamu andmed</b></li>
					</ul>
					<table>
						<tr>
							<td width="40%">Vaba maad:</td>
							<td width="60%"><?php print($_GENERAL->format_number($_USER->data('house')->land));?> m<sup>2</sup></td>
						</tr>
						<tr>
							<td>Kanepi limiit</td>
							<td><?php print($_GENERAL->format_number($cur_house_data->weed_limit));?></td>
						</tr>
						<tr>
							<td>Kasvuhoone pindala</td>
							<td><?php print($_GENERAL->format_number($cur_house_data->greenhouse_land));?> m<sup>2</sup></td>
						</tr>
						<tr>
							<td>Seemneid:</td>
							<td><?php print($_GENERAL->format_number($_USER->data('house')->seed));?></td>
						</tr>
						<tr>
							<td>Toiduaineid:</td>
							<td><?php print($_GENERAL->format_number($_USER->data('house')->foods));?></td>
						</tr>
						<tr>
							<td>&nbsp;</td>
							<td>&nbsp;</td>
						</tr>
					</table>
					<ul>
						<li><b>Valige kuhu te soovite edasi minna</b></li>
					</ul>
					<table>
						<tr>
							<td width="30%"><a href="p.php?p=house&page=interior">Maja esemed</a></td>
							<td width="70%"><i>Osta majale esemeid</i></td>
						</tr>
						<tr>
							<td><a href="p.php?p=house&page=land">Maakler</a></td>
							<td><i>Osta omale maad</i></td>
						</tr>
						<tr>
							<td><a href="p.php?p=house&page=weed">Kuur</a></td>
							<td><i>Kasvata kanepit</i></td>
						</tr>
						<tr>
							<td><a href="p.php?p=house&page=greenhouse">Kasvuhoone</a></td>
							<td><i>Kasvata omale toiduaineid</i></td>
						</tr>
						<tr>
							<td><a href="p.php?p=house&page=food">Köök</a></td>
							<td><i>Valmista omale toitu</i></td>
						</tr>
					</table>
				</td>
				<td width="50%">
					<form action="p.php?p=house" method="POST">
						<select name="house" onchange="this.form.submit();">
							<?php print($output_line);?>
						</select>
					</form>
					<form action="p.php?p=house" method="POST">
						<table>
							<tr>
								<td width="45%">Kanepi limiit:</td>
								<td width="55%"><?php print($_GENERAL->format_number($house_lvl_data->weed_limit));?></td>
							</tr>
							<tr>
								<td>Kasvuhoone pindala:</td>
								<td><?php print($_GENERAL->format_number($house_lvl_data->greenhouse_land));?> m<sup>2</sup></td>
							</tr>
							<tr>
								<td>Vaba maad vaja:</td>
								<td><?php print($_GENERAL->format_number($house_lvl_data->land));?> m<sup>2</sup></td>
							</tr>
							<tr>
								<td>Raha vaja:</td>
								<td><?php print($_GENERAL->format_number($house_price));?></td>
							</tr>
							<tr>
								<td></td>
								<td>
									<input type="hidden" name="house" value="<?php print($_GENERAL->format_number($house_lvl_data->id));?>">
									<input type="hidden" name="token" value="<?php echo Token::generate('UPGRADE_HOUSE'); ?>">
									<input type="submit" value="Uuenda oma elamut">
								</td>
							</tr>
						</table>
					</form>
				</td>
			</tr>
		</table>
	</div>

<?php
}
include("includes/overall/footer.php");
