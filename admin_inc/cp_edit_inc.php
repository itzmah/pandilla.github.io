<?php
/*
 * Name: FreeLand v2
 * Author: Marko Murumaa
 * Created: July 2015
 * Website: www.freelandplay.eu
*/

if (empty(Input::get('id')) === true || is_numeric(Input::get('id')) === false) {
	Redirect::to('p.php?p=cpanel');
	exit();
}

$edit_query =  DB::getInstance()->query("SELECT * FROM settings_game WHERE `id` = " . (int)Input::get('id'));
if ($edit_query->count() <= 0) {
	Redirect::to('p.php?p=cpanel');
	exit();
}

$edit_data = $edit_query->first();

if(Input::exists()) {
	if(Token::check(Input::get('token')) ) {
		if (empty(Input::get('value')) === true) {
			$_GENERAL->addError("Value is required.");
		}

		if (empty($_GENERAL->errors()) === true) {
			$edit_query->update('settings_game', Input::get('id'), array('value' => Input::get('value')));
			Session::flash('cpanel', 'Setting is successfully changed.');
			Redirect::to('p.php?p=cpanel&cp=edit&id='.Input::get('id'));
			exit();
		}
	}
}
?>
<div class="page-title">Muuda seadet</div>
<p>
<?php
if (empty($_GENERAL->errors()) === false) {
	print($_GENERAL->output_errors());
}

if(Session::exists('cpanel')) {
	$_GENERAL->addOutSuccess(Session::flash('cpanel'));
	print($_GENERAL->output_success());
}
?>
	<form action="p.php?p=cpanel&cp=edit&id=<?php print($edit_data->id);?>" method="POST">
		<ul align="center">
			<li>Name: <b><?php print($edit_data->name);?></b></li>
			<li><textarea name="value" cols="50" rows="5"><?php print($edit_data->value);?></textarea></li>
			<li>
				<input type="hidden" name="token" value="<?php echo Token::generate(); ?>">
				<input type="submit" value="Change setting">
			</li>
		</ul>
	</form>
</p>