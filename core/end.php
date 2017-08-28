<?php
/*
 * Name: FreeLand v2
 * Author: Marko Murumaa
 * Created: July 2015
 * Website: www.freelandplay.eu
*/

$_USER = null;

$_GENERAL = null;

$end_time = microtime();
$end_time = explode(' ', $end_time);
$end_time = $end_time[1] + $end_time[0];
$end_finish = $end_time;
$end_total_time = round(($end_finish - $start_start), 4);
echo 'Leht laeti '.$end_total_time.' sekundiga.';
