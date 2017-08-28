<?php
/*
 * Name: FreeLand v2
 * Author: Marko Murumaa
 * Created: July 2015
 * Website: www.freelandplay.eu
*/
$start_time = microtime();
$start_time = explode(' ', $start_time);
$start_time = $start_time[1] + $start_time[0];
$start_start = $start_time;

session_start();
ob_start();
error_reporting(1);

$GLOBALS['config'] = array(
  'mysql' => array(
    'host' => "localhost",
    'username' => "freeland",
    'password' => "password",
    'db_default' => "freeland_w1",
    'db_2' => "freeland_w2",
  ),
  'session' => array(
    'session_name' => "freelandsession",
    'token_name' => "token"
  )
);

require_once 'functions/sanitize.php';

spl_autoload_register(function($class) {
  require_once 'classes/'.$class.'.php';
});

date_default_timezone_set("Europe/Tallinn");

$_GENERAL = new General();

$_WORLD = (Session::exists("world") === true) ? Session::get('world') : 1;

$_USER = new User($user = null, $world = $_WORLD);
$_BBCODE = new bbcode();

if ($_USER->isLoggedIn() === true) {
  if ($_linr === true) {
    Redirect::to('p.php?p=home');
  }

  if ($_USER->data()->groups != 2) {
    if ($_GENERAL->settings('settings_game','GAME_UPDATE') == 1) {
      $_USER->logout();
      Redirect::to('p.php?p=update');
    }
  }

  if ($_USER->data()->groups == 3) {
    $_USER->logout();
    Redirect::to('p.php?p=home');
  }
  
  if (Input::get('p') != "logout" and Input::get('p') != "rules" and Input::get('p') != "activation") {
    if ($_USER->data()->active == 2) {
      if (Input::get('p') != "notactivated") {
        Redirect::to('p.php?p=notactivated');
      }
    }
  }

  if ($_USER->data()->active == 0) {
    $_USER->logout();
    Redirect::to('p.php?p=home');
  }
  
  if (strtotime($_USER->data('data')->toetaja_time) < time()) {
    $_USER->update(array('toetaja' => 0),$_USER->data()->id, 'users_data');
  }

  $_USER->update(array('ip_address' => $_SERVER['REMOTE_ADDR']),$_USER->data()->id, 'users');
  $_USER->update(array('last_active' => date("Y-m-d H:i:s")));
  $_USER->user_defence();
  $_USER->user_offence();

} else {
  if ($_lir === true) {
    Redirect::to('p.php?p=home');
  }
}
