<?php
/*
 * Name: FreeLand v2
 * Author: Marko Murumaa
 * Created: July 2015
 * Website: www.freelandplay.eu
*/

$_lir = true;
require_once("core/init.php");

	$game1_min = 1000;
	$game1_max = 5000000;
	$game1_turns = 10;
	$game1_win = 3;

	$game2_min = 100;
	$game2_max = 1000000;
	$game2_turns = 10;
	$game2_win = 2;

	$game3_min = 10000;
	$game3_max = 100000;

if(Input::exists()) {
	if(Token::check(Input::get('token'), 'GAME1') ) {
		$bet = round(Input::get('bet'));

		if ($bet < $game1_min or $bet > $game1_max) {
			$_GENERAL->addError("Minimaalne panus on ".$_GENERAL->format_number($game1_min)." ja maksimaalne panus on ".$_GENERAL->format_number($game1_max).".");
		}

		if ($game1_turns > $_USER->data('data')->turns) {
			$_GENERAL->addError("Teil ei ole piisavalt käike.");
		}

		if ($bet > $_USER->data('data')->money) {
			$_GENERAL->addError("Teil ei ole piisavalt raha.");
		}

		if (empty($_GENERAL->errors()) === true) {
			$rand = mt_rand(-25,10);

			if ($rand > 0) {
				$win = ($game1_win * $bet);
				$_USER->update(array(
					'money' => $_USER->data('data')->money + $win,
					'turns' => $_USER->data('data')->turns - $game1_turns
				),$_USER->data()->id, 'users_data');

				Session::flash('casino', 'Te võitsite '.$_GENERAL->format_number($win).'.');
				Redirect::to('p.php?p=casino&game=1');
			} else {
				$_USER->update(array(
					'money' => $_USER->data('data')->money - $bet,
					'turns' => $_USER->data('data')->turns - $game1_turns
				),$_USER->data()->id, 'users_data');

				$_GENERAL->addError("Te kaotasite ".$_GENERAL->format_number($bet).".");
			}
		}

	} else if(Token::check(Input::get('token'), 'GAME2') ) {
		$bet = round(Input::get('bet'));

		if ($bet < $game2_min or $bet > $game2_max) {
			$_GENERAL->addError("Minimaalne panus on ".$_GENERAL->format_number($game2_min)." ja maksimaalne panus on ".$_GENERAL->format_number($game2_max).".");
		}

		if ($game2_turns > $_USER->data('data')->turns) {
			$_GENERAL->addError("Teil ei ole piisavalt käike.");
		}

		if ($bet > $_USER->data('data')->money) {
			$_GENERAL->addError("Teil ei ole piisavalt raha.");
		}

		if (empty($_GENERAL->errors()) === true) {
			$pc_dice = mt_rand(1,6);
			$usr_dice = mt_rand(1,6);

			if ($usr_dice > $pc_dice) {
				$win = ($game2_win * $bet);
				$_USER->update(array(
					'money' => $_USER->data('data')->money + $win,
					'turns' => $_USER->data('data')->turns - $game2_turns
				),$_USER->data()->id, 'users_data');

				Session::flash('casino_info', 'Sina veeretasid: '.$usr_dice.'<br>Vastane veeretas: '.$pc_dice.'.');
				Session::flash('casino', 'Sa võitsid '.$_GENERAL->format_number($win).'.');
				Redirect::to('p.php?p=casino&game=2');
			} else if ($usr_dice == $pc_dice) {
				$_USER->update(array(
					'turns' => $_USER->data('data')->turns - $game2_turns
				),$_USER->data()->id, 'users_data');

				Session::flash('casino_info', 'Te jäite viiki!<br><br>Sina veeretasid: '.$usr_dice.'<br>Vastane veeretas: '.$pc_dice.'.');
				Redirect::to('p.php?p=casino&game=2');
			} else {
				$_USER->update(array(
					'money' => $_USER->data('data')->money - $bet,
					'turns' => $_USER->data('data')->turns - $game2_turns
				),$_USER->data()->id, 'users_data');

				Session::flash('casino_info', 'Sina veeretasid: '.$usr_dice.'<br>Vastane veeretas: '.$pc_dice.'.');
				$_GENERAL->addError("Sa kaotasid ".$_GENERAL->format_number($bet).".");
			}
		}
	} else if(Token::check(Input::get('token'), 'GAME3') ) {
		$bet = round(Input::get('bet'));

		$lottery_last_query = DB::getInstance()->query("SELECT * FROM `lottery_winners` ORDER BY `id` DESC LIMIT 1");
		$lottery_last = $lottery_last_query->first();

		if ($bet < $game3_min or $bet > $game3_max) {
			$_GENERAL->addError("Minimaalne panus on ".$_GENERAL->format_number($game3_min)." ja maksimaalne panus on ".$_GENERAL->format_number($game3_max).".");
		}

		if ($bet > $_USER->data('data')->money) {
			$_GENERAL->addError("Teil ei ole piisavalt raha.");
		}

		if ($_USER->data('data')->lottery_last == 1) {
			$_GENERAL->addError("Te juba osalete loteriis.");
		}

		if ($_USER->data('data')->id == $lottery_last->user_id) {
			$_GENERAL->addError("Te võitsite eelmise loterii ja te ei saa osaleda selles loteriis.");
		}

		if (empty($_GENERAL->errors()) === true) {
			$_USER->update(array(
					'money' => $_USER->data('data')->money - $bet,
					'lottery_last' => 1
				),$_USER->data()->id, 'users_data');

			$currnet_day = date("H ", time());
			if ($currnet_day >= 20) {
				$lotid = date("dmY", time() + 14400);
			} else {
				$lotid = date("dmY", time());
			}

			$fields_bets = array(
				'user_id' => $_USER->data()->id,
				'lottery_number' => $lotid,
				'bet' => $bet);

			DB::getInstance()->insert('lottery_bets', $fields_bets);

			Session::flash('casino', 'Te tegite oma panuse loteriis.');
			Redirect::to('p.php?p=casino&game=3');
		}
	}
}

if (empty(Input::get('take')) === false) {
	$lottery_take_query = DB::getInstance()->query("SELECT * FROM `lottery_winners` WHERE `id` = " . (int)Input::get('take'));
	if (!$lottery_take_query->count()) {
		$_GENERAL->addError("Seda loterii võitu ei leitud.");
	} else {
		$lottery_take = $lottery_take_query->first();
		if ($lottery_take->user_id != $_USER->data()->id) {
			$_GENERAL->addError("See loterii võit ei kuulu teile.");
		}

		if ($lottery_take->active == 0) {
			$_GENERAL->addError("Te olete juba selle loterii võidu võtnud.");
		}

		if (empty($_GENERAL->errors()) === true) {
			$_USER->update(array(
					'money' => $_USER->data('data')->money + $lottery_take->money
				),$_USER->data()->id, 'users_data');

			DB::getInstance()->update('lottery_winners', $lottery_take->id, array('active' => 0));

			Session::flash('casino', 'Te võtsite edukalt oma loterii võidu välja.');
			Redirect::to('p.php?p=casino&game=3');
		}
	}
}

include("includes/overall/header.php");
?>

<div id="page">
	<div class="page-title">Kasiino</div>
	<p>
	<?php
	if(Session::exists('casino_info')) {
		$_GENERAL->addOutInfo(Session::flash('casino_info'));
		print($_GENERAL->output_info());
	}

	if (empty($_GENERAL->errors()) === false) {
		print($_GENERAL->output_errors());
	}

	if(Session::exists('casino')) {
		$_GENERAL->addOutSuccess(Session::flash('casino'));
		print($_GENERAL->output_success());
	}
	?>
		<table>
			<tr valign="top">
				<td width="20%"><img src="css/default/images/casino.png" width="100" height="100"></td>
				<td width="80%">
					Kasiinos saate mängida erinevaid mänge. 
					Aga tuleb teada, et siin võib ka kõik oma raha maha mängida.<br>
					Erinevates mängudes on erinevad piirangud.
				</td>
			</tr>
		</table>
		<ul>
			<li><b>Valige mäng mida soovite mängida:</b></li>
			<li><a href="p.php?p=casino&game=1">Õnnemäng</a></li>
			<li><a href="p.php?p=casino&game=2">Veereta täringuid</a></li>
			<li><a href="p.php?p=casino&game=3">Loterii</a></li>
		</ul>
	</p>
</div>
<?php
if (Input::get('game') == 1) {
?>
<div id="page">
	<div class="page-title">Õnnemäng</div>
	<p>
		<ul>
			<li>Ühe korra mängimine vajab <?php print($_GENERAL->format_number($game1_turns));?> käiku.</li>
			<li>Minimaalne panus on <?php print($_GENERAL->format_number($game1_min));?> ja maksimaalne panus on <?php print($_GENERAL->format_number($game1_max));?></li>
			<li>Võidu korral saate <?php print($_GENERAL->format_number($game1_win));?> kordse summa, kaotuse korral ei saa kahjuks midagi.</li>
		</ul>
		<form action="p.php?p=casino&game=1" method="POST">
			<table>
				<tr>
					<td width="20%">Palju panustad</td>
					<td width="80%">
						<input type="text" name="bet" autocomplete="off">
						<input type="hidden" name="token" value="<?php echo Token::generate('GAME1'); ?>">
						<input type="submit" value="Mängi">
					</td>
				</tr>
			</table>
		</form>
	</p>
</div>
<?php
} else if (Input::get('game') == 2) {
?>
<div id="page">
	<div class="page-title">Veereta täringuid</div>
	<p>
		<ul>
			<li>Ühe korra mängimine vajab <?php print($_GENERAL->format_number($game2_turns));?> käiku.</li>
			<li>Minimaalne panus on <?php print($_GENERAL->format_number($game2_min));?> ja maksimaalne panus on <?php print($_GENERAL->format_number($game2_max));?></li>
			<li>Võidu korral saate <?php print($_GENERAL->format_number($game2_win));?> kordse summa, kaotuse korral ei saa kahjuks midagi.</li>
		</ul>
		<form action="p.php?p=casino&game=2" method="POST">
			<table>
				<tr>
					<td width="20%">Palju panustad</td>
					<td width="80%">
						<input type="text" name="bet" autocomplete="off">
						<input type="hidden" name="token" value="<?php echo Token::generate('GAME2'); ?>">
						<input type="submit" value="Veereta taringuid">
					</td>
				</tr>
			</table>
		</form>
	</p>
</div>
<?php
} else if (Input::get('game') == 3) {

	$lottery_time = date("H ", time());
	if ($lottery_time >= 20) {
		$lotid = date("dmY", time() + 14400);
	} else {
		$lotid = date("dmY", time());
	}

	$lottery_jackpot_query = DB::getInstance()->query("SELECT * FROM `lottery_bets` WHERE `lottery_number` = " . $lotid);
	foreach ($lottery_jackpot_query->results() as $lottery) {
		$lottery_jackpot_raw += $lottery->bet;
	}

	$lottery_jackpot = 10 * $lottery_jackpot_raw;

	$lottery_query = DB::getInstance()->query("SELECT * FROM `lottery_winners` ORDER BY `id` DESC LIMIT 10");
	foreach ($lottery_query->results() as $lottery) {
		if ($lottery->user_id != 0) {
			$lottery_user_query = DB::getInstance()->query("SELECT `username` FROM `users` WHERE `id` = "  .$lottery->user_id);
			$lottery_user = $lottery_user_query->first();

			$winner = '<a href="p.php?p=profile&user='.$lottery_user->username.'">'.$lottery_user->username.'</a>';
		} else {
			$winner = '<i>Mitte keegi</i>';
		}


		if ($lottery->user_id == $_USER->data()->id) {
			if ($lottery->active == 1) {
				$take_link = '<a href="p.php?p=casino&game=3&take='.$lottery->id.'">Võta raha</a>';
			} else {
				$take_link = '<i>Võetud</i>';
			}
		} else {
			$take_link = '-';
		}

		$output_line .= '
			<tr>
				<td>'.$winner.'</td>
				<td align="center">'.date("d.m.Y", $lottery->time).'</td>
				<td align="center">'.$_GENERAL->format_number($lottery->money).'</td>
				<td align="center">'.$take_link .'</td>
			</tr>
		';
	}

?>
<div id="page">
	<div class="page-title">Loterii</div>
	<p>
		<ul>
			<li>Loteriiga on võimalik võita suuri summasid siin mängus.</li>
			<li>Loterii loostiakse välja igapäev kell <b>20:00</b>.</li>
			<li>Praegu on loterii jackpot: <b><?php print($_GENERAL->format_number($lottery_jackpot));?></b>.</li>
		</ul>
		<form action="p.php?p=casino&game=3" method="POST">
			<table>
				<tr>
					<td width="20%">Minimaalne panus: </td>
					<td width="80%"><?php print($_GENERAL->format_number($game3_min));?></td>
				</tr>
				<tr>
					<td width="20%">Maksimaalne panus: </td>
					<td width="80%"><?php print($_GENERAL->format_number($game3_max));?></td>
				</tr>
				<tr>
					<td>Palju panustad</td>
					<td>
						<input type="text" name="bet" autocomplete="off">
						<input type="hidden" name="token" value="<?php echo Token::generate('GAME3'); ?>">
						<input type="submit" value="Osale loteriis">
					</td>
				</tr>
			</table>
		</form>
	</p>
	<table>
		<tr>
			<th width="30%">Võitja nimi</th>
			<th width="15%">Kuupäev</th>
			<th width="40%">Summa</th>
			<th width="15%">#</th>
		</tr>
		<?php print($output_line);?>
	</table>
</div>
<?php
}
include("includes/overall/footer.php");
