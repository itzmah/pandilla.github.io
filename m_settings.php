<?php
/*
 * Name: FreeLand v2
 * Author: Marko Murumaa
 * Created: July 2015
 * Website: www.freelandplay.eu
*/

$_lir = true;
require_once("core/init.php");

if(Input::exists()) {
	if(Token::check(Input::get('token'), 'EDIT_SETTINGS') ) {
		if (empty(Input::get('password')) === true) {
			$_GENERAL->addError("Kehtiv parool on kohustuslik kui te soovite muuta seadeid.");
		} else {
			if (Hash::verify(Input::get('password'), $_USER->data()->password)) {
				if (empty(Input::get('newpassword')) === false) {
					if (strlen(Input::get('newpassword')) < 6) {
						$_GENERAL->addError("Parool peab olema vahemalt 6 sümbolit pikk.");
					}
					
					if (Input::get('newpassword') !== Input::get('newpassword2x')) {
						$_GENERAL->addError("Paroolid ei klapi omavahel.");
					}
				}
			} else {
				$_GENERAL->addError("Teie sisestatud kehtiv parool on vale.");
			}
		}

		if (empty($_GENERAL->errors()) === true) {
			if (empty(Input::get('newpassword')) === false) {
				$newpassword_hash = Hash::make(Input::get('newpassword'));
				$_USER->update(array(
					'password' => $newpassword_hash
				));
			}

			Session::flash('settings', 'Teie konto seaded on edukalt uuendatud.');
			Redirect::to('p.php?p=settings');
		}
	} else if(Token::check(Input::get('token'), 'DELETE_ACCOUNT') ) {
		if (Hash::verify(Input::get('password'), $_USER->data()->password)) {
			$_USER->update(array(
					'groups' => 3,
					'ban_text' => "Kasutaja soovis oma konto kustutada."
				));
		} else {
			$_GENERAL->addError("Teie sisestatud kehtiv parool on vale.");
		}
	}
}

include("includes/overall/header.php");
?>

<div id="page">
	<div class="page-title">Konto seaded</div>
	<p>
	<?php
	if (empty($_GENERAL->errors()) === false) {
		print($_GENERAL->output_errors());
	}

	if(Session::exists('settings')) {
		$_GENERAL->addOutSuccess(Session::flash('settings'));
		print($_GENERAL->output_success());
	}
	?>
	<table>
			<tr valign="top">
				<td width="20%"><img src="css/default/images/settings.png" width="100" height="100"></td>
				<td width="80%">
					Siin saate muuta oma konto seadeid.<br>
					Konto seadete muutmiseks tuleb sisestada kasutaja kehtiv parool.
				</td>
			</tr>
		</table>
	</p>
	<form action="p.php?p=settings" method="POST">
		<table>
			<tr>
				<td width="20%">Kehtiv parool:</td>
				<td width="80%"><input type="password" name="password" id="password" placeholder="Kehtiv parool"></td>
			</tr>
			<tr>
				<td></td>
				<td><i>Kehtiv parool on kohustuslik kui te soovite muuta konto seadeid.</i></td>
			</tr>
			<tr>
				<td>Uus parool:</td>
				<td><input type="password" name="newpassword" id="newpassword" placeholder="Uus parool"></td>
			</tr>
			<tr>
				<td></td>
				<td><i>Kirjuta uus parool ainult juhul kui sa soovid seda muuta.</i></td>
			</tr>
			<tr>
				<td>Uus parool uuesti:</td>
				<td><input type="password" name="newpassword2x" id="newpassword2x" placeholder="Uus parool uuesti"></td>
			</tr>
			<tr>
				<td></td>
				<td>
					<input type="hidden" name="token" value="<?php echo Token::generate('EDIT_SETTINGS'); ?>">
					<input type="submit" value="Muuda konto seadeid">
				</td>
			</tr>
		</table>
	</form>
</div>
<div id="page">
	<div class="page-title">Kustuta oma kasutaja</div>
	<br>
	<?php
		$_GENERAL->addOutInfo("Peale kasutaja kustutamiset läheb kasutaja blokeeritud staatusesse.");
		print($_GENERAL->output_info());
	?>
	<form action="p.php?p=settings" method="POST">
		<table>
			<tr>
				<td width="35%">Sisestage kehtiv kasutaja parool:</td>
				<td width="65%">
					<input type="password" name="password" placeholder="Kehtiv parool">
					<input type="hidden" name="token" value="<?php echo Token::generate('DELETE_ACCOUNT'); ?>">
					<input type="submit" value="Kustuta oma kasutaja">
				</td>
			</tr>
		</table>
	</form>
</div>

<?php
include("includes/overall/footer.php");
