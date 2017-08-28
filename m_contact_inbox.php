<?php
/*
 * Name: FreeLand v2
 * Author: Marko Murumaa
 * Created: July 2015
 * Website: www.freelandplay.eu
*/

$_lir = true;
require_once("core/init.php");

if (!$_USER->hasPermission('contact_inbox')) {
	Redirect::to('p.php?p=home');
	exit();
}

$output_line = null;
$inbox_query = DB::getInstance()->query("SELECT * FROM contact_inbox ORDER BY id DESC");
foreach ($inbox_query->results() as $msg) {

	$type_query = DB::getInstance()->query("SELECT * FROM contact_types WHERE id = ".$msg->type);
	$type = $type_query->first();
	$priority = ($type->priority_level == 1) ? "High" : "Low";
	$status = ($msg->status == 1) ? "<b>New</b>" : "<i>Read</i>";

	$output_line .= '
			<tr>
				<td><a href="p.php?p=contact_inbox&msg='.$msg->id.'">'.$msg->subject.'</a></td>
				<td align="center">'.$type->name.'</td>
				<td align="center">'.$msg->time.'</td>
				<td align="center">'.$priority.'</td>
				<td align="center">'.$status.'</td>
			</tr>
	';
}

include("includes/overall/header.php");
?>

<div id="page">
<?php
if (empty(Input::get('msg')) === false) {
	$msg_query = DB::getInstance()->query("SELECT * FROM contact_inbox WHERE id = " . (int)Input::get('msg'));
	if ($msg_query->count() <= 0) {
		Redirect::to('p.php?p=contact_inbox');
		exit();
	}
	$msg_data = $msg_query->first();
	DB::getInstance()->update('contact_inbox', $msg_data->id, array('status' => 0));

?>
	<div class="page-title">Read message</div>
	<p>
	<?php
	if(Session::exists('contact_inbox')) {
		$_GENERAL->addOutSuccess(Session::flash('contact_inbox'));
		print($_GENERAL->output_success());
	}
	?>
		<div align="right" style="padding-right: 10px;"><a href="p.php?p=contact_inbox&reply=<?php print($msg_data->id);?>">Reply to message</a></div>
		<table>
			<tr>
				<td><b>Sender name:</b> <?php print($msg_data->name);?></td>
			</tr>
			<tr>
				<td><b>Sender email:</b> <?php print($msg_data->email);?></td>
			</tr>
			<tr>
				<td><b>Sender username:</b> <?php print($msg_data->username);?></td>
			</tr>
			<tr>
				<td><b>Message recived:</b> <?php print($msg_data->time);?></td>
			</tr>
			<tr>
				<td><b>Message type:</b> Game bug</td>
			</tr>
			<tr>
				<td><b>Message subject:</b> <?php print($msg_data->subject);?></td>
			</tr>
			<tr>
				<td><b>Message:</b></td>
			</tr>
			<tr>
				<td><?php print(nl2br($msg_data->body));?></td>
			</tr>
		</table>
	</p>
<?php
} else if (empty(Input::get('reply')) === false) {
	$reply_query = DB::getInstance()->query("SELECT * FROM contact_inbox WHERE id = " . (int)Input::get('reply'));
	if ($reply_query->count() <= 0) {
		Redirect::to('p.php?p=contact_inbox');
		exit();
	}
	$reply_data = $reply_query->first();

	if(Input::exists()) {
		if(Token::check(Input::get('token'), 'REPLY')) {
			$to = $reply_data->email;
			$subject = $reply_data->subject;
			$body = Input::get('body');
			$from = $_GENERAL->settings('settings_game','GAME_EMAIL');
			$_GENERAL->email($to, $subject, $body, $from);

			Session::flash('contact_inbox', 'You have successfully replied to message.');
			Redirect::to('p.php?p=contact_inbox&msg='.$reply_data->id);
		}
	}

?>
	<div class="page-title">Reply</div>
	<p>
	<?php
	$_GENERAL->addOutInfo("Your web server must have SMTP enabled to reply.");
	print($_GENERAL->output_info());
	?>
		<form action="p.php?p=contact_inbox&reply=<?php print($reply_data->id);?>" method="POST">
			<ul>
				<li><b>Message subject:</b> <?php print($reply_data->subject);?></li>
				<li><b>Email:</b> <?php print($reply_data->email);?></li>
				<li><textarea name="body" rows="15" cols="100"><?php print($reply_data->body);?></textarea></li>
				<li>
					<input type="hidden" name="token" value="<?php echo Token::generate('REPLY'); ?>">
					<input type="submit" value="Reply to email">
				</li>
				<li><b>NB: This message is replied to <?php print($reply_data->email);?></b></li>
			</ul>
		</form>
	</p>
<?php

} else {
?>
	<div class="page-title">Inbox</div>
	<p>
	<?php
	if (empty($output_line) === true) {
		$_GENERAL->addOutInfo("Contact inbox is empty.");
		print($_GENERAL->output_info());
	} else {

	?>
		<table>
			<tr>
				<th width="40%">Subject</th>
				<th width="20%">Type</th>
				<th width="20%">Date</th>
				<th width="10%">Priority</th>
				<th width="10%">Status</th>
			</tr>
			<?php print($output_line);?>
		</table>
	<?php
	}
	?>
	</p>
<?php
}
?>
</div>

<?php
include("includes/overall/footer.php");
