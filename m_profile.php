<?php
/*
 * Name: FreeLand v2
 * Author: Marko Murumaa
 * Created: July 2015
 * Website: www.freelandplay.eu
*/

$_lir = true;
require_once("core/init.php");

if(!$username = Input::get('user')) {
	Redirect::to('p.php?p=home');
} else {
	$user = new User($user = $username, $world = $_WORLD);
	if(!$user->exists()) {
		Redirect::to('p.php?p=home');
	}

	$fight_turns = 10;
	$fight_max_money = 150000000;
	
	$ROB_TURNS = $_GENERAL->settings('settings_game','ROB_TURNS');
	$ROB_TURNS_GANG = $_GENERAL->settings('settings_game','ROB_TURNS_GANG');
	$ROB_POINTS_GANG_EARN = $_GENERAL->settings('settings_game','ROB_POINTS_GANG_EARN');
	$ROB_defence_man_protected = $_GENERAL->settings('settings_game','ROB_DEFMAN_PROTECTED');

	$group_query = DB::getInstance(1)->get('groups', array('id','=',$user->data()->groups));
	$status = '<font color="'.$group_query->first()->color.'">'.$group_query->first()->name.'</font>';
	$group_id = $group_query->first()->id;

	$sponsor = ($user->data('data')->toetaja == 1) ? '<font color="green">Jah</font>' : '<font color="red">Ei</font>';

	$house_lvl_query = DB::getInstance()->get('house_levels', array('id','=',$user->data('house')->house_level));
	$house_lvl = $house_lvl_query->first();

	if ($user->data('data')->gang != 0) {
		$gang_info_query = DB::getInstance()->get('gang', array('id','=',$user->data('data')->gang));
		$gang_info = $gang_info_query->first();

		$gang_name = '<a href="p.php?p=gangs&gang='.$gang_info->id.'">'.$gang_info->name.'</a>';
		if (empty($gang_info->logo_url) === false) {
			$gang_logo = $gang_info->logo_url;
		} else {
			$gang_logo = 'css/default/images/gang.png';
		}
	} else {
		$gang_name = '<i>Puudub</i>';
		$gang_logo = 'css/default/images/gang.png';
	}

	if ($_USER->data('data')->gang != 0) {
		$robbery = '';
	} else {
		$robbery = ' disabled';
	}

	$rob_energy = 1;

	$fill_fight = ($_USER->data('data')->money > $fight_max_money) ? $fight_max_money : $_USER->data('data')->money;

if(Input::exists()) {
	if(Token::check(Input::get('token'), 'ROB') ) {
		$value = round(Input::get('value'));
		$gang_help = (Input::get('gang_help') == 1) ? true : false ;

		$protect_time = time() - (60 * 60 * 24 * 2);
		$last_rob_time = date("Y-m-d H:i:s", (time() - 60 * 15));

		$robs_count_query = DB::getInstance()->query("SELECT * FROM `user_logs` WHERE `user_id` = ".$user->data()->id." AND `type` = 2 AND `date` > '".$last_rob_time."'");
		$robs_count = $robs_count_query->count();

		if ($gang_help === true) {
			$turns = $ROB_TURNS + $ROB_TURNS_GANG;
		} else {
			$turns = $ROB_TURNS;
		}
		if (strtotime($user->data()->joined) > $protect_time) {
			$_GENERAL->addError("Kasutaja on alles registreerinud ja on algaja kaitse all 48h.");
		}

		if ($group_id == 3) {
			$_GENERAL->addError("Blokeerituid kasutajaid ei saa röövida.");
		}

		if ($group_id == 2) {
			$_GENERAL->addError("Teil ei ole võimalik röövida mängu omaniku.");
		}

		if ($value < 1 or empty($value) === true) {
			$_GENERAL->addError("Kirjuta mitu ründajat sa röövima saadad.");
		}

		if ($user->data()->id == $_USER->data()->id) {
			$_GENERAL->addError("Sa ei saa ennast röövida.");
		}

		if ($value > $_USER->data('house')->offence_man) {
			$_GENERAL->addError("Teil ei ole niipalju ründajaid.");
		}

		if ($robs_count >= 5) {
			$_GENERAL->addError("Ühte kasutajat saab röövida 15 minuti jooksul 5 korda.");
		}

		if ($user->data('data')->score < $_USER->data('data')->score / 100) {
			$_GENERAL->addError("Sa ei saa röövida endast 100 korda nõrgemat.");
		}

		if ($_USER->data('data')->turns < $turns) {
			$_GENERAL->addError("Teil ei ole piisavalt käike.");
		}

		if ($_USER->data('data')->gang == 0 and $gang_help === true) {
			$_GENERAL->addError("Te ei ole kambas, et kasutada kamba ründe abi.");
		}

		if ($user->data('data')->gang != 0) {
			if ($user->data('data')->gang == $_USER->data('data')->gang) {
				$_GENERAL->addError("Enda kamba liiget ei saa röövida.");
			}
		}

		if (empty($_GENERAL->errors()) === true) {

			$weapons_protected = ($user->data('house')->defence_man + $user->data('house')->offence_man) * 2;
			$defence_man_protected = $user->data('house')->defence_level * $user->data('house')->defence_level * $ROB_defence_man_protected;

			$defence_man_not = ($user->data('house')->defence_man > $defence_man_protected) ? $user->data('house')->defence_man - $defence_man_protected : 0;

			$weapon_1_not = ($user->data('house')->wep_1 > $weapons_protected) ? $user->data('house')->wep_1 - $weapons_protected : 0;
			$weapon_2_not = ($user->data('house')->wep_2 > $weapons_protected) ? $user->data('house')->wep_2 - $weapons_protected : 0;
			$weapon_3_not = ($user->data('house')->wep_3 > $weapons_protected) ? $user->data('house')->wep_3 - $weapons_protected : 0;
			$weapon_4_not = ($user->data('house')->wep_4 > $weapons_protected) ? $user->data('house')->wep_4 - $weapons_protected : 0;
			$weapon_5_not = ($user->data('house')->wep_5 > $weapons_protected) ? $user->data('house')->wep_5 - $weapons_protected : 0;
			$weapon_6_not = ($user->data('house')->wep_6 > $weapons_protected) ? $user->data('house')->wep_6 - $weapons_protected : 0;

			if ($gang_help === true) {
				$offence = $_USER->user_offence_i();
			} else {
				$offence = $_USER->user_offence_i() - $_USER->user_offence_i('gang');
			}

			$user->user_defence();
			$defence = $user->user_defence_i();

			if ($offence > $defence) {
				$win_percent = 100 - (($defence / $offence) * 100);
				$win_money = round(($user->data('data')->money / 100) * $win_percent);

				$defence_man_lose = ceil( ($defence_man_not / 100) * $win_percent);

				$weapon_1_lose = ceil( ($weapon_1_not / 100) * $win_percent);
				$weapon_2_lose = ceil( ($weapon_2_not / 100) * $win_percent);
				$weapon_3_lose = ceil( ($weapon_3_not / 100) * $win_percent);
				$weapon_4_lose = ceil( ($weapon_4_not / 100) * $win_percent);
				$weapon_5_lose = ceil( ($weapon_5_not / 100) * $win_percent);
				$weapon_6_lose = ceil( ($weapon_6_not / 100) * $win_percent);


				$_USER->update(array(
						'money' => $_USER->data('data')->money + $win_money,
						'turns' => $_USER->data('data')->turns - $turns
					),$_USER->data()->id, 'users_data');

				$_USER->update(array(
						'wep_1' => $_USER->data('house')->wep_1 + $weapon_1_lose,
						'wep_2' => $_USER->data('house')->wep_2 + $weapon_2_lose,
						'wep_3' => $_USER->data('house')->wep_3 + $weapon_3_lose,
						'wep_4' => $_USER->data('house')->wep_4 + $weapon_4_lose,
						'wep_5' => $_USER->data('house')->wep_5 + $weapon_5_lose,
						'wep_6' => $_USER->data('house')->wep_6 + $weapon_6_lose
					),$_USER->data()->id, 'users_data_house');


				$user->update(array(
						'money' => $user->data('data')->money - $win_money
					),$user->data()->id, 'users_data');

				$user->update(array(
						'defence_man' => $user->data('house')->defence_man - $defence_man_lose,
						'wep_1' => $user->data('house')->wep_1 - $weapon_1_lose,
						'wep_2' => $user->data('house')->wep_2 - $weapon_2_lose,
						'wep_3' => $user->data('house')->wep_3 - $weapon_3_lose,
						'wep_4' => $user->data('house')->wep_4 - $weapon_4_lose,
						'wep_5' => $user->data('house')->wep_5 - $weapon_5_lose,
						'wep_6' => $user->data('house')->wep_6 - $weapon_6_lose
					),$user->data()->id, 'users_data_house');
				
				if ($_USER->data('data')->gang != 0) {
					$out_msg = '<b>Kamp teenis selle rööviga punkte.</b>';

					$gang_i_query = DB::getInstance()->get('gang', array('id','=',$_USER->data('data')->gang));
					$gang_i = $gang_i_query->first();

					$gang_member_query = DB::getInstance()->get('gang_members', array('user_id','=',$_USER->data()->id));
					$gang_member = $gang_member_query->first();

					DB::getInstance()->update('gang', $gang_i->id, array('points' => $gang_i->points + $ROB_POINTS_GANG_EARN));
					DB::getInstance()->update('gang_members', $gang_member->id, array('points' => $gang_member->points + $ROB_POINTS_GANG_EARN));

					$log_body = '<font color="green">
						Kasutaja <b><a href="p.php?p=profile&user='.$_USER->data()->username.'">'.$_USER->data()->username.'</a></b>
						Röövis kasutajat <b><a href="p.php?p=profile&user='.$user->data()->username.'">'.$user->data()->username.'</a></b> ja õnnestus.<br>
						Kamp teenis punkte.</font>';

					$gang_logs_fields = array(
						'gang_id' => $gang_i->id,
						'body' => $log_body
						);

					DB::getInstance()->insert('gang_logs', $gang_logs_fields);
				} else {
					$out_msg = '';
				}


				$log_msg = '<font color="red">
					Kasutaja <a href=p.php?p=profile&user='.$_USER->data()->username.'>'.$_USER->data()->username.'</a> 
					Röövis teilt:<br>
					Raha: '.$_GENERAL->format_number($win_money).',
					Kaitse prille: '.$_GENERAL->format_number($weapon_1_lose).',
					Nukke: '.$_GENERAL->format_number($weapon_2_lose).',
					Kuuliveste: '.$_GENERAL->format_number($weapon_3_lose).',
					Tavalisi relvi: '.$_GENERAL->format_number($weapon_4_lose).',
					Kilpe: '.$_GENERAL->format_number($weapon_5_lose).',
					Automaat relvi: '.$_GENERAL->format_number($weapon_6_lose).' ja 
					tappis teil kaitsemehi: '.$_GENERAL->format_number($defence_man_lose).'.</font>';

				DB::getInstance()->insert('user_logs', array(
				'user_id' => $user->data()->id,
				'type' => 2,
				'body' => $log_msg,
				'active' => 1
				));

				$rob_output = 'Te rööv oli edukas ja te rööviste:<br>
					Raha: '.$_GENERAL->format_number($win_money).',<br>
					Kaitse prille: '.$_GENERAL->format_number($weapon_1_lose).',
					Nukke: '.$_GENERAL->format_number($weapon_2_lose).',
					Kuuliveste: '.$_GENERAL->format_number($weapon_3_lose).',
					Tavalisi relvi: '.$_GENERAL->format_number($weapon_4_lose).',
					Kilpe: '.$_GENERAL->format_number($weapon_5_lose).',
					Automaat relvi: '.$_GENERAL->format_number($weapon_6_lose).'<br>
					Tapsite vastasel kaitsemehi: '.$_GENERAL->format_number($defence_man_lose).'.<br>'.$out_msg;

				Session::flash('profile', $rob_output);
				Redirect::to('p.php?p=profile&user='.$user->data()->username);
			} else {
				$lose_percent = 100 - ($offence / $defence * 100);
				$offence_man_lose = ceil(($value / 100) * $lose_percent);

				$_USER->update(array(
						'turns' => $_USER->data('data')->turns - $turns
					),$_USER->data()->id, 'users_data');

				$_USER->update(array(
						'offence_man' => $_USER->data('house')->offence_man - $offence_man_lose
					),$_USER->data()->id, 'users_data_house');

				if ($_USER->data('data')->gang != 0) {
					$out_gang = '<br><b>Kamp kaotas punkte selle rööviga.</b>';

					$gang_i_query = DB::getInstance()->get('gang', array('id','=',$_USER->data('data')->gang));
					$gang_i = $gang_i_query->first();

					$gang_member_query = DB::getInstance()->get('gang_members', array('user_id','=',$_USER->data()->id));
					$gang_member = $gang_member_query->first();

					DB::getInstance()->update('gang', $gang_i->id, array('points' => $gang_i->points - $ROB_POINTS_GANG_EARN));
					DB::getInstance()->update('gang_members', $gang_member->id, array('points' => $gang_member->points - $ROB_POINTS_GANG_EARN));

					$log_body = '<font color="red">
						Kasutaja <b><a href="p.php?p=profile&user='.$_USER->data()->username.'">'.$_USER->data()->username.'</a></b>
						Röövis kasutajat <b><a href="p.php?p=profile&user='.$user->data()->username.'">'.$user->data()->username.'</a></b> ja ebaõnnestus.<br>
						Kamp kaotas punkte.</font>';

					$gang_logs_fields = array(
						'gang_id' => $gang_i->id,
						'body' => $log_body
						);

					DB::getInstance()->insert('gang_logs', $gang_logs_fields);
				} else {
					$out_gang = '';
				}

				$log_msg = '<font color="green">
				Kasutaja <a href=p.php?p=profile&user='.$_USER->data()->username.'>'.$_USER->data()->username.'</a> 
				üritas teid röövida, kuid kaotas '.$_GENERAL->format_number($offence_man_lose).' ründajat.</font>';

				DB::getInstance()->insert('user_logs', array(
				'user_id' => $user->data()->id,
				'type' => 3,
				'body' => $log_msg,
				'active' => 1
				));

				Session::flash('profileE', "Vastane oli tugevam ja sa kaotasid ".$_GENERAL->format_number($offence_man_lose)." ründajat.".$out_gang);
				Redirect::to('p.php?p=profile&user='.$user->data()->username);
			}

		}

	} else if(Token::check(Input::get('token'), 'FIGHT') ) {
		$value = round(Input::get('value'));
		$cur_fight_count_query = DB::getInstance()->query("SELECT * FROM `fight_requests` WHERE `type` = 1 AND `user_id` = ".$_USER->data()->id." AND `status` = 0");

		if ($value < 1 or empty($value) === true) {
			$_GENERAL->addError("Palun kirjutage kui suure panuse te teete.");
		}

		if ($_USER->data()->id == $user->data()->id) {
			$_GENERAL->addError("Endale ei saa väljakutset esitada.");
		}

		if ($cur_fight_count_query->count() >= 5) {
			$_GENERAL->addError("Kokku saab esitada 5 kutset.");
		}

		if ($_USER->data('data')->money < $value) {
			$_GENERAL->addError("Teil ei ole piisavalt sularaha.");
		}

		if ($_USER->data('data')->turns < $fight_turns) {
			$_GENERAL->addError("Teil ei ole piisavalt käike.");
		}

		if ($value > $fight_max_money) {
			$_GENERAL->addError("Maksimaalne panus on ".$_GENERAL->format_number($fight_max_money).".");
		}

		if (empty($_GENERAL->errors()) === true) {
			$_USER->update(array(
					'money' => $_USER->data('data')->money - $value,
					'turns' => $_USER->data('data')->turns - $fight_turns
				),$_USER->data()->id, 'users_data');

			$fight_fields = array(
				'type' => 1,
				'user_id' => $_USER->data()->id,
				'o_user_id' => $user->data()->id,
				'money' => $value
				);
			DB::getInstance()->insert('fight_requests', $fight_fields);

			Session::flash('profile', "Te esitasite edukalt vastasele kaklemise väljakutse.");
			Redirect::to('p.php?p=profile&user='.$user->data()->username);
		}
	}
}

include("includes/overall/header.php");
?>

<div id="page">
	<div class="page-title">Kasutaja profiil</div>
	<p>
	<?php
	if (Session::exists('profileE')) {
		$_GENERAL->addError(Session::flash('profileE'));
	}

	if (empty($_GENERAL->errors()) === false) {
		print($_GENERAL->output_errors());
	}

	if(Session::exists('profile')) {
		$_GENERAL->addOutSuccess(Session::flash('profile'));
		print($_GENERAL->output_success());
	}
	?>	
		<table>
			<tr>
				<td width="20%">Kasutajanimi: </td>
				<td width="50%"><?php print(escape($user->data()->username));?> [<a href="p.php?p=mail&new=1&user=<?php print(escape($user->data()->username)); ?>">Saada kiri</a>]</td>
				<td width="30%" rowspan="7" align="center">Kamba logo:<br><img src="<?php print($gang_logo);?>" width="100" height="100"></td>
			</tr>
			<tr>
				<td>Skoor:</td>
				<td><?php print($_GENERAL->format_number($user->data('data')->score));?></td>
			</tr>
			<tr>
				<td>Sularaha:</td>
				<td><?php print($_GENERAL->format_number($user->data('data')->money));?></td>
			</tr>
			<tr>
				<td>Haridus:</td>
				<td><?php print($_GENERAL->format_number($user->data('data')->education));?></td>
			</tr>
			<tr>
				<td>Elamu:</td>
				<td><?php print($house_lvl->name);?></td>
			</tr>
			<tr>
				<td>Kamp:</td>
				<td><?php print($gang_name);?></td>
			</tr>
			<tr>
				<td>Staatus</td>
				<td><?php print($status);?></td>
			</tr>
			<?php
			if ($_WORLD == 1) {
			?>
			<tr>
				<td>Toetaja:</td>
				<td><?php print($sponsor);?></td>
			</tr>
			<?php
			}
			?>
			<tr>
				<td>Viimati aktiivne:</td>
				<td><?php print($user->data()->last_active);?></td>
			</tr>
			<tr>
				<td>Registreeris:</td>
				<td><?php print($user->data()->joined);?></td>
			</tr>
		</table>
	</p>
</div>
	<?php
	if ($user->data()->groups == 2) {
	?>
	<div id="page">
		<div class="page-title">Kasutaja info</div>
		<p>
			Email: <?php print($_GENERAL->settings('settings_game','GAME_EMAIL'));?>
		</p>
	</div>
	<?php
	} else if ($user->data()->groups == 3) {
	?>
	<div id="page">
		<div class="page-title">Blokeerimise info</div>
		<p>
			<?php print($user->data()->ban_text);?>
		</p>
	</div>
	<?php
	} else {
	?>
	<div id="page">
		<div class="page-title">Röövi kasutajat</div>
		<p>
			<form action="p.php?p=profile&user=<?php print(escape($user->data()->username));?>" method="POST">
				<table>
					<tr>
						<td width="30%">Teie isiklik rünne</td>
						<td width="70%"><?php print($_GENERAL->format_number($_USER->user_offence_i() - $_USER->user_offence_i('gang')));?></td>
					</tr>
					<tr>
						<td>Teil on ründajaid:</td>
						<td><?php print($_GENERAL->format_number($_USER->data('house')->offence_man));?></td>
					</tr>
					<tr>
						<td>Käike vaja röövimiseks</td>
						<td><?php print($_GENERAL->format_number($ROB_TURNS));?></td>
					</tr>
					<tr>
						<td>&nbsp;</td>
						<td>&nbsp;</td>
					</tr>
					<tr>
						<td>
							<input type="checkbox" name="gang_help" value="1" <?php print($robbery);?> >
							Kasuta kamba abi
						</td>
						<td>Vajab <?php print($_GENERAL->format_number($ROB_TURNS_GANG));?> lisa käiku. [Kamba rünne: <?php print($_GENERAL->format_number($_USER->user_offence_i('gang')));?>]</td>
					</tr>
					<tr>
						<td>Mitu ründajat saadad röövima:</td>
						<td>
							<input type="text" name="value" id="rob_fill" autocomplete="off">
							<img onclick="autoFill(rob_fill, <?php print($_USER->data('house')->offence_man);?>)" src="css/default/images/icons/autofill.png" alt="autofill" width="16" height="16">
							<input type="hidden" name="token" value="<?php echo Token::generate('ROB'); ?>">
							<input type="submit" value="Röövi kasutajat">
						</td>
					</tr>
				</table>
			</form>
		</p>
	</div>

	<div id="page">
		<div class="page-title">Esita tänava kakluse väljakutse</div>
		<p>
			<form action="p.php?p=profile&user=<?php print(escape($user->data()->username));?>" method="POST">
				<table>
					<tr>
						<td width="30%">Maksimaalne panus:</td>
						<td width="70%"><?php print($_GENERAL->format_number($fight_max_money));?></td>
					</tr>
					<tr>
						<td>Kui palju panustad:</td>
						<td>
							<input type="text" name="value" id="fight_fill" autocomplete="off">
							<img onclick="autoFill(fight_fill, <?php print($fill_fight);?>)" src="css/default/images/icons/autofill.png" alt="autofill" width="16" height="16">
							<input type="hidden" name="token" value="<?php echo Token::generate('FIGHT'); ?>">
							<input type="submit" value="Esita väljakutse">
						</td>
					</tr>
				</table>
			</form>
		</p>
	</div>

	<?php
	}
}
include("includes/overall/footer.php");
