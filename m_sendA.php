<?php
/*
 * Name: FreeLand v2
 * Author: Marko Murumaa
 * Created: August 2015
 * Website: www.freelandplay.eu
*/

require_once("core/init.php");

if(Input::exists('get')) {
	if(empty(Input::get('world')) === false && empty(Input::get('username')) === false) {
		$world = Input::get('world');
		if ($world != 1 AND $world != 2) {
			$world = 1;
		}

		$user_query = DB::getInstance($world)->query("SELECT * FROM `users` WHERE `username` = ?", array(Input::get('username')));
		if ($user_query->count() < 1) {
			$_GENERAL->addError('Sellist kasutajat ei leitud.');
		} else {
			$user_i = $user_query->first();

			if ($user_i->active == 1) {
				$_GENERAL->addError('Teie kasutaja on juba aktiveeritud.');
			}

			if (empty($_GENERAL->errors()) === true) {
	
				$register_text .= "Tere, ".$user_i->firstname." ".$user_i->lastname."!\n";
				$register_text .= "Te olete registreerinud mängu FreeLand (www.freelandplay.eu).\n";
				$register_text .= "\n";
				$register_text .= "Mängu maailm: ".$world."\n";
				$register_text .= "Kasutajanimi: ".Input::get('username')."\n";
				$register_text .= "\n";
				$register_text .= "Kasutaja aktiveerimiseks vajutage järgmisele lingile: \n";
				$register_text .= "http://freelandplay.eu/p.php?p=activation&world=".$world."&username=".$user_i->username."&code=".$user_i->activation_code."\n";
				$register_text .= "\n";
				$register_text .= "Kui teie ei ole FreeLand-i kasutajat registreerinud siis kustutage see kiri.\n";
				$register_text .= "\n";
				$register_text .= "- FreeLand-i meeskond\n";

				$email = $user_i->email;
				$email_title = 'FreeLand - Kasutaja aktiveerimine';
				$email_from = $_GENERAL->settings('settings_game','GAME_EMAIL');
				$_GENERAL->email($email, $email_title, $register_text, $email_from);
				
				Session::flash('activate', 'Teie emailile saadeti aktiveerimis kiri uuesti.');
				Redirect::to('p.php?p=sendA');
			}
		}
	} else {
		$_GENERAL->addError('Aadress on vigane.');
	}
}

include("includes/overall/header.php");
?>
<div id="page">
	<div class="page-title">Aktiveerimis kirja saatmine</div>
	<p>
	<?php
	if(Session::exists('activate')) {
		$_GENERAL->addOutSuccess(Session::flash('activate'));
		print($_GENERAL->output_success());
	} else {
		if (empty($_GENERAL->errors()) === false) {
			print($_GENERAL->output_errors());
		}
	}
	?>
	</p>
</div>

<?php
include("includes/overall/footer.php");
