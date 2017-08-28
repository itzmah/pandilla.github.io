<?php
/*
 * Name: FreeLand v2
 * Author: Marko Murumaa
 * Created: July 2015
 * Website: www.freelandplay.eu
*/

require_once("core/init.php");

$members_total_query = DB::getInstance()->query("SELECT * FROM users");
$members_total = $members_total_query->count();

$online_10_time = time() - 60 * 10;
$members_online_query = DB::getInstance()->query("SELECT * FROM users WHERE last_active > '".date("Y-m-d H:i:s", $online_10_time)."' ORDER BY last_active DESC");
$members_online = $members_online_query->count();

$news_list = null;
$news_per_page = $_GENERAL->settings('settings_game','NEWS_PER_PAGE');
$newsquery = DB::getInstance(1)->query("SELECT * FROM news");
$news_total = ceil($newsquery->count() / $news_per_page );

$page = (isset($_GET['page'])) ? (int)$_GET['page'] : 1;
$start = ($page - 1) * $news_per_page;

$x = ($page - 1) * $news_per_page;

if ($x < 0) {
	$x = 0;
}

$newsq = DB::getInstance(1)->query("SELECT * FROM news ORDER BY id DESC LIMIT $start, $news_per_page");

foreach ($newsq->results() as $news) {

	$news_list .= '
		<tr>
			<td width="25%">' . $news->date . '</td>
			<td width="75%"><a href="p.php?p=news&id='.$news->id.'">' . $news->subject . '</a></td>
		</tr>
	';
}

include("includes/overall/header.php");


?>
<div id="page">
	<div class="page-title">Uudised</div>
		<table>
			<?php print($news_list);?>
		</table>
	<p>
		<div align="center">Lehekülg:
			<?php
				if ($news_total >= 1 && $page <= $news_total) {
					for ($x=1; $x<=$news_total; $x++) {
						echo ($x == $page) ? '<b>'.$x.'</b>  ' : '<a href="p.php?p=home&page='.$x.'">'.$x.'</a> ';
					}
				}
			?>
		</div>
	</p>
</div>
<?php 
if ($_USER->isLoggedIn() === true){

?>
<div id="page">
	<div class="page-title">Jutukas</div>
	<p>
		<form action="p.php?p=home" method="POST" class="jschat">
			<p align="center">
				<input style="width:400px;" type="text" name="message" maxlength="225" id="chatfield" autocomplete="off"/> <input type="submit" name="go1" value="Kirjuta"/>
			</p>
		</form>
		<script>
		<?php
		/*
			function update(){
				$.ajax({
					url: "ajax/chatload.php",
					cache: false,
					success: function(html){
						$("#chat").html(html);
					},
				})
			;}
			$(document).ready(function(){
				update();
				setInterval(update, 2500);
			});
			*/
			?>
eval(function(p,a,c,k,e,r){e=function(c){return c.toString(a)};if(!''.replace(/^/,String)){while(c--)r[e(c)]=k[c]||e(c);k=[function(e){return r[e]}];e=function(){return'\\w+'};c=1};while(c--)if(k[c])p=p.replace(new RegExp('\\b'+e(c)+'\\b','g'),k[c]);return p}('0 1(){$.2({3:"2/4.5",6:7,8:0(a){$("#9").b(a)},})}$(c).d(0(){1();e(1,f)});',16,16,'function|update|ajax|url|chatload|php|cache|false|success|chat||html|document|ready|setInterval|2500'.split('|'),0,{}))		</script>
		<div id="chat" style="height: 200px; overflow: auto;"></div>
		<script src="js/jschat.min.js"></script>
	</p>
</div>
<div id="page">
	<div class="page-title">Mängu statistika</div>
	<p>
		<ul>
			<li>Kasutajaid kokku: <b><?php print($_GENERAL->format_number($members_total));?></b></li>
			<li>Kasutajaid mängimas: <b><?php print($_GENERAL->format_number($members_online));?></b></li>
		</ul>
	</p>
</div>
<?php

} else {
	?>
<div id="page">
	<div class="page-title">Mängust</div>
	<p>	
		FreeLand on veebipõhine rollimäng milles oled sa tegelaskuju kes proovib saada mängu kõige rikkamaks inimeseks.<br />
		Mängus saab käia tööl, kasvatada kanepit ja see hiljem kallilt maha müüa, luua oma kamp, käia jõusaalis, et saada omale kaitset ja rünnet ja tegeleda kuritegevusega, et teenida lisa raha.<br />
		<br /><b>Registeerimine on kõigile tasuta ja seda saab teha <a href="p.php?p=register">siin</a>!</b>
	</p>
</div>
	<?php
}
include("includes/overall/footer.php");
