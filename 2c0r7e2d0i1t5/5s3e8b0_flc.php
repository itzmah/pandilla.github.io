<?php
/*
 * Name: FreeLand v2
 * Author: Marko Murumaa
 * Created: July 2015
 * Website: www.freelandplay.eu
*/

require_once("../core/init.php");

if (Input::exists()) {
	$mail_to_http = Input::get("mail_to_http");
	$sender = (empty($mail_to_http["from"]) === true) ? " ": $mail_to_http["from"];
	$body = (empty($mail_to_http["body"]) === true) ? " ": $mail_to_http["body"];

	if ($sender == "marko.murumaa@gmail.com" OR $sender == "automailer@seb.ee") {

		$lines =  explode(PHP_EOL, $body);

		$price = "";
		$desc = "";

		$is_header = true;

		for ($i=0; $i < count($lines); $i++) {
			if (!$is_header) {
				if (preg_match("/^Summa: (.*)/", $lines[$i], $matches)) {
					$price = $matches[1];
				}
				if (preg_match("/^Selgitus: (.*)/", $lines[$i], $matches)) {
					$desc = $matches[1];
				}
			}

			if (trim($lines[$i])=="") {
				$is_header = false;
			}
		}


		$desc_x = explode(",", $desc);
		$found = false;

		if ($desc_x[0] == "freeland1") {
			$c_world = 1;
			$found = true;
		} else if ($desc_x[0] == "freeland2") {
			$c_world = 2;
			$found = true;
		} else if ($desc_x[0] == "freeland3") {
			$c_world = 3;
			$found = true;
		}

		if ($found === true) {

			$c_user = $desc_x[1];
			$price_x = substr($price, 1, -4);
			$flc = $price_x * 30;

			if ($price_x >= 100) {
				$percent = 100;
			} else if ($price_x >= 90) {
				$percent = 90;
			} else if ($price_x >= 80) {
				$percent = 80;
			} else if ($price_x >= 70) {
				$percent = 70;
			} else if ($price_x >= 60) {
				$percent = 60;
			} else if ($price_x >= 50) {
				$percent = 50;
			} else if ($price_x >= 40) {
				$percent = 40;
			} else if ($price_x >= 30) {
				$percent = 30;
			} else if ($price_x >= 20) {
				$percent = 20;
			} else if ($price_x >= 10) {
				$percent = 10;
			} else {
				$percent = 0;
			}

			$flc_bonus = $flc / 100 * $percent;
			$total_flc = $flc + $flc_bonus;

			$user_check_query = DB::getInstance($c_world)->query("SELECT `flc` FROM `users_data` WHERE `id` = ".$c_user." ");
			if ($user_check_query->count() > 0) {
				$u = $user_check_query->first();

				$user_data_query = DB::getInstance($c_world)->query("SELECT * FROM `users` WHERE `id` = ".$c_user." ");
				$u_data = $user_data_query->first();

				$bank_fields = array(
					'status' => 'ok',
					'user_id' => $c_user,
					'type' => 'PANK',
					'price' => $price_x,
					'flc' => $total_flc
					);

				if ($u_data->referer != 0) {

					$ref_flc = floor($flc * 25 / 100);
					$ref_user_data_query = DB::getInstance(1)->query("SELECT `flc` FROM `users_data` WHERE `id` = ".$u_data->referer." ");
          			$ref_user_data = $ref_user_data_query->first();

					$ref_fields = array(
						'status' => 'ok',
						'user_id' => $u_data->referer,
						'type' => 'BOONUS',
						'price' => $price_x,
						'flc' => $ref_flc
					);

					DB::getInstance($c_world)->update('users_data', $u_data->referer, array('flc' => $ref_user_data->flc + $ref_flc));
					DB::getInstance($c_world)->insert('credit_history', $ref_fields);
				}

				DB::getInstance($c_world)->update('users_data', $c_user, array('flc' => $u->flc + $total_flc));
			} else {
				$bank_fields = array(
					'status' => 'NOT_FOUND',
					'user_id' => 0,
					'type' => 'PANK',
					'price' => $price_x,
					'flc' => $total_flc
					);
			}

			DB::getInstance($c_world)->insert('credit_history', $bank_fields);
		}

	}
}
