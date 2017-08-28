<?php
/*
 * Name: FreeLand v2
 * Author: Marko Murumaa
 * Created: July 2015
 * Website: www.freelandplay.eu
*/

$_lir = true;
require_once("core/init.php");

include("includes/overall/header.php");

if (empty(Input::get('gang')) === false) {
	$gang_i_query = DB::getInstance()->query("SELECT * FROM `gang` WHERE `id` = " . (int)Input::get('gang') . " AND `deleted` = 0");
	if (!$gang_i_query->count()) {
		Redirect::to('p.php?p=gangs');
	} else {
		$gang_i = $gang_i_query->first();

		$leader_i_query = DB::getInstance()->query("SELECT `username` FROM `users` WHERE `id` = " . $gang_i->leader);
		$leader_i = $leader_i_query->first();

		$gang_members_query = DB::getInstance()->query("SELECT * FROM `gang_members` WHERE `gang_id` = " . $gang_i->id);
		$gang_members_count = $gang_members_query->count();

		$gang_building_query = DB::getInstance()->query("SELECT * FROM `gang_buildings` WHERE `id` = " . $gang_i->building_level);
		$gang_building = $gang_building_query->first();

		if ($gang_members_count >= $gang_building->max_members) {
			$members_line = '<font color="red">'.$_GENERAL->format_number($gang_members_count).' / '.$_GENERAL->format_number($gang_building->max_members).'</font>';
		} else {
			$members_line = $_GENERAL->format_number($gang_members_count).' / '.$_GENERAL->format_number($gang_building->max_members);
		}

		$gang_logo = (empty($gang_i->logo_url) === false) ? $gang_i->logo_url : 'css/default/images/gang.png';

	?>
<div id="page">
	<div class="page-title">Kamp</div>
	<table>
		<tr>
			<td width="20%">Kamba nimi:</td>
			<td width="50%"><?php print($gang_i->name);?></td>
			<td width="30%" rowspan="7" align="center">Kamba logo:<br><img src="<?php print($gang_logo);?>" width="100" height="100"></td>
		</tr>
		<tr>
			<td>Kamba juht:</td>
			<td><a href="p.php?p=profile&user=<?php print($leader_i->username);?>"><?php print($leader_i->username);?></a></td>
		</tr>
		<tr>
			<td>Kamba skoor:</td>
			<td><?php print($_GENERAL->format_number($gang_i->score));?></td>
		</tr>
		<tr>
			<td>Kambas liikmeid:</td>
			<td><?php print($members_line);?></td>
		</tr>
		<tr>
			<td>Kamba hoone:</td>
			<td><?php print($gang_building->name);?></td>
		</tr>
	</table>
</div>
<?php
if (empty($gang_i->info) === true) {
	$_GENERAL->addOutInfo('Kamba omanik ei ole veel kamba infot kirjutanud.');
	$info_line = $_GENERAL->output_info();
} else {
	$info_line = $_BBCODE->Parse($gang_i->info);
}
?>
<div id="page">
	<div class="page-title">Kamba info</div>
	<p><?php print($info_line);?></p>
</div>
<?php
	$gang_rank_name = json_decode($gang_i->access, true);
	$x = 0;
	$g_members_query = DB::getInstance()->query("SELECT * FROM `gang_members` WHERE `gang_id` = ".$gang_i->id." ORDER BY `rank_id` DESC");
	foreach ($g_members_query->results() as $member) {
		$x++;

		$u_i_query = DB::getInstance()->query("SELECT `username` FROM `users` WHERE `id` = " . $member->user_id);
		$u_i = $u_i_query->first();

		$output_line .= '
		<tr>
			<td align="center">'.$_GENERAL->format_number($x).'</td>
			<td><a href="p.php?p=profile&user='.$u_i->username.'">'.$u_i->username.'</a></td>
			<td align="center">'.$gang_rank_name[$member->rank_id]['name'].'</td>
			<td align="center">'.$member->joined.'</td>
		</tr>
		';
	}
?>
<div id="page">
	<div class="page-title">Kamba liikmed</div>
	<table>
		<tr>
			<th width="10%">Jrk.</th>
			<th width="30%">Kasutajanimi</th>
			<th width="30%">Kamba auaste</th>
			<th width="10%">Liitus</th>
		</tr>
		<?php print($output_line);?>
	</table>
</div>
<?php
	}
} else {

?>

<div id="page">
	<div class="page-title">Kampade edetabel</div>
	<p>
	<?php
	$gangs_per_page = 15;
	$gangsquery = DB::getInstance()->query("SELECT * FROM `gang` WHERE `deleted` = 0");
	$gangs_total = ceil($gangsquery->count() / $gangs_per_page );

	$page = (isset($_GET['page'])) ? (int)$_GET['page'] : 1;
	$start = ($page - 1) * $gangs_per_page;

	$x = ( $page - 1 ) * $gangs_per_page;

	if ( $x < 0 ) {
		$x = 0;
	}

	$count = $start;

	$gang_list_query = DB::getInstance()->query("SELECT * FROM `gang` WHERE `deleted` = 0 ORDER by `score` DESC LIMIT $start, $gangs_per_page");
	foreach ($gang_list_query->results() as $gang) {
		$x++;
		$user_i_query = DB::getInstance()->query("SELECT `username` FROM `users` WHERE `id` = " . $gang->leader);
		$user_i = $user_i_query->first();

		$gang_members_query = DB::getInstance()->query("SELECT * FROM `gang_members` WHERE `gang_id` = " . $gang->id);
		$gang_members_count = $gang_members_query->count();

		$gang_building_query = DB::getInstance()->query("SELECT * FROM `gang_buildings` WHERE `id` = " . $gang->building_level);
		$gang_building = $gang_building_query->first();

		if ($gang_members_count >= $gang_building->max_members) {
			$members_line = '<font color="red">'.$_GENERAL->format_number($gang_members_count).' / '.$_GENERAL->format_number($gang_building->max_members).'</font>';
		} else {
			$members_line = $_GENERAL->format_number($gang_members_count).' / '.$_GENERAL->format_number($gang_building->max_members);
		}

		$output_line .= '
		<tr>
			<td align="center">'.$_GENERAL->format_number($x).'</td>
			<td><a href="p.php?p=gangs&gang='.$gang->id.'">'.$gang->name.'</a></td>
			<td align="center">'.$_GENERAL->format_number($gang->score).'</td>
			<td align="center"><a href="p.php?p=profile&user='.$user_i->username.'">'.$user_i->username.'</a></td>
			<td align="center">'.$members_line.'</td>
		</tr>
		';
	}
	?>
		<table>
			<tr valign="top">
				<td width="20%"><img src="css/default/images/gangs.png" width="100" height="100"></td>
				<td width="80%">
					Siin on kirjas kõik kambad mis meie mängus on.
					Kampade järjekord on skoori järgi.<br>
					Kamp mille skoor kõige suurem on edetabelis esimene.
				</td>
			</tr>
		</table>
	</p>
	<table>
		<tr>
			<th width="10%">Koht</th>
			<th width="40%">Kamba nimi</th>
			<th width="20%">Skoor</th>
			<th width="20%">Kamba juht</th>
			<th width="10%">Liikmeid</th>
		</tr>
		<?php print($output_line);?>
	</table>
	<?php
		if ($gangs_total > 0) {
			if ($gangs_total >= 1 && $page <= $gangs_total) {
				echo '<div align="center">Lehekülg: ';
				for ($x=1; $x<=$gangs_total; $x++) {
					echo ($x == $page) ? '<b>'.$x.'</b> ' : '<a href="p.php?p=gangs&page='.$x.'">'.$x.'</a> ';
				}
				echo '</div>';
			}
		} else {
			$_GENERAL->addError("Kampasid ei leitud.");
			if (empty($_GENERAL->errors()) === false) {
				print($_GENERAL->output_errors());
			}
		}
	?>
</div>

<?php
}

include("includes/overall/footer.php");
