<?php
/*
 * Name: FreeLand v2
 * Author: Marko Murumaa
 * Created: July 2015
 * Website: www.freelandplay.eu
*/

if (!$_USER->hasPermission('cp_news')) {
	Redirect::to('p.php?p=cpanel');
	exit();
}

$news_list = null;
$news_query = DB::getInstance()->query("SELECT * FROM news");
foreach ($news_query->results() as $news) {
	$news_list .= '
		<tr>
			<td width="80%">'.$news->subject.'</td>
			<td width="20%" align="center"><a href="p.php?p=cpanel&cp=news&edit='.$news->id.'#bot">Edit</a> | <a href="p.php?p=cpanel&cp=news&delete='.$news->id.'">Delete</a></td>
		</tr>
	';
}
?>
<div class="page-title">Uudiste haldamine</div>
<p>
<?php
if(Session::exists('cpanelhd')) {
	$_GENERAL->addOutSuccess(Session::flash('cpanelhd'));
	print($_GENERAL->output_success());
}
?>
	<div align="right" style="padding:10px;"><a href="p.php?p=cpanel&cp=news&create=1#bot">Add news</a></div>
	<table>
		<tr>
			<th width="80%">Subject</th>
			<th width="20%">#</th>
		</tr>
		<?php print($news_list);?>
	</table>
</p>
<?php
if (Input::get('create') == 1) {
	if(Input::exists()) {
		if(Token::check(Input::get('token')) ) {
			if (empty(Input::get('name')) === true) {
				$_GENERAL->addError('Subject is required.');
			}

			if (empty(Input::get('body')) === true) {
				$_GENERAL->addError('Body is required.');
			}

			if (empty($_GENERAL->errors()) === true) {
				$news_fields = array(
					'subject' => Input::get('name'), 
					'body' => Input::get('body'),
					'user_id' => $_USER->data()->id);

				DB::getInstance()->insert('news', $news_fields);
				Session::flash('cpanel', 'News added successfully.');
				Redirect::to('p.php?p=cpanel&cp=news&create=1#bot');
				exit();
			}
		}
	}
	?>
<div id="bot" class="page-title">Lisa uudiseid</div>
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
	<form action="p.php?p=cpanel&cp=news&create=1#bot" method="POST">
		<ul align="center">
			<li>Subject:</li>
			<li><input type="text" name="name"></li>
			<li>Body:</li>
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
		$news_edit_query = DB::getInstance()->query("SELECT * FROM news WHERE `id` = " . (int)Input::get('edit'));
	if ($news_edit_query->count() < 1) {
		Redirect::to('p.php?p=cpanel&cp=news');
		exit();
	}

	$news_edit_data = $news_edit_query->first();

	if(Input::exists()) {
		if(Token::check(Input::get('token')) ) {
			if (empty(Input::get('name')) === true) {
				$_GENERAL->addError('Name is required.');
			}

			if (empty(Input::get('body')) === true) {
				$_GENERAL->addError('Body is required.');
			}

			if (empty($_GENERAL->errors()) === true) {
				DB::getInstance()->update('news', Input::get('edit'), array('subject' => Input::get('name'),'body' => Input::get('body')));
				Session::flash('cpanel', 'News is successfully edited.');
				Redirect::to('p.php?p=cpanel&cp=news&edit='.Input::get('edit').'#bot');
				exit();
			}
		}
	}		
	?>
<div id="bot" class="page-title">Muuda uudiseid</div>
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
	<form action="p.php?p=cpanel&cp=news&edit=<?php print($news_edit_data->id);?>#bot" method="POST">
		<ul align="center">
			<li>Subject:</li>
			<li><input type="text" name="name" value="<?php print($news_edit_data->subject);?>"></li>
			<li>Body:</li>
			<li><textarea name="body" cols="100" rows="20"><?php print($news_edit_data->body);?></textarea></li>
			<li>
				<input type="hidden" name="token" value="<?php echo Token::generate(); ?>">
				<input type="submit" value="Edit">
			</li>
		</ul>
	</form>
</p>	
	<?php
} elseif (empty(Input::get('delete')) === false) {
	$news_del_query = DB::getInstance()->query("SELECT * FROM news WHERE `id` = " . (int)Input::get('delete'));
	if ($news_del_query->count() < 1) {
		Redirect::to('p.php?p=cpanel&cp=news');
		exit();
	}

	DB::getInstance()->delete('news', array('id', '=', Input::get('delete')));
	Session::flash('cpanelhd', 'This news topic is successfully deleted.');
	Redirect::to('p.php?p=cpanel&cp=news');
	exit();
}
