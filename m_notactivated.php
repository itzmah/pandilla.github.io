<?php
/*
 * Name: FreeLand v2
 * Author: Marko Murumaa
 * Created: July 2015
 * Website: www.freelandplay.eu
*/

$_lir = true;
require_once("core/init.php");

if ($_USER->data()->active == 1) {
	Redirect::to('p.php?p=home');
}

$birth_time = strtotime($_USER->data()->birth);
$gender = ($_USER->data()->gender == 1) ? '' : ' selected';

if(Input::exists()) {
	if(Token::check(Input::get('token'), 'NEW_INFO') ) {
		$required_fields = array('firstname', 'lastname', 'day', 'month', 'year', 'gender', 'email');
			foreach($_POST as $key=>$value) {
				if (empty($value) && in_array($key, $required_fields) === true) {
					$_GENERAL->addError("Kõik väljad on kohustuslikud.");
					break 1;
				}
			}

		if (empty($_GENERAL->errors()) === true) {
			if (Input::get('gender') < 1 || Input::get('gender') > 2 ) {
				$_GENERAL->addError("Sugu on valesti sisestatud.");
			}

			if ((int)Input::get('day') <= 0 || (int)Input::get('day') >= 32) {
				$_GENERAL->addError("Päev on valesti sisestatud.");
			}

			if (strlen(Input::get('year')) !== 4 || Input::get('year') < 1900 || Input::get('year') > date("Y")) {
				$_GENERAL->addError("Aasta on valesti sisestatud.");
			}

			if ((int)Input::get('month') < 1 || (int)Input::get('month') > 13 ) {
				$_GENERAL->addError("Kuu on valesti sisestatud.");
			}

			if (filter_var(Input::get('email'), FILTER_VALIDATE_EMAIL) === false) {
				$_GENERAL->addError("Emaili aadress peab olema kehtiv.");
			}

			if (empty($_GENERAL->errors()) === true) {
				$activation_code = Hash::unique();

				$email_text .= "Tere, ".Input::get('firstname')." ".Input::get('lastname')."!\n";
				$email_text .= "Te olete registreerinud mängu FreeLand (www.freelandplay.eu).\n";
				$email_text .= "\n";
				$email_text .= "Mängu maailm: ".$_WORLD."\n";
				$email_text .= "Kasutajanimi: ".$_USER->data()->username."\n";
				$email_text .= "\n";
				$email_text .= "Kasutaja aktiveerimiseks vajutage järgmisele lingile: \n";
				$email_text .= "http://freelandplay.eu/p.php?p=activation&world=".$_WORLD."&username=".$_USER->data()->username."&code=".$activation_code."\n";
				$email_text .= "\n";
				$email_text .= "Kui teie ei ole FreeLand-i kasutajat registreerinud siis kustutage see kiri.\n";
				$email_text .= "\n";
				$email_text .= "- FreeLand-i meeskond\n";

				$email = Input::get('email');
				$email_title = 'FreeLand - Kasutaja aktiveerimine';
				$email_from = $_GENERAL->settings('settings_game','GAME_EMAIL');
				$_GENERAL->email($email, $email_title, $email_text, $email_from);

				$_USER->update(array(
					'firstname' => Input::get('firstname'),
					'lastname' => Input::get('lastname'),
					'birth' => Input::get('year').'-'.Input::get('month').'-'.Input::get('day'),
					'gender' => Input::get('gender'),
					'email' => Input::get('email'),
					'activation_code' => $activation_code
				),$_USER->data()->id, 'users');

				Session::flash('notactivated', 'Aktiveerimis link on saadetud teie emailile.');
				Redirect::to('p.php?p=notactivated');
			}
		}
	}
}

include("includes/overall/header.php");
?>

<div id="page">
	<div class="page-title">Kasutaja aktiveerimine</div>
	<p>
	<?php
	if (empty($_GENERAL->errors()) === false) {
		print($_GENERAL->output_errors());
	}

	if(Session::exists('notactivated')) {
		$_GENERAL->addOutSuccess(Session::flash('notactivated'));
		print($_GENERAL->output_success());
	}
	?>
		<form action="p.php?p=notactivated" method="POST">
			<table>
				<tr>
					<td width="20%"></td>
					<td width="80%"></td>
				</tr>
				<tr>
					<td>Eesnimi:</td>
					<td>
						<input type="text" name="firstname" id="firstname" value="<?php print($_USER->data()->firstname);?>" placeholder="Eesnimi" autocomplete="off">
					</td>
				</tr>
				<tr>
					<td>Perekonnanimi:</td>
					<td>
						<input type="text" name="lastname" id="lastname" value="<?php print($_USER->data()->lastname);?>" placeholder="Perekonnanimi" autocomplete="off">
					</td>
				</tr>
				<tr>
					<td>Sünniaeg:</td>
					<td>
						<input type="text" name="day" id="day" maxlength="2" size="1" placeholder="DD" value="<?php print(date("d", $birth_time));?>" autocomplete="off">
						<input type="text" name="month" id="month" maxlength="2" size="1" placeholder="MM" value="<?php print(date("m", $birth_time));?>" autocomplete="off">
						<input type="text" name="year" id="year" maxlength="4" size="3" placeholder="YYYY" value="<?php print(date("Y", $birth_time));?>" autocomplete="off">
					</td>
				</tr>
				<tr>
					<td>Sugu:</td>
					<td>
						<select name="gender">
							<option value="1">Mees</option>
							<option value="2"<?php print($gender);?>>Naine</option>
						</select>
					</td>
				</tr>
				<tr>
					<td>Email:</td>
					<td>
						<input type="text" name="email" id="email" size="50" value="<?php print($_USER->data()->email);?>" placeholder="Email" autocomplete="off">
					</td>
				</tr>
				<tr>
					<td></td>
					<td>
						Teie isikuandmeid ei avalikustata kolmandatele isikutele. (v.a. Eesti Politsei küsimisel.)<br>
						Aktiveerides kasutaja meie mängu olete kohustatud nõustuma meie mängu <a href="p.php?p=rules">reegleitega</a>.
						<br>
						Isiku andmete vale sisestamine toob kaasa kasutaja blokeerimise.
						<br>
						Reeglite rikkumine toob kaasa kasutaja blokeerimise.
						<br><br>
						Kui te ei nõustu meie tingimustega siis lahkuge siit lehelt.
					</td>
				</tr>
				<tr>
					<td></td>
					<td>
						<input type="hidden" name="token" value="<?php echo Token::generate('NEW_INFO'); ?>">
						<input type="submit" value="Nõustun tingimustega ja saadan aktiveerimis koodi">
					</td>
				</tr>
			</table>
		</form>
	</p>
</div>

<?php
include("includes/overall/footer.php");
