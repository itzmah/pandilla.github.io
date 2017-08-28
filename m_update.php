<?php
/*
 * Name: FreeLand v2
 * Author: Marko Murumaa
 * Created: July 2015
 * Website: www.freelandplay.eu
*/

$_linr = true;
require_once("core/init.php");

include("includes/overall/header.php");
?>

<div id="page">
	<div class="page-title">Mäng</div>
	<p>
	<?php
	$_GENERAL->addOutInfo('Mäng on hetkel uuenduste tõttu suletud. Proovige mõne aja pärast uuesti.');
	print($_GENERAL->output_info());
	?>
	</p>
</div>

<?php
include("includes/overall/footer.php");
