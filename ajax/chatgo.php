<?php
/*
 * Name: FreeLand v2
 * Author: Marko Murumaa
 * Created: July 2015
 * Website: www.freelandplay.eu
*/

require ('../core/init.php');

if(Input::exists()) {
	if ($_USER->isLoggedIn() === true) {

		if (empty(Input::get('message')) === true) {
			$_GENERAL->addError("Te ei sisestanud sõnumit.");
			print('ERROR_1');
		}

		if (strlen(Input::get('message')) > 255) {
			$_GENERAL->addError("Teie sisestatud sõnum on liiga pikk.");
			print('ERROR_2');
		}

		if ($_USER->data()->chat_mute == 1) {
			$_GENERAL->addError("Teil on keelatud jutukas rääkida.");
			print('ERROR_3');
		}

		if (empty($_GENERAL->errors()) === true) {
			$fields = array('user_id' => $_USER->data()->id, 'message' => input::get('message'));
			DB::getInstance()->insert('chat', $fields);
		}
	}
}
