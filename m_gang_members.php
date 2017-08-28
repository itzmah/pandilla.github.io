<?php
/*
 * Name: FreeLand v2
 * Author: Marko Murumaa
 * Created: July 2015
 * Website: www.freelandplay.eu
*/

if(Input::exists()) {
	if(Token::check(Input::get('token'), 'MEMBERS_STUFF') ) {
		$members_selected = Input::get('members');
		if (empty(Input::get('drop')) === false) {
			if ($GANG_ACCESS[$gang_member->rank_id]['members_drop'] == 0) {
				$_GENERAL->addError("Teil ei ole õigusi liikmete kambast välja viskamiseks.");
			} else {
				 if (empty($members_selected) === true) {
				 	$_GENERAL->addError("Palun valige liikmeid ka.");
				 } else {
				 	foreach ($members_selected as $drop) {
					    $drop = (int)$drop;
				 		$user_i_query = DB::getInstance()->query("SELECT * from `users_data` WHERE `id` = " . $drop);
				 		if (!$user_i_query->count()) {
				 			$_GENERAL->addError("Kasutajat ei leitud.");
				 		} else {
				 			if ($gang_info->id != $user_i_query->first()->gang) {
				 				$_GENERAL->addError("See kasutaja ei ole teie kambas.");
					 		} else if ($drop == $gang_info->leader) {
								$_GENERAL->addError("Kamba juhti ei saa kambast valja visata.");
							} else {
								DB::getInstance()->update('users_data', $drop, array('gang' => 0));
								DB::getInstance()->delete('gang_members', array('user_id', '=', $drop));

								$user_u_query = DB::getInstance()->query("SELECT `username` from `users` WHERE `id` = " . $drop);
								$user_u = $user_u_query->first();
								$log_body = '<font color="red">Kasutaja <b><a href="p.php?p=profile&user='.$_USER->data()->username.'">'.$_USER->data()->username.'</a></b>
									 viskas kasutaja <b><a href="p.php?p=profile&user='.$user_u->username.'">'.$user_u->username.'</a></b> kambast välja.</font>';

									$gang_logs_fields = array(
										'gang_id' => $gang_info->id,
										'body' => $log_body
										);
									DB::getInstance()->insert('gang_logs', $gang_logs_fields);

								$_GENERAL->addOutSuccess('Kasutaja '.$user_u->username.' on kambast välja visatud.');
							}
				 		}
					}
				 }
			}
		} else if (empty(Input::get('edit_rank')) === false) {
			if ($GANG_ACCESS[$gang_member->rank_id]['members_rank'] == 0) {
				$_GENERAL->addError("Teil ei ole õigusi liikmete auastmeid muuta.");
			} else {
				 if (empty($members_selected) === true) {
				 	$_GENERAL->addError("Palun valige liikmeid ka.");
				 } else {
				 	$rank_id = Input::get('rank');
				 	if ($rank_id < 1 or $rank_id > 5) {
				 		$_GENERAL->addError("Sellist auastet ei leitud.");
				 	} else {
					 	foreach ($members_selected as $edit) {
						    $edit = (int)$edit;
					 		$user_i_query = DB::getInstance()->query("SELECT * from `users_data` WHERE `id` = " . $edit);
					 		if (!$user_i_query->count()) {
					 			$_GENERAL->addError("Kasutajat ei leitud.");
					 		} else {
					 			$user_i = $user_i_query->first();
					 			$mem_rank_query = DB::getInstance()->query("SELECT * from `gang_members` WHERE `user_id` = " . $_USER->data()->id);
					 			$mem_rank = $mem_rank_query->first();
					 			if ($gang_info->id != $user_i->gang) {
					 				$_GENERAL->addError("See kasutaja ei ole teie kambas.");
						 		} else if ($rank_id > $mem_rank->rank_id) {
									$_GENERAL->addError("Te ei saa endast kõrgemat auastet panna.");
								} else if ($edit == $gang_info->leader) {
									$_GENERAL->addError("Te ei saa kamba juhi auastet muuta.");
								} else {
									$member_id_query = DB::getInstance()->query("SELECT * from `gang_members` WHERE `user_id` = " . $edit);
									$member_id = $member_id_query->first();
									DB::getInstance()->update('gang_members', $member_id->id, array('rank_id' => $rank_id));

									$user_u_query = DB::getInstance()->query("SELECT `username` from `users` WHERE `id` = " . $edit);
									$user_u = $user_u_query->first();
									$log_body = '<font color="green">Kasutaja <b><a href="p.php?p=profile&user='.$_USER->data()->username.'">'.$_USER->data()->username.'</a></b>
									 muutis kasutaja <b><a href="p.php?p=profile&user='.$user_u->username.'">'.$user_u->username.'</a></b> auastet.</font>';

									$gang_logs_fields = array(
										'gang_id' => $gang_info->id,
										'body' => $log_body
										);
									DB::getInstance()->insert('gang_logs', $gang_logs_fields);

									$user_u_query = DB::getInstance()->query("SELECT `username` from `users` WHERE `id` = " . $edit);
									$_GENERAL->addOutSuccess('Kasutaja '.$user_u->username.' auaste on edukalt muudetud.');
								}
					 		}
						}
					}
				 }
			}
		} else if (empty(Input::get('send')) === false) {
			if ($GANG_ACCESS[$gang_member->rank_id]['members_money'] == 0) {
				$_GENERAL->addError("Teil ei ole õigusi liimetele raha saata.");
			} else {
				 if (empty($members_selected) === true) {
				 	$_GENERAL->addError("Palun valige liikmeid ka.");
				 } else {
				 	$money = Input::get('value');
				 	if ($money < 1 or empty($money) === true) {
				 		$_GENERAL->addError("Kirjutage kui palju raha te tahate saata.");
				 	} else {
				 		$total_money = $money * count($members_selected);
				 		if ($total_money > $gang_money_send_limit) {
				 			$_GENERAL->addError("Kokku on võimalik ühe korraga raha saata ".$_GENERAL->format_number($gang_money_send_limit).".");
				 		} 

				 		if ($gang_info->points < $gang_money_send_points) {
				 			$_GENERAL->addError("Ühe korra saatmine vaja ".$_GENERAL->format_number($gang_money_send_points)." kamba punkti.");
				 		}

				 		if ($gang_info->money < $total_money) {
				 			$_GENERAL->addError("Kambal ei ole piisavalt raha.");
				 		}

				 		if (empty($_GENERAL->errors()) === true) {
						 	foreach ($members_selected as $send) {
							    $send = (int)$send;
						 		$user_i_query = DB::getInstance()->query("SELECT * from `users_data` WHERE `id` = ".$send);
						 		if (!$user_i_query->count()) {
						 			$_GENERAL->addError("Kasutajat ei leitud.");
						 		} else {
						 			$user_i = $user_i_query->first();
						 			if ($gang_info->id != $user_i->gang) {
						 				$_GENERAL->addError("See kasutaja ei ole teie kambas.");
							 		} else {
										DB::getInstance()->update('users_data', $send, array('money' => $user_i->money + $money));

										$user_u_query = DB::getInstance()->query("SELECT `username` from `users` WHERE `id` = ".$send);
										$user_u = $user_u_query->first();
										$log_body = '<font color="green">Kasutaja <b><a href="p.php?p=profile&user='.$_USER->data()->username.'">'.$_USER->data()->username.'</a></b>
											 saatis kasutaja <b><a href="p.php?p=profile&user='.$user_u->username.'">'.$user_u->username.'</a></b>-le '.$_GENERAL->format_number($money).' raha.</font>';

											$gang_logs_fields = array(
												'gang_id' => $gang_info->id,
												'body' => $log_body
												);
											DB::getInstance()->insert('gang_logs', $gang_logs_fields);

										$_GENERAL->addOutSuccess('Kasutajale '.$user_u->username.' on edukalt raha saadetud.');
									}
						 		}
							}
						}

						if (empty($_GENERAL->errors()) === true) {
							DB::getInstance()->update('gang', $gang_info->id, 
								array('money' => $gang_info->money - $total_money, 'points' => $gang_info->points - $gang_money_send_points));
						}
					}
				 }
			}
		}
	}
}

$gang_members_list_query = DB::getInstance()->query("SELECT * FROM `gang_members` WHERE `gang_id` = ".$_USER->data('data')->gang." ORDER BY `rank_id` DESC");
foreach ($gang_members_list_query->results() as $member) {
	$user_query = DB::getInstance()->query("SELECT `username` from `users` WHERE `id` = " . $member->user_id);
	$output_line .= '
		<tr>
			<td align="center"><input type="checkbox" name="members[]" value="'.$member->user_id.'"></td>
			<td><a href="p.php?p=profile&user='.$user_query->first()->username.'">'.$user_query->first()->username.'</a></td>
			<td align="center">'.$GANG_ACCESS[$member->rank_id]['name'].'</td>
			<td align="center">'.$_GENERAL->format_number($member->money).'</td>
			<td align="center">'.$_GENERAL->format_number($member->points).'</td>
		</tr>
	';
}
?>

<div id="page">
	<div class="page-title">Kamp</div>
	<p>
	<?php
	print($gang_menu);
	if (empty($_GENERAL->outSuccess()) === false) {
		print($_GENERAL->output_success());
	}

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
	<div class="page-title">Kamba liikmed</div>
	<p>
		<table>
			<tr valign="top">
				<td width="20%"><img src="css/default/images/gang_members.png" width="100" height="100"></td>
				<td width="80%">
					Siin lehel on kirjas kõik kamba liikmed ja kui palju on nad kambale raha annetanud ja punkte teeninud.<br>
					Kamba liikmed on järjestatud auastme järgi.<br>
					Liikmetele saab korraga raha saata <?php print($_GENERAL->format_number($gang_money_send_limit));?> ja see vajab <?php print($_GENERAL->format_number($gang_money_send_points));?> kamba punkti.
				</td>
			</tr>
		</table>
	</p>
	<form action="p.php?p=gang&page=members" method="POST">
		<table>
			<tr>
				<th width="5%">#</th>
				<th width="35%">Kasutajanimi</th>
				<th width="30%">Auaste</th>
				<th width="20%">Raha</th>
				<th width="10%">Punkte</th>
			</tr>
			<?php print($output_line);?>
		</table>
		<input type="hidden" name="token" value="<?php echo Token::generate('MEMBERS_STUFF'); ?>">
		<table>
		<?php
		if ($GANG_ACCESS[$gang_member->rank_id]['members_drop'] == 1) {
		?>
			<tr>
				<td width="30%"></td>
				<td width="70%"><input type="submit" name="drop" value="Viska kambast välja"></td>
			</tr>
		<?php
		}
		if ($GANG_ACCESS[$gang_member->rank_id]['members_rank'] == 1) {
		?>
			<tr>
				<td>
					<select name="rank">
						<option>Valige auaste</option>
						<option value="1"><?php print($GANG_ACCESS[1]['name']);?></option>
						<option value="2"><?php print($GANG_ACCESS[2]['name']);?></option>
						<option value="3"><?php print($GANG_ACCESS[3]['name']);?></option>
						<option value="4"><?php print($GANG_ACCESS[4]['name']);?></option>
						<option value="5"><?php print($GANG_ACCESS[5]['name']);?></option>
					</select>
				</td>
				<td><input type="submit" name="edit_rank" value="Vaheta liikmete auastet"></td>
			</tr>
		<?php
		}
		if ($GANG_ACCESS[$gang_member->rank_id]['members_money'] == 1) {
		?>
			<tr>
				<td><input type="text" name="value" autocomplete="off"></td>
				<td><input type="submit" name="send" value="Saada liikmetele raha"></td>
			</tr>
		<?php
		}
		?>
		</table>
	</form>
</div>