<?php
/*
 * Name: FreeLand v2
 * Author: Marko Murumaa
 * Created: July 2015
 * Website: www.freelandplay.eu
*/

	if ($GANG_ACCESS[$gang_member->rank_id]['building_buy'] == 0) {
		$_GENERAL->addError("Teil ei ole õigusi kamba hoonele.");
	}

if(Input::exists()) {
	if(Token::check(Input::get('token'), 'BUY_BUILDING') ) {
		if ($GANG_ACCESS[$gang_member->rank_id]['building_buy'] == 0) {
			$_GENERAL->addError("Teil ei ole õigusi kamba hoone ostmisel.");
		} else {
			$building_id = (int)Input::get('building');
			$building_info_query = DB::getInstance()->query("SELECT * FROM `gang_buildings` WHERE `id` = " . $building_id);
			if (!$building_info_query->count()) {
				$_GENERAL->addError("Sellist kamba hoonet ei leitud.");
			} else {
				$building_info = $building_info_query->first();
				if ($gang_info->building_level >= $building_id) {
					$_GENERAL->addError("Kambal on juba olemas see hoone.");
				} else {
					if (($gang_info->building_level+1) < $building_id) {
						$_GENERAL->addError("Kamba hooneid tuleb osta järjest.");
					}

					if ($gang_info->money < $building_info->money) {
						$_GENERAL->addError("Kambal ei ole piisavalt raha.");
					}

					if (empty($_GENERAL->errors()) === true) {
						DB::getInstance()->update('gang', $gang_info->id, 
							array(
								'money' => $gang_info->money - $building_info->money,
								'building_level' => $building_info->id
								));
				
						Session::flash('gang', 'Te ostsite kambale uue hoone.');
						Redirect::to('p.php?p=gang&page=building');
					}
				}
			}
		}
	}
}
?>
<div id="page">
	<div class="page-title">Kamp</div>
	<p>
	<?php
	print($gang_menu);
	if (empty($_GENERAL->errors()) === false) {
		print($_GENERAL->output_errors());
	}

	if(Session::exists('gang')) {
		$_GENERAL->addOutSuccess(Session::flash('gang'));
		print($_GENERAL->output_success());
	}
	?>
</div>
	<?php
if ($GANG_ACCESS[$gang_member->rank_id]['building_buy'] == 1) {

	$building_lvl_query = DB::getInstance()->query("SELECT * FROM `gang_buildings` ORDER BY `id` ASC");
	foreach ($building_lvl_query->results() as $b_lvl) {
		$selected = '';
		if(Input::exists()) {
			if (Input::get('building') == $b_lvl->id) {
				$selected = ' selected';
			}
		} else {
			if ($b_lvl->id == $gang_info->building_level) {
				$selected = ' selected';
			}
		}

		$output_line .= '<option'.$selected.' value="'.$b_lvl->id.'">'.$b_lvl->name.'</option>';
	}

	if(Input::exists()) {
		$building_lvl_i_query = DB::getInstance()->query("SELECT * FROM `gang_buildings` WHERE `id` = " . (int)Input::get('building'));
		if (!$building_lvl_i_query->count()) {
			Redirect::to('p.php?p=house');
		}
	} else {
		$building_lvl_i_query = DB::getInstance()->query("SELECT * FROM `gang_buildings` WHERE `id` = " . $gang_info->building_level);
	}

	$building_lvl_data = $building_lvl_i_query->first();

	?>
<div id="page">
	<div class="page-title">Kamba hoone</div>
	<p>
		<table>
			<tr valign="top">
				<td width="20%"><img src="css/default/images/gang_building.png" width="100" height="100"></td>
				<td width="80%">
					 Kamba hoonest soltub teie kamba maksimaalne liikmete arv.<br>
					 Mida suurem on teie kamba hoone seda rohkem skoori saate.
				</td>
			</tr>
		</table>
	</p>
	<table>
		<form action="p.php?p=gang&page=building" method="POST">
			<tr>
				<td width="20%">Hoone:</td>
				<td width="80%">
					<select name="building" onchange="this.form.submit();">
						<?php print($output_line);?>
					</select>
				</td>
			</tr>
		</form>
		<tr>
			<td>Liikmeid maks:</td>
			<td><?php print($_GENERAL->format_number($building_lvl_data->max_members));?></td>
		</tr>
		<tr>
			<td>Hind:</td>
			<td><?php print($_GENERAL->format_number($building_lvl_data->money));?></td>
		</tr>
	</table>
	<form action="p.php?p=gang&page=building" method="POST">
		<table>
			<tr>
				<td width="20%"></td>
				<td width="80%">
					<input type="hidden" name="building" value="<?php print($_GENERAL->format_number($building_lvl_data->id));?>">
					<input type="hidden" name="token" value="<?php echo Token::generate('BUY_BUILDING'); ?>">
					<input type="submit" value="Osta uus hoone">
				</td>
			</tr>
		</table>
	</form>
</div>
<?php
}
