<?php
/*
 * Name: FreeLand v2
 * Author: Marko Murumaa
 * Created: July 2015
 * Website: www.freelandplay.eu
*/

	$nav_group_query = DB::getInstance(1)->get('groups', array('id','=',$_USER->data()->groups));
	$nav_status = '<font color="'.$nav_group_query->first()->color.'">'.$nav_group_query->first()->name.'</font>';

	$system_cron_query = DB::getInstance(1)->get('system_time', array('id', '=', 1));
	$system_cron = $system_cron_query->first();

	if ($_USER->data('data')->toetaja == 1) {
		$nav_sponsor = '<font color="green">Jah</font>';
	} else {
		$nav_sponsor = '<font color="red">Ei</font>';
	}

	$nav_world = (Session::exists("world") === true) ? Session::get('world') : 1;

?>
		<script>
			jQuery(function ($) {
				var minutes = <?php print($_GENERAL->system_time_counter() - 1);?>,
					display = $('#countertimer');
				startTimer(minutes, display);
			});
		</script>
			<div id="menu">
				<ul>
					<li><img src="css/default/images/icons/world.png" width="16" height="16">Mängu maailm: <?php print($nav_world);?></li>
					<li><img src="css/default/images/icons/user.png" width="16" height="16">Kasutajanimi: <a href="p.php?p=profile&user=<?php print(escape($_USER->data()->username));?>"><?php print(escape($_USER->data()->username));?></a></li>
					<li><img src="css/default/images/icons/status.png" width="16" height="16">Staatus: <?php print($nav_status);?></li>
					<?php
					if ($_WORLD == 1) {
						?>
					<li><img src="css/default/images/icons/sponsor.png" width="16" height="16">Toetaja: <?php print($nav_sponsor);?></li>
						<?php
					}
					?>
					<li><img src="css/default/images/icons/clock.png" width="16" height="16">Uuenduseni aega: <span id="countertimer"></span></li>
				</ul>
			</div>