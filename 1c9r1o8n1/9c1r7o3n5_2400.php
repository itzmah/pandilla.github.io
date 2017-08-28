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
/*Iga 24 tunni tagant.*/

for ($world_c=1; $world_c < 3; $world_c++) { 

	//Kamba liikmete nullimine
		DB::getInstance($world_c)->query("UPDATE `gang_members` SET `points` = 0");

	//Restorani nullimine
		$resto_users_query =  DB::getInstance($world_c)->query("SELECT * FROM `users_data_resto` WHERE `created` = 1");
		foreach ($resto_users_query->results() as $resto) {

			DB::getInstance($world_c)->update('users_data_resto', $resto->id, array(
				'food_make_today' => 0,
				'food_sell_today' => 0,
				'income_today' => 0,
				'outcome_today' => 0,
				'food_sell_limit' => 0
			));

		}
}
	echo "Time Elapsed: ".(microtime(true) - $cron_start_time)."s";
