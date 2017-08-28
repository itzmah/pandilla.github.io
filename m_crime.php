<?php
/*
 * Name: FreeLand v2
 * Author: Marko Murumaa
 * Created: July 2015
 * Website: www.freelandplay.eu
*/

$_lir = true;
require_once("core/init.php");

$crime_turns = 5;
$crime_next_level_xp = $_USER->data('data')->crime_level * (100 * $_USER->data('data')->crime_level);
$crime_time_remaining = strtotime($_USER->data('data')->crime_last) - time();

$crime_remaining_text = ($crime_time_remaining < 1) ? 'Kohe' : $_GENERAL->time_ends($crime_time_remaining) . ' pärast';

if ($_USER->data('data')->crime_xp >= $crime_next_level_xp) {
	$_USER->update(array(
		'crime_xp' => 0,
		'crime_level' => $_USER->data('data')->crime_level + 1
	),$_USER->data()->id, 'users_data');
}

if(Input::exists('get')) {
	if (empty(Input::get('rob')) === false) {
		$crime_query = DB::getInstance()->get('crime_list', array('id','=',Input::get('rob')));
		if (!$crime_query->count()) {
			$_GENERAL->addError("See kuritegevus on juba sooritatud.");
		} else {
			$crime_data = $crime_query->first();

			if ($_USER->data('data')->turns < $crime_turns) {
				$_GENERAL->addError("Teil ei ole piisavalt käike.");
			}

			if ($_USER->data('data')->crime_level != $crime_data->level) {
				$_GENERAL->addError("Teie kuritegevuse level ei ole piisavalt kõrge.");
			}

			if (strtotime($_USER->data('data')->crime_last) > time()) {
				$_GENERAL->addError("Kuritegevust saab sooritada iga 2 minuti tagant.");
			}

			if (empty($_GENERAL->errors()) === true) {
				$next_crime = time() + 60*2;
				$_USER->update(array(
					'money' => $_USER->data('data')->money + $crime_data->money,
					'crime_xp' => $_USER->data('data')->crime_xp + 1,
					'turns' => $_USER->data('data')->turns - $crime_turns,
					'crime_last' => date("Y-m-d H:i:s", $next_crime)
				),$_USER->data()->id, 'users_data');

				DB::getInstance()->delete('crime_list', array('id', '=', $crime_data->id));

				Session::flash('crime', 'Teie rööv oli edukas ja te röövisite '.$_GENERAL->format_number($crime_data->money).'.');
				Redirect::to('p.php?p=crime');
			}
		}
	}
}

$crime_query = DB::getInstance()->query("SELECT * FROM crime_list WHERE `level` = ".$_USER->data('data')->crime_level." LIMIT 15");
foreach ($crime_query->results() as $crime) {
	$output_line .= '
		<tr>
			<td>'.$crime->name.'</td>
			<td align="center">'.$_GENERAL->format_number($crime->money).'</td>
			<td align="center">'.$_GENERAL->format_number($crime->level).'</td>
			<td align="center"><a href="p.php?p=crime&rob='.$crime->id.'">Soorita kuritegevus</a></td>
		</tr>
	';
}


include("includes/overall/header.php");
?>

<div id="page">
	<div class="page-title">Kuritegevus</div>
	<p>
	<?php
	if (empty($_GENERAL->errors()) === false) {
		print($_GENERAL->output_errors());
	}

	if(Session::exists('crime')) {
		$_GENERAL->addOutSuccess(Session::flash('crime'));
		print($_GENERAL->output_success());
	}
	?>
		<table>
			<tr valign="top">
				<td width="20%"><img src="css/default/images/crime.png" width="100" height="100"></td>
				<td width="80%">
					Kuritegevus on üks väga lihtne moodus raha teenimiseks. 
					Mida suurem on teie kuritegevuse level seda rohkem erinevaid rööve saate teha.<br>
					Kuritegevust on võimalik sooritada iga 2 minuti tagant.<br>
					Uued kuritegevused lisanduvad iga uue tunni alguses.<br>
					Kuritegevuse sooritamiseks läheb teil vaja <?php print($_GENERAL->format_number($crime_turns));?> käiku.
				</td>
			</tr>
		</table>
	</p>
	<table>
		<tr>
			<td width="25%">Kuritegevuse level:</td>
			<td width="75%"><?php print($_GENERAL->format_number($_USER->data('data')->crime_level));?></td>
		</tr>
		<tr>
			<td>Kuritegevuse kogemusi:</td>
			<td><?php print($_GENERAL->format_number($_USER->data('data')->crime_xp));?>/<?php print($_GENERAL->format_number($crime_next_level_xp));?></td>
		</tr>
		<tr>
			<td>Kuritegevust saab sooritada:</td>
			<td><?php print($crime_remaining_text);?></td>
		</tr>
	</table>

	<table>
		<tr>
			<th width="35%">Nimi</th>
			<th width="30%">Röövitav summa</th>
			<th width="15%">Nõutud level</th>
			<th width="20%">#</th>
		</tr>
		<?php print($output_line);?>
	</table>
	<?php 
	if (empty($output_line) === true) {
		$_GENERAL->addOutInfo("Hetkel ei ole ühtegi kuritegevust. Uued kuritegevused tulevad uuel tunnil.");
		print("<br>");
		print($_GENERAL->output_info());
	}
	?>
</div>

<?php
include("includes/overall/footer.php");
