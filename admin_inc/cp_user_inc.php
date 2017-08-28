<?php
/*
 * Name: FreeLand v2
 * Author: Marko Murumaa
 * Created: July 2015
 * Website: www.freelandplay.eu
*/

if (!$_USER->hasPermission('cp_user_management')) {
	Redirect::to('p.php?p=cpanel');
	exit();
}

if(Input::exists()) {
	if(Token::check(Input::get('token')) ) {
		if (empty(Input::get('username')) === false) {
			Redirect::to('p.php?p=cpanel&cp=user&search='.Input::get('username'));
		}
	}
}

if (empty(Input::get('search')) === false) {
	$search = Input::get('search');
} else {
	$search = null;
}

$user_query =  DB::getInstance()->query("SELECT * FROM users WHERE `username` LIKE ? ORDER BY `id` ASC", ['%' . $search . '%']);
foreach ($user_query->results() as $usr) {

	$cp_group_query = DB::getInstance()->get('groups', array('id','=',$usr->groups));
	$cp_status = '<font color="'.$cp_group_query->first()->color.'">'.$cp_group_query->first()->name.'</font>';

	$output_line .= '
		<tr>
			<td align="center">'.$usr->id.'</td>
			<td><a href="p.php?p=profile&user='.$usr->username.'">'.$usr->username.'</a></td>
			<td align="center">'.$cp_status.'</td>
			<td align="center"><a href="p.php?p=cpanel&cp=user&manage='.$usr->username.'#bot">Manage</a> | <a href="p.php?p=cpanel&cp=user&mute='.$usr->username.'#bot">Add mute</a></td>
		</tr>
	';
}
?>
<h1>User management</h1>
<p>
	<table>
		<tr>
			<td width="50%">
				<form action="p.php?p=cpanel&cp=user" method="POST">
					<ul>
						<li>Search by username:</li>
						<li><input type="text" name="username"></li>
						<li>
							<input type="hidden" name="token" value="<?php echo Token::generate(); ?>">
							<input type="submit" value="Search">
						</li>
					</ul>
				</form>
			</td>
			<td width="50%"></td>
		</tr>
	</table>
	<table>
		<tr>
			<th width="10%">Id</th>
			<th width="40%">Username</th>
			<th width="30%">Status</th>
			<th width="20%">Manage</th>
		</tr>
		<?php print($output_line);?>
	</table>
</p>
<?php

if (empty(Input::get('manage')) === false) {

	$manage_user = New User(Input::get('manage'));
	if(!$manage_user->exists()) {
		Redirect::to('p.php?p=cpanel&cp=user');
	}

	if(Input::exists()) {
		if(Token::check(Input::get('token'), 'MANAGE_USER') ) {

			if (empty($_GENERAL->errors()) === true) {

				$manage_user->update(array(
					'firstname' => Input::get('firstname'),
					'lastname' => Input::get('lastname'),
					'birth' => Input::get('birth'),
					'gender' => Input::get('gender'),
					'groups' => Input::get('group'),
					'email' => Input::get('email'),
					'chat_mute' => Input::get('muted')
				), $manage_user->data()->id);

				$manage_user->update(array(
					'credit' => Input::get('credit'),
					'score' => Input::get('score'),
					'money' => Input::get('cash'),
					'money_bank' => Input::get('moneybank'),
					'bank_level' => Input::get('banklevel'),
					'energy' => Input::get('energy'),
					'energy_max' => Input::get('energymax'),
					'food' => Input::get('food'),
					'education' => Input::get('education'),
					'strength' => Input::get('strength'),
					'stamina' => Input::get('stamina'),
					'speed' => Input::get('speed')
				), $manage_user->data()->id, 'users_data');

				$manage_user->update(array(
					'house_level' => Input::get('houselevel'),
					'weed' => Input::get('weed'),
					'alcohol' => Input::get('alcohol'),
					'cigarettes' => Input::get('cigarettes'),
					'weed_seed' => Input::get('weedseed'),
					'sugar' => Input::get('sugar'),
					'cereals' => Input::get('cereals'),
					'yeast' => Input::get('yeast'),
					'defence_level' => Input::get('defencelevel'),
					'defman_1' => Input::get('defman1'),
					'defman_2' => Input::get('defman2'),
					'defman_3' => Input::get('defman3'),
					'offence_level' => Input::get('offencelevel'),
					'ofeman_1' => Input::get('ofeman1'),
					'ofeman_2' => Input::get('ofeman2'),
					'ofeman_3' => Input::get('ofeman3')
				), $manage_user->data()->id, 'users_data_house');

				Session::flash('cpanel', 'User is successfully changed.');
				Redirect::to('p.php?p=cpanel&cp=user&manage='.Input::get('manage').'#bot');
			}
		}
	}

	$group_query = DB::getInstance()->query('SELECT * FROM groups');
	foreach ($group_query->results() as $group) {
		if ($group->id == $manage_user->data()->groups) {
			$g_select = ' selected';
		} else {
			$g_select = '';
		}
		$group_option .= '<option value="'.$group->id.'"'.$g_select.'>'.$group->name.'</option>';
	}

	$gender_men = ($manage_user->data()->gender == 1) ? ' selected' : '';
	$gender_women = ($manage_user->data()->gender == 2) ? ' selected' : '';
	$is_muted = ($manage_user->data()->chat_mute == 1) ? ' selected' : '';
	?>
	<h1 id="bot">Manage user</h1>
	<p>
	<?php
	if (empty($_GENERAL->errors()) === false) {
		print($_GENERAL->output_errors());
	}

	if(Session::exists('cpanel')) {
		$_GENERAL->addOutSuccess(Session::flash('cpanel'));
		print($_GENERAL->output_success());
	}
	?>
		<form action="p.php?p=cpanel&cp=user&manage=<?php print(Input::get('manage'));?>" method="POST">
			<ul>
				<li><b>Personal info</b></li>
				<li>Username:<br><input type="text" value="<?php print($manage_user->data()->username);?>" disabled></li>
				<li>First name:<br><input type="text" name="firstname" value="<?php print($manage_user->data()->firstname);?>"></li>
				<li>Last name:<br><input type="text" name="lastname" value="<?php print($manage_user->data()->lastname);?>"></li>
				<li>Birth:<br><input type="text" name="birth" value="<?php print($manage_user->data()->birth);?>"></li>
				<li>Gender:<br>
					<select name="gender">
						<option value="1"<?php print($gender_men);?>>Male</option>
						<option value="2"<?php print($gender_women);?>>Female</option>
					</select>
				</li>
				<li>Email:<br><input type="text" name="email" value="<?php print($manage_user->data()->email);?>"></li>
				<li>Registered:<br><input type="text" value="<?php print($manage_user->data()->joined);?>" disabled></li>
				<li>Status:<br>
					<select name="group">
						<?php print($group_option);?>
					</select>
				</li>
				<li>Muted:<br>
					<select name="muted">
						<option value="0">NO</option>
						<option value="1"<?php print($is_muted);?>>YES</option>
					</select>
				</li>
				<li><br><b>User info</b></li>
				<li>Credit:<br><input type="text" name="credit" value="<?php print($manage_user->data('data')->credit);?>"></li>
				<li>Score:<br><input type="text" name="score" value="<?php print($manage_user->data('data')->score);?>"></li>
				<li>Cash:<br><input type="text" name="cash" value="<?php print($manage_user->data('data')->money);?>"></li>
				<li>Money in bank:<br><input type="text" name="moneybank" value="<?php print($manage_user->data('data')->money_bank);?>"></li>
				<li>Bank level:<br><input type="text" name="banklevel" value="<?php print($manage_user->data('data')->bank_level);?>"></li>
				<li>Energy:<br><input type="text" name="energy" value="<?php print($manage_user->data('data')->energy);?>"></li>
				<li>Energy max:<br><input type="text" name="energymax" value="<?php print($manage_user->data('data')->energy_max);?>"></li>
				<li>Education:<br><input type="text" name="education" value="<?php print($manage_user->data('data')->education);?>"></li>
				<li><br><b>House info</b></li>
				<li>House level:<br><input type="text" name="houselevel" value="<?php print($manage_user->data('house')->house_level);?>"></li>
				<li>Food:<br><input type="text" name="food" value="<?php print($manage_user->data('data')->food);?>"></li>
				<li>Weed:<br><input type="text" name="weed" value="<?php print($manage_user->data('house')->weed);?>"></li>
				<li>Illegal alcohol:<br><input type="text" name="alcohol" value="<?php print($manage_user->data('house')->alcohol);?>"></li>
				<li>Illegal cigarettes:<br><input type="text" name="cigarettes" value="<?php print($manage_user->data('house')->cigarettes);?>"></li>
				<li>Weed seeds:<br><input type="text" name="weedseed" value="<?php print($manage_user->data('house')->weed_seed);?>"></li>
				<li>Sugar:<br><input type="text" name="sugar" value="<?php print($manage_user->data('house')->sugar);?>"></li>
				<li>Cereals:<br><input type="text" name="cereals" value="<?php print($manage_user->data('house')->cereals);?>"></li>
				<li>Yeast:<br><input type="text" name="yeast" value="<?php print($manage_user->data('house')->yeast);?>"></li>
				<li><br><b>Gym info</b></li>
				<li>Strength:<br><input type="text" name="strength" value="<?php print($manage_user->data('data')->strength);?>"></li>
				<li>Stamina:<br><input type="text" name="stamina" value="<?php print($manage_user->data('data')->stamina);?>"></li>
				<li>Speed:<br><input type="text" name="speed" value="<?php print($manage_user->data('data')->speed);?>"></li>
				<li><br><b>Defence info</b></li>
				<li>Defence level:<br><input type="text" name="defencelevel" value="<?php print($manage_user->data('house')->defence_level);?>"></li>
				<li><?php print($_GENERAL->settings('settings_game','DEFMAN_1_NAME'));?>:<br><input type="text" name="defman1" value="<?php print($manage_user->data('house')->defman_1);?>"></li>
				<li><?php print($_GENERAL->settings('settings_game','DEFMAN_2_NAME'));?>:<br><input type="text" name="defman2" value="<?php print($manage_user->data('house')->defman_2);?>"></li>
				<li><?php print($_GENERAL->settings('settings_game','DEFMAN_3_NAME'));?>:<br><input type="text" name="defman3" value="<?php print($manage_user->data('house')->defman_3);?>"></li>
				<li><br><b>Offence info</b></li>
				<li>Offence level:<br><input type="text" name="offencelevel" value="<?php print($manage_user->data('house')->offence_level);?>"></li>
				<li><?php print($_GENERAL->settings('settings_game','OFEMAN_1_NAME'));?>:<br><input type="text" name="ofeman1" value="<?php print($manage_user->data('house')->ofeman_1);?>"></li>
				<li><?php print($_GENERAL->settings('settings_game','OFEMAN_2_NAME'));?>:<br><input type="text" name="ofeman2" value="<?php print($manage_user->data('house')->ofeman_2);?>"></li>
				<li><?php print($_GENERAL->settings('settings_game','OFEMAN_3_NAME'));?>:<br><input type="text" name="ofeman3" value="<?php print($manage_user->data('house')->ofeman_3);?>"></li>
				<li>
					<input type="hidden" name="token" value="<?php echo Token::generate('MANAGE_USER'); ?>">
					<input type="submit" value="Edit user">
				</li>
			</ul>
		</form>
	</p>
	<?php
}

if (empty(Input::get('mute')) === false) {
	$mute_user = New User(Input::get('mute'));
	if(!$mute_user->exists()) {
		Redirect::to('p.php?p=cpanel&cp=user');
	}

	$is_mute = ($mute_user->data()->chat_mute == 1) ? 'YES' : 'NO';

	if(Input::exists()) {
		if(Token::check(Input::get('token'), 'MUTE_USER') ) {
			if ($mute_user->data()->chat_mute == 1) {
				$_GENERAL->addError("This user has already muted.");
			}

			if (empty(Input::get('mutetext')) === true) {
				$_GENERAL->addError("Please enter description.");
			}

			if (empty($_GENERAL->errors()) === true) {
				$mute_user->update(array(
					'chat_mute' => 1
				), $mute_user->data()->id);

				$log_msg = '<font color="red">Your account is muted in chat. Reason: '.Input::get('mutetext').'</font>';

				DB::getInstance()->insert('user_logs', array(
				'user_id' => $mute_user->data()->id,
				'type' => 4,
				'body' => $log_msg,
				'active' => 1
				));

				Session::flash('cpanel', 'Mute is successfully added to user.');
				Redirect::to('p.php?p=cpanel&cp=user&mute='.$mute_user->data()->username.'#bot');
			}
		}
	}

	?>
	<h1 id="bot">Add mute to user</h1>
	<p>
	<?php
	if (empty($_GENERAL->errors()) === false) {
		print($_GENERAL->output_errors());
	}

	if(Session::exists('cpanel')) {
		$_GENERAL->addOutSuccess(Session::flash('cpanel'));
		print($_GENERAL->output_success());
	}
	?>
		<form action="p.php?p=cpanel&cp=user&mute=<?php print($mute_user->data()->username);?>#bot" method="POST">
			<ul>
				<li>Username: <b><?php print($mute_user->data()->username);?></b></li>
				<li>Is user currently muted: <b><?php print($is_mute);?></b></li>
				<li>Mute description:</li>
				<li><textarea name="mutetext" cols="100" rows="5"></textarea></li>
				<li>
					<input type="hidden" name="token" value="<?php echo Token::generate('MUTE_USER'); ?>">
					<input type="submit" value="Add mute to user">
				</li>
			</ul>
		</form>
	</p>
	<?php
}
