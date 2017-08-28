<?php
/*
 * Name: FreeLand v2
 * Author: Marko Murumaa
 * Created: July 2015
 * Website: www.freelandplay.eu
*/

class Redirect {
	public static function to($location = null) {
		if($location) {
			if(is_numeric($location)) {
				switch($location) {
					case 404:
						header('HTTP/1.0 404 Not Found');
						//include 'includes/errors/404.php';
						exit();
					break;
				}
			}
			header('Location: ' . $location);
			exit();
		}
	}
}
