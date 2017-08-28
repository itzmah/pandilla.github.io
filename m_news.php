<?php
/*
 * Name: FreeLand v2
 * Author: Marko Murumaa
 * Created: July 2015
 * Website: www.freelandplay.eu
*/

require_once("core/init.php");

if (Input::exists('get') && empty(Input::get('id')) === false) {
	$newsq = DB::getInstance(1)->get('news', array('id','=',Input::get('id')));
	if (!$newsq->count()) {
		Redirect::to('p.php?p=home');
	}
	$news_user_id = $newsq->first()->user_id;
	$news_subject = $newsq->first()->subject;
}

include("includes/overall/header.php");
?>

<div id="page">
	<div class="page-title"><?php print($news_subject);?></div>
	<p>
		<table>
			<?php
			
			if ($_USER->isLoggedIn()) {
				$newsqu = DB::getInstance(1)->get('users', array('id','=',$news_user_id));
				$news_postedby = $newsqu->first()->username;
				$newsq = DB::getInstance(1)->get('news', array('id','=',Input::get('id')));
				print('
					<td width="25%">
						<div align="center">
						Postitas: <a href="p.php?p=profile&user='.$news_postedby.'">'.$news_postedby.'</a><br/><br/>
						<b>'.$newsq->first()->date.'</b><br/>
						</div>
					</td>
					<td width="75%" valign="top" style="border-left: 1px solid #dddddd;">'.nl2br($newsq->first()->body).'</td>
					');
			} else {
				$newsqu = DB::getInstance(1)->get('users', array('id','=',$news_user_id));
				$news_postedby = $newsqu->first()->username;
				$newsq = DB::getInstance(1)->get('news', array('id','=',Input::get('id')));
				print('
					<td width="25%">
						<div align="center">
						Postitas: '.$news_postedby.'<br/><br/>
						<b>'.$newsq->first()->date.'</b><br/>
						</div>
					</td>
					<td width="75%" valign="top" style="border-left: 1px solid #dddddd;">'.nl2br($newsq->first()->body).'</td>
					');
			}
			?>
		</table>
	</p>
</div>

<?php
include("includes/overall/footer.php");
