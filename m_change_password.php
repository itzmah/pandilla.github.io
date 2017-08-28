<?php
/*
 * Name: FreeLand v2
 * Author: Marko Murumaa
 * Created: July 2015
 * Website: www.freelandplay.eu
*/

$_linr = true;
require_once("core/init.php");

if(Input::exists('get')) {
	if(empty(Input::get('m')) === false && empty(Input::get('u')) === false && empty(Input::get('e')) === false && empty(Input::get('c')) === false) {
		$world = Input::get('m');
		if ($world != 1 AND $world != 2) {
			$world = 1;
		}

		$pass_q = DB::getInstance($world)->query("SELECT * FROM password_recovery WHERE activation_code = ? AND username = ? AND email = ?", array(Input::get('c'),Input::get('u'), Input::get('e')));
		if ($pass_q->count() < 1) {
			$_GENERAL->addError('Ei leidnud seda aadressi.');
		} else {
			if ($pass_q->first()->active == 0) {
				$_GENERAL->addError('Seda aadressi on juba kasutatud.');
			}

			if (empty($_GENERAL->errors()) === true) {
				$fields = array('password' => $pass_q->first()->newpassword);
				$pass_q->update('password_recovery', $pass_q->first()->id, array('active' => 0));
				$u = new User($user = Input::get('u'), $world = $world);
				$u->update($fields, $u->data()->id);

				Session::flash('newpassword', 'Teie uus parool on edukalt aktiveeritud.');
				Redirect::to('p.php?p=change_password');
			}
		}
	} else {
		$_GENERAL->addError('Ei leidnud seda aadressi.');
	}
}

include("includes/overall/header.php");
?>
<div id="page">
	<div class="page-title">Uus parool</div>
	<p>
	<?php
	if(Session::exists('newpassword')) {
		$_GENERAL->addOutSuccess(Session::flash('newpassword'));
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
