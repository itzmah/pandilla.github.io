<?php
/*
 * Name: FreeLand v2
 * Author: Marko Murumaa
 * Created: July 2015
 * Website: www.freelandplay.eu
*/
if (!$_USER->hasPermission('cp_help')) {
	Redirect::to('p.php?p=cpanel');
	exit();
}

$help_list = null;
$help_query = DB::getInstance()->query("SELECT * FROM help");
foreach ($help_query->results() as $help) {
	$help_list .= '
		<tr>
			<td width="80%">'.$help->name.'</td>
			<td width="20%" align="center"><a href="p.php?p=cpanel&cp=help&edit='.$help->id.'#bot">Edit</a> | <a href="p.php?p=cpanel&cp=help&delete='.$help->id.'#bot">Delete</a></td>
		</tr>
	';
}
?>
<div class="page-title">Abi haldamine</div>
<p>
<?php
if(Session::exists('cpanelhd')) {
	$_GENERAL->addOutSuccess(Session::flash('cpanelhd'));
	print($_GENERAL->output_success());
}
?>
	<div align="right" style="padding:10px;"><a href="p.php?p=cpanel&cp=help&create=1#bot">Create a new one</a></div>
	<table>
		<tr>
			<th width="80%">Subject</th>
			<th width="20%">#</th>
		</tr>
		<?php print($help_list);?>
	</table>
</p>
<?php
if (Input::get('create') == 1) {
	if(Input::exists()) {
		if(Token::check(Input::get('token')) ) {
			if (empty(Input::get('name')) === true) {
				$_GENERAL->addError('Name is required.');
			}

			if (empty(Input::get('body')) === true) {
				$_GENERAL->addError('Body is required.');
			}

			if (empty($_GENERAL->errors()) === true) {
				DB::getInstance()->insert('help', array('name' => Input::get('name'),'body' => Input::get('body')));
				Session::flash('cpanel', 'New help topic is created.');
				Redirect::to('p.php?p=cpanel&cp=help&create=1#bot');
				exit();
			}
		}
	}
	?>
<div id="bot" class="page-title">Lisa uus abi teema</div>
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
	<form action="p.php?p=cpanel&cp=help&create=1#bot" method="POST">
		<ul align="center">
			<li>Help subject:</li>
			<li><input type="text" name="name"></li>
			<li>Help body:</li>
			<li><textarea name="body" cols="100" rows="20"></textarea></li>
			<li>
				<input type="hidden" name="token" value="<?php echo Token::generate(); ?>">
				<input type="submit" value="Create">
			</li>
		</ul>
	</form>
</p>
	<?php
} elseif (empty(Input::get('edit')) === false) {
	$help_edit_query = DB::getInstance()->query("SELECT * FROM help WHERE `id` = " . (int)Input::get('edit'));
	if ($help_edit_query->count() < 1) {
		Redirect::to('p.php?p=cpanel&cp=help');
		exit();
	}

	$help_edit_data = $help_edit_query->first();

	if(Input::exists()) {
		if(Token::check(Input::get('token')) ) {
			if (empty(Input::get('name')) === true) {
				$_GENERAL->addError('Name is required.');
			}

			if (empty(Input::get('body')) === true) {
				$_GENERAL->addError('Body is required.');
			}

			if (empty($_GENERAL->errors()) === true) {
				DB::getInstance()->update('help', Input::get('edit'), array('name' => Input::get('name'),'body' => Input::get('body')));
				Session::flash('cpanel', 'Help topic is successfully edited.');
				Redirect::to('p.php?p=cpanel&cp=help&edit='.Input::get('edit').'#bot');
				exit();
			}
		}
	}
	?>
<div id="bot" class="page-title">Muuda abi teemasid</div>
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
	<form action="p.php?p=cpanel&cp=help&edit=<?php print($help_edit_data->id);?>#bot" method="POST">
		<ul align="center">
			<li>Help subject:</li>
			<li><input type="text" name="name" value="<?php print($help_edit_data->name);?>"></li>
			<li>Help body:</li>
			<li><textarea name="body" cols="100" rows="20"><?php print($help_edit_data->body);?></textarea></li>
			<li>
				<input type="hidden" name="token" value="<?php echo Token::generate(); ?>">
				<input type="submit" value="Edit">
			</li>
		</ul>
	</form>
</p>
	<?php
} elseif (empty(Input::get('delete')) === false) {
	$help_del_query = DB::getInstance()->query("SELECT * FROM help WHERE `id` = " . (int)Input::get('delete'));
	if ($help_del_query->count() < 1) {
		Redirect::to('p.php?p=cpanel&cp=help');
		exit();
	}

	DB::getInstance()->delete('help', array('id', '=', Input::get('delete')));
	Session::flash('cpanelhd', 'This help topic is successfully deleted.');
	Redirect::to('p.php?p=cpanel&cp=help');
	exit();
}
