<?php

$restos_query = DB::getInstance()->query("SELECT * FROM `users_data_resto` WHERE `created` = 1 ORDER BY `reputation` DESC");
foreach ($restos_query->results() as $resto) {
	$position++;
	$resto_username_query = DB::getInstance()->query("SELECT `username` FROM `users` WHERE `id` = " . $resto->id);
	$resto_username = $resto_username_query->first();

	if ($resto->money < 0) {
		$money = '<font color="red">'.$_GENERAL->format_number($resto->money).'</font>';
	} else {
		$money = $_GENERAL->format_number($resto->money);
	}

	if ($_USER->data()->id == $resto->id) {
		$bold_s = "<b>";
		$bold_e = "</b>";
	} else {
		$bold_s = "";
		$bold_e = "";
	}

	$output_line .= '
		<tr>
			<td align="center">'.$bold_s.$position.$bold_e.'</td>
			<td>'.$bold_s.$resto->name.$bold_e.'</td>
			<td align="center">'.$bold_s.$_GENERAL->format_number($resto->reputation).$bold_e.'</td>
			<td align="center">'.$bold_s.$money.$bold_e.'</td>
			<td align="center">'.$bold_s.'<a href="p.php?p=profile&user='.$resto_username->username.'">'.$resto_username->username.'</a>'.$bold_e.'</td>
		</tr>
	';
}
?>
	<div id="page">
		<div class="page-title">Restoranide edetabel</div>
		<p>
		<?php 
		print($resto_menu);
		
		if (empty($_GENERAL->errors()) === false) {
			print("<br>");
			print($_GENERAL->output_errors());
		}

		if(Session::exists('restaurant')) {
			$_GENERAL->addOutSuccess(Session::flash('restaurant'));
			print("<br>");
			print($_GENERAL->output_success());
		}
		?>
			<table>
				<tr valign="top">
					<td width="20%"><img src="css/default/images/restaurant_ranking.png" width="100" height="100"></td>
					<td width="80%">
						Siin lehel on välja toodud kõik restoranid reputatsiooni järjekorras.<br>
						Mida kõrgem on teie restorani reputasioon seda kõrgemal kohal te siin edetabelis olete.
					</td>
				</tr>
			</table>

			<table>
				<tr>
					<th width="5%">Koht</th>
					<th width="30%">Restorani nimi</th>
					<th width="20%">Reputatsioon</th>
					<th width="25%">Raha</th>
					<th width="20%">Omanik</th>
				</tr>
				<?php print($output_line);?>
			</table>
		</p>
	</div>

