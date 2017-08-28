<?php
/*
 * Name: FreeLand v2
 * Author: Marko Murumaa
 * Created: July 2015
 * Website: www.freelandplay.eu
*/

class User {
	private $_db,
			$_db1,
			$_data,
			$_data1,
			$_data2,
			$_data3,
			$_sessionName,
			$_isLoggedIn,
			$_defence_gang,
			$_defence_gym,
			$_defence_weapons,
			$_defence_self,
			$_defence_total,
			$_offence_gang,
			$_offence_gym,
			$_offence_weapons,
			$_offence_self,
			$_offence_total;

	public function __construct($user = null, $world = null) {
		$this->_db1 = DB::getInstance(1);
		$this->_db = DB::getInstance($world);

		$this->_sessionName = Config::get('session/session_name');

		if(!$user) {
			if(Session::exists($this->_sessionName)) {

				$session = Session::get($this->_sessionName);
				$userid = $this->userid_from_session($session);

				if($this->find($userid)) {
					$this->_isLoggedIn = true;
				} else {
					$this->logout();
				}
			}
		} else {
			$this->find($user);
		}

	}

	public function update($fields = array(), $id = null, $table = 'users') {

		if(!$id && $this->isLoggedIn()) {
			$id = $this->data()->id;
		}

		if(!$this->_db->update($table, $id, $fields)) {
			throw new Exception('There was a problem updating.');
		}
	}

	public function create($fields) {
		$create_users = $this->_db->insert('users', $fields);
		$create_data = $this->_db->query("INSERT INTO users_data () VALUES ()");
		$create_data_house = $this->_db->query("INSERT INTO users_data_house () VALUES ()");
		$create_data_resto = $this->_db->query("INSERT INTO users_data_resto () VALUES ()");

		if(!$create_users && !$create_data && !$create_data_house && !$create_data_resto) {
			throw new Exception('There was a problem creating an account.');
		}
	}

	public function find($user = null) {
		if ($user) {
			$field = (is_numeric($user)) ? 'id' : 'username';
			$data = $this->_db->get('users', array($field, '=', $user));
			
			if($data->count()) {
				$this->_data = $data->first();
				$return = true;
			}

			$data1 = $this->_db->get('users_data', array('id', '=', $data->first()->id));
			if($data1->count()) {
				$this->_data1 = $data1->first();
				$return1 = true;
			}

			$data2 = $this->_db->get('users_data_house', array('id', '=', $data->first()->id));
			if($data2->count()) {
				$this->_data2 = $data2->first();
				$return2 = true;
			}

			$data3 = $this->_db->get('users_data_resto', array('id', '=', $data->first()->id));
			if($data2->count()) {
				$this->_data3 = $data3->first();
				$return3 = true;
			}

			if ($return === true && $return1 === true && $return2 === true && $return3 === true) {
				return true;
			}
		}
	}

	public function login($username = null, $password = null) {	
		$newsession = Hash::unique();
		if (!$username && !$password && $this->exists()) {
			Session::put($this->_sessionName, $newsession);
			$this->update(array('session' => $newsession), $this->data()->id);
		} else {
			$user = $this->find($username);

			if ($user) {
				if (Hash::verify($password, $this->data()->password)) {
					Session::put($this->_sessionName, $newsession);
					$this->update(array('session' => $newsession), $this->data()->id);
					return true;
				}
			}
			return false;
		}
	}

	public function password_recovery($username, $world = 1) {
		$gen = new General();
		$newpassword = Hash::random_password(8);
		$newpassword_hash = Hash::make($newpassword);

		$activation_code = Hash::unique();
		$datetime = date('Y-m-d H:i:s');

		$email = $this->data()->email;
		$email_title = 'FreeLand - Teie uus parool.';
		$email_body = "
Tere ".$this->data()->username.",
Te olete tellinud omale uue parooli mängus FreeLand.

Maailm: ".$world."

Uue parooli aktiveerimiseks vajutage sellele lingile:
http://www.freelandplay.eu/p.php?p=change_password&m=".$world."&c=".$activation_code."&u=".$this->data()->username."&e=".$this->data()->email."

Teie uus parool on: ".$newpassword."

Palun muutke parool kohe peale sisselogimist ära.

- FreeLand - www.freelandplay.eu -
";
		$email_from = $gen->settings('settings_game','GAME_EMAIL');
		$gen->email($email, $email_title, $email_body, $email_from);

		$this->_db->insert('password_recovery', array(
			'date' => $datetime,
			'activation_code' => $activation_code,
			'newpassword' => $newpassword_hash,
			'user_id' => $this->data()->id,
			'username' => $this->data()->username,
			'email' => $this->data()->email,
			'active' => 1
			));
	}

	public function hasPermission($key) {
		$group = $this->_db1->get('groups', array('id', '=', $this->data()->groups));

		if($group->count()) {
			$permissions = json_decode($group->first()->permissions, true);
			
			if($permissions[$key] == true) {
				return true;
			}

		}

		return false;
	}

	public function userid_from_session($session) {
		$user = $this->_db->get('users', array('session', '=', $session));
		return $user->first()->id;
	}

	public function user_defence() {
		$gen = new General();
		if ($this->data('data')->gang != 0) {
			$gang_query = $this->_db->get('gang', array('id', '=', $this->data('data')->gang));
			$gang_i = $gang_query->first();

			$gang = round(($gang_i->defence / 100) * $this->data('house')->defence_level);
		} else {
			$gang = 0;
		}

		$gym = round( ($this->data('data')->speed * $gen->settings('settings_game','GYM_1_DEF')) + 
			($this->data('data')->strength * $gen->settings('settings_game','GYM_2_DEF')) + 
			($this->data('data')->stamina * $gen->settings('settings_game','GYM_2_DEF')));

		$self = $this->data('house')->defence_level * $this->data('house')->defence_man * 3;

		$weapon_give = ($this->data('house')->defence_man + $this->data('house')->offence_man) * 2;

		$wep_1 = ($this->data('house')->wep_1 > $weapon_give) ? $weapon_give * $gen->settings('settings_game','WEP_1_DEF') : $this->data('house')->wep_1 * $gen->settings('settings_game','WEP_1_DEF');
		$wep_2 = ($this->data('house')->wep_2 > $weapon_give) ? $weapon_give * $gen->settings('settings_game','WEP_2_DEF') : $this->data('house')->wep_2 * $gen->settings('settings_game','WEP_2_DEF');
		$wep_3 = ($this->data('house')->wep_3 > $weapon_give) ? $weapon_give * $gen->settings('settings_game','WEP_3_DEF') : $this->data('house')->wep_3 * $gen->settings('settings_game','WEP_3_DEF');
		$wep_4 = ($this->data('house')->wep_4 > $weapon_give) ? $weapon_give * $gen->settings('settings_game','WEP_4_DEF') : $this->data('house')->wep_4 * $gen->settings('settings_game','WEP_4_DEF');
		$wep_5 = ($this->data('house')->wep_5 > $weapon_give) ? $weapon_give * $gen->settings('settings_game','WEP_5_DEF') : $this->data('house')->wep_5 * $gen->settings('settings_game','WEP_5_DEF');
		$wep_6 = ($this->data('house')->wep_6 > $weapon_give) ? $weapon_give * $gen->settings('settings_game','WEP_6_DEF') : $this->data('house')->wep_6 * $gen->settings('settings_game','WEP_6_DEF');

		$weapons = round($wep_1 + $wep_2 + $wep_3 + $wep_4 + $wep_5 + $wep_6);

		$total = $gang + $weapons + $gym + $self;

		$this->_defence_gang = $gang;
		$this->_defence_gym = $gym;
		$this->_defence_weapons = $weapons;
		$this->_defence_self = $self;
		$this->_defence_total = $total;

		return true;
	}

	public function user_defence_i($x = '') {
		if ($x == 'gang') {
			return $this->_defence_gang;
		} else if ($x == 'gym') {
			return $this->_defence_gym;
		} else if ($x == 'weapons') {
			return $this->_defence_weapons;
		} else if ($x == 'self') {
			return $this->_defence_self;
		} else {
			return $this->_defence_total;
		}
	}

	public function user_offence() {
		$gen = new General();
		if ($this->data('data')->gang != 0) {
			$gang_query = $this->_db->get('gang', array('id', '=', $this->data('data')->gang));
			$gang_i = $gang_query->first();

			$gang = round(($gang_i->offence / 100) * $this->data('house')->offence_level);
		} else {
			$gang = 0;
		}

		$gym = round( ($this->data('data')->speed * $gen->settings('settings_game','GYM_1_OFE') ) + 
			($this->data('data')->strength * $gen->settings('settings_game','GYM_2_OFE') ) + 
			($this->data('data')->stamina * $gen->settings('settings_game','GYM_3_OFE') ));

		$self = $this->data('house')->offence_level * $this->data('house')->offence_man * 2;

		$weapon_give = ($this->data('house')->defence_man + $this->data('house')->offence_man) * 2;

		$wep_1 = ($this->data('house')->wep_1 > $weapon_give) ? $weapon_give * $gen->settings('settings_game','WEP_1_OFE') : $this->data('house')->wep_1 * $gen->settings('settings_game','WEP_1_OFE');
		$wep_2 = ($this->data('house')->wep_2 > $weapon_give) ? $weapon_give * $gen->settings('settings_game','WEP_2_OFE') : $this->data('house')->wep_2 * $gen->settings('settings_game','WEP_2_OFE');
		$wep_3 = ($this->data('house')->wep_3 > $weapon_give) ? $weapon_give * $gen->settings('settings_game','WEP_3_OFE') : $this->data('house')->wep_3 * $gen->settings('settings_game','WEP_3_OFE');
		$wep_4 = ($this->data('house')->wep_4 > $weapon_give) ? $weapon_give * $gen->settings('settings_game','WEP_4_OFE') : $this->data('house')->wep_4 * $gen->settings('settings_game','WEP_4_OFE');
		$wep_5 = ($this->data('house')->wep_5 > $weapon_give) ? $weapon_give * $gen->settings('settings_game','WEP_5_OFE') : $this->data('house')->wep_5 * $gen->settings('settings_game','WEP_5_OFE');
		$wep_6 = ($this->data('house')->wep_6 > $weapon_give) ? $weapon_give * $gen->settings('settings_game','WEP_6_OFE') : $this->data('house')->wep_6 * $gen->settings('settings_game','WEP_6_OFE');

		$weapons = round($wep_1 + $wep_2 + $wep_3 + $wep_4 + $wep_5 + $wep_6);

		$total = $gang + $weapons + $gym + $self;

		$this->_offence_gang = $gang;
		$this->_offence_gym = $gym;
		$this->_offence_weapons = $weapons;
		$this->_offence_self = $self;
		$this->_offence_total = $total;
		
		return true;
	}

	public function user_offence_i($x = '') {
		if ($x == 'gang') {
			return $this->_offence_gang;
		} else if ($x == 'gym') {
			return $this->_offence_gym;
		} else if ($x == 'weapons') {
			return $this->_offence_weapons;
		} else if ($x == 'self') {
			return $this->_offence_self;
		} else {
			return $this->_offence_total;
		}
	}

	public function exists() {
		return (!empty($this->_data)) ? true : false;
	}

	public function logout() {
		Session::delete($this->_sessionName);
		Session::delete("world");
	}

	public function data($table = 'users') {
		if ($table == 'users') {
			return $this->_data;
		} elseif ($table == 'data') {
			return $this->_data1;
		} elseif ($table == 'house') {
			return $this->_data2;
		} elseif ($table == 'resto') {
			return $this->_data3;
		}
	}

	public function isLoggedIn() {
		return $this->_isLoggedIn;
	}
}
