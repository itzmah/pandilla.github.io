<?php
/*
 * Name: FreeLand v2
 * Author: Marko Murumaa
 * Created: July 2015
 * Website: www.freelandplay.eu
*/

class Token {
	public static function generate($id = 0) {
		$name = Config::get('session/token_name') . '_' . $id;
		return Session::put($name, md5(uniqid()));
	}

	public static function check($token, $id = 0) {
		$tokenName = Config::get('session/token_name'). '_' . $id;

		if(Session::exists($tokenName) && $token === Session::get($tokenName)) {
			Session::delete($tokenName);
			return true;
		}

		return false;
	}
}
