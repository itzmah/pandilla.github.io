<?php
/*
 * Name: FreeLand v2
 * Author: Marko Murumaa
 * Created: July 2015
 * Website: www.freelandplay.eu
*/
?>
<!DOCTYPE html>
<html>
<?php include("includes/head.php");?>
<body>
	<div id="wrapper" class="container">
		<div id="header"></div>
		<?php
		if ($_USER->isLoggedIn() === true) {
			include("includes/widgets/nav_in.php");
		} else {
			include("includes/widgets/nav_out.php");
		}
		
		include("includes/menu.php");
