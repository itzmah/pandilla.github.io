<?php
/*
 * Name: FreeLand v2
 * Author: Marko Murumaa
 * Created: July 2015
 * Website: www.freelandplay.eu
*/

	$logscount = DB::getInstance()->query("SELECT * FROM user_logs WHERE user_id = '".$_USER->data()->id."' AND active = 1 AND `date` > '".date("Y-m-d H:i:s ", time()-172800)."' ");
	$logscountnum = $logscount->count();
	if ($logscountnum > 0) {
		$logsfullnav = '(<font color="red">'.$_GENERAL->format_number($logscountnum).'</font>)';
	} else {
		$logsfullnav = '('.$_GENERAL->format_number($logscountnum).')';
	}

	$mailcount = DB::getInstance()->query("SELECT * FROM mail WHERE `to` = '".$_USER->data()->id."' AND new = 1 AND `deleted` = 0");
	$mailcountnum = $mailcount->count();
	$mailnew_word = ($mailcountnum == 1) ? 'uus kiri': 'uut kirja';
	if ($mailcountnum > 0) {
		$mailfullnav = '(<font color="red">'.$_GENERAL->format_number($mailcountnum).' '.$mailnew_word.'</font>)';
	} else {
		$mailfullnav = '('.$_GENERAL->format_number($mailcountnum).' '.$mailnew_word.')';
	}

	$nav_members_query = DB::getInstance()->query("SELECT * FROM users INNER JOIN users_data ON users.id = users_data.id WHERE users.groups != 2 AND users.groups != 3 ORDER BY users_data.score DESC");
	$x = 0;
	foreach ($nav_members_query->results() as $nav_m) {
		if ($_USER->data()->groups == 2) {
			$nav_members_page = 1;
		} else {
			$x++;
			if ($_USER->data()->id == $nav_m->id) {
				$nav_members_page = ceil($x / 20);
			}
		}
	}

?>
		<div id="nav">
			<ul>
				<li><img src="css/default/images/icons/home.png" width="16" height="16"><a href="p.php?p=home">Avaleht</a></li>
				<li><img src="css/default/images/icons/members.png" width="16" height="16"><a href="p.php?p=members&page=<?php print($nav_members_page);?>">Mängijate edetabel</a></li>
				<li><img src="css/default/images/icons/logs.png" width="16" height="16"><a href="p.php?p=logs">Sündmused <?php print($logsfullnav);?></a></li>
				<li><img src="css/default/images/icons/mailbox.png" width="16" height="16"><a href="p.php?p=mail&inbox=1">Postkast <?php print($mailfullnav);?></a></li>
				<li><img src="css/default/images/icons/settings.png" width="16" height="16"><a href="p.php?p=settings">Konto seaded</a></li>
				<li><img src="css/default/images/icons/forum.png" width="16" height="16"><a href="p.php?p=forum">Foorum</a></li>
				<li><img src="css/default/images/icons/logout.png" width="16" height="16"><a href="p.php?p=logout">Logi välja</a></li>
			</ul>
		</div>