<?php
/*
 * Name: FreeLand v2
 * Author: Marko Murumaa
 * Created: July 2015
 * Website: www.freelandplay.eu
*/

if (!$_USER->hasPermission('cp_general')) {
	Redirect::to('p.php?p=cpanel');
	exit();
}
$output_line = null;
$general_query = DB::getInstance()->query("SELECT * FROM settings_game WHERE `group` = 'general' ");

foreach ($general_query->results() as $setting) {
	$output_line .= '
		<tr>
			<td>'.$setting->name.'</td>
			<td>'.$setting->value.'</td>
			<td align="center"><a href="p.php?p=cpanel&cp=edit&id='.$setting->id.'">Edit</a></td>
		</tr>
	';
}
?>
<div class="page-title">General settings</div>
<p>
	<table>
		<tr>
			<th width="40%">Name</th>
			<th width="50%">Value</th>
			<th width="10%">#</th>
		</tr>
		<?php print($output_line);?>
	</table>
</p>