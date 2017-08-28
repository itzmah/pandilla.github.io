<?php
/*
 * Name: FreeLand v2
 * Author: Marko Murumaa
 * Created: July 2015
 * Website: www.freelandplay.eu
*/

if (!$_USER->hasPermission('cp_group')) {
	Redirect::to('p.php?p=cpanel');
	exit();
}

$groups_list = null;
$groups_query = DB::getInstance()->query("SELECT * FROM groups");
foreach ($groups_query->results() as $group) {
	$groups_list .= '
		<tr>
			<td width="60%">'.$group->name.'</td>
			<td width="20%" align="center"><font color="'.$group->color.'">'.$group->color.'</font></td>
			<td width="20%" align="center"><a href="p.php?p=cpanel&cp=group&edit='.$group->id.'#bot">Edit</a> | <a href="p.php?p=cpanel&cp=group&delete='.$group->id.'">delete</a></td>
		</tr>
	';
}

?>
<div class="page-title">Group management</div>
<p>
<?php
if(Session::exists('cpanelgd')) {
	$_GENERAL->addOutSuccess(Session::flash('cpanelgd'));
	print($_GENERAL->output_success());
}
if(Session::exists('cpanelge')) {
	$_GENERAL->addError(Session::flash('cpanelge'));
	print($_GENERAL->output_errors());
}
?>
	<div align="right" style="padding:10px;"><a href="p.php?p=cpanel&cp=group&create=1#bot">Add new group</a></div>
	<table>
		<tr>
			<th width="60%">Group name</th>
			<th width="20%">Color code</th>
			<th width="20%">#</th>
		</tr>
		<?php print($groups_list);?>
	</table>
</p>
<?php

if (Input::get('create') == 1) {

	$group_access_list = null;
	$group_access_query = DB::getInstance()->query("SELECT * FROM settings_group");
	foreach ($group_access_query->results() as $access) {
		$group_access_list .= '
			<tr>
				<td width="5%"><input type="checkbox" name="access'.$access->id.'" value="1"></td>
				<td width="95%">'.$access->desc.'</td>
			</tr>
		';
	}

	if(Input::exists()) {
		if(Token::check(Input::get('token')) ) {
			if (empty(Input::get('name')) === true) {
				$_GENERAL->addError('Group name is required.');
			} 

			if (empty(Input::get('color')) === true) {
				$_GENERAL->addError('Group color is required.');
			}

			if (empty($_GENERAL->errors()) === true) {
				$access_count = $group_access_query->count();
				$group_permissions .= '{';
				foreach ($group_access_query->results() as $acc) {
					$i++;
					$comma = ($i == $access_count) ? '' : ', ';
					$access_value = (empty(Input::get('access'.$acc->id)) === true) ? 0 : 1;
					$group_permissions .= '"'.$acc->name.'":'.$access_value.$comma;
				}
				$group_permissions .= '}';
				$group_fields = array(
					'name' => Input::get('name'),
					'permissions' => $group_permissions,
					'color' => Input::get('color'));
				DB::getInstance()->insert('groups', $group_fields);
				Session::flash('cpanel', 'New group is successfully added.');
				Redirect::to('p.php?p=cpanel&cp=group&create=1#bot');
				exit();
			}
		}
	}

	?>
<div id="bot" class="page-title">Loo uus group</div>
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
	<form action="p.php?p=cpanel&cp=group&create=1#bot" method="POST">
		<ul>
			<li>Group name:</li>
			<li><input type="text" name="name"></li>
			<li>Group color:</li>
			<li><input type="text" name="color" value="#"></li>
			<li>Group access:</li>
			<li>
				<table>
					<?php print($group_access_list);?>
				</table>
			</li>
			<li>
				<input type="hidden" name="token" value="<?php echo Token::generate(); ?>">
				<input type="submit" value="Create new group">
			</li>
		</ul>
	</form>
</p>
	<?php

} elseif (empty(Input::get('edit')) === false) {
	$group_edit_query = DB::getInstance()->query("SELECT * FROM groups WHERE id = " . (int)Input::get('edit'));
	if ($group_edit_query->count() < 1) {
		Redirect::to('p.php?p=cpanel&cp=group');
		exit();
	}
	$group_edit_data = $group_edit_query->first();
	$group_edit_permissions = json_decode($group_edit_data->permissions, true);
	
	$group_access_list = null;
	$group_access_query = DB::getInstance()->query("SELECT * FROM settings_group");
	foreach ($group_access_query->results() as $access) {
		$checked = ($group_edit_permissions[$access->name] == 1) ? 'checked' : '';

		$group_access_list .= '
			<tr>
				<td width="5%"><input type="checkbox" name="access'.$access->id.'" value="1" '.$checked.'></td>
				<td width="95%">'.$access->desc.'</td>
			</tr>
		';
	}

	if(Input::exists()) {
		if(Token::check(Input::get('token')) ) {
			if (empty(Input::get('name')) === true) {
				$_GENERAL->addError('Group name is required.');
			} 

			if (empty(Input::get('color')) === true) {
				$_GENERAL->addError('Group color is required.');
			}

			if (empty($_GENERAL->errors()) === true) {
				$access_count = $group_access_query->count();
				$group_permissions .= '{';
				foreach ($group_access_query->results() as $acc) {
					$i++;
					$comma = ($i == $access_count) ? '' : ', ';
					$access_value = (empty(Input::get('access'.$acc->id)) === true) ? 0 : 1;
					$group_permissions .= '"'.$acc->name.'":'.$access_value.$comma;
				}
				$group_permissions .= '}';

				$group_fields = array(
					'name' => Input::get('name'),
					'permissions' => $group_permissions,
					'color' => Input::get('color'));
				DB::getInstance()->update('groups', Input::get('edit'), $group_fields);
				Session::flash('cpanel', 'Group is successfully edited.');
				Redirect::to('p.php?p=cpanel&cp=group&edit='.Input::get('edit').'#bot');
				exit();
			}
		}
	}

	?>
<div id="bot" class="page-title">Muuda groupi</div>
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
	<form action="p.php?p=cpanel&cp=group&edit=<?php print($group_edit_data->id);?>#bot" method="POST">
		<ul>
			<li>Group name:</li>
			<li><input type="text" name="name" value="<?php print($group_edit_data->name);?>"></li>
			<li>Group color:</li>
			<li><input type="text" name="color" value="<?php print($group_edit_data->color);?>"></li>
			<li>Group access:</li>
			<li>
				<table>
					<?php print($group_access_list);?>
				</table>
			</li>
			<li>
				<input type="hidden" name="token" value="<?php echo Token::generate(); ?>">
				<input type="submit" value="Edit">
			</li>
		</ul>
	</form>
</p>
	<?php
} elseif (empty(Input::get('delete')) === false) {
	$group_del_query = DB::getInstance()->query("SELECT * FROM groups WHERE `id` = " . (int)Input::get('delete'));
	if ($group_del_query->count() < 1) {
		Redirect::to('p.php?p=cpanel&cp=group');
		exit();
	}

	if (Input::get('delete') == 1) {
		Session::flash('cpanelge', 'This is main group and that can\'t delete.');
		Redirect::to('p.php?p=cpanel&cp=group');
		exit();
	}
	DB::getInstance()->query("UPDATE users SET `groups`= 1 WHERE `groups` = " . (int)Input::get('delete'));

	DB::getInstance()->delete('groups', array('id', '=', Input::get('delete')));
	Session::flash('cpanelgd', 'This group is successfully deleted. - Users may affected.');
	Redirect::to('p.php?p=cpanel&cp=group');
	exit();	
}
