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
?>

<div id="page">
	<div class="page-title">Postkast</div>
	<p>
	<?php
	if (empty($_GENERAL->errors()) === false) {
		print($_GENERAL->output_errors());
	}

	if(Session::exists('mail')) {
		$_GENERAL->addOutSuccess(Session::flash('mail'));
		print($_GENERAL->output_success());
	}
	?>
		<table>
			<tr valign="top">
				<td width="20%"><img src="css/default/images/mail.png" width="100" height="100"></td>
				<td width="80%">
					Postkast on koht kus kohas te saate teiste inimestega suhelda omavahel.

				</td>
			</tr>
			<tr>
				<td></td>
				<td align="right"><a href="p.php?p=mail&new=1">Saada uus kiri</a> | <a href="p.php?p=mail&inbox=1">Saabunud kirjad</a> | <a href="p.php?p=mail&outbox=1">Teie saadetud kirjad</a></td>
			</tr>
		</table>
	</p>
</div>
<?php
if (Input::get('inbox') == 1) {

	$inbox_per_page = 20;
	$inboxquery = DB::getInstance()->query("SELECT * FROM mail WHERE `to` = ".$_USER->data()->id." AND `deleted` = 0");
	$inbox_total = ceil($inboxquery->count() / $inbox_per_page );

	$page = (isset($_GET['page'])) ? (int)$_GET['page'] : 1;
	$start = ($page - 1) * $inbox_per_page;

	$x = ( $page - 1 ) * $inbox_per_page;

	if ( $x < 0 ) {
		$x = 0;
	}
	$count = $start;

	$msg_line = null;
	$inbox_query = DB::getInstance()->query("SELECT * FROM mail WHERE `to` = ".$_USER->data()->id." AND `deleted` = 0 ORDER BY id  DESC LIMIT $start, $inbox_per_page");

	foreach ($inbox_query->results() as $msg) {
		$count++;
		if ($msg->new == 1) {
			$inbox_new = '<b>Uus</b>';
		} else {
			$inbox_new = '<i>Loetud</i>';
		}

		$sender = new User($msg->from);
		$msg_line .= '
			<tr>
				<td align="center">'.$count.'</td>
				<td><a href="p.php?p=profile&user='.$sender->data()->username.'">'.$sender->data()->username.'</a></td>
				<td><a href="p.php?p=mail&read=1&id='.$msg->id.'">'.$msg->subject.'</a></td>
				<td align="center">'.$msg->date.'</td>
				<td align="center">'.$inbox_new.'</td>
			</tr>
		';
	}
?>
<div id="page">
	<div class="page-title">Saabunud kirjad</div>
	<p>
	<?php
	if (empty($msg_line) === false) {
	?>
		<table>
			<tr>
				<th width="10%">#</th>
				<th width="23%">Kellelt</th>
				<th width="37%">Pealkiri</th>
				<th width="20%">Kellaaeg</th>
				<th width="10%">#</th>
			</tr>
			<?php print($msg_line);?>
		</table>
		<br>
		<div align="center">Lehekülg: 
			<?php
				if ($inbox_total >= 1 && $page <= $inbox_total) {
					for ($x=1; $x<=$inbox_total; $x++) {
						echo ($x == $page) ? '<b>'.$x.'</b>  ' : '<a href="p.php?p=mail&inbox=1&page='.$x.'">'.$x.'</a> ';
					}
				}
			?>
		</div>
	<?php
	} else {
		$_GENERAL->addOutInfo('Teile ei ole saabunud ühtegi kirja.');
		print($_GENERAL->output_info());
	}
	?>
	</p>
</div>
<?php
} elseif (Input::get('outbox') == 1) {
	$msg_line = null;
	$count = 0;
	$inbox_query = DB::getInstance()->query("SELECT * FROM mail WHERE `from` = ".$_USER->data()->id." AND `deleted` = 0 ORDER BY id DESC");

	foreach ($inbox_query->results() as $msg) {
		$count++;
		if ($msg->new == 1) {
			$inbox_new = '<b>Uus</b>';
		} else {
			$inbox_new = '<i>Loetud</i>';
		}

		$to = new User($msg->to);
		$msg_line .= '
			<tr>
				<td align="center">'.$count.'</td>
				<td><a href="p.php?p=profile&user='.$to->data()->username.'">'.$to->data()->username.'</a></td>
				<td><a href="p.php?p=mail&read=2&id='.$msg->id.'">'.$msg->subject.'</a></td>
				<td align="center">'.$msg->date.'</td>
				<td align="center">'.$inbox_new.'</td>
			</tr>
		';
	}
?>
<div id="page">
	<div class="page-title">Teie saadetud kirjad</div>
	<p>
	<?php
	if (empty($msg_line) === false) {
	?>
		<table>
			<tr>
				<th width="10%">#</th>
				<th width="23%">Kellele</th>
				<th width="37%">Pealkiri</th>
				<th width="20%">Kellaaeg</th>
				<th width="10%">#</th>
			</tr>
			<?php print($msg_line);?>
		</table>
	<?php
	} else {
		$_GENERAL->addOutInfo('Te ei ole saatnud ühtegi kirja.');
		print($_GENERAL->output_info());
	}
	?>	
	</p>
</div>
<?php
} elseif (Input::get('new') == 1) {
	$reply_subject = null;
	$reply_msg = null;
	if (Input::get('reply') == 1) {
		$reply_sql = "SELECT * FROM mail WHERE `id` = " . (int)Input::get('id');
		$reply_query = DB::getInstance()->query($reply_sql);
		if ($reply_query->first()->to == $_USER->data()->id) {
			$reply_subject = 'RE: ' . $reply_query->first()->subject;
			$reply_msg = ($reply_query->first()->body);
		}
	}

	if(Input::exists()) {
		if(Token::check(Input::get('token'), 'MAILNEW') ) {
			if (empty(Input::get('username')) === true) {
				$_GENERAL->addError("Te unustasite täita kasutajanime välja.");
			}

			if (empty(Input::get('subject')) === true) {
				$_GENERAL->addError("Te unustasite täita pealkirja välja.");
			}

			if (empty(Input::get('msg')) === true) {
				$_GENERAL->addError("Te unustasite kirjutada sõnumi.");
			}

			if (strlen(Input::get('subject')) >= 255) {
				$_GENERAL->addError("Pealkiri on liiga pikk.");
			}

			if (strlen(Input::get('msg')) >= 3000) {
				$_GENERAL->addError("Sõnum on liiga pikk.");
			}

			if (empty($_GENERAL->errors()) === true) {
				$to = new User(Input::get('username'));
				if ($to->exists()) {
					$fields = array(
							'to' => $to->data()->id,
							'from' => $_USER->data()->id,
							'subject' => Input::get('subject'),
							'body' => Input::get('msg'));

					DB::getInstance()->insert('mail', $fields);

					Session::flash('mailnew', 'Teie kiri on edukalt saadetud.');
					Redirect::to('p.php?p=mail&new=1');
				} else {
					$_GENERAL->addError("Sellist kasutajat ei eksisteeri meie mängus.");
				}
			}
		}
	}
	?>
	<div id="page">
		<div class="page-title">Koosta uus kiri</div>
		<p>
	<?php
	if (empty($_GENERAL->errors()) === false) {
		print($_GENERAL->output_errors());
	}
	if(Session::exists('mailnew')) {
		$_GENERAL->addOutSuccess(Session::flash('mailnew'));
		print($_GENERAL->output_success());
	}
	?>
			<form action="p.php?p=mail&new=1" method="POST">
				<ul>
					<li>Kellele (kasutajanimi):<br /><input type="text" name="username" value="<?php print(Input::get('user'));?>"></li>
					<li>Pealkiri:<br /><input type="text" name="subject" value="<?php print($reply_subject);?>"></li>
					<li>Teie sõnum:<br /><br /><textarea name="msg" cols="50" rows="10"><?php print($reply_msg);?></textarea></li>
					<li>
						<input type="hidden" name="token" value="<?php echo Token::generate('MAILNEW'); ?>">
						<input type="submit" name="sendnew" value="Saada kiri">
					</li>
				</ul>
			</form>
		</p>
	</div>
<?php
} elseif (Input::get('read') == 1) {
	if (empty(Input::get('id')) === false) {
		$read_sql = "SELECT * FROM mail WHERE `id` = " . (int)Input::get('id');
		$read_query = DB::getInstance()->query($read_sql);
		if ($read_query->first()->to == $_USER->data()->id or $read_query->first()->deleted == 0) {

			$read_i = $read_query->first();
			$read_query->update('mail', Input::get('id'), array('new' => 0));

			$from_username_query = DB::getInstance()->query("SELECT `username` FROM `users` WHERE `id` = " . $read_i->from);
			$from_username = $from_username_query->first()->username;

			$to_username_query = DB::getInstance()->query("SELECT `username` FROM `users` WHERE `id` = " . $read_i->to);
			$to_username = $to_username_query->first()->username;

?>
<div id="page">
	<div class="page-title">Loe kirja</div>
	<p>
		<div align="right" style="padding-right: 10px;"><a href="p.php?p=mail&new=1&reply=1&id=<?php print($read_i->id);?>&user=<?php print($from_username);?>">Vasta kirjale</a> | <a href="p.php?p=mail&delete=1&id=<?php print($read_query->first()->id);?>">Kustuta kiri</a></div>
		<table>
			<tr>
				<td width="10%"><b>Kellelt</b>:</td>
				<td width="90%"><a href="p.php?p=profile&user=<?php print($from_username);?>"><?php print($from_username);?></a></td>
			</tr>
			<tr>
				<td><b>Kellele</b>:</a></td>
				<td><a href="p.php?p=profile&user=<?php print($to_username);?>"><?php print($to_username);?></td>
			</tr>
			<tr>
				<td><b>Kellaaeg</b>:</td>
				<td><?php print($read_i->date);?></td>
			</tr>
			<tr>
				<td><b>Pealkiri</b>:</td>
				<td><?php print($read_i->subject);?></td>
			</tr>
			<tr>
				<td>&nbsp;</td>
				<td>&nbsp;</td>
			</tr>
			<tr valign="top">
				<td><b>Sõnum</b>:</td>
				<td><?php print(nl2br($read_i->body));?></td>
			</tr>
		</table>
	</p>
</div>
<?php
		} else {
			$_GENERAL->addError("Seda kirja ei leitud.");
			if (empty($_GENERAL->errors()) === false) {
				print($_GENERAL->output_errors());
			}
		}
	}
} elseif (Input::get('read') == 2) {
	if (empty(Input::get('id')) === false) {
		$read_sql = "SELECT * FROM mail WHERE `id` = " . (int)Input::get('id');
		$read_query = DB::getInstance()->query($read_sql);
		if ($read_query->first()->from == $_USER->data()->id or $read_query->first()->deleted == 0) {
			$read_i = $read_query->first();

			$from_username_query = DB::getInstance()->query("SELECT `username` FROM `users` WHERE `id` = " . $read_i->from);
			$from_username = $from_username_query->first()->username;

			$to_username_query = DB::getInstance()->query("SELECT `username` FROM `users` WHERE `id` = " . $read_i->to);
			$to_username = $to_username_query->first()->username;

?>
<div id="page">
	<div class="page-title">Loe kirja</div>
	<p>
		<table>
			<tr>
				<td width="10%"><b>Kellelt</b>:</td>
				<td width="90%"><a href="p.php?p=profile&user=<?php print($from_username);?>"><?php print($from_username);?></a></td>
			</tr>
			<tr>
				<td><b>Kellele</b>:</td>
				<td><a href="p.php?p=profile&user=<?php print($to_username);?>"><?php print($to_username);?></a></td>
			</tr>
			<tr>
				<td><b>Kellaaeg</b>:</td>
				<td><?php print($read_i->date);?></td>
			</tr>
			<tr>
				<td><b>Pealkiri</b>:</td>
				<td><?php print($read_i->subject);?></td>
			</tr>
			<tr>
				<td>&nbsp;</td>
				<td>&nbsp;</td>
			</tr>
			<tr>
				<td><b>Sõnum</b>:</td>
				<td><?php print(nl2br($read_i->body));?></td>
			</tr>
		</table>
	</p>
</div>
<?php
		} else {
			$_GENERAL->addError("Seda kirja ei leitud.");
			if (empty($_GENERAL->errors()) === false) {
				print($_GENERAL->output_errors());
			}
		}
	}
} elseif (Input::get('delete') == 1) {

	if (empty(Input::get('id')) === false) {
		$delete_sql = "SELECT * FROM mail WHERE `id` = " . (int)Input::get('id');
		$delete_query = DB::getInstance()->query($delete_sql);
		if ($delete_query->first()->to == $_USER->data()->id) {
			DB::getInstance()->update('mail', Input::get('id'), array('deleted' => 1));

			Session::flash('maildelete', 'Teie kiri on edukalt kustutatud.');
			Redirect::to('p.php?p=mail&delete=1');
		} else {
			$_GENERAL->addError("Teie kirja ei leitud.");
		}
	}
?>
<div id="page">
	<div class="page-title">Kustuta kiri</div>
	<p>
<?php 
if (empty($_GENERAL->errors()) === false) {
	print($_GENERAL->output_errors());
}
if(Session::exists('maildelete')) {
	$_GENERAL->addOutSuccess(Session::flash('maildelete'));
	print($_GENERAL->output_success());
}
?>
	</p>
</div>
<?php

}
?>

<?php
include("includes/overall/footer.php");
