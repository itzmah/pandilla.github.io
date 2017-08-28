<?php
/*
 * Name: FreeLand v2
 * Author: Marko Murumaa
 * Created: July 2015
 * Website: www.freelandplay.eu
*/

$_lir = true;
require_once("core/init.php");

if(Input::exists()) {
	if(Token::check(Input::get('token'), 'NEW_JOB') ) {
		$job_info_query = DB::getInstance(1)->query("SELECT * FROM `job_list` WHERE `id` = ".Input::get('job')." ");
		if (!$job_info_query->count()) {
			Redirect::to('p.php?p=job');
		} else {
			$job_i = $job_info_query->first();
			if ($_USER->data('data')->education < $job_i->education) {
				$_GENERAL->addError("Teil ei ole piisavalt kõrge haridus.");
			}

			if ($_USER->data('data')->job == $job_i->id) {
				$_GENERAL->addError("Teil on juba see töö.");
			}

			if (empty($_GENERAL->errors()) === true) {
				$_USER->update(array(
						'job' => Input::get('job')
					),$_USER->data()->id, 'users_data');

				Session::flash('job', 'Teil on nüüd uus töökoht nimega '.$job_i->name.'.');
				Redirect::to('p.php?p=job');
			}
		}
	}
}


$job_query = DB::getInstance(1)->query("SELECT * FROM `job_list` ORDER BY `education` ASC");
foreach ($job_query->results() as $job) {
	$selected = '';
	if(Input::exists()) {
		if (Input::get('job') == $job->id) {
			$selected = ' selected';
		}
	} else {
		if ($job->id == $_USER->data('data')->job) {
			$selected = ' selected';
		}
	}

	$output_line .= '<option'.$selected.' value="'.$job->id.'">'.$job->name.'</option>';
}


if(Input::exists()) {
	$job_i_query = DB::getInstance(1)->query("SELECT * FROM `job_list` WHERE `id` = ".Input::get('job')." ");
	if (!$job_i_query->count()) {
		Redirect::to('p.php?p=job');
	}
} else {
	$job_i_query = DB::getInstance(1)->query("SELECT * FROM `job_list` WHERE `id` = ".$_USER->data('data')->job." ");
}

	$job_data = $job_i_query->first();

include("includes/overall/header.php");
?>

<div id="page">
	<div class="page-title">Töökoht</div>
	<p>
	<?php
	if (empty($_GENERAL->errors()) === false) {
		print($_GENERAL->output_errors());
	}

	if(Session::exists('job')) {
		$_GENERAL->addOutSuccess(Session::flash('job'));
		print($_GENERAL->output_success());
	}
	?>
		<table>
			<tr valign="top">
				<td width="20%"><img src="css/default/images/job.png" width="100" height="100"></td>
				<td width="80%">
					Töökohas teenite iga 15 minuti tagant palka. 
					Mida kõrgem on teie haridus seda kõrgemat tööd te saate omale lubada.
					Mida kõrgem on töö seda kõrgemat palka te saate.
				</td>
			</tr>
		</table>
	</p>
	<table>
		<tr>
			<td width="20%">Töökoha nimetus:</td>
			<td width="80%">
				<form action="p.php?p=job" method="POST">
					<select name="job" onchange="this.form.submit();">
						<?php print($output_line);?>
					</select>
				</form>
			</td>
		</tr>
		<tr>
			<td>Töö palk:</td>
			<td><?php print($_GENERAL->format_number($job_data->salary));?></td>
		</tr>
		<tr>
			<td>Panga limiit:</td>
			<td><?php print($_GENERAL->format_number($job_data->bank_limit));?></td>
		</tr>
		<tr>
			<td>Laenu protsent:</td>
			<td><?php print($_GENERAL->format_number($job_data->loan_percent));?></td>
		</tr>
		<tr>
			<td>Ühe aktsia saab</td>
			<td><?php print($_GENERAL->format_number($job_data->stock_num));?> skoori eest</td>
		</tr>
		<tr>
			<td>Nõtav haridus:</td>
			<td><?php print($_GENERAL->format_number($job_data->education));?></td>
		</tr>
		<tr>
			<td></td>
			<td>
				<form action="p.php?p=job" method="POST">
					<input type="hidden" name="job" value="<?php print($_GENERAL->format_number($job_data->id));?>">
					<input type="hidden" name="token" value="<?php echo Token::generate('NEW_JOB'); ?>">
					<input type="submit" value="Võta uus töö">
				</form>
			</td>
		</tr>
	</table>
</div>

<?php
include("includes/overall/footer.php");
