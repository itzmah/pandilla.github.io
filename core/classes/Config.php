<?php
/*
 * Name: FreeLand v2
 * Author: Marko Murumaa
 * Created: July 2015
 * Website: www.freelandplay.eu
*/

class Config {
	public static function get($path = null) {
		if($path) {
			$config = $GLOBALS['config'];
			$path = explode('/', $path);

			foreach($path as $bit) {
				if(isset($config[$bit])) {
					$config = $config[$bit];
				}
			}
			return $config;
		}
		return false;
	}
}
