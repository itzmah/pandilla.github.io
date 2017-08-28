<?php
/*
 * Name: FreeLand v2
 * Author: Marko Murumaa
 * Created: July 2015
 * Website: www.freelandplay.eu
*/
?>
<div id="page">
	<div class="page-title">Kamp</div>
	<p>
	<?php
	print($gang_menu);
	if (empty($_GENERAL->errors()) === false) {
		print($_GENERAL->output_errors());
	}

	if(Session::exists('gang')) {
		$_GENERAL->addOutSuccess(Session::flash('gang'));
		print($_GENERAL->output_success());
	}
	
	$gang_logs_per_page = 40;
	$log_time = time() - 60*60*48;
	$gang_logs_query_p = DB::getInstance()->query("SELECT * FROM `gang_logs` WHERE `gang_id` = ".$_USER->data('data')->gang." AND `date` > '".date("Y-m-d H:i:s", $log_time)."' ");
	$gang_logs_total = ceil($gang_logs_query_p->count() / $gang_logs_per_page );

	$page = (isset($_GET['lp'])) ? (int)$_GET['lp'] : 1;
	$start = ($page - 1) * $gang_logs_per_page;

	$x = ( $page - 1 ) * $gang_logs_per_page;

	if ( $x < 0 ) {
		$x = 0;
	}

	$gang_logs_query = DB::getInstance()->query("SELECT * FROM `gang_logs` WHERE `gang_id` = ".$_USER->data('data')->gang." AND `date` > '".date("Y-m-d H:i:s", $log_time)."' ORDER BY `id` DESC LIMIT $start, $gang_logs_per_page");
	foreach ($gang_logs_query->results() as $log) {
		$output_line .= '
			<tr>
				<td width="30%" align="center">'.$log->date.'</td>
				<td width="70%">'.$log->body.'</td>
			</tr>';
	}

	?>
</div>
<div id="page">
	<div class="page-title">Kamba sündmused</div>
	<p>
		<?php
		if ($gang_logs_total > 0) {
			if ($gang_logs_total >= 1 && $page <= $gang_logs_total) {
				echo '<div align="center">Lehekülg: ';
				for ($x=1; $x<=$gang_logs_total; $x++) {
					echo ($x == $page) ? '<b>'.$x.'</b> ' : '<a href="p.php?p=gang&page=logs&lp='.$x.'">'.$x.'</a> ';
				}
				echo '</div>';
			}
		} else {
			$_GENERAL->addError("Sündmuseid ei leitud.");
			if (empty($_GENERAL->errors()) === false) {
				print($_GENERAL->output_errors());
			}
		}
		?>
		<table>
			<?php print($output_line);?>
		</table>
	</p>
</div>