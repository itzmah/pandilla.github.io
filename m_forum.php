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

if (empty(Input::get('theard')) === false) {
	$theard_i_query = DB::getInstance(1)->query("SELECT * FROM `forum_theard` WHERE `id` = " . (int)Input::get('theard'));
	if ($theard_i_query->count()) {
		$theard_i = $theard_i_query->first();

		if(Input::exists()) {
			if(Token::check(Input::get('token'), 'NEW_TOPIC') ) {
				if (empty(Input::get('subject'))) {
					$_GENERAL->addError("Teema pealkiri on kohustuslik.");
				}

				if (empty(Input::get('body'))) {
					$_GENERAL->addError("Teema sisu on kohustuslik.");
				}

				if (empty($_GENERAL->errors()) === true) {

					$new_topic_fields = array(
						'theard_id' => $theard_i->id,
						'user_id' => $_USER->data()->id,
						'subject' => Input::get('subject')
						);
					DB::getInstance(1)->insert('forum_topic', $new_topic_fields);

					$new_topic_i_query = DB::getInstance(1)->query("SELECT * FROM `forum_topic` WHERE `theard_id` = ".$theard_i->id." AND `user_id` = ".$_USER->data()->id." ORDER BY `date` DESC LIMIT 1");
					$new_topic_i = $new_topic_i_query->first();

					$new_post_fields = array(
						'topic_id' => $new_topic_i->id,
						'user_id' => $_USER->data()->id,
						'first' => 1,
						'world' => $_WORLD,
						'body' => Input::get("body")
						);
					DB::getInstance(1)->insert('forum_post', $new_post_fields);
					Redirect::to('p.php?p=forum&topic='.$new_topic_i->id);
				}
			}
		}

		$topic_query = DB::getInstance(1)->query("SELECT * FROM `forum_topic` WHERE `theard_id` = " . (int)Input::get('theard') . " AND `deleted` = 0 ORDER BY `date` DESC");
		foreach ($topic_query->results() as $topic) {
			$topic_post_count_query = DB::getInstance(1)->query("SELECT * FROM `forum_post` WHERE `topic_id` = ".$topic->id." AND `first` = 0 AND `deleted` = 0 ");
			$topic_post_count = $topic_post_count_query->count();

			if ($topic->closed == 1) {
				$icon = 'topic_lock';
			} else {
				$icon = 'topic_open';
			}

			$topic_post_last_query = DB::getInstance(1)->query("SELECT * FROM `forum_post` WHERE `topic_id` = ".$topic->id." AND `deleted` = 0 ORDER BY `date` DESC");
			$topic_post_last = $topic_post_last_query->first();

			if ($topic_post_last->world == 1) {
				$last_username_query = DB::getInstance(1)->query("SELECT `username` FROM `users` WHERE `id` = ".$topic_post_last->user_id." ");
			} else if ($topic_post_last->world == 2) {
				$last_username_query = DB::getInstance(2)->query("SELECT `username` FROM `users` WHERE `id` = ".$topic_post_last->user_id." ");
			} else if ($topic_post_last->world == 3) {
				$last_username_query = DB::getInstance(3)->query("SELECT `username` FROM `users` WHERE `id` = ".$topic_post_last->user_id." ");
			}
			
			$last_username = $last_username_query->first()->username;

			$last_line = $last_username.' (Maailm '.$topic_post_last->world.')<br>'.$topic_post_last->date;
			$output_line .= '
				<tr>
					<td align="center"><img src="css/default/images/icons/'.$icon.'.png" width="32" height="32"></td>
					<td><a href="p.php?p=forum&topic='.$topic->id.'">'.$topic->subject.'</a></td>
					<td align="center">'.$_GENERAL->format_number($topic_post_count).'</td>
					<td align="center">'.$last_line.'</td>
				</tr>
			';
		}

?>
<div id="page">
	<div class="page-title">Alamfoorum - <?php print($theard_i->name);?></div>
	<p>
	<?php
	if (empty($_GENERAL->errors()) === false) {
		print($_GENERAL->output_errors());
	}

	if(Session::exists('forum')) {
		$_GENERAL->addOutSuccess(Session::flash('forum'));
		print($_GENERAL->output_success());
	}
	?>
		<?php
		if (empty($output_line) === true) {
			$_GENERAL->addOutInfo("Selles alamfoorumis ei ole ühtegi teemat.");
			print($_GENERAL->output_info());
		} else {
		?>
		<table>
			<tr>
				<th width="10%"></th>
				<th width="40%">Teema</th>
				<th width="15%">Postitusi</th>
				<th width="35%">Viimane postitus</th>
			</tr>
			<?php print($output_line);?>
		<?php
		}
		?>
		</table>
	</p>
</div>
<div id="page">
	<div class="page-title">Postita uus teema</div>
	<p>
		<form action="p.php?p=forum&theard=<?php print($theard_i->id);?>" method="POST">
			<ul>
				<li>Teema nimi:</li>
				<li><input type="text" name="subject" autocomplete="off"></li>
				<li>Teema sisu:</li>
				<li><textarea name="body" rows="10" cols="70"></textarea></li>
				<li>
					<input type="hidden" name="token" value="<?php echo Token::generate('NEW_TOPIC'); ?>">
					<input type="submit" value="Postita uus teema">
				</li>
			</ul>
		</form>
	</p>
</div>
<?php
	} else {
		Redirect::to('p.php?p=forum');
	}

} else if (empty(Input::get('topic')) === false) {
	$topic_i_query = DB::getInstance(1)->query("SELECT * FROM `forum_topic` WHERE `id` = " . (int)Input::get('topic') . " AND `deleted` = 0");
	if ($topic_i_query->count()) {
		$topic_i = $topic_i_query->first();

		if(Input::exists()) {
			if(Token::check(Input::get('token'), 'REPLY_TOPIC') ) {
				if (empty(Input::get('body'))) {
					$_GENERAL->addError("Teema sisu on kohustuslik.");
				}

				if ($topic_i->closed == 1) {
					$_GENERAL->addError("See teema on suletud.");
				}

				if (empty($_GENERAL->errors()) === true) {

					$reply_topic_fields = array(
						'topic_id' => $topic_i->id,
						'user_id' => $_USER->data()->id,
						'world' => $_WORLD,
						'body' => Input::get("body")
						);
					DB::getInstance(1)->insert('forum_post', $reply_topic_fields);

					DB::getInstance(1)->update('forum_topic', $topic_i->id, array('date' => date("Y-m-d H:i:s")));

					Redirect::to('p.php?p=forum&topic='.$topic_i->id);
				}
			} else if(Token::check(Input::get('token'), 'LOCK_TOPIC') ) {

				if ($_USER->hasPermission('forum_close') === false) {
					$_GENERAL->addError("Teie ei saa seda teemat lukku panna.");
				}

				if (empty($_GENERAL->errors()) === true) {

					$close_topic_fields = array(
						'closed' => 1
						);
					DB::getInstance(1)->update('forum_topic', $topic_i->id, $close_topic_fields);

					Session::flash('forum', 'See teema on edukalt lukku pandud.');
					Redirect::to('p.php?p=forum&topic='.$topic_i->id);
				}
			}
		}


		$post_query = DB::getInstance(1)->query("SELECT * FROM `forum_post` WHERE `topic_id` = " . (int)Input::get('topic') . " AND `deleted` = 0 ORDER BY `date` ASC");
		foreach ($post_query->results() as $post) {

			if ($post->world == 1) {
				$p_username_query = DB::getInstance(1)->query("SELECT `username`,`groups` FROM `users` WHERE `id` = ".$post->user_id." ");
			} else if ($post->world == 2) {
				$p_username_query = DB::getInstance(2)->query("SELECT `username`,`groups` FROM `users` WHERE `id` = ".$post->user_id." ");
			} else if ($post->world == 3) {
				$p_username_query = DB::getInstance(3)->query("SELECT `username`,`groups` FROM `users` WHERE `id` = ".$post->user_id." ");
			}
			$p_username = $p_username_query->first();

			$group_query = DB::getInstance(1)->get('groups', array('id','=',$p_username->groups));
			$group_name = '<font color="'.$group_query->first()->color.'">'.$group_query->first()->name.'</font>';

			$edit_line = '';
			$delete_line = '';
			if ($_WORLD == $post->world) {
				if ($post->user_id == $_USER->data()->id or $_USER->hasPermission('forum_edit')) {
					$edit_line = '<a href="p.php?p=forum&edit='.$post->id.'">Muuda</a>';
				}

				if ($post->user_id == $_USER->data()->id or $_USER->hasPermission('forum_delete')) {
					$delete_line = '<a href="p.php?p=forum&delete='.$post->id.'">Kustuta</a>';
				}

			}
			$output_line .= "
				<tr>
					<td width=\"25%\" style=\"border-bottom:1px solid #dddddd; border-right:1px solid #dddddd;\">
						<div align=\"center\">
							".$p_username->username." (Maailm ".$post->world.")<br/>
							".$group_name."<br/><br/>
							Postitatud:<br/><b>".$post->date."</b><br/>
							".$edit_line."
							".$delete_line."
						</div>
					</td>
					<td width=\"75%\" valign=\"top\" style=\"border-bottom:1px solid #dddddd;\">".$_BBCODE->Parse($post->body)."</td>
				</tr>
			";
		}
		?>
		<div id="page">
			<div class="page-title">Teema - <?php print($topic_i->subject);?></div>
			<p>
				<?php
				if (empty($_GENERAL->errors()) === false) {
					print($_GENERAL->output_errors());
				}

				if(Session::exists('forum')) {
					$_GENERAL->addOutSuccess(Session::flash('forum'));
					print($_GENERAL->output_success());
				}

				if ($_USER->hasPermission('forum_close')) {
					?>
					<form action="p.php?p=forum&topic=<?php print($topic_i->id);?>" method="POST">
						<ul>
							<li align="right">
								<input type="hidden" name="token" value="<?php echo Token::generate('LOCK_TOPIC'); ?>">
								<input type="submit" value="Pane see teema lukku">
							</li>
						</ul>
					</form>
					<?php
				}
				?>
				<table>
					<?php print($output_line);?>
				</table>
			</p>
		</div>
		<?php
		if ($topic_i->closed != 1) {
		?>
		<div id="page">
			<div class="page-title">Vasta teemale</div>
			<p>
				<form action="p.php?p=forum&topic=<?php print($topic_i->id);?>" method="POST">
					<ul>
						<li>Teie vastus:</li>
						<li><textarea name="body" rows="10" cols="70"></textarea></li>
						<li>
							<input type="hidden" name="token" value="<?php echo Token::generate('REPLY_TOPIC'); ?>">
							<input type="submit" value="Vasta teemale">
						</li>
					</ul>
				</form>
			</p>
		</div>
		<?php
		}
	} else {
		Redirect::to('p.php?p=forum');
	}
} else if (empty(Input::get('edit')) === false) {
	$edit_i_query = DB::getInstance(1)->query("SELECT * FROM `forum_post` WHERE `id` = " . (int)Input::get('edit') . " AND `deleted` = 0");
	if ($edit_i_query->count()) {
		$edit_i = $edit_i_query->first();

		if (!$_USER->hasPermission('forum_edit')) {
			if ($_USER->data()->id != $edit_i->user_id) {
				Redirect::to('p.php?p=forum&topic='.$edit_i->topic_id);
			}
		}

		if(Input::exists()) {
			if(Token::check(Input::get('token'), 'EDIT_POST') ) {
				$topic_i_query = DB::getInstance(1)->query("SELECT * FROM `forum_topic` WHERE `id` = ".$edit_i->topic_id." ");
				$topic_i = $topic_i_query->first();

				if (empty(Input::get('body'))) {
					$_GENERAL->addError("Sisu on kohustuslik.");
				}

				if ($topic_i->closed == 1) {
					$_GENERAL->addError("See teema on suletud.");
				}

				if (!$_USER->hasPermission('forum_edit')) {
					if ($_USER->data()->id != $edit_i->user_id) {
						$_GENERAL->addError("Teie ei saa seda postitust muuta.");
					}
				}

				if (empty($_GENERAL->errors()) === true) {

					$edit_post_fields = array(
						'body' => Input::get("body")
						);
					DB::getInstance(1)->update('forum_post', $edit_i->id, $edit_post_fields);

					Session::flash('forum', 'Postitus on edukalt muudetud.');
					Redirect::to('p.php?p=forum&topic='.$edit_i->topic_id);
				}
			}
		}
	?>
	<div id="page">
		<div class="page-title">Muuda postitust</div>
		<p>
			<?php
			if (empty($_GENERAL->errors()) === false) {
				print($_GENERAL->output_errors());
			}
			?>
			<form action="p.php?p=forum&edit=<?php print($edit_i->id);?>" method="POST">
				<ul>
					<li>Sisu:</li>
					<li><textarea name="body" rows="10" cols="70"><?php print($edit_i->body);?></textarea></li>
					<li>
						<input type="hidden" name="token" value="<?php echo Token::generate('EDIT_POST'); ?>">
						<input type="submit" value="Muuda postitust">
					</li>
				</ul>
			</form>
		</p>
	</div>
	<?php
	} else {
		Redirect::to('p.php?p=forum');
	}
} else if (empty(Input::get('delete')) === false) {
	$delete_i_query = DB::getInstance(1)->query("SELECT * FROM `forum_post` WHERE `id` = " . (int)Input::get('delete') . " AND `deleted` = 0");
	if ($delete_i_query->count()) {
		$delete_i = $delete_i_query->first();

		if (!$_USER->hasPermission('forum_delete')) {
			if ($_USER->data()->id != $delete_i->user_id) {
				Redirect::to('p.php?p=forum&topic='.$delete_i->topic_id);
			}
		}

		if(Input::exists()) {
			if(Token::check(Input::get('token'), 'DELETE_POST') ) {
				$topic_i_query = DB::getInstance(1)->query("SELECT * FROM `forum_topic` WHERE `id` = ".$delete_i->topic_id." ");
				$topic_i = $topic_i_query->first();

				if ($topic_i->closed == 1) {
					$_GENERAL->addError("See teema on suletud.");
				}

				if (!$_USER->hasPermission('forum_delete')) {
					if ($_USER->data()->id != $delete_i->user_id) {
						$_GENERAL->addError("Teie ei saa seda postitust/teemat kustutada.");
					}
				}

				if (empty($_GENERAL->errors()) === true) {
					if ($delete_i->first == 1) {
						$delete_topic_fields = array(
							'deleted' => 1
							);
						DB::getInstance(1)->update('forum_topic', $delete_i->topic_id, $delete_topic_fields);
						DB::getInstance(1)->query("UPDATE `forum_post` SET `deleted`= 1 WHERE `topic_id` = ".$delete_i->topic_id." ");

						Session::flash('forum', 'Teema on edukalt kustutatud.');
						Redirect::to('p.php?p=forum&theard='.$topic_i->theard_id);
					} else {
						$delete_post_fields = array(
							'deleted' => 1
							);
						DB::getInstance(1)->update('forum_post', $delete_i->id, $delete_post_fields);
						Session::flash('forum', 'Postitus on edukalt kustutatud.');
						Redirect::to('p.php?p=forum&topic='.$delete_i->topic_id);
					}
				}
			}
		}
	?>
	<div id="page">
		<div class="page-title">Kustuta postitust/teemat</div>
		<p>
			<?php
			if (empty($_GENERAL->errors()) === false) {
				print($_GENERAL->output_errors());
			}
			?>
			<form action="p.php?p=forum&delete=<?php print($delete_i->id);?>" method="POST">
				<ul>
					<li>Kas te olete kindel, et soovite selle postituse/teema kustutada?</li>
					<li>
						<input type="hidden" name="token" value="<?php echo Token::generate('DELETE_POST'); ?>">
						<input type="submit" value="Kustuta postitus/teema">
					</li>
				</ul>
			</form>
		</p>
	</div>
	<?php
	} else {
		Redirect::to('p.php?p=forum');
	}

} else {
	$theard_query = DB::getInstance(1)->query("SELECT * FROM `forum_theard`");
	foreach ($theard_query->results() as $th) {
		$th_topic_count_query = DB::getInstance(1)->query("SELECT * FROM `forum_topic` WHERE `theard_id` = ".$th->id." AND `deleted` = 0 ");
		$th_topic_count = $th_topic_count_query->count();

		if ($th_topic_count == 0) {
			$last_line = '<i>Puudub</i>';
		} else {

			$topic_last_query = DB::getInstance(1)->query("SELECT * FROM `forum_topic` WHERE `theard_id` = ".$th->id." AND `deleted` = 0 ORDER BY `date` DESC");
			$topic_last = $topic_last_query->first();

			$topic_last_post_query = DB::getInstance(1)->query("SELECT * FROM `forum_post` WHERE `topic_id` = ".$topic_last->id." AND `deleted` = 0 ORDER BY `date` DESC");
			$topic_last_post = $topic_last_post_query->first();

			if ($topic_last_post->world == 1) {
				$last_username_query = DB::getInstance(1)->query("SELECT `username` FROM `users` WHERE `id` = ".$topic_last_post->user_id." ");
			} else if ($topic_last_post->world == 2) {
				$last_username_query = DB::getInstance(2)->query("SELECT `username` FROM `users` WHERE `id` = ".$topic_last_post->user_id." ");
			} else if ($topic_last_post->world == 3) {
				$last_username_query = DB::getInstance(3)->query("SELECT `username` FROM `users` WHERE `id` = ".$topic_last_post->user_id." ");
			}

			$last_username = $last_username_query->first()->username;

			$last_line = $last_username.' (Maailm '.$topic_last_post->world.')<br>'.$topic_last_post->date;

		}

		$output_line .= '
			<tr>
				<td align="center"><img src="css/default/images/icons/theard.png" width="32" height="32"></td>
				<td><a href="p.php?p=forum&theard='.$th->id.'">'.$th->name.'</a></td>
				<td align="center">'.$_GENERAL->format_number($th_topic_count).'</td>
				<td align="center">'.$last_line.'</td>
			</tr>
		';

		$topic_last_query = null;
		$topic_last_post_query = null;
		$last_username_query = null;
	}
?>
<div id="page">
	<div class="page-title">Foorum</div>
	<p>
		<table>
			<tr>
				<th width="10%"></th>
				<th width="40%">Alamfoorum</th>
				<th width="15%">Teemasid</th>
				<th width="35%">Viimane postitaja</th>
			</tr>
			<?php print($output_line);?>
		</table>
	</p>
</div>
<?php
}
include("includes/overall/footer.php");
