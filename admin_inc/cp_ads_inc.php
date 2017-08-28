<?php
/*
 * Name: FreeLand v2
 * Author: Marko Murumaa
 * Created: July 2015
 * Website: www.freelandplay.eu
*/

if (!$_USER->hasPermission('cp_ads')) {
	Redirect::to('p.php?p=cpanel');
	exit();
}

$enabled_selected = null;

if ($_GENERAL->settings('settings_game','ADS_ENABLED') == 1) {
	$enabled_selected = ' selected';
}

if(Input::exists()) {
	if(Token::check(Input::get('token'), 'EDIT_ADS') ) {
		DB::getInstance()->query("UPDATE settings_game SET value=? WHERE name='ADS_ENABLED'", [Input::get('is')]);
		DB::getInstance()->query("UPDATE settings_game SET value=? WHERE name='ADS_728x90'", [Input::get('top')]);
		DB::getInstance()->query("UPDATE settings_game SET value=? WHERE name='ADS_160x600'", [Input::get('side')]);

		Session::flash('cpanel', 'Ads system is successfully edited.');
		Redirect::to('p.php?p=cpanel&cp=ads');
	}
}

?>
<h1>Ads management</h1>
<p>
<?php
if(Session::exists('cpanel')) {
	$_GENERAL->addOutSuccess(Session::flash('cpanel'));
	print($_GENERAL->output_success());
}
?>
	<form action="p.php?p=cpanel&cp=ads" method="POST">
		<ul>
			<li>Ads system is: 
				<select name="is">
					<option value="0">Disabled</option>
					<option value="1"<?php print($enabled_selected);?>>Enabled</option>
				</select>
			</li>
			<li>Top banner <b>728x90</b> (Enter ads service code in there) <br><textarea name="top" cols="100" rows="10"><?php print($_GENERAL->settings('settings_game','ADS_728x90'));?></textarea></li>
			<li>Side banner <b>160x600</b> (Enter ads service code in there) <br><textarea name="side" cols="100" rows="10"><?php print($_GENERAL->settings('settings_game','ADS_160x600'));?></textarea></li>
			<li>
				<input type="hidden" name="token" value="<?php echo Token::generate('EDIT_ADS'); ?>">
				<input type="submit" value="Edit">
			</li>
		</ul>
	</form>
</p>
