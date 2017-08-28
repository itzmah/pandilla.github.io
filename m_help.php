<?php
/*
 * Name: FreeLand v2
 * Author: Marko Murumaa
 * Created: July 2015
 * Website: www.freelandplay.eu
*/

require_once("core/init.php");
	$line = NULL;
if(Input::exists()) {
	if(Token::check(Input::get('token'))) {

		$search_value = Input::get('search_value');
		$qhelp = DB::getInstance(1)->query("SELECT * FROM help WHERE name LIKE '%".$search_value."%' OR body LIKE '%".$search_value."%' ");
		foreach ($qhelp->results() as $help) {
			$body_raw = nl2br($help->body);
			$islonger = false;
			$body_length = strlen($body_raw);

			if ($body_length >= 100) {
				$body_raw = substr($body_raw, 0, 100);
				$islonger = true;
			}

			$subject = str_ireplace($search_value, "<b>".$search_value."</b>", $help->name);
			$body = str_ireplace($search_value, "<b>".$search_value."</b>", $body_raw);
			if ($islonger === true) {
				$body .= '...';
			}
			$line .= '
			<ul>
				<li><a href="p.php?p=help&i='.$help->id.'">'.$subject.'</a></li>
				<li>'.$body.'</li>
			</ul><br />
			';
		}
	}

} else if (Input::exists('get') && empty(Input::get('i')) === false) {
	$help_i = DB::getInstance(1)->get('help', array('id','=',Input::get('i')));
	if ($help_i->count() == 1) {
		$line = '
			<ul>
				<li><b>'.$help_i->first()->name.'</b></li>
				<li>'.nl2br($help_i->first()->body).'</li>
			</ul>';
	} else {
		$line = 'Me ei leidnud ühtegi vastet.';
	}

} else {
	$qhelp = DB::getInstance(1)->query('SELECT * FROM help');
	foreach ($qhelp->results() as $help) {
		$line .= '<ul><li><a href="p.php?p=help&i='.$help->id.'">'.$help->name.'</a></li></ul>';
	}
}

include("includes/overall/header.php");
?>
<div id="page">
	<div class="page-title">KKK/Abi</div>
	<p>
		<table>
			<tr valign="top">
				<td width="20%"><img src="css/default/images/help.png" width="100" height="100"></td>
				<td width="80%">
					Ei saanud mängus millestki aru? Siin on palju teemasid mis sind aitavad mängus.<br>
					Kui sa siit vastust ei leia siis küsi foorumist ja saad kindlasti vastuse oma küsimusele.
					
				</td>
			</tr>
		</table>
		<div align="center">
			<form action="p.php?p=help&search" method="POST">
				<input type="text" name="search_value" placeholder="Kirjuta mida sa otsida tahad." autocomplete="off" size="40"> 
				<input type="hidden" name="token" value="<?php echo Token::generate(); ?>">
				<input type="submit" name="search" value="Otsi abi">
			</form>
		</div>
		<br />
		<?php print($line);?>
	</p>
</div>
<?php
include("includes/overall/footer.php");
