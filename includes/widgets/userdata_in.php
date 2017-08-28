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
					<li><img src="css/default/images/icons/moves.png" width="16" height="16">Käike: <?php print($_GENERAL->format_number($_USER->data('data')->turns));?></li>
					<li><img src="css/default/images/icons/credit.png" width="16" height="16">FLC: <?php print($_GENERAL->format_number($_USER->data('data')->flc));?></li>
					<li><img src="css/default/images/icons/cash.png" width="16" height="16">Sularaha: <?php print($_GENERAL->format_number($_USER->data('data')->money));?></li>
					<li><img src="css/default/images/icons/education.png" width="16" height="16">Haridust: <?php print($_GENERAL->format_number($_USER->data('data')->education));?></li>
					<li><img src="css/default/images/icons/food.png" width="16" height="16">Toitu: <?php print($_GENERAL->format_number($_USER->data('data')->food));?></li>
					<li><img src="css/default/images/icons/defence.png" width="16" height="16">Kaitse: <?php print($_GENERAL->format_number($_USER->user_defence_i()));?></li>
					<li><img src="css/default/images/icons/offence.png" width="16" height="16">Rünne: <?php print($_GENERAL->format_number($_USER->user_offence_i()));?></li>
				</ul>
			</div>