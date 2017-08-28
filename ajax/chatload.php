<?php
/*
 * Name: FreeLand v2
 * Author: Marko Murumaa
 * Created: July 2015
 * Website: www.freelandplay.eu
*/

require ('../core/init.php');

if ($_USER->isLoggedIn() === true) {
	$output = null;
	//$chat_rows_max = $_GENERAL->settings('settings_game','CHAT_PER_PAGE');
	$chat_rows_max = 50;

	function smiles($sona) {
		$r = Array(
			':)' => '<img src="smileys/smile.gif"/>', ':-)' => '<img src="smileys/smile.gif"/>',
			'=)' => '<img src="smileys/smile.gif"/>', '=-)' => '<img src="smileys/smile.gif"/>',
			':(' => '<img src="smileys/frown.gif"/>', ':-(' => '<img src="smileys/frown.gif"/>',
			'=(' => '<img src="smileys/frown.gif"/>', '=-(' => '<img src="smileys/frown.gif"/>',
			':D' => '<img src="smileys/bigsmile.gif"/>', ':-D' => '<img src="smileys/bigsmile.gif"/>',
			'=D' => '<img src="smileys/bigsmile.gif"/>', '=-D' => '<img src="smileys/bigsmile.gif"/>',
			'>:('=> '<img src="smileys/angry.gif"/>', '>:-('=> '<img src="smileys/angry.gif"/>',
			'>=('=> '<img src="smileys/angry.gif"/>', '>=-('=> '<img src="smileys/angry.gif"/>',
			'D:' => '<img src="smileys/angry.gif"/>', 'D-:' => '<img src="smileys/angry.gif"/>',
			'D=' => '<img src="smileys/angry.gif"/>', 'D-=' => '<img src="smileys/angry.gif"/>',
			'>:)'=> '<img src="smileys/evil.gif"/>', '>:-)'=> '<img src="smileys/evil.gif"/>',
			'>=)'=> '<img src="smileys/evil.gif"/>', '>=-)'=> '<img src="smileys/evil.gif"/>',
			'>:D'=> '<img src="smileys/evil.gif"/>', '>:-D'=> '<img src="smileys/evil.gif"/>',
			'>=D'=> '<img src="smileys/evil.gif"/>', '>=-D'=> '<img src="smileys/evil.gif"/>',
			'>;)'=> '<img src="smileys/sneaky.gif"/>', '>;-)'=> '<img src="smileys/sneaky.gif"/>',
			'>;D'=> '<img src="smileys/sneaky.gif"/>', '>;-D'=> '<img src="smileys/sneaky.gif"/>',
			'O:)' => '<img src="smileys/saint.gif"/>', 'O:-)' => '<img src="smileys/saint.gif"/>',
			'O=)' => '<img src="smileys/saint.gif"/>', 'O=-)' => '<img src="smileys/saint.gif"/>',
			':O' => '<img src="smileys/surprise.gif"/>', ':-O' => '<img src="smileys/surprise.gif"/>',
			'=O' => '<img src="smileys/surprise.gif"/>', '=-O' => '<img src="smileys/surprise.gif"/>',
			':?' => '<img src="smileys/confuse.gif"/>', ':-?' => '<img src="smileys/confuse.gif"/>',
			'=?' => '<img src="smileys/confuse.gif"/>', '=-?' => '<img src="smileys/confuse.gif"/>',
			':s' => '<img src="smileys/worry.gif"/>', ':-S' => '<img src="smileys/worry.gif"/>',
			'=s' => '<img src="smileys/worry.gif"/>', '=-S' => '<img src="smileys/worry.gif"/>',
			':|' => '<img src="smileys/neutral.gif"/>', ':-|' => '<img src="smileys/neutral.gif"/>',
			'=|' => '<img src="smileys/neutral.gif"/>', '=-|' => '<img src="smileys/neutral.gif"/>',
			':I' => '<img src="smileys/neutral.gif"/>', ':-I' => '<img src="smileys/neutral.gif"/>',
			'=I' => '<img src="smileys/neutral.gif"/>', '=-I' => '<img src="smileys/neutral.gif"/>',
			':/' => '<img src="smileys/irritated.gif"/>', ':-/' => '<img src="smileys/irritated.gif"/>',
			'=/' => '<img src="smileys/irritated.gif"/>', '=-/' => '<img src="smileys/irritated.gif"/>',
			':\\' => '<img src="smileys/irritated.gif"/>', ':-\\' => '<img src="smileys/irritated.gif"/>',
			'=\\' => '<img src="smileys/irritated.gif"/>', '=-\\' => '<img src="smileys/irritated.gif"/>',
			':P' => '<img src="smileys/tongue.gif"/>', ':-P' => '<img src="smileys/tongue.gif"/>',
			'=P' => '<img src="smileys/tongue.gif"/>', '=-P' => '<img src="smileys/tongue.gif"/>',
			'X-P' => '<img src="smileys/tongue.gif"/>',
			'8)' => '<img src="smileys/bigeyes.gif"/>', '8-)' => '<img src="smileys/bigeyes.gif"/>',
			'B)' => '<img src="smileys/cool.gif"/>', 'B-)' => '<img src="smileys/cool.gif"/>',
			';)' => '<img src="smileys/wink.gif"/>', ';-)' => '<img src="smileys/wink.gif"/>',
			';D' => '<img src="smileys/bigwink.gif"/>', ';-D' => '<img src="smileys/bigwink.gif"/>',
			'^_^'=> '<img src="smileys/anime.gif"/>', '^^;' => '<img src="smileys/sweatdrop.gif"/>',
			'>_>'=> '<img src="smileys/lookright.gif"/>', '>.>' => '<img src="smileys/lookright.gif"/>',
			'<_<'=> '<img src="smileys/lookleft.gif"/>', '<.<' => '<img src="smileys/lookleft.gif"/>',
			'XD' => '<img src="smileys/laugh.gif"/>', 'X-D' => '<img src="smileys/laugh.gif"/>',
			';D' => '<img src="smileys/bigwink.gif"/>', ';-D' => '<img src="smileys/bigwink.gif"/>',
			':3' => '<img src="smileys/smile3.gif"/>', ':-3' => '<img src="smileys/smile3.gif"/>',
			'=3' => '<img src="smileys/smile3.gif"/>', '=-3' => '<img src="smileys/smile3.gif"/>',
			';3' => '<img src="smileys/wink3.gif"/>', ';-3' => '<img src="smileys/wink3.gif"/>',
			'<g>' => '<img src="smileys/teeth.gif"/>', '<G>' => '<img src="smileys/teeth.gif"/>',
			'o.O' => '<img src="smileys/boggle.gif"/>', 'O.o' => '<img src="smileys/boggle.gif"/>',
			':blue:' => '<img src="smileys/blue.gif"/>',
			':zzz:' => '<img src="smileys/sleepy.gif"/>',
			'<3' => '<img src="smileys/heart.gif"/>',
			':star:' => '<img src="smileys/star.gif"/>',
			);
		$sona = str_replace(array_keys($r), $r, $sona);
		return $sona;
	}

	$chat_query = DB::getInstance()->query("SELECT * FROM chat WHERE `deleted` = 0 ORDER BY id DESC LIMIT $chat_rows_max");
	
	if ($chat_query-> count() < 1) {
		print('Jutukas ei ole veel midagi kirjutatud.');
	} else {
		foreach ($chat_query->results() as $row) {
			$user_query = DB::getInstance()->get('users', array('id','=',$row->user_id));
			$username = $user_query->first()->username;

			$group_query = DB::getInstance(1)->get('groups', array('id','=',$user_query->first()->groups));
			$color = $group_query->first()->color;
			
			$message = smiles($row->message);
			$time = date("H:i:s", strtotime($row->date));

			$output .= '<div class="chat-msg">['.$time.'] <a href="p.php?p=profile&user='.$username.'"><font color="'.$color.'">'.$username.'</font></a>: '.$message.'</div>';
		}
		print($output);
	}

} else {
	print('<div align="center">Palun logige sisse!</div>');
}
