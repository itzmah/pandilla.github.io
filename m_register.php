<?php
/*
 * Name: FreeLand v2
 * Author: Marko Murumaa
 * Created: July 2015
 * Website: www.freelandplay.eu
*/

$_linr = true;
require_once("core/init.php");

if(Input::exists()) {
	if(Token::check(Input::get('token'))) {

		$required_fields = array('username', 'password', 'password_again', 'firstname', 'lastname', 'day', 'month', 'year', 'gender', 'email');
			foreach($_POST as $key=>$value) {
				if (empty($value) && in_array($key, $required_fields) === true) {
					$_GENERAL->addError("Kõik väljad peale kutsuja on kohustuslikud.");
					break 1;
				}
			}

		if (Input::get('world') != 1 AND Input::get('world') != 2) {
			$_GENERAL->addError("Sellist maailma ei leitud.");
		}

		if (empty($_GENERAL->errors()) === true) {
			$register_world = Input::get('world');

			$check_username = DB::getInstance($register_world)->get('users', array('username', '=', Input::get('username')));
			if($check_username->count()) {
				$_GENERAL->addError("Teie valitud kasutajanimi on juba kasutusel.");
			}
			if (preg_match("/[^A-z0-9_\-]/", Input::get('username')) == 1) {
				$_GENERAL->addError("Kasutajanimi võib sisalda ainult tähti ja numberid.");
			}
			if (strlen(Input::get('username')) < 3) {
				$_GENERAL->addError("Kasutajanimi peab olema vahemalt 3 sümbolit pikk.");
			}
			if (strlen(Input::get('username')) > 20) {
				$_GENERAL->addError("Kasutajanimi võib olla maksimaalseslt 20 sümbolit pikk.");
			}
			if (strlen(Input::get('password')) < 6) {
				$_GENERAL->addError("Parool peab olema vahemalt 6 sümbolit pikk.");
			}
			if (Input::get('password') !== Input::get('password_again')) {
				$_GENERAL->addError("Teie sisestatud paroolid ei ühti omavahel.");
			}
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
			$check_email = DB::getInstance($register_world)->get('users', array('email', '=', Input::get('email')));
			if($check_email->count()) {
				$_GENERAL->addError("See emaili aadress on juba kasutusel.");
			}

			if(empty($_GENERAL->errors()) === true) {
				$ref_user_query = DB::getInstance($register_world)->get('users', array('username', '=', Input::get('referer')));
				if ($ref_user_query->count() > 0) {
					$ref_user = $ref_user_query->first()->id;
				} else {
					$ref_user = 0;
				}

				$user = new User($user = 0, $world = $register_world);

				$activation_code = Hash::unique();
				$user->create(array(
					'username' => Input::get('username'),
					'password' => Hash::make(Input::get('password')),
					'firstname' => Input::get('firstname'),
					'lastname' => Input::get('lastname'),
					'birth' => Input::get('year').'-'.Input::get('month').'-'.Input::get('day'),
					'gender' => Input::get('gender'),
					'email' => Input::get('email'),
					'referer' => $ref_user,
					'active' => 0,
					'activation_code' => $activation_code,
					'groups' => 1
				));

				$register_text .= "Tere, ".Input::get('firstname')." ".Input::get('lastname')."!\n";
				$register_text .= "Te olete registreerinud mängu FreeLand (www.freelandplay.eu).\n";
				$register_text .= "\n";
				$register_text .= "Mängu maailm: ".$register_world."\n";
				$register_text .= "Kasutajanimi: ".Input::get('username')."\n";
				$register_text .= "\n";
				$register_text .= "Kasutaja aktiveerimiseks vajutage järgmisele lingile: \n";
				$register_text .= "http://freelandplay.eu/p.php?p=activation&world=".$register_world."&username=".Input::get('username')."&code=".$activation_code."\n";
				$register_text .= "\n";
				$register_text .= "Kui teie ei ole FreeLand-i kasutajat registreerinud siis kustutage see kiri.\n";
				$register_text .= "\n";
				$register_text .= "- FreeLand-i meeskond\n";

				$email = Input::get('email');
				$email_title = 'FreeLand - Kasutaja aktiveerimine';
				$email_from = $_GENERAL->settings('settings_game','GAME_EMAIL');
				$_GENERAL->email($email, $email_title, $register_text, $email_from);


				Session::flash('register', 'Teie kasutaja on edukalt registreeritud ja teie emailile saadeti aktiveerimis link.');
				Redirect::to('p.php?p=register');
			}

		}
	}
}

include("includes/overall/header.php");
?>

<div id="page">
	<div class="page-title">Registreeri</div>
	<p>
	<?php
	if (empty($_GENERAL->errors()) === false) {
		print($_GENERAL->output_errors());
	}

	if(Session::exists('register')) {
		$_GENERAL->addOutSuccess(Session::flash('register'));
		print($_GENERAL->output_success());
	}
	?>
		<form action="" method="post">
			<table width="100%">
				<tr>
					<td width="20%">Kasutajanimi:</td>
					<td width="80%"><input type="text" name="username" id="username" value="<?php echo escape(Input::get('username'));?>" placeholder="Kasutajanimi" autocomplete="off"></td>
				</tr>
				<tr>
					<td>Parool:</td>
					<td><input type="password" name="password" id="password" placeholder="Parool"></td>
				</tr>
				<tr>
					<td>Parool uuesti:</td>
					<td><input type="password" name="password_again" id="password_again" placeholder="Parool uuesti"></td>
				</tr>
				<tr>
					<td>Eesnimi:</td>
					<td>
						<input type="text" name="firstname" id="firstname" value="<?php echo escape(Input::get('firstname'));?>" placeholder="Eesnimi" autocomplete="off">
					</td>
				</tr>
				<tr>
					<td>Perekonnanimi:</td>
					<td>
						<input type="text" name="lastname" id="lastname" value="<?php echo escape(Input::get('lastname'));?>" placeholder="Perekonnanimi" autocomplete="off">
					</td>
				</tr>
				<tr>
					<td>Sünniaeg:</td>
					<td>
						<input type="text" name="day" id="day" maxlength="2" size="1" placeholder="DD" value="<?php echo escape(Input::get('day'));?>" autocomplete="off">
						<input type="text" name="month" id="month" maxlength="2" size="1" placeholder="MM" value="<?php echo escape(Input::get('month'));?>" autocomplete="off">
						<input type="text" name="year" id="year" maxlength="4" size="3" placeholder="YYYY" value="<?php echo escape(Input::get('year'));?>" autocomplete="off">
					</td>
				</tr>
				<tr>
					<td>Sugu:</td>
					<td>
						<select name="gender">
							<option value="1">Mees</option>
							<option value="2">Naine</option>
						</select>
					</td>
				</tr>
				<tr>
					<td>Email:</td>
					<td>
						<input type="text" name="email" id="email" value="<?php echo escape(Input::get('email'));?>" placeholder="Email" autocomplete="off">
						<br>
						<i>Teie emailile saadetakse aktiveerimis link.</i>
					</td>
				</tr>
				<tr>
					<td>Maailm:</td>
					<td>
						<select name="world">
							<option value="1">1. Maailm (Vana)</option>
							<option value="2" selected>2. Maailm (Uus)</option>
						</select>
					</td>
				</tr>
				<tr>
					<td>Kutsuja:</td>
					<td>
						<input type="text" name="referer" value="<?php echo escape(Input::get('referer'));?>" placeholder="Kutsuja kasutajanimi" autocomplete="off">
						<br>
						<i>Kui teid ei kutsunud keegi jätke kasutajanimi sisestamata.</i>
					</td>
				</tr>
				<tr>
					<td></td>
					<td><br>
						Teie isikuandmeid ei avalikustata kolmandatele isikutele.<br>
						Registreerides meie mängu olete kohustatud nõustuma meie mängu <a href="p.php?p=rules">reeglitega</a>.
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
						<input type="hidden" name="token" value="<?php echo Token::generate(); ?>">
						<input type="submit" value="Nõustun tingimustega ja registreerin kasutaja">
					</td>
				</tr>
			</table>
			
		</form>
	</p>
</div>

<?php
include("includes/overall/footer.php");
