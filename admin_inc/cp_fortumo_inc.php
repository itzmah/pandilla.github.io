<?php
/*
 * Name: FreeLand v2
 * Author: Marko Murumaa
 * Created: July 2015
 * Website: www.freelandplay.eu
*/

if (!$_USER->hasPermission('cp_fortumo_services')) {
	Redirect::to('p.php?p=cpanel');
	exit();
}

$enabled_selected = null;

if ($_GENERAL->settings('settings_game','SMS_SERVICES_ENABLED') == 1) {
	$enabled_selected = ' selected';
}

if(Input::exists()) {
	if(Token::check(Input::get('token'), 'EDIT_FORTUMO') ) {
		DB::getInstance()->query("UPDATE settings_game SET value=? WHERE name='SMS_SERVICES_ENABLED'", [Input::get('service')]);
		DB::getInstance()->query("UPDATE settings_game SET value=? WHERE name='FORTUMO_SERVICEID'", [Input::get('serviceid')]);
		DB::getInstance()->query("UPDATE settings_game SET value=? WHERE name='FORTUMO_SERVICE_SECRET'", [Input::get('servicesecret')]);

		Session::flash('cpanel', 'Fortumo settings is  successfully edited.');
		Redirect::to('p.php?p=cpanel&cp=fortumo');
	}
}

?>
<h1>Fortumo services</h1>
<p>
<?php
if(Session::exists('cpanel')) {
	$_GENERAL->addOutSuccess(Session::flash('cpanel'));
	print($_GENERAL->output_success());
}
?>
	<form action="p.php?p=cpanel&cp=fortumo" method="POST">
		<ul>
			<li>Sms services: <br>
				<select name="service">
					<option value="0">Disabled</option>
					<option value="1"<?php print($enabled_selected);?>>Enabled</option>
				</select>
			</li>
			<li>Sms service id:<br><input type="text" name="serviceid" size="70" value="<?php print($_GENERAL->settings('settings_game','FORTUMO_SERVICEID'));?>"></li>
			<li>Sms service secret:<br><input type="text" name="servicesecret" size="70" value="<?php print($_GENERAL->settings('settings_game','FORTUMO_SERVICE_SECRET'));?>"></li>
			<li>
				<input type="hidden" name="token" value="<?php echo Token::generate('EDIT_FORTUMO'); ?>">
				<input type="submit" value="Edit">
			</li>
		</ul>
	</form>
</p>