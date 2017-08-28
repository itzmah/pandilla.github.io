<?php
/*
 * Name: FreeLand v2
 * Author: Marko Murumaa
 * Created: July 2015
 * Website: www.freelandplay.eu
*/

require_once("core/init.php");

$chapter_x = NULL;
$rule_x = NULL;
$line = NULL;

$rchapters = DB::getInstance(1)->query('SELECT * FROM rules_chapters');
foreach ($rchapters->results() as $rchapter) {
	$chapter_x++;
	$line_rules = NULL;
	$qrules = DB::getInstance(1)->get('rules', array('chapter_id','=',$rchapter->id));
	$rule_x = NULL;
	foreach ($qrules->results() as $rule) {
		$rule_x++;
		$line_rules .= '<li><b>'.$chapter_x.'.'.$rule_x.'</b> '.$rule->rule.'</li>';
	}
	$line .= '
		<ul>
			<li><b>'.$rchapter->name.'</b></li>
			'.$line_rules.'
		</ul>
		';
}

include("includes/overall/header.php");
?>
<div id="page">
	<div class="page-title">Reeglid</div>
	<p>
		<table>
			<tr valign="top">
				<td width="20%"><img src="css/default/images/rules.png" width="100" height="100"></td>
				<td width="80%">
					Reeglid on mängu tähtsaim osa. Kõik kasutajad on kohustatud neid reegleid järgima.<br>
					Kasutajad kes neid reegleid ei jargi saavad karistada.<br>
					Kui te rikute reegleid võib teie karistuseks saada konto blokeerimine.
				</td>
			</tr>
		</table>
		<?php print($line);?>
	</p>
</div>
<?php
include("includes/overall/footer.php");
