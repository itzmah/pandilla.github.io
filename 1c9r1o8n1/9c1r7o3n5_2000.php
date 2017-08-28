<?php
/*
 * Name: FreeLand v2
 * Author: Marko Murumaa
 * Created: July 2015
 * Website: www.freelandplay.eu
*/

if (php_sapi_name() !== 'cli') {
	die();
}

$cron_start_time = microtime(true);

require (__DIR__ . '/../core/init.php');
/*Iga paev kell 20:00.*/
	
for ($world_c=1; $world_c < 3; $world_c++) { 

	$lotid = date("dmY", time());
	$lottery_jackpot_query = DB::getInstance($world_c)->query("SELECT * FROM `lottery_bets` WHERE `lottery_number` = ".$lotid." ");
	$x = 0;
	$users = null;
	$lottery_jackpot_raw = 0;
	foreach ($lottery_jackpot_query->results() as $lottery) {
		$x++;
		$lottery_jackpot_raw += $lottery->bet;
		$users[$x] = $lottery->user_id;
	}

	$lottery_jackpot = 10 * $lottery_jackpot_raw;

	$winners_count = count($users);
	$winner = mt_rand(1, $winners_count);

	if (empty($users) === true) {
		$user = 0;
	} else {
		$user = $users[$winner];
	}

	$lottery_fields = array(
		'user_id' => $user,
		'money' => $lottery_jackpot,
		'time' => time(),
		'active' => 1
		);
	DB::getInstance($world_c)->insert('lottery_winners', $lottery_fields);

	DB::getInstance($world_c)->query("UPDATE `users_data` set `lottery_last` = 0");

}
	echo "Time Elapsed: ".(microtime(true) - $cron_start_time)."s";
