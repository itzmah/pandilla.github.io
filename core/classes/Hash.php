<?php
/*
 * Name: FreeLand v2
 * Author: Marko Murumaa
 * Created: July 2015
 * Website: www.freelandplay.eu
*/

class Hash {

	public static function make($string) {
		$options = [
			'cost' => 10
		];
		return password_hash($string, PASSWORD_DEFAULT, $options);
	}

	public static function unique() {
		return hash('sha256', uniqid());
	}

	public static function verify($string , $hash) {
		return password_verify($string, $hash);
	}

	public static function random_password($length = 8) {
		$symbols = '1234567890qwertyuiopasfghjklzxcvbnmQWERTYUIOPASDFGHJKLZXCVBNM!@#$%&';
		$symbols_length = strlen($symbols);
		$newpass = null;
		for ($i = 0;$i<$length;$i++) {
			$r = rand(1,$symbols_length);
			$newpass .= substr($symbols, $r, 1);
		}
		return $newpass;
	}
}
