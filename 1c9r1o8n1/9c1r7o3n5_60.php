<?php
/*
 * Name: FreeLand v2
 * Author: Marko Murumaa
 * Created: August 2015
 * Website: www.freelandplay.eu
*/

if (php_sapi_name() !== 'cli') {
	die();
}

$cron_start_time = microtime(true);

require (__DIR__ . '/../core/init.php');
/*Iga 60 minuti tagant.*/

for ($world_c=1; $world_c < 3; $world_c++) { 

//Crime uuendus
	DB::getInstance($world_c)->query("DELETE FROM `crime_list`");
	$crime_name = array('Vana inimene',
						'Kooli õpilane',
						'Kohalik pood',
						'Toidupood',
						'Turist',
						'Panga automaat',
						'Elektroonika pood',
						'Auto tänaval',
						'Juveelipood',
						'Rikas inimene tänaval',
						'Kaubanduskeskus',
						'Pank',
						'Riigipank',
						'Kulla ladu',
						'Sportauto',
						'Autopood');
	$crime_money = array(
			1 => array('min' => 100, 'max' => 2000),
			2 => array('min' => 2000, 'max' => 15000),
			3 => array('min' => 15000, 'max' => 75000),
			4 => array('min' => 75000, 'max' => 200000),
			5 => array('min' => 200000, 'max' => 500000),
			6 => array('min' => 500000, 'max' => 750000),
			7 => array('min' => 750000, 'max' => 1000000),
			8 => array('min' => 1000000, 'max' => 1500000),
			9 => array('min' => 1500000, 'max' => 2000000),
			10 => array('min' => 2000000, 'max' => 2500000),
			11 => array('min' => 2500000, 'max' => 3000000),
			12 => array('min' => 3000000, 'max' => 3500000),
			13 => array('min' => 3500000, 'max' => 4000000),
			14 => array('min' => 4000000, 'max' => 4500000),
			15 => array('min' => 4500000, 'max' => 5000000),
			16 => array('min' => 5000000, 'max' => 5500000),
			17 => array('min' => 5500000, 'max' => 6000000),
			18 => array('min' => 6000000, 'max' => 6500000),
			19 => array('min' => 6500000, 'max' => 7000000),
			20 => array('min' => 7000000, 'max' => 7500000));

		$users_data_query =  DB::getInstance($world_c)->query("SELECT * FROM `users_data`");
		foreach ($users_data_query->results() as $data) {
			for ($i=0; $i < 3; $i++) { 
				$rnd_name = $crime_name[mt_rand(0,(count($crime_name)-1))];
				$rnd_money = mt_rand($crime_money[$data->crime_level]['min'], $crime_money[$data->crime_level]['max']);
				$crime_fields = array(
					'name' => $rnd_name,
					'money' => $rnd_money,
					'level' => $data->crime_level
					);
				DB::getInstance($world_c)->insert('crime_list', $crime_fields);
			}
		}

//Resto tyhjendamine
	$resto_users_query =  DB::getInstance($world_c)->query("SELECT * FROM `users_data_resto` WHERE `created` = 1");
	foreach ($resto_users_query->results() as $resto) {

		$new_chef = 0;
		$new_waiter = 0;
		$new_chef_area = 0;
		$new_waiter_area = 0;
		if ($resto->chef_morale < 40) {
			if ($resto->chef >= 1) {
				$new_chef = 1;
				$new_chef_area = 5;
			}
		}

		if ($resto->waiter_morale < 40) {
			if ($resto->waiter >= 1) {
				$new_waiter = 1;
				$new_waiter_area = 4;
			}
		}

		$new_total_area = $new_chef_area + $new_waiter_area;
		DB::getInstance($world_c)->update('users_data_resto', $resto->id, array(
			'food_make_limit' => 0,
			'chef' => $resto->chef - $new_chef,
			'waiter' => $resto->waiter - $new_waiter,
			'area' => $resto->area + $new_total_area
		));
	}

}
	echo "Time Elapsed: ".(microtime(true) - $cron_start_time)."s";
