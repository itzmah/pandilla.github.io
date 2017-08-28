<?php
/*
 * Name: FreeLand v2
 * Author: Marko Murumaa
 * Created: July 2015
 * Website: www.freelandplay.eu
*/

$_lir = true;
require_once("core/init.php");

if (!$_USER->hasPermission('cpanel')) {
	Redirect::to('p.php?p=home');
	exit();
}

include("includes/overall/header.php");
?>
<div id="page">
	<div class="page-title">Control panel</div>
	<p>
		<table>
			<tr>
				<td width="33%" valign="top">
					<ul>
						<li><b>Website settings</b></li>
						<?php
						if ($_USER->hasPermission('cp_general')) {
							print('<li> >>> <a href="p.php?p=cpanel&cp=general">General settings</a></li>');
						}
						if ($_USER->hasPermission('cp_group')) {
							print('<li> >>> <a href="p.php?p=cpanel&cp=group">Group management</a></li>');
						}
						if ($_USER->hasPermission('cp_rules')) {
							print('<li> >>> <a href="p.php?p=cpanel&cp=rules">Rules management</a></li>');
						}
						if ($_USER->hasPermission('cp_help')) {
							print('<li> >>> <a href="p.php?p=cpanel&cp=help">Help management</a></li>');
						}
						if ($_USER->hasPermission('cp_news')) {
							print('<li> >>> <a href="p.php?p=cpanel&cp=news">News management</a></li>');
						}
						if ($_USER->hasPermission('cp_fortumo_services')) {
							print('<li> >>> <a href="p.php?p=cpanel&cp=fortumo">Fortumo sms services</a></li>');
						}
						if ($_USER->hasPermission('cp_contact_settings')) {
							print('<li> >>> <a href="p.php?p=cpanel&cp=contact">Contact form settings</a></li>');
						}
						?>
					</ul>
				</td>
				<td width="33%" valign="top">
					<ul>
						<li><b>Game settings</b></li>
						<?php
						if ($_USER->hasPermission('cp_user_management')) {
							print('<li> >>> <a href="p.php?p=cpanel&cp=user">User management</a></li>');
						}
						if ($_USER->hasPermission('cp_deletechat')) {
							print('<li> >>> <a href="p.php?p=cpanel&cp=delchat">Delete chat rows</a></li>');
						}
						if ($_USER->hasPermission('cp_gameplay')) {
							print('<li> >>> <a href="p.php?p=cpanel&cp=settings&setting=chat">Chat settings</a></li>');
						}
						?>
					</ul>
				</td>
				<td width="33%" valign="top">
					<ul>
						<?php if ($_USER->hasPermission('cp_gameplay')) {?>
						<li><b>Gameplay</b></li>
						<li> >>> <a href="p.php?p=cpanel&cp=settings&setting=house_offence">Offence</a></li>
						<li> >>> <a href="p.php?p=cpanel&cp=settings&setting=house_defence">Defence</a></li>
						<li> >>> <a href="p.php?p=cpanel&cp=settings&setting=stocks">Stocks</a></li>
						<li> >>> <a href="p.php?p=cpanel&cp=settings&setting=school">School</a></li>
						<li> >>> <a href="p.php?p=cpanel&cp=settings&setting=gym">Gym</a></li>
						<li> >>> <a href="p.php?p=cpanel&cp=settings&setting=casino">Casino</a></li>
						<li> >>> <a href="p.php?p=cpanel&cp=settings&setting=house">House</a></li>
						<li> >>> <a href="p.php?p=cpanel&cp=settings&setting=bank">Bank</a></li>
						<li> >>> <a href="p.php?p=cpanel&cp=settings&setting=market">Market</a></li>
						<li> >>> <a href="p.php?p=cpanel&cp=settings&setting=weapons">Weapons</a></li>
						<li> >>> <a href="p.php?p=cpanel&cp=settings&setting=gang">Gang</a></li>
						<?php }?>
					</ul>
				</td>
			</tr>
		</table>
	</p>
<?php
if (Input::get('cp') == 'general') {

	include('admin_inc/cp_general_inc.php');

} elseif (Input::get('cp') == 'group') {

	include('admin_inc/cp_group_inc.php');

} elseif (Input::get('cp') == 'rules') {

	include('admin_inc/cp_rules_inc.php');

} elseif (Input::get('cp') == 'help') {

	include('admin_inc/cp_help_inc.php');

} elseif (Input::get('cp') == 'news') {

	include('admin_inc/cp_news_inc.php');

} elseif (Input::get('cp') == 'settings') {

	include('admin_inc/cp_settings_inc.php');

} elseif (Input::get('cp') == 'delchat') {

	include('admin_inc/cp_delchat_inc.php');

} elseif (Input::get('cp') == 'user') {

	include('admin_inc/cp_user_inc.php');

} elseif (Input::get('cp') == 'fortumo') {

	include('admin_inc/cp_fortumo_inc.php');

} elseif (Input::get('cp') == 'contact') {

	include('admin_inc/cp_contact_inc.php');

} elseif (Input::get('cp') == 'edit') {

	include('admin_inc/cp_edit_inc.php');

}
?>
</div>

<?php
include("includes/overall/footer.php");
