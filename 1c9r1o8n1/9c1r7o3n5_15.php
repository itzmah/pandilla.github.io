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
/*Iga 15 minuti tagant.*/

for ($world_c=1; $world_c < 3; $world_c++) { 

	//Aeg
		$cron_query = DB::getInstance(1)->get('system_time', array('id', '=', 1));
		$cron = $cron_query->first();

		$old_time = strtotime($cron->time);
		$time_remain = $old_time - time();

		if ($time_remain <= 0) {
			$new_time = $old_time + 900;
			$new_date = date("Y-m-d H:i:s", $new_time);
			DB::getInstance(1)->update('system_time', $cron->id, array('time' => $new_date));
		}

	//Kamba skoor
		$g_query = DB::getInstance($world_c)->query("SELECT * FROM `gang`");
		foreach ($g_query->results() as $g) {
			
			$wep_1 = $g->wep_1 * $g->wep_1 * 1;
			$wep_2 = $g->wep_2 * $g->wep_2 * 1.5;
			$wep_3 = $g->wep_3 * $g->wep_3 * 2;
			$wep_4 = $g->wep_4 * $g->wep_4 * 2.5;
			$wep_5 = $g->wep_5 * $g->wep_5 * 3;
			$wep_6 = $g->wep_6 * $g->wep_6 * 3.5;

			$money = $g->money / 10000;

			$total = round( ($wep_1 + $wep_2 + $wep_3 + $wep_4 + $wep_5 + $wep_6 + $money) * $g->building_level);
			DB::getInstance($world_c)->update('gang', $g->id, array('score' => $total));
		}	

	//Tookoht
		$u_query = DB::getInstance($world_c)->query("SELECT * FROM `users_data`");
		foreach ($u_query->results() as $u) {
			$job_query = DB::getInstance(1)->query("SELECT * FROM `job_list` WHERE `id` = ".$u->job." ");
			$job = $job_query->first();

			if ($u->loan > 0) {
				if ($job->salary > $u->loan) {
					$loan = $u->loan;
					$money = $job->salary - $u->loan;
				} else {
					$loan = $job->salary;
					$money = 0;
				}

				DB::getInstance($world_c)->query("UPDATE `users_data` SET `loan`=`loan` - ".$loan." WHERE `id` = ".$u->id." ");
				DB::getInstance($world_c)->query("UPDATE `users_data` SET `money`=`money` + ".$money." WHERE `id` = ".$u->id." ");
			} else {
				DB::getInstance($world_c)->query("UPDATE `users_data` SET `money`=`money` + ".$job->salary." WHERE `id` = ".$u->id." ");
			}
		}	

	//Kaigud
		$u_query = DB::getInstance($world_c)->query("SELECT * FROM `users_data`");
		foreach ($u_query->results() as $u) {
			if ($u->toetaja == 1) {
				$turns_max = 1500;
				$turns_add = 20;
			} else {
				$turns_max = 700;
				$turns_add = 15;
			}

			if ($u->turns >= $turns_max) {
				$turns = 0;
			} else {
				if (($u->turns+$turns_add) > $turns_max) {
					$turns = $turns_max - $u->turns;
				} else {
					$turns = $turns_add;
				}
			}

			DB::getInstance($world_c)->query("UPDATE `users_data` SET `turns`=`turns` + ".$turns." WHERE `id` = ".$u->id." ");
		}	

	//Kasutajate skoor
			
		$u_query = DB::getInstance($world_c)->query("SELECT * FROM `users_data`");
		foreach ($u_query->results() as $u) {
			$u_house_query = DB::getInstance($world_c)->query("SELECT * FROM `users_data_house` WHERE `id` = ".$u->id." ");
			$u_house = $u_house_query->first();

			$wep_1 = $u_house->wep_1 * ($u_house->wep_1 / 2) * 0.05;
			$wep_2 = $u_house->wep_2 * ($u_house->wep_2 / 2) * 0.1;
			$wep_3 = $u_house->wep_3 * ($u_house->wep_3 / 2) * 0.15;
			$wep_4 = $u_house->wep_4 * ($u_house->wep_4 / 2) * 0.20;
			$wep_5 = $u_house->wep_5 * ($u_house->wep_5 / 2) * 0.25;
			$wep_6 = $u_house->wep_6 * ($u_house->wep_6 / 2) * 0.30;

			$weapons = ($wep_1 + $wep_2 + $wep_3 + $wep_4 + $wep_5 + $wep_6) * $u_house->defence_level * $u_house->offence_level;

			$money = ($u->money * 0.00002) + ($u->money_bank * 0.00001);

			$defence = ($u_house->defence_level * 150) + ($u_house->defence_man * 65 * $u_house->defence_level);
			$offence = ($u_house->offence_level * 120) + ($u_house->offence_man * 35 * $u_house->offence_level);

			$house_items = json_decode($u_house->items, true);
			$house_items_query = DB::getInstance($world_c)->query("SELECT * FROM `house_interior`");
			$items_score = 0;
			foreach ($house_items_query->results() as $item) {
				$item_score = $house_items['item_'.$item->id] * $item->score * $u_house->house_level;
				$items_score += $item_score;
			}

			$total = round($weapons + $money + $defence + $offence + $education + $items_score);

			DB::getInstance($world_c)->update('users_data', $u->id, array('score' => $total));
		}	

	//Restorani tellimused
			$resto_users_query =  DB::getInstance($world_c)->query("SELECT * FROM `users_data_resto` WHERE `created` = 1");
		foreach ($resto_users_query->results() as $resto) {

			//Food 1 orders
			$food_1_rec_price = 1000 * ($resto->waiter_level + $resto->chef_level + $resto->kitchen_level + $resto->furniture_level);
			$food_1_price_per = round($food_1_rec_price * 100 / $resto->food_1_price);

			if ($food_1_price_per > 200) {
				$food_1_price_percent = 200;
			} else if ($food_1_price_per < 10) {
				$food_1_price_percent = 0;
			} else {
				$food_1_price_percent = $food_1_price_per;
			}

			$food_1_orders_rec = (2 * $resto->furniture_level * $resto->waiter);
			$food_1_orders_per = ceil($food_1_orders_rec * $food_1_price_percent / 100);
			$food_1_orders = ceil($food_1_orders_per * $resto->waiter_morale / 100);

			//Food 2 orders
			$food_2_rec_price = 1400 * ($resto->waiter_level + $resto->chef_level + $resto->kitchen_level + $resto->furniture_level);
			$food_2_price_per = round($food_2_rec_price * 100 / $resto->food_2_price);

			if ($food_2_price_per > 200) {
				$food_2_price_percent = 200;
			} else if ($food_2_price_per < 50) {
				$food_2_price_percent = 0;
			} else {
				$food_2_price_percent = $food_2_price_per;
			}

			$food_2_orders_rec = (1.5 * $resto->furniture_level * $resto->waiter);
			$food_2_orders_per = ceil($food_2_orders_rec * $food_2_price_percent / 100);
			$food_2_orders = ceil($food_2_orders_per * $resto->waiter_morale / 100);

			//Food 3 orders
			$food_3_rec_price = 1100 * ($resto->waiter_level + $resto->chef_level + $resto->kitchen_level + $resto->furniture_level);
			$food_3_price_per = round($food_3_rec_price * 100 / $resto->food_3_price);

			if ($food_3_price_per > 200) {
				$food_3_price_percent = 200;
			} else if ($food_3_price_per < 50) {
				$food_3_price_percent = 0;
			} else {
				$food_3_price_percent = $food_3_price_per;
			}

			$food_3_orders_rec = (2 * $resto->furniture_level * $resto->waiter);
			$food_3_orders_per = ceil($food_3_orders_rec * $food_3_price_percent / 100);
			$food_3_orders = ceil($food_3_orders_per * $resto->waiter_morale / 100);

			//Food 4 orders
			$food_4_rec_price = 2300 * ($resto->waiter_level + $resto->chef_level + $resto->kitchen_level + $resto->furniture_level);
			$food_4_price_per = round($food_4_rec_price * 100 / $resto->food_4_price);

			if ($food_4_price_per > 200) {
				$food_4_price_percent = 200;
			} else if ($food_4_price_per < 50) {
				$food_4_price_percent = 0;
			} else {
				$food_4_price_percent = $food_4_price_per;
			}

			$food_4_orders_rec = (1 * $resto->furniture_level * $resto->waiter);
			$food_4_orders_per = ceil($food_4_orders_rec * $food_4_price_percent / 100);
			$food_4_orders = ceil($food_4_orders_per * $resto->waiter_morale / 100);

			//Food 5 orders
			$food_5_rec_price = 1800 * ($resto->waiter_level + $resto->chef_level + $resto->kitchen_level + $resto->furniture_level);
			$food_5_price_per = round($food_5_rec_price * 100 / $resto->food_5_price);

			if ($food_5_price_per > 200) {
				$food_5_price_percent = 200;
			} else if ($food_5_price_per < 50) {
				$food_5_price_percent = 0;
			} else {
				$food_5_price_percent = $food_5_price_per;
			}

			$food_5_orders_rec = (1 * $resto->furniture_level * $resto->waiter);
			$food_5_orders_per = ceil($food_5_orders_rec * $food_5_price_percent / 100);
			$food_5_orders = ceil($food_5_orders_per * $resto->waiter_morale / 100);

			DB::getInstance($world_c)->update('users_data_resto', $resto->id, array(
				'food_1_orders' => $food_1_orders,
				'food_2_orders' => $food_2_orders,
				'food_3_orders' => $food_3_orders,
				'food_4_orders' => $food_4_orders,
				'food_5_orders' => $food_5_orders
			));
		}
}

	//Aktsiad
	$stock_price = mt_rand($_GENERAL->settings('settings_game','STOCK_PRICE_MIN'),$_GENERAL->settings('settings_game','STOCK_PRICE_MAX'));
	DB::getInstance(1)->query("UPDATE `settings_game` set `value`='".$stock_price."' WHERE `name` = 'STOCK_PRICE' ");

	echo "Time Elapsed: ".(microtime(true) - $cron_start_time)."s";
