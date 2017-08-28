<?php
/*
 * Name: FreeLand v2
 * Author: Marko Murumaa
 * Created: July 2015
 * Website: www.freelandplay.eu
*/

$_lir = true;
require_once("core/init.php");

$new_gang_money = $_GENERAL->settings('settings_game','GANG_CREATE_MONEY');
$join_gang_money = $_GENERAL->settings('settings_game','GANG_JOIN_MONEY');

$gang_money_send_limit = $_GENERAL->settings('settings_game','GANG_MONEY_SEND_LIMIT');
$gang_money_send_points = $_GENERAL->settings('settings_game','GANG_MONEY_SEND_POINTS');

$gang_edit_leader_money = $_GENERAL->settings('settings_game','GANG_EDIT_LEADER_MONEY');

if(Input::exists()) {
	if(Token::check(Input::get('token'), 'NEW_GANG') ) {
		$name = Input::get('name');
		$password = Input::get('password');

		$name_check_query = DB::getInstance()->query("SELECT * FROM `gang` WHERE `name` = ? AND `deleted` = 0", [$name]);

		if ($_USER->data('data')->gang != 0) {
			$_GENERAL->addError("Te olete juba kambas.");
		}

		if (empty($name) === true or empty($password) === true) {
			$_GENERAL->addError("Kamba nimi ja parool on kohustuslikud.");
		}

		if ($name_check_query->count() > 0) {
			$_GENERAL->addError("See kamba nimi on juba kasutusel.");
		}

		if (preg_match("/[^A-z0-9_\-]/", $name) == 1) {
			$_GENERAL->addError("Kamba nimi võib sisalda ainult tähti ja numberid.");
		}

		if (strlen($name) < 3 or strlen($name) > 20) {
			$_GENERAL->addError("Kamba nimi peab olema 3 kuni 20 sümbolit pikk.");
		}

		if ($_USER->data('data')->money < $new_gang_money) {
			$_GENERAL->addError("Teil ei ole piisavalt raha.");
		}

		if (empty($_GENERAL->errors()) === true) {
			$access_list = '{"1":{"name":"Reamees","forum_edit":0,"forum_delete":0,"market_buy":0,"market_sell":0,"members_drop":0,"members_money":0,"members_rank":0,"building_buy":0,"management_info":0,"management_logo":0,"management_password":0},"2":{"name":"Seersant","forum_edit":0,"forum_delete":0,"market_buy":0,"market_sell":0,"members_drop":0,"members_money":0,"members_rank":0,"building_buy":0,"management_info":0,"management_logo":0,"management_password":0},"3":{"name":"Veebel","forum_edit":0,"forum_delete":0,"market_buy":0,"market_sell":0,"members_drop":0,"members_money":0,"members_rank":0,"building_buy":0,"management_info":0,"management_logo":0,"management_password":0},"4":{"name":"Leitnant","forum_edit":0,"forum_delete":0,"market_buy":0,"market_sell":0,"members_drop":0,"members_money":0,"members_rank":0,"building_buy":0,"management_info":0,"management_logo":0,"management_password":0},"5":{"name":"Kapten","forum_edit":0,"forum_delete":0,"market_buy":0,"market_sell":0,"members_drop":0,"members_money":0,"members_rank":0,"building_buy":0,"management_info":0,"management_logo":0,"management_password":0},"6":{"name":"Kamba juht","forum_edit":1,"forum_delete":1,"market_buy":1,"market_sell":1,"members_drop":1,"members_money":1,"members_rank":1,"building_buy":1,"management_info":1,"management_logo":1,"management_password":1}}';
			$gang_fields = array(
				'name' => $name,
				'password' => $password,
				'leader' => $_USER->data()->id,
				'access' => $access_list
				);
			DB::getInstance()->insert('gang', $gang_fields);

			$new_gang_data = DB::getInstance()->query("SELECT * FROM `gang` WHERE `name` = ? AND `deleted` = 0", [$name]);
			$last_gang_id = $new_gang_data->first()->id;

			$gang_members_fields = array(
				'user_id' => $_USER->data()->id,
				'gang_id' => $last_gang_id,
				'rank_id' => 6,
				'joined' => date("Y-m-d")
				);
			DB::getInstance()->insert('gang_members', $gang_members_fields);
			
			$_USER->update(array(
					'money' => $_USER->data('data')->money - $new_gang_money,
					'gang' => $last_gang_id
				),$_USER->data()->id, 'users_data');

			Session::flash('gang', 'Teie kamp on edukalt loodud.');
			Redirect::to('p.php?p=gang');
		}
	} else if(Token::check(Input::get('token'), 'JOIN_GANG') ) {
		$name = Input::get('name');
		$password = Input::get('password');

		$gang_info_query = DB::getInstance()->query("SELECT * FROM `gang` WHERE `name` = ? AND `deleted` = 0", [$name]);

		if ($_USER->data('data')->gang != 0) {
			$_GENERAL->addError("Te olete juba kambas.");
		} else {
			if (!$gang_info_query->count()) {
				$_GENERAL->addError("Sellise nimega kampa ei leitud.");
			} else {
				$gang_info = $gang_info_query->first();

				$gang_join_members_query = DB::getInstance()->query("SELECT * FROM `gang_members` WHERE `gang_id` = " . $gang_info->id);
				$gang_join_members_count = $gang_join_members_query->count();

				$gang_building_query = DB::getInstance()->query("SELECT * FROM `gang_buildings` WHERE `id` = " . $gang_info->building_level);
				$gang_building = $gang_building_query->first();

				if ($_USER->data('data')->money < $join_gang_money) {
					$_GENERAL->addError("Teil ei ole piisavalt raha.");
				}

				if ($password != $gang_info->password) {
					$_GENERAL->addError("Teie sisestatud kamba parool on vale.");
				}

				if ($gang_join_members_count >= $gang_building->max_members) {
					$_GENERAL->addError("Kamp on täis.");
				}

				if (empty($_GENERAL->errors()) === true) {
					$join_gang_id = $gang_info->id;

					$gang_members_fields = array(
						'user_id' => $_USER->data()->id,
						'gang_id' => $join_gang_id,
						'rank_id' => 1,
						'joined' => date("Y-m-d")
						);
					DB::getInstance()->insert('gang_members', $gang_members_fields);
					
					$log_body = '<font color="green">Kasutaja <b><a href="p.php?p=profile&user='.$_USER->data()->username.'">'.$_USER->data()->username.'</a></b> liitus kambaga.</font>';

					$gang_logs_fields = array(
						'gang_id' => $join_gang_id,
						'body' => $log_body
						);
					DB::getInstance()->insert('gang_logs', $gang_logs_fields);

					$_USER->update(array(
							'money' => $_USER->data('data')->money - $join_gang_money,
							'gang' => $join_gang_id
						),$_USER->data()->id, 'users_data');

					Session::flash('gang', 'Te olete nüüd kambas.');
					Redirect::to('p.php?p=gang');
				}
			}
		}
	}
}

include("includes/overall/header.php");

if ($_USER->data('data')->gang != 0) {

	$gang_menu = '
			<ul class="gang-menu">
				<li><a href="p.php?p=gang">Peakontor</a></li>
				<li><a href="p.php?p=gang&page=forum">Kamba foorum</a></li>
				<li><a href="p.php?p=gang&page=market">Turg</a></li>
				<li><a href="p.php?p=gang&page=members">Kamba liikmed</a></li>
				<li><a href="p.php?p=gang&page=logs">Sündmused</a></li>
				<li><a href="p.php?p=gang&page=building">Kamba hoone</a></li>
				<li><a href="p.php?p=gang&page=management">Haldamine</a></li>
			</ul>
	';

	$gang_info_query = DB::getInstance()->query("SELECT * FROM `gang` WHERE `id` = " . $_USER->data('data')->gang);
	$gang_info = $gang_info_query->first();

	$gang_defence =
		($gang_info->wep_1 * $_GENERAL->settings('settings_game','WEP_1_DEF')) + 
		($gang_info->wep_2 * $_GENERAL->settings('settings_game','WEP_2_DEF')) +
		($gang_info->wep_3 * $_GENERAL->settings('settings_game','WEP_3_DEF')) +
		($gang_info->wep_4 * $_GENERAL->settings('settings_game','WEP_4_DEF')) +
		($gang_info->wep_5 * $_GENERAL->settings('settings_game','WEP_5_DEF')) +
		($gang_info->wep_6 * $_GENERAL->settings('settings_game','WEP_6_DEF'));

	$gang_offence = 
		($gang_info->wep_1 * $_GENERAL->settings('settings_game','WEP_1_OFE')) + 
		($gang_info->wep_2 * $_GENERAL->settings('settings_game','WEP_2_OFE')) +
		($gang_info->wep_3 * $_GENERAL->settings('settings_game','WEP_3_OFE')) +
		($gang_info->wep_4 * $_GENERAL->settings('settings_game','WEP_4_OFE')) +
		($gang_info->wep_5 * $_GENERAL->settings('settings_game','WEP_5_OFE')) +
		($gang_info->wep_6 * $_GENERAL->settings('settings_game','WEP_6_OFE'));

	DB::getInstance()->update('gang', $gang_info->id, array('defence' => $gang_defence, 'offence' => $gang_offence));

	$gang_info_query = DB::getInstance()->query("SELECT * FROM `gang` WHERE `id` = " . $_USER->data('data')->gang);
	$gang_info = $gang_info_query->first();

	$GANG_ACCESS = json_decode($gang_info->access, true);

	$gang_member_query = DB::getInstance()->query("SELECT * FROM `gang_members` WHERE `user_id` = " . $_USER->data()->id);
	$gang_member = $gang_member_query->first();

	if (Input::get('page') == "forum") {
		include("m_gang_forum.php");
	} else if (Input::get('page') == "market") {
		include("m_gang_market.php");
	} else if (Input::get('page') == "members") {
		include("m_gang_members.php");
	} else if (Input::get('page') == "logs") {
		include("m_gang_logs.php");
	} else if (Input::get('page') == "building") {
		include("m_gang_building.php");
	} else if (Input::get('page') == "management") {
		include("m_gang_management.php");
	} else {
		$gang_leader_query = DB::getInstance()->query("SELECT `username` FROM `users` WHERE `id` = " . $gang_info->leader);
		$gang_leader = $gang_leader_query->first();

		$gang_members_query = DB::getInstance()->query("SELECT * FROM `gang_members` WHERE `gang_id` = " . $gang_info->id);
		$gang_members_count = $gang_members_query->count();

		$gang_building_query = DB::getInstance()->query("SELECT * FROM `gang_buildings` WHERE `id` = " . $gang_info->building_level);
		$gang_building = $gang_building_query->first();

		$gang_logo = (empty($gang_info->logo_url) === false) ? $gang_info->logo_url : 'css/default/images/gang.png';

		if(Input::exists()) {
			if(Token::check(Input::get('token'), 'GIVE_MONEY') ) {
				$value = (int)Input::get('value');
				if ($value < 1 or empty($value) === true) {
					$_GENERAL->addError("Palun sisestage kui palju te soovite raha annetada.");
				}

				if ($_USER->data('data')->money < $value) {
					$_GENERAL->addError("Teil ei ole piisavalt sularaha.");
				}

				if (empty($_GENERAL->errors()) === true) {
					$_USER->update(array(
							'money' => $_USER->data('data')->money - $value
						),$_USER->data()->id, 'users_data');

					DB::getInstance()->update('gang', $gang_info->id, array('money' => $gang_info->money + $value));
					DB::getInstance()->update('gang_members', $gang_member->id, array('money' => $gang_member->money + $value));

					$log_body = '<font color="green">Kasutaja <b><a href="p.php?p=profile&user='.$_USER->data()->username.'">'.$_USER->data()->username.'</a></b> annetas kambale <b>'.$_GENERAL->format_number($value).'</b> raha.</font>';

					$gang_logs_fields = array(
						'gang_id' => $gang_info->id,
						'body' => $log_body
						);
					DB::getInstance()->insert('gang_logs', $gang_logs_fields);

					Session::flash('gang', 'Te annetasite kambale raha.');
					Redirect::to('p.php?p=gang');
				}
			} else if(Token::check(Input::get('token'), 'LEAVE') ) {
				if ($_USER->data('data')->gang == 0) {
					$_GENERAL->addError("Te ei ole kambas.");
				} else {
					DB::getInstance()->update('users_data', $_USER->data()->id, array('gang' => 0));
					DB::getInstance()->delete('gang_members', array('user_id', '=', $_USER->data()->id));

					Session::flash('gang', 'Te lahkusite kambast.');
					Redirect::to('p.php?p=gang');
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
		<div id="page">
			<div class="page-title">Peakontor</div>
			<p>
				<table>
					<tr>
						<td width="20%">Kamba nimi:</td>
						<td width="50%"><?php print($gang_info->name);?></td>
						<td width="30%" rowspan="7" align="center">Kamba logo:<br><img src="<?php print($gang_logo);?>" width="100" height="100"></td>
					</tr>
					<tr>
						<td>Kamba juht:</td>
						<td><a href="p.php?p=profile&user=<?php print($gang_leader->username);?>"><?php print($gang_leader->username);?></a></td>
					</tr>
					<tr>
						<td>Kambas liikmeid:</td>
						<td><?php print($_GENERAL->format_number($gang_members_count));?> / <?php print($_GENERAL->format_number($gang_building->max_members));?></td>
					</tr>
					<tr>
						<td>Skoor:</td>
						<td><?php print($_GENERAL->format_number($gang_info->score));?></td>
					</tr>
					<tr>
						<td>Kamba kaitse:</td>
						<td><?php print($_GENERAL->format_number($gang_info->defence));?></td>
					</tr>
					<tr>
						<td>Kamba rünne:</td>
						<td><?php print($_GENERAL->format_number($gang_info->offence));?></td>
					</tr>
					<tr>
						<td>Raha:</td>
						<td><?php print($_GENERAL->format_number($gang_info->money));?></td>
					</tr>
					<tr>
						<td>Punkte:</td>
						<td><?php print($_GENERAL->format_number($gang_info->points));?></td>
					</tr>
					<tr>
						<td>Kaitse prille:</td>
						<td><?php print($_GENERAL->format_number($gang_info->wep_1));?></td>
					</tr>
					<tr>
						<td>Nukke:</td>
						<td><?php print($_GENERAL->format_number($gang_info->wep_2));?></td>
					</tr>
					<tr>
						<td>Kuuliveste:</td>
						<td><?php print($_GENERAL->format_number($gang_info->wep_3));?></td>
					</tr>
					<tr>
						<td>Tavalisi relvi:</td>
						<td><?php print($_GENERAL->format_number($gang_info->wep_4));?></td>
					</tr>
					<tr>
						<td>Kilpe:</td>
						<td><?php print($_GENERAL->format_number($gang_info->wep_5));?></td>
					</tr>
					<tr>
						<td>Automaat relvi:</td>
						<td><?php print($_GENERAL->format_number($gang_info->wep_6));?></td>
					</tr>
					<tr>
						<td>Kamba hoone:</td>
						<td><?php print($gang_building->name);?></td>
					</tr>
					<?php 
					if ($_USER->data()->id != $gang_info->leader) {
					?>
					<tr>
						<td></td>
						<td></td>
						<td align="center">
							<form action="p.php?p=gang" method="POST">
								<input type="hidden" name="token" value="<?php echo Token::generate('LEAVE'); ?>">
								<input type="submit" value="Lahku kambast">
							</form>
						</td>
					</tr>
					<?php
					}
					?>
				</table>
			</p>
		</div>
		<div id="page">
			<div class="page-title">Anneta kambale raha</div>
			<form action="p.php?p=gang" method="POST">
				<table>
					<tr>
						<td width="20%">Palju raha annetad</td>
						<td width="80%">
							<input type="text" name="value" autocomplete="off">
							<input type="hidden" name="token" value="<?php echo Token::generate('GIVE_MONEY'); ?>">
							<input type="submit" value="Anneta raha">
						</td>
					</tr>
				</table>
			</form>
		</div>
<?php
	}
} else {

?>

	<div id="page">
		<div class="page-title">Kamp</div>
		<p>
		<?php
		if (empty($_GENERAL->errors()) === false) {
			print($_GENERAL->output_errors());
		}

		if(Session::exists('gang')) {
			$_GENERAL->addOutSuccess(Session::flash('gang'));
			print($_GENERAL->output_success());
		}
		?>
			<table>
				<tr valign="top">
					<td width="20%"><img src="css/default/images/gang.png" width="100" height="100"></td>
					<td width="80%">
						Kamp on koht kuhu te saate kutsuda oma sõbrad ühinema.<br> 
						Kambas olles te ei saa üksteist rünnata ja te saate kambast lisa kaitset ja rünnet.<br>
						Kamba loomine maksab 500 000 ja kambaga liitumine maksab 100 000.
					</td>
				</tr>
			</table>
		</p>
	</div>
	<div id="page">
		<div class="page-title">Kamba loomine</div>
		<form action="p.php?p=gang" method="POST">
			<table>
				<tr>
					<td width="20%">Uue kamba nimi:</td>
					<td width="80%"><input type="text" name="name" placeholder="Kamba nimi" autocomplete="off"></td>
				</tr>
				<tr>
					<td>Uue kamba parool</td>
					<td><input type="password" name="password" placeholder="Kamba parool"></td>
				</tr>
				<tr>
					<td></td>
					<td>
						<input type="hidden" name="token" value="<?php echo Token::generate('NEW_GANG'); ?>">
						<input type="submit" value="Loo uus kamp">
					</td>
				</tr>
			</table>
		</form>
	</div>
	<div id="page">
		<div class="page-title">Kambaga liitumine</div>
		<form action="p.php?p=gang" method="POST">
			<table>
				<tr>
					<td width="20%">Kamba nimi:</td>
					<td width="80%"><input type="text" name="name" placeholder="Kamba nimi" autocomplete="off"></td>
				</tr>
				<tr>
					<td>Kamba parool</td>
					<td><input type="password" name="password" placeholder="Kamba parool"></td>
				</tr
				<tr>
					<td></td>
					<td>
						<input type="hidden" name="token" value="<?php echo Token::generate('JOIN_GANG'); ?>">
						<input type="submit" value="Liitu kambaga">
					</td>
				</tr>
			</table>
		</form>
	</div>
<?php
}
?>

<?php
include("includes/overall/footer.php");
