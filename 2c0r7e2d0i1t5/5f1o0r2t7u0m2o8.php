<?php
/*
 * Name: GECode
 * Author: Marko Murumaa
 * Created: February 2015 - July 2015
 * Website: www.markofy.eu/gecode
*/

  require( '../core/init.php');

  // check that the request comes from Fortumo server
  if(!in_array($_SERVER['REMOTE_ADDR'],
    array('54.72.6.126', '54.72.6.27', '54.72.6.17', '54.72.6.23', '79.125.125.1', '79.125.5.95', '79.125.5.205'))) {
    header("HTTP/1.0 403 Forbidden");
    die("Error: Unknown IP");
  }
 
  // check the signature
  $secret = '837028544d19f580c59f32ba938dd3e0'; // insert your secret
  if(empty($secret) || !check_signature($_GET, $secret)) {
    header("HTTP/1.0 404 Not Found");
    die("Error: Invalid signature");
  }
 
  $amount = $_GET['amount'];//credit
  $cuid = $_GET['cuid'];//resource i.e. user
  $price = $_GET['price'];//price
  $test = $_GET['test']; // this parameter is present only when the payment is a test payment, it's value is either 'ok' or 'fail'

  $sms_user = new User($user = $cuid, $world = 1);

  if ($sms_user->exists() === true) {
    if($test){
      $sms_fields = array(
          'status' => 'TEST',
          'user_id' => 1,
          'type' => 'SMS',
          'price' => $price,
          'flc' => $amount
          );
    } else {
      if(preg_match("/failed/i", $_GET['status'])) {
        $sms_fields = array(
          'status' => 'FAILED',
          'user_id' => $cuid,
          'type' => 'SMS',
          'price' => $price,
          'flc' => $amount
          );
      } else {
        $sms_fields = array(
          'status' => 'ok',
          'user_id' => $cuid,
          'type' => 'SMS',
          'price' => $price,
          'flc' => $amount
          );
        $sms_user->update(array(
            'flc' => $sms_user->data('data')->flc + $amount
          ), $sms_user->data()->id, 'users_data');

        if ($sms_user->data()->referer != 0) {
          $ref_flc = floor($amount * 25 / 100);
          $ref_user_data_query = DB::getInstance(1)->query("SELECT `flc` FROM `users_data` WHERE `id` = ".$sms_user->data()->referer." ");
          $ref_user_data = $ref_user_data_query->first();

          $ref_fields = array(
            'status' => 'ok',
            'user_id' => $sms_user->data()->referer,
            'type' => 'BOONUS',
            'price' => $price,
            'flc' => $ref_flc
            );

          DB::getInstance(1)->update('users_data', $sms_user->data()->referer, array('flc' => $ref_user_data->flc + $ref_flc));
          DB::getInstance(1)->insert('credit_history', $ref_fields);
        }
      }
    }
  } else {
  $sms_fields = array(
    'status' => 'FAILED',
    'user_id' => $cuid,
    'type' => 'SMS',
    'price' => $price,
    'flc' => $amount
    );
  }

  DB::getInstance(1)->insert('credit_history', $sms_fields);

  function check_signature($params_array, $secret) {
    ksort($params_array);
 
    $str = '';
    foreach ($params_array as $k=>$v) {
      if($k != 'sig') {
        $str .= "$k=$v";
      }
    }
    $str .= $secret;
    $signature = md5($str);
 
    return ($params_array['sig'] == $signature);
  }

  $sms_user = null;
