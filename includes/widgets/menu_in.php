<?php
/*
 * Name: FreeLand v2
 * Author: Marko Murumaa
 * Created: July 2015
 * Website: www.freelandplay.eu
*/
?>
			<div id="menu">
				<ul>
					<li><a href="p.php?p=home">Avaleht</a></li>
					<li><a href="p.php?p=data">Andmed</a></li>
					<br>
					<li><a href="p.php?p=bank">Pank</a></li>
					<li><a href="p.php?p=stocks">Aktsiad (<?php print($_GENERAL->format_number($_GENERAL->settings('settings_game','STOCK_PRICE')));?>)</a></li>
					<li><a href="p.php?p=house">Elamu</a></li>
					<li><a href="p.php?p=market">Turg</a></li>
					<br>
					<li><a href="p.php?p=job">Töökoht</a></li>
					<li><a href="p.php?p=school">Kool</a></li>
					<li><a href="p.php?p=gym">Jõusaal</a></li>
					<li><a href="p.php?p=casino">Kasiino</a></li>
					<li><a href="p.php?p=restaurant">Restoran</a></li>
					<br>
					<li><a href="p.php?p=defence">Kaitsesüsteem</a></li>
					<li><a href="p.php?p=offence">Ründesüsteem</a></li>
					<li><a href="p.php?p=streetfight">Tänava kaklus</a></li>
					<li><a href="p.php?p=crime">Kuritegevus</a></li>
					<li><a href="p.php?p=gang">Kamp</a></li>
					<li><a href="p.php?p=gangs">Kampade edetabel</a></li>
					<br>
					<li><a href="p.php?p=help">KKK/Abi</a></li>
					<!--<li><a href="p.php?p=credit">Tasulised teenused</a></li>-->
					<li><a href="p.php?p=flcshop">FLC pood</a></li>
					<li><a href="p.php?p=rules">Reeglid</a></li>
					<li><a href="p.php?p=contact">Kontakt</a></li>
					<?php
					if($_USER->hasPermission('cpanel')) {
						print('<li>- - - - - -</li>');
						print('<li><a href="p.php?p=cpanel">Control panel</a></li>');
					}
					if($_USER->hasPermission('contact_inbox')) {
						$contact_query_count = DB::getInstance(1)->query("SELECT * FROM contact_inbox WHERE status = 1");
						$contact_count_num = $contact_query_count->count();
						print('<li><a href="p.php?p=contact_inbox">Contact inbox ['.$contact_count_num.']</a></li>');
					}
					?>
				</ul>
			</div>