<?php
/*
 * Name: FreeLand v2
 * Author: Marko Murumaa
 * Created: July 2015
 * Website: www.freelandplay.eu
*/

require_once("core/init.php");

if(Input::exists('get')) {
	if(empty(Input::get('world')) === false && empty(Input::get('username')) === false && empty(Input::get('code')) === false) {
		$world = Input::get('world');
		if ($world != 1 AND $world != 2) {
			$world = 1;
		}

		$activate_q = DB::getInstance($world)->query("SELECT * FROM `users` WHERE `username` = ?", array(Input::get('username')));
		if ($activate_q->count() < 1) {
			$_GENERAL->addError('Sellist kasutajat ei leitud.');
		} else {
			$activate_i = $activate_q->first();

			if ($activate_i->active == 1) {
				$_GENERAL->addError('Teie kasutaja on juba aktiveeritud.');
			}

			if (Input::get('code') != $activate_i->activation_code) {
				$_GENERAL->addError('Aktiveerimis kood on vale.');
			}

			if (empty($_GENERAL->errors()) === true) {

				DB::getInstance($world)->update('users', $activate_i->id, array('active' => 1));

				Session::flash('activate', 'Teie kasutaja on edukalt aktiveeritud.');
				Redirect::to('p.php?p=activation');
			}
		}
	} else {
		$_GENERAL->addError('Aadress on vigane.');
	}
}

include("includes/overall/header.php");
?>
<div id="page">
	<div class="page-title">Kasutaja aktiveerimine</div>
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
