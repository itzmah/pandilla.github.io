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
/*Iga 60 minuti tagant.*/

for ($world_c=1; $world_c < 3; $world_c++) { 

	$resto_users_query =  DB::getInstance($world_c)->query("SELECT * FROM `users_data_resto` WHERE `created` = 1");
	foreach ($resto_users_query->results() as $resto) {

		$pay_chef = $resto->chef * $resto->chef_salary;
		$pay_waiter = $resto->waiter * $resto->waiter_salary;
		$pay_total = ($pay_chef + $pay_waiter) * $resto->work_hours;

		DB::getInstance($world_c)->update('users_data_resto', $resto->id, array(
			'money' => $resto->money - $pay_total,
			'outcome_today' => $resto->outcome_today + $pay_total
			));

	}
}
	echo "Time Elapsed: ".(microtime(true) - $cron_start_time)."s";
