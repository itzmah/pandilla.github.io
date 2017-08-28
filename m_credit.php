<?php
/*
 * Name: FreeLand v2
 * Author: Marko Murumaa
 * Created: July 2015
 * Website: www.freelandplay.eu
*/

$_lir = true;
require_once("core/init.php");

$cr = false;

if (false) {

	if (Input::exists()) {
		if (Token::check(Input::get('token'), 'CREDIT_FRIEND')) {
			$check_username = DB::getInstance()->get('users', array('username', '=', Input::get('username')));
			if (!$check_username->count()) {
				$_GENERAL->addError("Teie valitud kasutajat ei leitud.");
			} else {
				$credit_user = $check_username->first();
				$cr = true;
			}
		}
	}

	if ($_WORLD == 1) {
		$fortumo_service = 'bfa2ea845f66a63a7db6bf6ae172ccc6/' . $_USER->data()->id;
		if ($cr === true) {
			$fortumo_service_other = 'bfa2ea845f66a63a7db6bf6ae172ccc6/' . $credit_user->id;
		}
		$bank_desc = 'freeland1,' . $_USER->data()->id;
	} else if ($_WORLD == 2) {
		$fortumo_service = '4586ae7b3f9a1b70abe413f23173b325/' . $_USER->data()->id;
		if ($cr === true) {
			$fortumo_service_other = '4586ae7b3f9a1b70abe413f23173b325/' . $credit_user->id;
		}

		$bank_desc = 'freeland2,' . $_USER->data()->id;
	}
}

	include("includes/overall/header.php");
if (false) {
	?>

	<div id="page">
		<div class="page-title">Tasulised teenused</div>
		<p>
			<?php
			if (empty($_GENERAL->errors()) === false) {
				print($_GENERAL->output_errors());
			}
			?>
		<table>
			<tr valign="top">
				<td width="20%"><img src="css/default/images/credit.png" width="100" height="100"></td>
				<td width="80%">
					Tasulised teenused on teenused mille eest on kasutaja nõus maksma, et saada endale mängus eelist teiste mängijate suhtes.<br>
					Tasulised teenused on täielikult vabatahtlikud ja te kasutate neid oma vastutusel.<br><br>
					<b>Tasulisi teenuseid kasutades nõustute meie <a href="p.php?p=rules">reeglitega</a>.</b>
				</td>
			</tr>
		</table>
		</p>
	</div>

	<div id="page">
		<div class="page-title">SMS teenused</div>
		<p>
		<table>
			<tr valign="top">
				<td width="20%"><img src="css/default/images/sms_credit.png" width="100" height="100"></td>
				<td width="80%">
					SMS teenuseid saab tellida telefoniga ja summa lisandub telefoni arevele juurde.
				</td>
			</tr>
		</table>
		<table>
			<tr>
				<th width="50%">Telli endale</th>
				<th width="50%">Telli sõbrale</th>
			</tr>
			<tr valign="top">
				<td align="center">
					<?php
					if ($cr === false) {
						?>
						<br>
						<a id="fmp-button" href="#" rel="<?php print($fortumo_service); ?>"><img src="http://pay.fortumo.com/images/fmp/fortumopay_150x50_red.png" width="150" height="50" alt="Mobile Payments by Fortumo" border="0"/></a>
						<?php
					}
					?>
				</td>
				<td>
					<?php
					if ($cr === true) {
						?>
						<ul>
							<li>Hetkel tellite käike kasutajale: <b><?php print($credit_user->username); ?></b></li>
							<li>
								<br><a id="fmp-button" href="#" rel="<?php print($fortumo_service_other); ?>"><img src="http://pay.fortumo.com/images/fmp/fortumopay_150x50_red.png" width="150" height="50" alt="Mobile Payments by Fortumo" border="0"/></a>
							</li>
						</ul>
						<?php
					} else {
						?>
						<form action="p.php?p=credit" method="POST"><br>
							<input type="text" name="username" placeholder="Kasutajanimi" autocomplete="off">
							<input type="hidden" name="token" value="<?php echo Token::generate('CREDIT_FRIEND'); ?>">
							<input type="submit" value="Otsi kasutaja">
						</form>
						<?php
					}
					?>
				</td>
			</tr>
		</table>
		</p>
	</div>

	<div id="page">
		<div class="page-title">Panga teenused</div>
		<p>
		<table>
			<tr valign="top">
				<td width="20%"><img src="css/default/images/bank_credit.png" width="100" height="100"></td>
				<td width="80%">
					Panga teel on võimalik tellida FLC-sid suuremates kogustes.<br>
					Panga teel tellimisel on suur soodustus, see tähendab, et <b>1 EURO</b> eest saab tervelt <b>30 FLC</b>.<br>
					Summad mis on suuremad kui <b>10 EUR-i</b> nendelt saab boonust.
				</td>
			</tr>
		</table>
		<ul>
			<li align="center"><b>Summa ei tohi olla väiksem kui 1 euro!</b></li>
		</ul>
		<table>
			<tr>
				<td width="20%">Saaja nimi:</td>
				<td width="80%">Marko Murumaa</td>
			</tr>
			<tr>
				<td>IBAN (SEB):</td>
				<td>EE941010010462316011</td>
			</tr>
			<tr>
				<td>Selgitus:</td>
				<td><?php print($bank_desc); ?></td>
			</tr>
			<tr>
				<td>SWIFT/BIC kood:</td>
				<td>EEUHEE2X</td>
			</tr>
			<tr>
				<td>&nbsp;</td>
				<td>&nbsp;</td>
			</tr>
			<tr>
				<td></td>
				<td><b>NB! Teisiti vormistatud selgituste puhul automaatne teenus ei tööta.</b></td>
			</tr>
		</table>
		<ul>
			<li>Kui FLC ei ole laekunud ühe tunni jooksul peale makse sooritamist palume teil võtta <a href="p.php?p=contact">meiega ühendust</a>.</li>
			<li>Kui saata mõnest muust pangast mis ei ole SEB laekuvad käigud üldjuhul järgmisel päeval.</li>
		</ul>
		</p>
	</div>
	<?php
	$credit_list_query = DB::getInstance()->query("SELECT * FROM `credit_history` WHERE `user_id` = '" . $_USER->data()->id . "' AND `status` = 'ok' ORDER BY `id`");
	foreach ($credit_list_query->results() as $credit) {
		$x++;
		$output_line .= '
			<tr align="center">
				<td>' . $_GENERAL->format_number($x) . '</td>
				<td>' . $credit->date . '</td>
				<td>' . $credit->type . '</td>
				<td>' . number_format($credit->price, 2, '.', ' ') . ' EUR</td>
				<td>' . $_GENERAL->format_number($credit->flc) . '</td>
			</tr>
		';
	}
	?>
	<div id="page">
		<div class="page-title">Tellimuste ajalugu</div>
		<p>
			<?php
			if (empty($output_line) === true) {
				$_GENERAL->addOutInfo('Te ei ole kasutanud tasulisi teenuseid.');
				print($_GENERAL->output_info());
			} else {
			?>
		<table>
			<tr>
				<th width="10%">Jrk.</th>
				<th width="25%">Tellimise aeg</th>
				<th width="25%">Tüüp</th>
				<th width="20%">Summa</th>
				<th width="20%">FLC</th>
			</tr>
			<?php print($output_line); ?>
		</table>
		<?php
		}
		?>
		</p>
	</div>

	<?php
}
include("includes/overall/footer.php");
