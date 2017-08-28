<?php
/*
 * Name: FreeLand v2
 * Author: Marko Murumaa
 * Created: July 2015
 * Website: www.freelandplay.eu
*/

$_lir = true;
require_once("core/init.php");

include("includes/overall/header.php");
?>

<div id="page">
	<div class="page-title">Sündmused</div>
	<p>
		<table>
			<tr valign="top">
				<td width="20%"><img src="css/default/images/logs.png" width="100" height="100"></td>
				<td width="80%">
					Sündmuste alt näete te oma kasutaja viimase 48 tunni tegevusi.
				</td>
			</tr>
		</table>
	</p>
<?php
	$checktime = date("Y-m-d H:i:s ", time()-172800);
	$logs = DB::getInstance()->query("SELECT * FROM `user_logs` WHERE `user_id` = '".$_USER->data()->id."' AND `date` > '".$checktime."' ORDER BY `id` DESC");
	$log_line = null;

	foreach ($logs->results() as $log) {
		if ($log->active == 1) {
			$log_line .= '
				<tr>
					<td style="border-bottom: 1px solid #B1B292;" align="center" width="30%"><b>'.$log->date.'</b></td>
					<td style="border-bottom: 1px solid #B1B292;" width="70%"><b>'.$log->body.'</b></td>
				</tr>';
		} else {
			$log_line .= '
				<tr>
					<td style="border-bottom: 1px solid #B1B292;" align="center" width="30%">'.$log->date.'</td>
					<td style="border-bottom: 1px solid #B1B292;" width="70%">'.$log->body.'</td>
				</tr>';
		}
	}
?>
		<table>
			<?php print($log_line);?>
		</table>
</div>

<?php

$logquery = DB::getInstance()->get('user_logs', array('user_id','=',$_USER->data()->id));
$logquery->query("UPDATE `user_logs` SET `active` = ? WHERE `user_id` = '".$_USER->data()->id."' ", array('active' => 0));

include("includes/overall/footer.php");
