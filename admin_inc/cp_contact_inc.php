<?php
/*
 * Name: FreeLand v2
 * Author: Marko Murumaa
 * Created: July 2015
 * Website: www.freelandplay.eu
*/

if (!$_USER->hasPermission('cp_contact_settings')) {
	Redirect::to('p.php?p=cpanel');
	exit();
}

$contact_query =  DB::getInstance()->query("SELECT * FROM contact_types");
foreach ($contact_query->results() as $type) {

	$priority = ($type->priority_level == 1) ? "High" : "Low";

	$output_line .= '
		<tr>
			<td>'.$type->name.'</td>
			<td align="center">'.$priority.'</td>
			<td align="center"><a href="p.php?p=cpanel&cp=contact&edit='.$type->id.'#bot">Edit</a> | <a  href="p.php?p=cpanel&cp=contact&del='.$type->id.'#bot">Delete</a></td>
		</tr>
	';
}

?>
<div class="page-title">Kontakti seaded</div>
<p>
<?php
if(Session::exists('cpanel')) {
	$_GENERAL->addOutSuccess(Session::flash('cpanel'));
	print($_GENERAL->output_success());
}
?>
	<div align="right" style="padding:10px;"><a href="p.php?p=cpanel&cp=contact&t=new#bot">Add new message type</a></div>
	<table>
		<tr>
			<th width="50%">Type name</th>
			<th width="30%">Priority</th>
			<th wisth="20%">#</th>
		</tr>
		<?php print($output_line);?>
	</table>
</p>
<?php
if (empty(Input::get('del')) === false) {
	if (empty(Input::get('del')) === true) {
		Redirect::to('p.php?p=cpanel');
		exit();
	}

	$del_query =  DB::getInstance()->query("SELECT * FROM contact_types WHERE `id` = " . (int)Input::get('del'));
	if ($del_query->count() <= 0) {
		Redirect::to('p.php?p=cpanel&cp=contact');
		exit();
	}

	$del_data = $del_query->first();
	$delete_msg = "Youe have successfully deleted contact type: <b>".$del_data->name."</b>";
	DB::getInstance()->delete('contact_types', array('id', '=', Input::get('del')));

	Session::flash('cpanel', $delete_msg);
	Redirect::to('p.php?p=cpanel&cp=contact');

} else if (Input::get('t') == "new") {

	if(Input::exists()) {
		if(Token::check(Input::get('token'), 'NEW_CONTACT') ) {
			if (empty(Input::get('type')) === true) {
				$_GENERAL->addError("Type name is required.");
			}

			if (Input::get('priority') < 0 or Input::get('priority') > 1) {
				$_GENERAL->addError("Priority is not valid.");
			}

			if (empty($_GENERAL->errors()) === true) {
				DB::getInstance()->insert('contact_types', array('name' => Input::get('type'), 'priority_level' => Input::get('priority')));
				Session::flash('cpanel', 'New contact type is successfully added.');
				Redirect::to('p.php?p=cpanel&cp=contact');
				exit();
			}
		}
	}

?>
<div id="bot" class="page-title">Lisa uus kontakti tüüp</div>
	<p>
<?php
if (empty($_GENERAL->errors()) === false) {
		print($_GENERAL->output_errors());
	}
?>
		<form action="p.php?p=cpanel&cp=contact&t=new#bot" method="POST">
			<ul>
				<li>Priority level:<br>
					<select name="priority">
						<option value="0">Low</option>
						<option value="1">High</option>
					</select></li>
				<li>Type name: <br><input type="text" name="type" </li>
				<li>
					<input type="hidden" name="token" value="<?php echo Token::generate('NEW_CONTACT'); ?>">
					<input type="submit" value="Add new contact type">
				</li>
			</ul>
		</form>
	</p>
<?php

} else if (empty(Input::get('edit')) === false) {
	if (empty(Input::get('edit')) === true) {
		Redirect::to('p.php?p=cpanel');
		exit();
	}

	if(Input::exists()) {
		if(Token::check(Input::get('token'), 'EDIT_CONTACT') ) {

			DB::getInstance()->query("UPDATE contact_types SET name=? WHERE id = " . (int)Input::get('edit'), [Input::get('type')]);
			DB::getInstance()->query("UPDATE contact_types SET priority_level=? WHERE id = " . (int)Input::get('edit'), [Input::get('priority')]);

			Session::flash('cpanel', 'Contact type is  successfully edited.');
			Redirect::to('p.php?p=cpanel&cp=contact');
		}
	}


	$edit_query =  DB::getInstance()->query("SELECT * FROM contact_types WHERE `id` = " . (int)Input::get('edit'));
	if ($edit_query->count() <= 0) {
		Redirect::to('p.php?p=cpanel&cp=contact');
		exit();
	}

	$edit_data = $edit_query->first();
	$priority_high = ($edit_data->priority_level == 1) ? ' selected' : '';
	?>
	<div id="bot" class="page-title">Muuda</div>
	<p>
		<form action="p.php?p=cpanel&cp=contact&edit=<?php print(Input::get('edit'));?>#bot" method="POST">
			<ul>
				<li>Priority level:<br>
					<select name="priority">
						<option value="0">Low</option>
						<option value="1"<?php print($priority_high);?>>High</option>
					</select></li>
				<li>Type name: <br><input type="text" name="type" value="<?php print($edit_data->name);?>"></li>
				<li>
					<input type="hidden" name="token" value="<?php echo Token::generate('EDIT_CONTACT'); ?>">
					<input type="submit" value="Edit">
				</li>
			</ul>
		</form>
	</p>
	<?php
}
