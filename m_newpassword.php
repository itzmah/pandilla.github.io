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
	if(Token::check(Input::get('token')) ) {

		$username = Input::get('username');
		$email = Input::get('email');

		$world = Input::get('world');
		if ($world != 1 AND $world != 2) {
			$world = 1;
		}

		$check_username = DB::getInstance($world)->get('users', array('username', '=', $username));

		if (empty($username) === true || empty($email) === true) {
			$_GENERAL->addError('Sisestage kasutajanimi ja email.');
		} else if ($check_username->count() == 0) {
			$_GENERAL->addError('Sellise nimega kasutajat ei eksisteeri.');
		} else if (strtolower($check_username->first()->email) != strtolower($email)) {
			$_GENERAL->addError('Kasutajanimi ja email ei ühti.');
		}

		if(empty($_GENERAL->errors()) === true) {
			$u = new User($user = $username, $world = $world);
			$u->password_recovery($username, $world);
			Session::flash('newpassword', 'Teie uus parool on teile emailile saadetud.');
			Redirect::to('p.php?p=newpassword');
		}
	}
}

include("includes/overall/header.php");
?>
<div id="page">
	<div class="page-title">Unustasid parooli</div>
	<p>
<?php
if (empty($_GENERAL->errors()) === false) {
	print($_GENERAL->output_errors());
}

if(Session::exists('newpassword')) {
	$_GENERAL->addOutSuccess(Session::flash('newpassword'));
	print($_GENERAL->output_success());
}
?>
		<form action="" method="post">
			<ul>
				<li>Teie kasutajanimi:</li>
				<li><input type="text" name="username"></li>
				<li>Teie email:</li>
				<li><input type="text" name="email"></li>
				<li>Maailm:</li>
				<li>
					<select name="world">
						<option value="1">1. Maailm (Vana)</option>
						<option value="2">2. Maailm (Uus)</option>
					</select>
				</li>
				<input type="hidden" name="token" value="<?php echo Token::generate(); ?>">
				<li><input type="submit" name="go" value="Telli omale uus parool"></li>
			</ul>
		</form>
	</p>
</div>

<?php
include("includes/overall/footer.php");
