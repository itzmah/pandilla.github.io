<?php
/*
 * Name: FreeLand v2
 * Author: Marko Murumaa
 * Created: July 2015
 * Website: www.freelandplay.eu
*/

$_lir = true;
require_once("core/init.php");


	$gym_total = $_USER->data('data')->speed + $_USER->data('data')->strength + $_USER->data('data')->stamina;

	$gender = ($_USER->data()->gender == 1) ? 'Mees' : 'Naine';

	$group_query = DB::getInstance(1)->get('groups', array('id','=',$_USER->data()->groups));
	$status = '<font color="'.$group_query->first()->color.'">'.$group_query->first()->name.'</font>';

	$house_query = DB::getInstance()->get('house_levels', array('id','=',$_USER->data('house')->house_level));
	$house = $house_query->first()->name;

include("includes/overall/header.php");
?>

<div id="page">
	<div class="page-title">Sinu andmed</div>
	<p>
		<table>
			<tr valign="top">
				<td width="20%"><img src="css/default/images/data.png" width="100" height="100"></td>
				<td width="80%">
					Siin lehel on välja toodud enamus teie konto andmeid. Isiklikke andmeid näete ainult teie.
				</td>
			</tr>
		</table>
		<table>
			<tr valign="top">
				<td width="50%">
					<table>
						<tr>
							<th width="40%" colspan="2">Kasutaja andmed</th>
						</tr>
						<tr>
							<td>Kasutajanimi:</td>
							<td width="60%"><?php print($_USER->data()->username);?></td>
						</tr>
						<tr>
							<td>Eesnimi: </td>
							<td><?php print($_USER->data()->firstname);?></td>
						</tr>
						<tr>
							<td>Perekonnanimi:</td>
							<td><?php print($_USER->data()->lastname);?></td>
						</tr>
						<tr>
							<td>Sünniaeg:</td>
							<td><?php print($_USER->data()->birth);?></td>
						</tr>
						<tr>
							<td>Sugu:</td>
							<td><?php print($gender);?></td>
						</tr>
						<tr>
							<td>Email:</td>
							<td><?php print($_USER->data()->email);?></td>
						</tr>
						<tr>
							<td>Registreerimis aeg:</td>
							<td><?php print($_USER->data()->joined);?></td>
						</tr>
						<tr>
							<td>Staatus:</td>
							<td><?php print($status);?></td>
						</tr>
						<tr>
							<th colspan="2">Majanduslik info</th>
						</tr>
						<tr>
							<td>Sularaha:</td>
							<td><?php print($_GENERAL->format_number($_USER->data('data')->money));?></td>
						</tr>
						<tr>
							<td>Raha pangas:</td>
							<td><?php print($_GENERAL->format_number($_USER->data('data')->money_bank));?></td>
						</tr>
						<tr>
							<td>Raha võlgu:</td>
							<td><?php print($_GENERAL->format_number($_USER->data('data')->loan));?></td>
						</tr>
						<tr>
							<td>Valmis kanepit:</td>
							<td><?php print($_GENERAL->format_number($_USER->data('house')->weed));?></td>
						</tr>
						<tr>
							<td>Elamu:</td>
							<td><?php print($house);?></td>
						</tr>
						<tr>
							<td>Vaba maad:</td>
							<td><?php print($_GENERAL->format_number($_USER->data('house')->land));?></td>
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
							<td>Toitu:</td>
							<td><?php print($_GENERAL->format_number($_USER->data('data')->food));?></td>
						</tr>
						<tr>
							<td>Haridus:</td>
							<td><?php print($_GENERAL->format_number($_USER->data('data')->education));?></td>
						</tr>
						<tr>
							<td>Skoor:</td>
							<td><?php print($_GENERAL->format_number($_USER->data('data')->score));?></td>
						</tr>
						<tr>
							<th colspan="2">Relvad</th>
						</tr>
						<tr>
							<td>Kaitse prillid:</td>
							<td><?php print($_GENERAL->format_number($_USER->data('house')->wep_1));?></td>
						</tr>
						<tr>
							<td>Nukid:</td>
							<td><?php print($_GENERAL->format_number($_USER->data('house')->wep_2));?></td>
						</tr>
						<tr>
							<td>Kuulivestid:</td>
							<td><?php print($_GENERAL->format_number($_USER->data('house')->wep_3));?></td>
						</tr>
						<tr>
							<td>Tavalised relvad:</td>
							<td><?php print($_GENERAL->format_number($_USER->data('house')->wep_4));?></td>
						</tr>
						<tr>
							<td>Kilbid:</td>
							<td><?php print($_GENERAL->format_number($_USER->data('house')->wep_5));?></td>
						</tr>
						<tr>
							<td>Automaat relvad:</td>
							<td><?php print($_GENERAL->format_number($_USER->data('house')->wep_6));?></td>
						</tr>
					</table>
				</td>
				<td width="50%">
					<table>
						<tr>
							<th width="40%" colspan="2">Kaitsesüsteem</th>
						</tr>
						<tr>
							<td>Kaitse level:</td>
							<td width="60%"><?php print($_GENERAL->format_number($_USER->data('house')->defence_level));?></td>
						</tr>
						<tr>
							<td>Kaitsjaid:</td>
							<td><?php print($_GENERAL->format_number($_USER->data('house')->defence_man));?></td>
						</tr>
						<tr>
							<td>Kaitse kambast:</td>
							<td><?php print($_GENERAL->format_number($_USER->user_defence_i('gang')));?></td>
						</tr>
						<tr>
							<td>Kaitse jõusaalist:</td>
							<td><?php print($_GENERAL->format_number($_USER->user_defence_i('gym')));?></td>
						</tr>
						<tr>
							<td>Kaitse relvadest:</td>
							<td><?php print($_GENERAL->format_number($_USER->user_defence_i('weapons')));?></td>
						</tr>
						<tr>
							<td>Kaitse kaitsjatest:</td>
							<td><?php print($_GENERAL->format_number($_USER->user_defence_i('self')));?></td>
						</tr>
						<tr>
							<td>Kaitse punkte kokku:</td>
							<td><?php print($_GENERAL->format_number($_USER->user_defence_i()));?></td>
						</tr>
						<tr>
							<th colspan="2">Ründesüsteem:</th>
						</tr>
						<tr>
							<td>Ründe level:</td>
							<td><?php print($_GENERAL->format_number($_USER->data('house')->offence_level));?></td>
						</tr>
						<tr>
							<td>Ründajaid:</td>
							<td><?php print($_GENERAL->format_number($_USER->data('house')->offence_man));?></td>
						</tr>
						<tr>
							<td>Rünne kambast:</td>
							<td><?php print($_GENERAL->format_number($_USER->user_offence_i('gang')));?></td>
						</tr>
						<tr>
							<td>Rünne jõusaalist:</td>
							<td><?php print($_GENERAL->format_number($_USER->user_offence_i('gym')));?></td>
						</tr>
						<tr>
							<td>Rünne relvadest:</td>
							<td><?php print($_GENERAL->format_number($_USER->user_offence_i('weapons')));?></td>
						</tr>
						<tr>
							<td>Rünne ründajatest:</td>
							<td><?php print($_GENERAL->format_number($_USER->user_offence_i('self')));?></td>
						</tr>
						<tr>
							<td>Ründe punkte kokku:</td>
							<td><?php print($_GENERAL->format_number($_USER->user_offence_i()));?></td>
						</tr>
						<tr>
							<th colspan="2">Jõusaali info</th>
						</tr>
						<tr>
							<td>Kiirus:</td>
							<td><?php print($_GENERAL->format_number($_USER->data('data')->speed));?></td>
						</tr>
						<tr>
							<td>Tugevus:</td>
							<td><?php print($_GENERAL->format_number($_USER->data('data')->strength));?></td>
						</tr>
						<tr>
							<td>Vastupidavus:</td>
							<td><?php print($_GENERAL->format_number($_USER->data('data')->stamina));?></td>
						</tr>
						<tr>
							<td>Punkte kokku:</td>
							<td><?php print($_GENERAL->format_number($gym_total));?></td>
						</tr>
					</table>
				</td>
			</tr>
		</table>
	</p>
</div>

<?php
include("includes/overall/footer.php");
