<?php
/*
 * Name: FreeLand v2
 * Author: Marko Murumaa
 * Created: July 2015
 * Website: www.freelandplay.eu
*/

$_lir = true;
require_once("core/init.php");

$search = null;

if(Input::exists()) {
	if(Token::check(Input::get('token')) ) {
		if (empty(Input::get('username')) === false) {
			Redirect::to('p.php?p=members&search='.Input::get('username'));
		}
	}
}

if (empty(Input::get('search')) === false) {
	$search = Input::get('search');
}

if (empty(Input::get('sort')) === false) {
	if (Input::get('sort') == "team") {
		$where_count = "`groups` = 2 OR `groups` = 4 OR `groups` = 5";
		$where_out = "`users`.`groups` = 2 OR `users`.`groups` = 4 OR `users`.`groups` = 5";
		$sort_team = true;
	} else if (Input::get('sort') == "banned") {
		$where_count = "`groups` = 3";
		$where_out = "`users`.`groups` = 3";
		$sort_banned = true;
	}
}

if (!$sort_banned && !$sort_team) {
	$where_count = "`groups` != 2 AND `groups` != 3";
	$where_out = "`users`.`groups` != 2 AND `users`.`groups` != 3";
}

$members_per_page = 20;
$membersquery = DB::getInstance()->query("SELECT * FROM `users` WHERE $where_count AND `username` LIKE ?", ['%' . $search . '%']);
$members_total = ceil($membersquery->count() / $members_per_page );

$page = (empty(Input::get('page')) === false) ? (int)Input::get('page') : 1;
$start = ($page - 1) * $members_per_page;

$x = ( $page - 1 ) * $members_per_page;

if ( $x < 0 ) {
	$x = 0;
}

$count = $start;
$members_list = null;
$members_query = DB::getInstance()->query("SELECT * FROM `users` INNER JOIN `users_data` ON `users`.`id` = `users_data`.`id` WHERE $where_out AND `username` LIKE ? ORDER BY `users_data`.`score` DESC, `users`.`id` LIMIT $start, $members_per_page", ['%' . $search . '%']);

foreach ($members_query->results() as $member) {
	$count++;

	$group_query = DB::getInstance(1)->get('groups', array('id','=',$member->groups));
	$color = $group_query->first()->color;

	if ($member->id == $_USER->data()->id) {
		$bold_start = '<b>';
		$bold_end = '</b>';
	} else {
		$bold_start = '';
		$bold_end = '';
	}

	if ($member->gang != 0) {
		$gang_line = 'Jah';
	} else {
		$gang_line = 'Ei';
	}

	$members_list .= '
			<tr>
				<td align="center">'.$bold_start.$count.$bold_end.'</td>
				<td>'.$bold_start.'<a href="p.php?p=profile&user='.$member->username.'"><font color="'.$color.'">'.$member->username.'</font></a>'.$bold_end.'</td>
				<td align="center">'.$bold_start.$_GENERAL->format_number($member->score).$bold_end.'</td>
				<td align="center">'.$bold_start.$_GENERAL->format_number($member->money).$bold_end.'</td>
				<td align="center">'.$bold_start.date("Y-m-d", strtotime($member->joined)).$bold_end.'</td>
				<td align="center">'.$bold_start.$gang_line.$bold_end.'</td>
			</tr>';
}

include("includes/overall/header.php");
?>

<div id="page">
	<div class="page-title">Mängijate edetabel</div>
	<p>
		<table>
			<tr valign="top">
				<td width="20%"><img src="css/default/images/rankings.png" width="100" height="100"></td>
				<td width="80%">
					Mängijate edetabelis on toodud välja kõik mängijad skoori järjekorras. 
					Mida kõrgem on teie skoor seda kõrgemal kohal te edetabelis olete.
					
				</td>
			</tr>
		</table>
	</p>
	<table>
		<tr>
			<td width="55%">
				<form action="p.php?p=members" method="POST">
				<table>
					<tr>
						<td width="30%">Otsi kasutajat: </td>
						<td width="70%">
							<input type="text" name="username">
							<input type="hidden" name="token" value="<?php echo Token::generate(); ?>">
							<input type="submit" value="Otsi">
						</td>
					</tr>
				</table>
				</form>
			</td>
			<td width="45%" align="right">
				<a href="p.php?p=members&sort=team">Juhtkonna kasutajad</a> | 
				<a href="p.php?p=members&sort=banned">Blokeeritud kasutajad</a>
			</td>
		</tr>
	</table>
	<table>
		<tr>
			<th width="5%">Koht</th>
			<th width="30%">Kasutajanimi</th>
			<th width="20%">Skoor</th>
			<th width="20%">Sularaha</th>
			<th width="15%">Liitus</th>
			<th width="10%">Kamp</th>
		</tr>
		<?php print($members_list);?>
	</table>
		<?php
		if ($members_total > 0) {
			if ($sort_team === true) {
				$page_name = "&sort=team";
			} else if ($sort_banned === true) {
				$page_name = "&sort=banned";
			} else {
				$page_name = "";
			}

			if ($members_total >= 1 && $page <= $members_total) {
				echo '<div align="center">Lehekülg: ';
				for ($x=1; $x<=$members_total; $x++) {
					echo ($x == $page) ? '<b>'.$x.'</b>  ' : '<a href="p.php?p=members'.$page_name.'&page='.$x.'">'.$x.'</a> ';
				}
				echo '</div>';
			}
		} else {
			print("<br>");
			$_GENERAL->addError("Kasutajaid ei leitud.");
			if (empty($_GENERAL->errors()) === false) {
				print($_GENERAL->output_errors());
			}
		}
		?>
</div>

<?php
include("includes/overall/footer.php");
