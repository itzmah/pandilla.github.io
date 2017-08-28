<?php
/*
 * Name: FreeLand v2
 * Author: Marko Murumaa
 * Created: July 2015
 * Website: www.freelandplay.eu
*/

if (!$_USER->hasPermission('cp_rules')) {
	Redirect::to('p.php?p=cpanel');
	exit();
}

?>
<div class="page-title">Reeglite haldamine</div>
<p>
	<table>
		<tr>
			<td width="50%" valign="top">
				<ul>
					<li><b>Category settings</b></li>
					<li> >>> <a href="p.php?p=cpanel&cp=rules&new=cat#bot">Add new category</a></li>
					<li> >>> <a href="p.php?p=cpanel&cp=rules&edit=cat#bot">Edit categorys</a></li>
					<li> >>> <a href="p.php?p=cpanel&cp=rules&delete=cat#bot">Delete categorys</a></li>
				</ul>
			</td>
			<td width="50%" valign="top">
					<ul>
					<li><b>Rule settings</b></li>
					<li> >>> <a href="p.php?p=cpanel&cp=rules&new=rule#bot">Add new rule</a></li>
					<li> >>> <a href="p.php?p=cpanel&cp=rules&edit=rule#bot">Edit rules</a></li>
					<li> >>> <a href="p.php?p=cpanel&cp=rules&delete=rule#bot">Delete rules</a></li>
				</ul>
			</td>
		</tr>
	</table>
</p>
<?php
if (Input::get('new') == 'cat') {

	if(Token::check(Input::get('token')) ) {
		if (empty(Input::get('value')) === true) {
			$_GENERAL->addError("Category name is required.");
		}

		if (empty($_GENERAL->errors()) === true) {
			DB::getInstance()->insert('rules_chapters', array('name' => Input::get('value')));
			Session::flash('cpanel', 'New rule category is successfully added.');
			Redirect::to('p.php?p=cpanel&cp=rules&new=cat#bot');
			exit();
		}
	}
?>
<div id="bot" class="page-title">Lisa uus kategooria</div>
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
	<form action="p.php?p=cpanel&cp=rules&new=cat#bot" method="POST">
		<ul align="center">
			<li><b>Category name</b></li>
			<li><input type="text" name="value"></li>
			<li>
				<input type="hidden" name="token" value="<?php echo Token::generate(); ?>">
				<input type="submit" value="Create new category">
			</li>
		</ul>
	</form>
</p>
<?php

} elseif (Input::get('new') == 'rule') {

	if(Token::check(Input::get('token')) ) {
		$last_cat_id = DB::getInstance()->query("SELECT * FROM rules_chapters ORDER BY id DESC");
		$lci = $last_cat_id->first();
		if (empty(Input::get('value')) === true) {
			$_GENERAL->addError("Rule is required.");
		}

		if (Input::get('category') < 1 or Input::get('category') > $lci->id) {
			$_GENERAL->addError("Rule category is not valid.");
		}

		if (empty($_GENERAL->errors()) === true) {
			DB::getInstance()->insert('rules', array('rule' => Input::get('value'), 'chapter_id' => Input::get('category')));
			Session::flash('cpanel', 'New rule is successfully added.');
			Redirect::to('p.php?p=cpanel&cp=rules&new=rule#bot');
			exit();
		}
	}

	$cat_option = null;
	$rule_category_query = DB::getInstance()->query('SELECT * FROM rules_chapters');
	foreach ($rule_category_query->results() as $cat) {
		$cat_option .= '<option value="'.$cat->id.'">'.$cat->name.'</option>';
	}
?>
<div id="bot" class="page-title">Lisa uus reegel</div>
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
	<form action="p.php?p=cpanel&cp=rules&new=rule#bot" method="POST">
		<ul align="center">
			<li><b>Rule:</b></li>
			<li><textarea name="value" cols="50" rows="5"></textarea></li>
			<li><b>Category:</b></li>
			<li>
				<select name="category">
					<?php print($cat_option);?>
				</select>
			</li>
			<li>
				<input type="hidden" name="token" value="<?php echo Token::generate(); ?>">
				<input type="submit" value="Add new rule">
			</li>
		</ul>
	</form>
</p>
<?php
} elseif (Input::get('edit') == 'cat') {
	
?>
<div id="bot" class="page-title">Muuda kategooriat</div>
<p>
<?php
	if (empty(Input::get('id')) === false) {
		$cat_edit_query = DB::getInstance()->query("SELECT * FROM rules_chapters WHERE `id` = " . (int)Input::get('id'));
		if ($cat_edit_query->count() < 1) {
			Redirect::to('p.php?p=cpanel&cp=rules');
			exit();
		}

		$cat_data = $cat_edit_query->first();

		if(Input::exists()) {
			if(Token::check(Input::get('token')) ) {
				if (empty(Input::get('value')) === true) {
					$_GENERAL->addError("Category name is required.");
				}

				if (empty($_GENERAL->errors()) === true) {
					$cat_edit_query->update('rules_chapters', Input::get('id'), array('name' => Input::get('value')));
					Session::flash('cpanel', 'Category name is successfully changed.');
					Redirect::to('p.php?p=cpanel&cp=rules&edit=cat&id='.Input::get('id').'#bot');
					exit();
				}
			}
		}

		?>
	<?php
	if (empty($_GENERAL->errors()) === false) {
		print($_GENERAL->output_errors());
	}

	if(Session::exists('cpanel')) {
		$_GENERAL->addOutSuccess(Session::flash('cpanel'));
		print($_GENERAL->output_success());
	}
	?>
	<form action="p.php?p=cpanel&cp=rules&edit=cat&id=<?php print(Input::get('id'));?>#bot" method="POST">
		<ul align="center">
			<li>Category name: </li>
			<li><input type="text" name="value" value="<?php print($cat_data->name);?>"></li>
			<li>
				<input type="hidden" name="token" value="<?php echo Token::generate(); ?>">
				<input type="submit" value="Change category name">
			</li>
		</ul>
	</form>
		<?php
	} else {
		$cat_line = null;
		$cat_query = DB::getInstance()->query("SELECT * FROM rules_chapters");
		foreach ($cat_query->results() as $cat) {
			$cat_line .= '
				<tr>
					<td width="80%">'.$cat->name.'</td>
					<td width="20%" align="center"><a href="p.php?p=cpanel&cp=rules&edit=cat&id='.$cat->id.'#bot">Edit</a></td>
				</tr>
			';
		}
		?>
	<table>
		<tr>
			<th width="80%">Name</th>
			<th width="20%">#</th>
		</tr>
		<?php print($cat_line);?>
	</table>
		<?php
	}
?>
</p>
<?php
} elseif (Input::get('edit') == 'rule') {
?>
<div id="bot" class="page-title">Muuda reegleid</div>
<p>
<?php
	if (empty(Input::get('id')) === false) {
		$rule_edit_query = DB::getInstance()->query("SELECT * FROM rules WHERE `id` = " . (int)Input::get('id'));
		if ($rule_edit_query->count() < 0) {
			Redirect::to('p.php?p=cpanel&cp=rules');
			exit();
		}

		$rule_data = $rule_edit_query->first();

		if(Input::exists()) {
			if(Token::check(Input::get('token')) ) {
				$last_cat_id = DB::getInstance()->query("SELECT * FROM rules_chapters ORDER BY id DESC");
				$lci = $last_cat_id->first();

				if (empty(Input::get('value')) === true) {
					$_GENERAL->addError("Rule is required.");
				}

				if (Input::get('category') < 1 or Input::get('category') > $lci->id) {
					$_GENERAL->addError("Rule category is not valid.");
				}

				if (empty($_GENERAL->errors()) === true) {
					$rule_edit_query->update('rules', Input::get('id'), array('rule' => Input::get('value'), 'chapter_id' => Input::get('category')));
					Session::flash('cpanel', 'Rule is successfully changed.');
					Redirect::to('p.php?p=cpanel&cp=rules&edit=rule&id='.Input::get('id').'#bot');
					exit();
				}
			}
		}

		$cat_option = null;
		$rule_category_query = DB::getInstance()->query('SELECT * FROM rules_chapters');
		foreach ($rule_category_query->results() as $cat) {
			if ($rule_data->chapter_id == $cat->id) {
				$selected = 'selected';
			} else {
				$selected = null;
			}

			$cat_option .= '<option value="'.$cat->id.'" '.$selected.'>'.$cat->name.'</option>';
		}

		if (empty($_GENERAL->errors()) === false) {
			print($_GENERAL->output_errors());
		}

		if(Session::exists('cpanel')) {
			$_GENERAL->addOutSuccess(Session::flash('cpanel'));
			print($_GENERAL->output_success());
		}
?>
	<form action="p.php?p=cpanel&cp=rules&edit=rule&id=<?php print(Input::get('id'));?>#bot" method="POST">
		<ul align="center">
			<li>Rule: </li>
			<li><textarea name="value" cols="50" rows="5"><?php print($rule_data->rule);?></textarea></li>
			<li>Category: </li>
			<li>
				<select name="category">
					<?php print($cat_option);?>
				</select>
			</li>
			<li>
				<input type="hidden" name="token" value="<?php echo Token::generate(); ?>">
				<input type="submit" value="Change rule">
			</li>
		</ul>
	</form>
		<?php
	} else {
		$rule_line = null;
		$rule_query = DB::getInstance()->query("SELECT * FROM rules");
		foreach ($rule_query->results() as $rule) {

			$chapter_i_query = DB::getInstance()->query("SELECT * FROM rules_chapters WHERE `id` = " . $rule->chapter_id);
			$chapter_data = $chapter_i_query->first();
			$rule_line .= '
				<tr>
					<td width="60%">'.$rule->rule.'</td>
					<td width="30%">'.$chapter_data->name.'</td>
					<td width="10%" align="center"><a href="p.php?p=cpanel&cp=rules&edit=rule&id='.$rule->id.'#bot">Edit</a></td>
				</tr>
			';
		}
		?>
	<table>
		<tr>
			<th width="60%">Name</th>
			<th width="30%">Category</th>
			<th width="10%">#</th>
		</tr>
		<?php print($rule_line);?>
	</table>
		<?php
	}
?>
</p>
<?php

} elseif (Input::get('delete') == 'cat') {
?>
<div id="bot" class="page-title">Kustuta kategooriaid</div>
<p>
<?php
	if (empty(Input::get('id')) === false) {
		$cat_delete_query = DB::getInstance()->query("SELECT * FROM rules_chapters WHERE `id` = " . (int)Input::get('id'));
		if ($cat_delete_query->count() > 0) {
			$cat_delete_data = $cat_delete_query->first();

			 DB::getInstance()->delete('rules_chapters', array('id', '=', Input::get('id')));

			$_GENERAL->addOutSuccess('This rule category is successfully deleted.');
			print($_GENERAL->output_success());
		}
	} else {
		$cat_del_line = null;
		$cat_del_query = DB::getInstance()->query("SELECT * FROM rules_chapters");
		foreach ($cat_del_query->results() as $cat) {
			$cat_del_line .= '
				<tr>
					<td width="80%">'.$cat->name.'</td>
					<td width="20%" align="center"><a href="p.php?p=cpanel&cp=rules&delete=cat&id='.$cat->id.'#bot">Delete</a></td>
				</tr>
			';
		}
		?>
	<table>
		<tr>
			<th width="80%">Category name</th>
			<th width="20%">#</th>
		</tr>
		<?php print($cat_del_line);?>
	</table>
		<?php
	}
?>
</p>
<?php
} elseif (Input::get('delete') == 'rule') {
?>
<div id="bot" class="page-title">Kustuta reegleid</div>
<p>
<?php
	if (empty(Input::get('id')) === false) {
		$rule_delete_query = DB::getInstance()->query("SELECT * FROM rules WHERE `id` = " . (int)Input::get('id'));
		if ($rule_delete_query->count() > 0) {

			DB::getInstance()->delete('rules', array('id', '=', Input::get('id')));

			$_GENERAL->addOutSuccess('This rule is successfully deleted.');
			print($_GENERAL->output_success());
		}
	} else {
		$rule_del_line = null;
		$rule_del_query = DB::getInstance()->query("SELECT * FROM rules");
		foreach ($rule_del_query->results() as $rule) {
			$chapter_i_query = DB::getInstance()->query("SELECT * FROM rules_chapters WHERE `id` = " . $rule->chapter_id);
			$chapter_data = $chapter_i_query->first();
			$rule_del_line .= '
				<tr>
					<td width="60%">'.$rule->rule.'</td>
					<td width="30%">'.$chapter_data->name.'</td>
					<td width="10%" align="center"><a href="p.php?p=cpanel&cp=rules&delete=rule&id='.$rule->id.'#bot">Delete</a></td>
				</tr>
			';
		}
		?>
	<table>
		<tr>
			<th width="60%">Name</th>
			<th width="30%">Category</th>
			<th width="10%">#</th>
		</tr>
		<?php print($rule_del_line);?>
	</table>
		<?php
	}
?>
</p>
<?php
}
