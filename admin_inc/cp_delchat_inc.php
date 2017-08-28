<?php
/*
 * Name: FreeLand v2
 * Author: Marko Murumaa
 * Created: July 2015
 * Website: www.freelandplay.eu
*/

if (!$_USER->hasPermission('cp_deletechat')) {
  Redirect::to('p.php?p=cpanel');
  exit();
}
$output = null;
$chat_rows_max = $_GENERAL->settings('settings_game','CHAT_PER_PAGE');

if(Input::exists()) {
  if(Token::check(Input::get('token'), 'DEL_CHAT') ) {
    if (empty(Input::get('cid')) === true) {
      $_GENERAL->addError("Please select row.");
    }

    if (empty($_GENERAL->errors()) === true) {
      foreach (Input::get('cid') as $row) {
        DB::getInstance()->update("chat", $row, array('deleted' => '1'));
      }
      Session::flash('cpanel', 'Chat rows are successfully deleted.');
      Redirect::to('p.php?p=cpanel&cp=delchat');
    }
  }
}

$chat_query = DB::getInstance()->query("SELECT * FROM chat WHERE `deleted` = 0 ORDER BY id DESC LIMIT $chat_rows_max");  
if ($chat_query-> count() < 1) {
  print('Chat is empty.');
} else {
  foreach ($chat_query->results() as $row) {
    $user_query = DB::getInstance()->get('users', array('id','=',$row->user_id));
    $username = $user_query->first()->username;

    $group_query = DB::getInstance(1)->get('groups', array('id','=',$user_query->first()->groups));
    $color = $group_query->first()->color;
    
    $message = $row->message;
    $time = date("H:i:s", strtotime($row->time));

    $output .= '<div class="chat-msg"><input type="checkbox" name="cid[]" value="'.$row->id.'"> ['.$time.'] <a href="p.php?p=profile&user='.$username.'"><font color="'.$color.'">'.$username.'</font></a>: '.$message.'</div>';
  }
}

?>
<div class="page-title">Kustuta jutuka ridu</div>
<p>
<?php
if (empty($_GENERAL->errors()) === false) {
  print($_GENERAL->output_errors());
}

if(Session::exists('cpanel')) {
  $_GENERAL->addOutSuccess(Session::flash('cpanel'));
  print($_GENERAL->output_success());
}
?>
  <form action="p.php?p=cpanel&cp=delchat" method="POST">
    <?php print($output);?>
    <br>
    <input type="hidden" name="token" value="<?php echo Token::generate('DEL_CHAT'); ?>">
    <input type="submit" value="Delete chat rows">
  </form>
</p>