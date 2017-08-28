<?php
/*
 * Name: FreeLand v2
 * Author: Marko Murumaa
 * Created: July 2015
 * Website: www.freelandplay.eu
*/

$_lir = true;
require_once("core/init.php");

	$school_education = $_GENERAL->settings('settings_game','SCHOOL_GET');
	$school_money = $_GENERAL->settings('settings_game','SCHOOL_MONEY');
	$school_food = $_GENERAL->settings('settings_game','SCHOOL_FOOD');

	if ($_USER->data('data')->toetaja == 1) {
		$school_education = 3;
	}
	
if(Input::exists()) {
	if(Token::check(Input::get('token'), 'LEARN') ) {
		$value = round(Input::get('value'));
		$price = $value * $school_money;
		$food = $value * $school_food;
		$education = $value * $school_education;

		if ($value <= 0) {
			$_GENERAL->addError("Palun kirjutage mitu käiku te soovite kasutada koolis.");
		}

		if ($price > $_USER->data('data')->money) {
			$_GENERAL->addError("Teil ei ole piisavalt sularaha.");
		}

		if ($food > $_USER->data('data')->food) {
			$_GENERAL->addError("Teil ei ole piisavalt toitu.");
		}

		if ($value > $_USER->data('data')->turns) {
			$_GENERAL->addError("Teil ei ole piisavalt käike.");
		}


		if (empty($_GENERAL->errors()) === true) {
			$_USER->update(array(
					'money' => $_USER->data('data')->money - $price,
					'food' => $_USER->data('data')->food - $food,
					'turns' => $_USER->data('data')->turns - $value,
					'education' => $_USER->data('data')->education + $education
				),$_USER->data()->id, 'users_data');

			Session::flash('school', 'Te käisite koolis ja saite haridust juurde.');
			Redirect::to('p.php?p=school');
		}
	}
}

include("includes/overall/header.php");
?>

<div id="page">
	<div class="page-title">Kool</div>
	<p>
	<?php
	if (empty($_GENERAL->errors()) === false) {
		print($_GENERAL->output_errors());
	}

	if(Session::exists('school')) {
		$_GENERAL->addOutSuccess(Session::flash('school'));
		print($_GENERAL->output_success());
	}
	?>
		<table>
			<tr valign="top">
				<td width="20%"><img src="css/default/images/school.png" width="100" height="100"></td>
				<td width="80%">
					Koolis on kasulik käia, et saada juurde haridust.<br>
					Mida suurem on teie haridus seda suurem on teie töökoht ja panga tase.<br>
					Ühe käigu eest saad sa koolis <?php print($_GENERAL->format_number($school_education));?> 
					haridust ja see vajab <?php print($_GENERAL->format_number($school_money));?> raha ja <?php print($_GENERAL->format_number($school_food));?> toitu.
				</td>
			</tr>
		</table>
	</p>
	<table>
		<tr>
			<td width="20%">Mitu käiku kulutad?</td>
			<td width="80%">
				<form action="p.php?p=school" method="POST">
					<input type="text" name="value" autocomplete="off">
					<input type="hidden" name="token" value="<?php echo Token::generate('LEARN'); ?>">
					<input type="submit" value="Mine kooli">
				</form>
			</td>
		</tr>
	</table>
</div>

<?php
include("includes/overall/footer.php");
