<?php
/*
 * Name: FreeLand v2
 * Author: Marko Murumaa
 * Created: July 2015
 * Website: www.freelandplay.eu
*/
?>
		<div id="left">
			<?php
			if ($_USER->isLoggedIn() === true) {
				include("includes/widgets/gamedata_in.php");
				include("includes/widgets/userdata_in.php");
				include("includes/widgets/menu_in.php");
			} else {
				include("includes/widgets/login_out.php");
				include("includes/widgets/menu_out.php");
			}
			?>
		</div>