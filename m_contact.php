<?php
/*
 * Name: FreeLand v2
 * Author: Marko Murumaa
 * Created: July 2015
 * Website: www.freelandplay.eu
*/

require_once("core/init.php");

$contact_types = null;
$ctypes = DB::getInstance(1)->query('SELECT * FROM contact_types');
foreach ($ctypes->results() as $type) {
	$contact_types .= '<option value="' . $type->id . '">' . $type->name . '</option>';
}

if (Input::exists()) {
	if (Token::check(Input::get('token'))) {

		if (empty(Input::get('name')) === true) {
			$_GENERAL->addError('Palun sisestage oma nimi.');
		}
		if (strlen(Input::get('name')) > 255) {
			$_GENERAL->addError('Nimi ei saa olla pikem kui 255 sümbolit.');
		}
		if (filter_var(Input::get('email'), FILTER_VALIDATE_EMAIL) === false) {
			$_GENERAL->addError('Emaili aadress peab olema kehtiv.');
		}
		if (Input::get('msg-type') <= 0 || Input::get('msg-type') > $ctypes->count()) {
			$_GENERAL->addError('Te sisestasite vale sõnumi tüübi.');
		}
		if (strlen(Input::get('msg-subject')) > 255) {
			$_GENERAL->addError('Pealkiri ei saa olla pikem kui 255 sümbolit.');
		}
		if (empty(Input::get('msg-body')) === true) {
			$_GENERAL->addError('Palun kirjutage sõnum ka.');
		}

		if (empty($_GENERAL->errors()) === true) {
			if ($_USER->isLoggedIn()) {
				$username = $_USER->data()->username;
			} else {
				$username = 'NOT_LOGGED_IN';
			}
			$contact_fields = array(
				'type' => Input::get('msg-type'),
				'subject' => Input::get('msg-subject'),
				'body' => Input::get('msg-body'),
				'email' => Input::get('email'),
				'name' => Input::get('name'),
				'ip' => $_SERVER['REMOTE_ADDR'],
				'username' => $username,
			);
			DB::getInstance(1)->insert('contact_inbox', $contact_fields);

			$type_query = DB::getInstance(1)->get('contact_types', array('id', '=', Input::get('msg-type')));
			$type_i = $type_query->first();

			if ($type_i->priority_level == 1) {
				$send_body .= Input::get('msg-subject');
				$send_body .= "\n-\n";
				$send_body .= Input::get('msg-body');
				$send_body .= "\n\n- EMAIL -\n\n";
				$send_body .= Input::get('email');
				$_GENERAL->email('rmoisto@gmail.com', 'Priority message from FreeLand', $send_body, 'support@freelandplay.eu');
			}

			Session::flash('contact', 'Teie sõnum on edukalt meieni jõudnud.');
			Redirect::to('p.php?p=contact');
		}
	}
}
include("includes/overall/header.php");
?>
<div id="page">
	<div class="page-title">Kontakt</div>
	<p>
		<?php
		if (empty($_GENERAL->errors()) === false) {
			print($_GENERAL->output_errors());
		}

		if (Session::exists('contact')) {
			$_GENERAL->addOutSuccess(Session::flash('contact'));
			print($_GENERAL->output_success());
		}
		?>
	<table>
		<tr valign="top">
			<td width="20%"><img src="css/default/images/contact.png" width="100" height="100"></td>
			<td width="80%">
				Siin on kirjas kõik vajalik, et meiega ühendust saada.

			</td>
		</tr>
	</table>
	<b>Rauno Moisto</b> - mängu omanik, programmeerija<br>
	E-mail: <b><?php print($_GENERAL->settings('settings_game', 'GAME_EMAIL')); ?></b><br>
	<br>
	Mängu reeglite rikkumise kaebused saata e-maili aadressile <b><?php print($_GENERAL->settings('settings_game', 'GAME_EMAIL')); ?></b><br>
	Mängu <b>FreeLand</b> reeglid <a href="p.php?p=rules">loe siit</a>.<br><br>
	<b>NB! Kindlasti märkida kirja sisus ära ka kasutajanimi, sest muidu me ei saa aidata!</b><br>

	</p>
</div>
<div id="page">
	<div class="page-title">Saatke meile sõnum</div>
	<p>
	<form action="" method="POST">
		<table width="100%">
			<tr>
				<td width="20%">Teie nimi:</td>
				<td width="80%"><input type="text" name="name" id="name" autocomplete="off"></td>
			</tr>
			<tr>
				<td>Teie email:</td>
				<td><input type="text" name="email" id="email" autocomplete="off"></td>
			</tr>
			<tr>
				<td>Sõnumi tüüp:</td>
				<td><select name="msg-type" id="msg-type">
						<option value="0">Palun valige üks</option><?php print($contact_types); ?></select></td>
			</tr>
			<tr>
				<td>Sõnumi pealkiri:</td>
				<td><input type="text" name="msg-subject" id="msg-subject" autocomplete="off"></td>
			</tr>
			<tr>
				<td>Sõnum:</td>
				<td><textarea name="msg-body" id="msg-body" cols="60" rows="10"></textarea></td>
			</tr>
			<tr>
				<td></td>
				<td>
					<input type="hidden" name="token" value="<?php echo Token::generate(); ?>">
					<input type="submit" value="Saada sõnum">
				</td>
			</tr>
		</table>
	</form>
	</p>
</div>
<?php
include("includes/overall/footer.php");
