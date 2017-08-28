<?php
/*
 * Name: FreeLand v2
 * Author: Marko Murumaa
 * Created: July 2015
 * Website: www.freelandplay.eu
*/

if(Input::exists()) {
	if(Token::check(Input::get('token'), 'NEW_TOPIC') ) {
		if (empty(Input::get('topic')) === true or empty(Input::get('body')) === true) {
			$_GENERAL->addError("Teema nimi ja sisu on kohustuslikud.");
		} else {
			$gang_topic_fields = array(
				'gang_id' => $gang_info->id,
				'user_id' => $_USER->data()->id,
				'subject' => Input::get('topic')
				);
			DB::getInstance()->insert('gang_forum_topic', $gang_topic_fields);

			$forum_last_id_query = DB::getInstance()->query("SELECT * FROM `gang_forum_topic` WHERE `gang_id` = ".$gang_info->id." AND `user_id` = ".$_USER->data()->id." ORDER BY `id` DESC");
			$forum_last_id = $forum_last_id_query->first();

			$gang_post_fields = array(
				'topic_id' => $forum_last_id->id,
				'user_id' => $_USER->data()->id,
				'body' => Input::get('body'),
				'first' => 1
				);
			DB::getInstance()->insert('gang_forum_post', $gang_post_fields);
			
			Session::flash('gang', 'Uus teema on edukalt tehtud.');
			Redirect::to('p.php?p=gang&page=forum&topic='.$forum_last_id->id);
		}
	} else if(Token::check(Input::get('token'), 'REPLY_POST') ) {
		if (empty(Input::get('body')) === true) {
			$_GENERAL->addError("Teema sisu on kohustuslik.");
		} else {
			$topic_i_query = DB::getInstance()->query("SELECT * FROM `gang_forum_topic` WHERE `id` = " . (int)Input::get('topic') . " AND `deleted` = 0");
			if (!$topic_i_query->count()) {
				$_GENERAL->addError("Sellist teemat ei leitud.");
			} else {
				$topic_i = $topic_i_query->first();
				if ($gang_info->id != $topic_i->gang_id) {
					$_GENERAL->addError("See teema ei ole selle kamba teema.");
				} else {
					$gang_post_fields = array(
						'topic_id' => $topic_i->id,
						'user_id' => $_USER->data()->id,
						'body' => Input::get('body')
						);
					DB::getInstance()->insert('gang_forum_post', $gang_post_fields);
					DB::getInstance()->update('gang_forum_topic', $topic_i->id, array('date' => date("Y-m-d H:i:s") ));
					
					Session::flash('gang', 'Te vastasite teemale.');
					Redirect::to('p.php?p=gang&page=forum&topic='.$topic_i->id);
				}
			}
		}
	} else if(Token::check(Input::get('token'), 'EDIT_POST') ) {
		if (empty(Input::get('body')) === true) {
			$_GENERAL->addError("Teema sisu on kohustuslik.");
		} else {
			$post_i_query = DB::getInstance()->query("SELECT * FROM `gang_forum_post` WHERE `id` = " . (int)Input::get('edit') . " AND `deleted` = 0");
			if (!$post_i_query->count()) {
				$_GENERAL->addError("Sellist postitust ei leitud.");
			} else {
				$post_i = $post_i_query->first();
				if ($GANG_ACCESS[$gang_member->rank_id]['forum_edit'] == 0) {
					if ($post_i->user_id != $_USER->data()->id) {
						$_GENERAL->addError("Teie ei saa seda postitust muuta.");
					} 
				}

				if (empty($_GENERAL->errors()) === true) {
					DB::getInstance()->update('gang_forum_post', $post_i->id, array('body' => Input::get('body') ));
					
					Session::flash('gang', 'Te muutsite postitust.');
					Redirect::to('p.php?p=gang&page=forum&topic='.$post_i->topic_id);
				}
			}
		}
	} 
}

if (Input::exists('get')) {
	if(empty(Input::get('delete')) === false) {
		$post_i_query = DB::getInstance()->query("SELECT * FROM `gang_forum_post` WHERE `id` = " . (int)Input::get('delete') . " AND `deleted` = 0");
		if (!$post_i_query->count()) {
			$_GENERAL->addError("Sellist postitust ei leitud.");
		} else {
			$post_i = $post_i_query->first();
			if ($GANG_ACCESS[$gang_member->rank_id]['forum_delete'] == 0) {
				if ($post_i->user_id != $_USER->data()->id) {
					$_GENERAL->addError("Teie ei saa seda postitust kustutada.");
				} 
			}

			if (empty($_GENERAL->errors()) === true) {
				if ($post_i->first == 1) {
					DB::getInstance()->update('gang_forum_topic', $post_i->topic_id, array('deleted' => 1 ));
				}
				DB::getInstance()->update('gang_forum_post', $post_i->id, array('deleted' => 1 ));
				
				Session::flash('gang', 'Te kustutasite postituse.');
				Redirect::to('p.php?p=gang&page=forum');
			}
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
<?php
?>
<div id="page">
	<div class="page-title">Kamba foorum</div>
	<p>
		<?php
		if (empty(Input::get('topic')) === false) {
			$posts_query = DB::getInstance()->query("SELECT * FROM `gang_forum_post` WHERE `topic_id` = " . (int)Input::get('topic') . " AND `deleted` = 0 ORDER by `id`");
			foreach ($posts_query->results() as $post) {
				$edit_line = null;
				$delete_line = null;

				$gang_member_f_query = DB::getInstance()->query("SELECT * FROM `gang_members` WHERE `user_id` = " . $post->user_id);
				$gang_member_f = $gang_member_f_query->first();

				$my_member_f_query = DB::getInstance()->query("SELECT * FROM `gang_members` WHERE `user_id` = " . $_USER->data()->id);
				$my_member_f = $my_member_f_query->first();

				if ($GANG_ACCESS[$my_member_f->rank_id]['forum_edit'] == 1 or $_USER->data()->id == $post->user_id) {
					$edit_line = '<a href="p.php?p=gang&page=forum&edit='.$post->id.'">Muuda</a>';
				}
				if ($GANG_ACCESS[$my_member_f->rank_id]['forum_delete'] == 1 or $_USER->data()->id == $post->user_id) {
					$delete_line = '<a href="p.php?p=gang&page=forum&delete='.$post->id.'">Kustuta</a>';
				}

				$rank_line = $GANG_ACCESS[$gang_member_f->rank_id]['name'];

				$user_i_query = DB::getInstance()->query("SELECT `username` FROM `users` WHERE `id` = " . $post->user_id);
				$user_i = $user_i_query->first();

				$output_line .= '
				<tr>
					<td width="30%" align="center"style="border-bottom: 1px solid #dddddd;">
						Postitas: <a href="p.php?p=profile&user='.$user_i->username.'">'.$user_i->username.'</a><br/>
						<b>'.$rank_line.'</b><br/><br/>
						Lisatud:<br/><b>'.$post->date.'</b><br/>
						'.$edit_line.' '.$delete_line.'
					</td>
					<td width="70%" valign="top" style="border-left: 1px solid #dddddd;border-bottom: 1px solid #dddddd; padding:5px;">'.$_BBCODE->Parse($post->body).'</td>
				</tr>
				';
			}
		?>
		<table>
			<?php print($output_line);?>
		</table>
		<form action="p.php?p=gang&page=forum&topic=<?php print(Input::get('topic'));?>" method="POST">
			<ul>
				<li align="center"><textarea name="body" rows="5" cols="60"></textarea></li>
				<li align="right">
					<input type="hidden" name="token" value="<?php echo Token::generate('REPLY_POST'); ?>">
					<input type="submit" value="Vasta teemale">
				</li>
			</ul>
		</form>
		<?php
		} else if (empty(Input::get('edit')) === false) {
			$post_i_query = DB::getInstance()->query("SELECT * FROM `gang_forum_post` WHERE `id` = " . (int)Input::get('edit'));
			if (!$post_i_query->count()) {
				Redirect::to('p.php?p=gang&page=forum');
			}

			$post_i = $post_i_query->first();

			$my_member_f_query = DB::getInstance()->query("SELECT * FROM `gang_members` WHERE `user_id` = " . $_USER->data()->id);
			$my_member_f = $my_member_f_query->first();

			if ($post_i->user_id == $_USER->data()->id or $GANG_ACCESS[$my_member_f->rank_id]['forum_edit'] == 1) {
				?>
			<form action="p.php?p=gang&page=forum&edit=<?php print(Input::get('edit'));?>" method="POST">
				<ul>
					<li align="center"><textarea name="body" rows="5" cols="60"><?php print($post_i->body);?></textarea></li>
					<li align="right">
						<input type="hidden" name="token" value="<?php echo Token::generate('EDIT_POST'); ?>">
						<input type="submit" value="Muuda">
					</li>
				</ul>
			</form>
				<?php
			} else {
				Redirect::to('p.php?p=gang&page=forum');
			}
			?>
			<?php
		} else if (Input::get('f') == 'new') {

		?>
		<form action="p.php?p=gang&page=forum&f=new" method="POST">
			<table>
				<tr>
					<td width="20%">Teema nimi:</td>
					<td width="80%"><input type="text" name="topic" autocomplete="off"></td>
				</tr>
				<tr>
					<td>Teema sisu:</td>
					<td><textarea name="body" rows="5" cols="60"></textarea></td>
				</tr>
				<tr>
					<td></td>
					<td>
						<input type="hidden" name="token" value="<?php echo Token::generate('NEW_TOPIC'); ?>">
						<input type="submit" value="Postita uus teema">
					</td>
				</tr>
			</table>
		</form>
		<?php

		} else {
			$gang_forum_topic_query = DB::getInstance()->query("SELECT * FROM `gang_forum_topic` WHERE `gang_id` = " . $gang_info->id . " AND `deleted` = 0 ORDER BY `date` DESC");
			foreach ($gang_forum_topic_query->results() as $topic) {
				$user_i_query = DB::getInstance()->query("SELECT `username` FROM `users` WHERE `id` = " . $topic->user_id);
				$user_i = $user_i_query->first();

				$count_posts_query = DB::getInstance()->query("SELECT * FROM `gang_forum_post` WHERE `topic_id` = " . $topic->topic_id);
				$count_posts = $count_posts_query->count();

				$output_line .= '
					<tr>
						<td><a href="p.php?p=gang&page=forum&topic='.$topic->id.'">'.$topic->subject.'</a></td>
						<td align="center"><a href="p.php?p=profile&user='.$user_i->username.'">'.$user_i->username.'</a></td>
						<td align="center">'.$_GENERAL->format_number($count_posts).'</td>
						<td align="center">'.$topic->date.'</td>
					</tr>
				';
			}
		?>
		<a href="p.php?p=gang&page=forum&f=new">Tee uus teema</a>
		<table>
			<tr>
				<th width="50%">Teema</th>
				<th width="20%">Autor</th>
				<th width="10%">Postitusi</th>
				<th width="20%">Viimane postitus</th>
			</tr>
			<?php print($output_line);?>
		</table>
		<?php
		}
		?>
	</p>
</div>