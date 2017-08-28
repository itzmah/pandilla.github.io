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
	if(Token::check(Input::get('token')) || Token::check(Input::get('token'), 'LOGIN')) {

		if (empty(Input::get('username')) === true) {
			$_GENERAL->addError('Kasutajanime sisestamine on kohustuslik.');
		}

		if (empty(Input::get('password')) === true) {
			$_GENERAL->addError('Parooli sisestamine on kohustuslik.');
		}

		if (Input::get('world') != 1 AND Input::get('world') != 2) {
			$_GENERAL->addError("Sellist maailma ei leitud.");
		}

		if (empty($_GENERAL->errors()) === true) {
			$login_world = Input::get('world');
			$user = new User($user = null, $world = $login_world);
			$login = $user->login(Input::get('username'), Input::get('password'));

			if (!$user->exists()) {
				$_GENERAL->addError('Sellist kasutajanime ei leitud.');
			}
			
			if ($user->exists()) {
				
				if ($user->data()->groups == 3) {
					$_GENERAL->addError('Teie kasutaja on blokeeritud.');
				} 

				if ($user->data()->active == 0) {
					$_GENERAL->addError('Teie kasutaja on aktiveerimata. (<a href="p.php?p=sendA&world='.$world.'&username='.$user->data()->username.'">Saada aktiveerimis kiri uuesti</a>)');
				} 
				
			}

			if (empty($_GENERAL->errors()) === true) {
				if($login) {
					DB::getInstance($login_world)->insert('user_logs', array(
					'user_id' => $user->data()->id,
					'type' => 1,
					'body' => '<font color=green>Logisid sisse!</font><br> IP: '.$_SERVER['REMOTE_ADDR'].'',
					'active' => 0
					));

					Session::put("world", $login_world);
					Redirect::to('p.php?p=home');
				} else {
					DB::getInstance($login_world)->insert('user_logs', array(
					'user_id' => $user->data()->id,
					'type' => 1,
					'body' => '<font color=red>Üritati sisse logida!</font><br> IP: '.$_SERVER['REMOTE_ADDR'].'<br> Parooliks prooviti: '.Input::get('password').'',
					'active' => 1
					));
					$_GENERAL->addError('Kasutajanimi või parool on vale.');
				}
			} else {
				$user->logout();
			}
		}
	}
}

include("includes/overall/header.php");
?>
<div id="page">
	<div class="page-title">Logi sisse</div>
	<p>
<?php
if (empty($_GENERAL->errors()) === false) {
	print($_GENERAL->output_errors());
}
?>
		<form action="" method="POST">
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
					<td>Maailm:</td>
					<td><input type="radio" name="world" value="1">1 <input type="radio" name="world" value="2">2</td>
				</tr>
				<tr>
					<td></td>
					<td>
						<input type="hidden" name="token" value="<?php echo Token::generate(); ?>">
						<input type="submit" value="Logi sisse">
					</td>
				</tr>
				<tr>
					<td></td>
					<td><a href="p.php?p=newpassword">Unustasid parooli?</a></td>
				</tr>
			</table>
		</form>
	</p>
</div>

<?php
include("includes/overall/footer.php");
