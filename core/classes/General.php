<?php
/*
 * Name: FreeLand v2
 * Author: Marko Murumaa
 * Created: July 2015
 * Website: www.freelandplay.eu
*/

class General {
	private $_errors = array(),
			$_success = array(),
			$_info = array(),
			$_db;

	public function __construct() {
		$this->_db = DB::getInstance(1);
	}

	public function settings($table, $value) {
		$query = $this->_db->get($table, array('name','=',$value));
		return $query->first()->value;
	}

	public function email($to, $subject, $body, $from) {
		return mail($to, $subject, $body, 'From: '. $from);
	}

	public function addArrayError($errors = array()) {
		foreach($errors as $error) {
			$this->addError($error);
		}
	}

	public function percent_success($x) {
		$rand = mt_rand(0, 100);
		if ($rand < $x) {
			return true;
		} else if ($rand > $x) {
			return false;
		}
	}

	public function time_ends($x) {
		if ($x < 60) {
			$full = $x.' sekundi';
		} else if ($x < 60*60) {
			$aegmin = floor($x / 60);
			$aegsec = $x - ($aegmin * 60);
			$full = $aegmin.' minuti ja '.$aegsec.' sekundi';
		} else if ($x < 60*60*60) {
			$aegtund = floor($x / (60*60) );
			$aegminn = $x - ($aegtund * 60 * 60);
			$aegmin = floor($aegminn / 60);
			$aegsecc = $x - ($aegtund * 60*60);
			$aegsec = $aegsecc - ($aegmin * 60);
			$full = $aegtund.' tunni '.$aegmin.' minuti ja '.$aegsec.' sekundi';
		}
		return $full;
	}

	function system_time_counter() {
		$cron_query = $this->_db->get('system_time', array('id', '=', 1));
		$cron = $cron_query->first();

		$old_time = strtotime($cron->time);

		$time_remain = $old_time - time();

		if ($time_remain <= 0) {
			$new_time = $old_time + 900;
			$new_date = date("Y-m-d H:i:s", $new_time);
			$this->_db->update('system_time', $cron->id, array('time' => $new_date));
		}
		return $time_remain;
	}

	public function discount($x, $y) {
		$s1 = ($y / 100) * $x;
		return round($x - $s1);
	}

	public function addError($error) {
		$this->_errors[] = $error;
	}
	
	public function addOutInfo($info) {
		$this->_info[] = $info;
	}
	
	public function addOutSuccess($success) {
		$this->_success[] = $success;
	}

	public function errors() {
		return $this->_errors;
	}

	public function outInfo() {
		return $this->_info;
	}

	public function outSuccess() {
		return $this->_success;
	}

	public function output_errors() {
		$return = '<div class="alert-error">' . implode('<br>', $this->_errors) . '</div>';
		$this->_errors = null;
		return $return;
	}

	public function output_success() {
		$return = '<div class="alert-success">' . implode('<br>', $this->_success) . '</div>';
		$this->_success = null;
		return $return;
	}

	public function output_info() {
		$return = '<div class="alert-info">' . implode('<br>', $this->_info) . '</div>';
		$this->_info = null;
		return $return;
	}

	public function format_number($number) {
		return  number_format($number, 0, '', ' ');
	}
	public function format_word($sona) {
		$r = Array
		(
		'%u0161' => '&#353;',
		'%u0160' => '&#352;',
		'%u017E' => '&#382;',
		'%u017D' => '&#381;',
		'%u20AC' => '&euro;',
		'~' => '&tilde;',
		'õ' => '&otilde;',
		'ä' => '&auml;',
		'ö' => '&ouml;',
		'ü' => '&uuml;',
		'Õ' => '&Otilde;',
		'Ä' => '&Auml;',
		'Ö' => '&Ouml;',
		'Ü' => '&Uuml;',
		'\'' => '&#39;',
		'"' => '&quot;',
		'<' => '&lt;',
		'>' => '&gt;',
		'?' => '&#63;',
		'$' => '&#36;',
		'%' => '&#37;',
		'`' => '&#96;',
		'Þ' => '&#180;',
		'*' => '&#42;',
		'@' => '&#64;',
		'£' => '&pound;',
		'{' => '&#123;',
		'[' => '&#91;',
		']' => '&#93;',
		'}' => '&#125;',
		'\\' => '&#92;',
		);
		$sona = str_replace(array_keys($r), $r, $sona);
		return $sona;
	}
}
