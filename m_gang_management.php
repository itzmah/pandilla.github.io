<?php
/*
 * Name: FreeLand v2
 * Author: Marko Murumaa
 * Created: July 2015
 * Website: www.freelandplay.eu
*/

if(Input::exists()) {
	if(Token::check(Input::get('token'), 'EDIT_ACCESS') ) {
		if ($_USER->data()->id == $gang_info->leader) {
			if (preg_match("/[^0-9a-z\s-]/i", Input::get('access_1_0')) == 1 or 
				preg_match("/[^0-9a-z\s-]/i", Input::get('access_2_0')) == 1 or 
				preg_match("/[^0-9a-z\s-]/i", Input::get('access_3_0')) == 1 or 
				preg_match("/[^0-9a-z\s-]/i", Input::get('access_4_0')) == 1 or 
				preg_match("/[^0-9a-z\s-]/i", Input::get('access_5_0')) == 1) {
				$_GENERAL->addError("Nimi võib sisaldada ainult tähti ja numbreid.");
			}

			if (empty($_GENERAL->errors()) === true) {

				for ($i=0; $i < 12; $i++) { 
					if ($i == 0) {
						$name = 'name';
					} else if ($i == 1) {
						$name = 'forum_edit';
					} else if ($i == 2) {
						$name = 'forum_delete';
					} else if ($i == 3) {
						$name = 'market_buy';
					} else if ($i == 4) {
						$name = 'market_sell';
					} else if ($i == 5) {
						$name = 'members_drop';
					} else if ($i == 6) {
						$name = 'members_money';
					} else if ($i == 7) {
						$name = 'members_rank';
					} else if ($i == 8) {
						$name = 'building_buy';
					} else if ($i == 9) {
						$name = 'management_info';
					} else if ($i == 10) {
						$name = 'management_logo';
					} else if ($i == 11) {
						$name = 'management_password';
					}

					for ($j=1; $j <= 6; $j++) { 
						if ($j == 6) {
							if ($i == 0) {
								$input = 'Kamba juht';
							} else {
								$input = 1;
							}
						} else if ($i == 0) {
							$input = Input::get('access_'.$j.'_'.$i);
						} else {
							$input = (Input::get('access_'.$j.'_'.$i) == 1) ? 1 : 0;
						}
						$user_access[$j][$name] = $input;
					}
				}

				$jso = json_encode($user_access);

				DB::getInstance()->update('gang', $_USER->data('data')->gang, array('access' => $jso));
				
				Session::flash('gang', 'Te muutsite liikmete õigusi.');
				Redirect::to('p.php?p=gang&page=management');
			}
		}
	} else if(Token::check(Input::get('token'), 'EDIT_INFO') ) {
		if ($GANG_ACCESS[$gang_member->rank_id]['management_info'] == 0) {
			$_GENERAL->addError("Teil ei ole õigusi kamba info muutmiseks.");
		} else {
			$info = Input::get('value');
			DB::getInstance()->update('gang', $gang_info->id, array('info' => $info));
				
			Session::flash('gang', 'Te muutsite kamba infot.');
			Redirect::to('p.php?p=gang&page=management');
		}
	} else if(Token::check(Input::get('token'), 'EDIT_LOGO') ) {
		if ($GANG_ACCESS[$gang_member->rank_id]['management_logo'] == 0) {
			$_GENERAL->addError("Teil ei ole õigusi kamba logo muutmiseks.");
		} else {
			DB::getInstance()->update('gang', $gang_info->id, array('logo_url' => Input::get('value')));
				
			Session::flash('gang', 'Te muutsite kamba logo.');
			Redirect::to('p.php?p=gang&page=management');
		}
	} else if(Token::check(Input::get('token'), 'EDIT_PASSWORD') ) {
		if ($GANG_ACCESS[$gang_member->rank_id]['management_logo'] == 0) {
			$_GENERAL->addError("Teil ei ole õigusi kamba parooli muutmiseks.");
		} else {
			DB::getInstance()->update('gang', $gang_info->id, array('password' => Input::get('value')));
				
			Session::flash('gang', 'Te muutsite kamba parooli.');
			Redirect::to('p.php?p=gang&page=management');
		}
	} else if(Token::check(Input::get('token'), 'EDIT_LEADER') ) {
		if ($gang_info->leader != $_USER->data()->id) {
			$_GENERAL->addError("Ainult kamba juht saab kamba juhti vahetada.");
		} else {
			if ($gang_info->leader != $_USER->data()->id) {
				$_GENERAL->addError("Te ei ole kamba juht.");
			} else if ($gang_info->money < $gang_edit_leader_money) {
				$_GENERAL->addError("Kambal ei ole piisavalt raha.");
			} else {
				$new_leader_id = (int)Input::get('leader');
				$user_i_query = DB::getInstance()->query("SELECT * from `users_data` WHERE `id` = " . $new_leader_id);
		 		if (!$user_i_query->count()) {
		 			$_GENERAL->addError("Kasutajat ei leitud.");
		 		} else {
		 			if ($gang_info->id != $user_i_query->first()->gang) {
		 				$_GENERAL->addError("See kasutaja ei ole teie kambas.");
			 		} else {
			 			if ($new_leader_id == $_USER->data()->id) {
			 				$_GENERAL->addError("Teie olete kamba juht.");
			 			} else {
				 			$member_id_query = DB::getInstance()->query("SELECT * from `gang_members` WHERE `user_id` = " . $new_leader_id);
				 			$member_id = $member_id_query->first();
				 			DB::getInstance()->update('gang_members', $member_id->id, array('rank_id' => 6));
				 			$my_member_id_query = DB::getInstance()->query("SELECT * from `gang_members` WHERE `user_id` = " . $_USER->data()->id);
				 			$my_member_id = $my_member_id_query->first();
				 			DB::getInstance()->update('gang_members', $my_member_id->id, array('rank_id' => 1));

							DB::getInstance()->update('gang', $gang_info->id, array('leader' => $new_leader_id));
							DB::getInstance()->update('gang', $gang_info->id, array('money' => $gang_info->money - $gang_edit_leader_money));

							Session::flash('gang', 'Te vahetasite kamba juhti.');
							Redirect::to('p.php?p=gang');
						}
					}
		 		}
			}
		}
	} else if(Token::check(Input::get('token'), 'DELETE_GANG') ) {
		if ($gang_info->leader != $_USER->data()->id) {
			$_GENERAL->addError("Ainult kamba juht saab kampa kustutada.");
		} else {
			$password = Input::get('value');
			if (Hash::verify($password, $_USER->data()->password)) {
				$del_gang_mem_query = DB::getInstance()->query("SELECT * from `users_data` WHERE `gang` = " . $gang_info->id);
				foreach ($del_gang_mem_query->results() as $muser) {
					DB::getInstance()->update('users_data', $muser->id, array('gang' => 0));
				}
				DB::getInstance()->delete('gang_members', array('gang_id', '=', $gang_info->id));
				DB::getInstance()->update('gang', $gang_info->id, array('deleted' => 1));

				Session::flash('gang', 'Teie kamp on nüüd edukalt kustutatud.');
				Redirect::to('p.php?p=gang');
			} else {
				$_GENERAL->addError("Teie kasutaja parool on vale.");
			}
		}
	}
}

if ($GANG_ACCESS[$gang_member->rank_id]['management_info'] == 0 and 
	$GANG_ACCESS[$gang_member->rank_id]['management_logo'] == 0 and 
	$GANG_ACCESS[$gang_member->rank_id]['management_password'] == 0) {
	$_GENERAL->addError("Teil ei ole õigusi kamba halduses.");
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
if ($GANG_ACCESS[$gang_member->rank_id]['management_info'] == 1 or 
	$GANG_ACCESS[$gang_member->rank_id]['management_logo'] == 1 or 
	$GANG_ACCESS[$gang_member->rank_id]['management_password'] == 1) {
	?>
<div id="page">
	<div class="page-title">Kamba haldamine</div>
	<p>
		<table>
			<?php
			if ($GANG_ACCESS[$gang_member->rank_id]['management_info'] == 1) {
			?>
			<form action="p.php?p=gang&page=management" method="POST">
				<tr>
					<td width="25%">Muuda kamba infot:</td>
					<td width="75%">
						<textarea name="value" rows="10" cols="60"><?php print($gang_info->info);?></textarea><br>
						<input type="hidden" name="token" value="<?php echo Token::generate('EDIT_INFO'); ?>">
						<input type="submit" value="Muuda kamba infot">
					</td>
				</tr>
			</form>
			<?php
			}
			if ($GANG_ACCESS[$gang_member->rank_id]['management_logo'] == 1) {
			?>
			<form action="p.php?p=gang&page=management" method="POST">
				<tr>
					<td width="25%">Muuda kamba logo:</td>
					<td width="75%">
						<input type="text" name="value" size="40" placeholder="Sisesta logo URL" value="<?php print($gang_info->logo_url);?>" autocomplete="off">
						<input type="hidden" name="token" value="<?php echo Token::generate('EDIT_LOGO'); ?>">
						<input type="submit" value="Muuda logo">
					</td>
				</tr>
			</form>
			<?php
			}
			if ($GANG_ACCESS[$gang_member->rank_id]['management_password'] == 1) {
			?>
			<form action="p.php?p=gang&page=management" method="POST">
				<tr>
					<td width="25%">Muuda kamba parooli:</td>
					<td width="75%">
						<input type="text" name="value" value="<?php print($gang_info->password);?>" autocomplete="off">
						<input type="hidden" name="token" value="<?php echo Token::generate('EDIT_PASSWORD'); ?>">
						<input type="submit" value="Muuda kamba parooli">
					</td>
				</tr>
			</form>
			<?php
			}
			?>
		</table>
	</p>
</div>
<?php
}

if ($_USER->data()->id == $gang_info->leader) {

	$check_access = array(
		1 => array(
			"name" 					=> ($GANG_ACCESS[1]['name'] == 1) ? 'checked' : '',
			"forum_edit" 			=> ($GANG_ACCESS[1]['forum_edit'] == 1) ? 'checked' : '',
			"forum_delete" 			=> ($GANG_ACCESS[1]['forum_delete'] == 1) ? 'checked' : '',
			"market_buy" 			=> ($GANG_ACCESS[1]['market_buy'] == 1) ? 'checked' : '',
			"market_sell"			=> ($GANG_ACCESS[1]['market_sell'] == 1) ? 'checked' : '',
			"members_drop" 			=> ($GANG_ACCESS[1]['members_drop'] == 1) ? 'checked' : '',
			"members_money" 		=> ($GANG_ACCESS[1]['members_money'] == 1) ? 'checked' : '',
			"members_rank" 			=> ($GANG_ACCESS[1]['members_rank'] == 1) ? 'checked' : '',
			"building_buy" 			=> ($GANG_ACCESS[1]['building_buy'] == 1) ? 'checked' : '',
			"management_info" 		=> ($GANG_ACCESS[1]['management_info'] == 1) ? 'checked' : '',
			"management_logo" 		=> ($GANG_ACCESS[1]['management_logo'] == 1) ? 'checked' : '',
			"management_password" 	=> ($GANG_ACCESS[1]['management_password'] == 1) ? 'checked' : ''
		),
		2 => array(
			"name" 					=> ($GANG_ACCESS[2]['name'] == 1) ? 'checked' : '',
			"forum_edit" 			=> ($GANG_ACCESS[2]['forum_edit'] == 1) ? 'checked' : '',
			"forum_delete" 			=> ($GANG_ACCESS[2]['forum_delete'] == 1) ? 'checked' : '',
			"market_buy" 			=> ($GANG_ACCESS[2]['market_buy'] == 1) ? 'checked' : '',
			"market_sell"			=> ($GANG_ACCESS[2]['market_sell'] == 1) ? 'checked' : '',
			"members_drop" 			=> ($GANG_ACCESS[2]['members_drop'] == 1) ? 'checked' : '',
			"members_money" 		=> ($GANG_ACCESS[2]['members_money'] == 1) ? 'checked' : '',
			"members_rank" 			=> ($GANG_ACCESS[2]['members_rank'] == 1) ? 'checked' : '',
			"building_buy" 			=> ($GANG_ACCESS[2]['building_buy'] == 1) ? 'checked' : '',
			"management_info" 		=> ($GANG_ACCESS[2]['management_info'] == 1) ? 'checked' : '',
			"management_logo" 		=> ($GANG_ACCESS[2]['management_logo'] == 1) ? 'checked' : '',
			"management_password" 	=> ($GANG_ACCESS[2]['management_password'] == 1) ? 'checked' : ''
		),
		3 => array(
			"name" 					=> ($GANG_ACCESS[3]['name'] == 1) ? 'checked' : '',
			"forum_edit" 			=> ($GANG_ACCESS[3]['forum_edit'] == 1) ? 'checked' : '',
			"forum_delete" 			=> ($GANG_ACCESS[3]['forum_delete'] == 1) ? 'checked' : '',
			"market_buy" 			=> ($GANG_ACCESS[3]['market_buy'] == 1) ? 'checked' : '',
			"market_sell"			=> ($GANG_ACCESS[3]['market_sell'] == 1) ? 'checked' : '',
			"members_drop" 			=> ($GANG_ACCESS[3]['members_drop'] == 1) ? 'checked' : '',
			"members_money" 		=> ($GANG_ACCESS[3]['members_money'] == 1) ? 'checked' : '',
			"members_rank" 			=> ($GANG_ACCESS[3]['members_rank'] == 1) ? 'checked' : '',
			"building_buy" 			=> ($GANG_ACCESS[3]['building_buy'] == 1) ? 'checked' : '',
			"management_info" 		=> ($GANG_ACCESS[3]['management_info'] == 1) ? 'checked' : '',
			"management_logo" 		=> ($GANG_ACCESS[3]['management_logo'] == 1) ? 'checked' : '',
			"management_password" 	=> ($GANG_ACCESS[3]['management_password'] == 1) ? 'checked' : ''
		),
		4 => array(
			"name" 					=> ($GANG_ACCESS[4]['name'] == 1) ? 'checked' : '',
			"forum_edit" 			=> ($GANG_ACCESS[4]['forum_edit'] == 1) ? 'checked' : '',
			"forum_delete" 			=> ($GANG_ACCESS[4]['forum_delete'] == 1) ? 'checked' : '',
			"market_buy" 			=> ($GANG_ACCESS[4]['market_buy'] == 1) ? 'checked' : '',
			"market_sell"			=> ($GANG_ACCESS[4]['market_sell'] == 1) ? 'checked' : '',
			"members_drop" 			=> ($GANG_ACCESS[4]['members_drop'] == 1) ? 'checked' : '',
			"members_money" 		=> ($GANG_ACCESS[4]['members_money'] == 1) ? 'checked' : '',
			"members_rank" 			=> ($GANG_ACCESS[4]['members_rank'] == 1) ? 'checked' : '',
			"building_buy" 			=> ($GANG_ACCESS[4]['building_buy'] == 1) ? 'checked' : '',
			"management_info" 		=> ($GANG_ACCESS[4]['management_info'] == 1) ? 'checked' : '',
			"management_logo" 		=> ($GANG_ACCESS[4]['management_logo'] == 1) ? 'checked' : '',
			"management_password" 	=> ($GANG_ACCESS[4]['management_password'] == 1) ? 'checked' : ''
		),
		5 => array(
			"name" 					=> ($GANG_ACCESS[5]['name'] == 1) ? 'checked' : '',
			"forum_edit" 			=> ($GANG_ACCESS[5]['forum_edit'] == 1) ? 'checked' : '',
			"forum_delete" 			=> ($GANG_ACCESS[5]['forum_delete'] == 1) ? 'checked' : '',
			"market_buy" 			=> ($GANG_ACCESS[5]['market_buy'] == 1) ? 'checked' : '',
			"market_sell"			=> ($GANG_ACCESS[5]['market_sell'] == 1) ? 'checked' : '',
			"members_drop" 			=> ($GANG_ACCESS[5]['members_drop'] == 1) ? 'checked' : '',
			"members_money" 		=> ($GANG_ACCESS[5]['members_money'] == 1) ? 'checked' : '',
			"members_rank" 			=> ($GANG_ACCESS[5]['members_rank'] == 1) ? 'checked' : '',
			"building_buy" 			=> ($GANG_ACCESS[5]['building_buy'] == 1) ? 'checked' : '',
			"management_info" 		=> ($GANG_ACCESS[5]['management_info'] == 1) ? 'checked' : '',
			"management_logo" 		=> ($GANG_ACCESS[5]['management_logo'] == 1) ? 'checked' : '',
			"management_password" 	=> ($GANG_ACCESS[5]['management_password'] == 1) ? 'checked' : ''
		)
	);

	for ($i=1; $i < 12; $i++) { 
		if ($i == 1) {
			$subject = 'Foorumis muutmine';
			$name = 'forum_edit';
		} else if ($i == 2) {
			$subject = 'Foorumis kustutamine';
			$name = 'forum_delete';
		} else if ($i == 3) {
			$subject = 'Turul ostmine';
			$name = 'market_buy';
		} else if ($i == 4) {
			$subject = 'Turul müümine';
			$name = 'market_sell';
		} else if ($i == 5) {
			$subject = 'Liikmete välja viskamine';
			$name = 'members_drop';
		} else if ($i == 6) {
			$subject = 'Liikmetele raha saatmine';
			$name = 'members_money';
		} else if ($i == 7) {
			$subject = 'Liikmete auastme muutmine';
			$name = 'members_rank';
		} else if ($i == 8) {
			$subject = 'Hoone uuendamine';
			$name = 'building_buy';
		} else if ($i == 9) {
			$subject = 'Kamba info muutmine';
			$name = 'management_info';
		} else if ($i == 10) {
			$subject = 'Kamba logo muutmine';
			$name = 'management_logo';
		} else if ($i == 11) {
			$subject = 'Kamba parooli muutmine';
			$name = 'management_password';
		}

		$x .= '<tr><td>'.$subject.'</td>';
		for ($j=1; $j <= 5; $j++) { 
			$x .= '
			<td align="center">
				<input type="checkbox" name="access_'.$j.'_'.$i.'" value="1" '.$check_access[$j][$name].'>
			</td>';
		}
		$x .= '</tr>';
	}

?>
	<div id="page">
		<div class="page-title">Liikmete õigused</div>
		<p>
			<form action="p.php?p=gang&page=management" method="POST">
				<table>
					<tr>
						<th width="25%">Selgitus</th>
						<th width="15%">Auaste 1</th>
						<th width="15%">Auaste 2</th>
						<th width="15%">Auaste 3</th>
						<th width="15%">Auaste 4</th>
						<th width="15%">Auaste 5</th>
					</tr>
					<tr>
						<td>Auastme nimetus</td>
						<td align="center"><input type="text" name="access_1_0" size="7" value="<?php print($GANG_ACCESS[1]['name']);?>"></td>
						<td align="center"><input type="text" name="access_2_0" size="7" value="<?php print($GANG_ACCESS[2]['name']);?>"></td>
						<td align="center"><input type="text" name="access_3_0" size="7" value="<?php print($GANG_ACCESS[3]['name']);?>"></td>
						<td align="center"><input type="text" name="access_4_0" size="7" value="<?php print($GANG_ACCESS[4]['name']);?>"></td>
						<td align="center"><input type="text" name="access_5_0" size="7" value="<?php print($GANG_ACCESS[5]['name']);?>"></td>
					</tr>
					<?php print($x);?>
				</table>
			<ul align="right">
				<li>
					<input type="hidden" name="token" value="<?php echo Token::generate('EDIT_ACCESS'); ?>">
					<input type="submit" value="Muuda liikmete õiguseid">
				</li>
			</ul>
			</form>
		</p>
	</div>
<?php
	$gang_edit_leader_query = DB::getInstance()->query("SELECT * from `gang_members` WHERE `gang_id` = " . $gang_info->id);
	foreach ($gang_edit_leader_query->results() as $gedit) {
		$ge_query = DB::getInstance()->query("SELECT `username` FROM `users` WHERE `id` = " . $gedit->user_id);
		$ge = $ge_query->first();
		$output_line .= '<option value="'.$gedit->user_id.'">'.$ge->username.'</option>';
	}
?>
	<div id="page">
		<div class="page-title">Kamba juhi vahetamine</div>
		<p>
			<table>
				<form action="p.php?p=gang&page=management" method="POST">
					<tr>
						<td width="25%">Vahetamine maksab:</td>
						<td width="75%">100 000 000</td>
					</tr>
					<tr>
						<td width="25%">Kamba juht:</td>
						<td width="75%">
							<select name="leader">
								<?php print($output_line);?>
							</select>
							<input type="hidden" name="token" value="<?php echo Token::generate('EDIT_LEADER'); ?>">
							<input type="submit" value="Muuda kamba juhti">
						</td>
					</tr>
				</form>
			</table>
		</p>
	</div>
	<div id="page">
		<div class="page-title">Kamba kustutamine</div>
		<p>
			<table>
				<form action="p.php?p=gang&page=management" method="POST">
					<tr>
						<td width="25%">Teie kasutaja parool:</td>
						<td width="75%">
							<input type="password" name="value" autocomplete="off">
							<input type="hidden" name="token" value="<?php echo Token::generate('DELETE_GANG'); ?>">
							<input type="submit" value="Kustuta kamp">
						</td>
					</tr>
				</form>
			</table>
		</p>
	</div>
<?php
}
