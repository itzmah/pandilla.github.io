<?php
/*
 * Name: FreeLand v2
 * Author: Marko Murumaa
 * Created: July 2015
 * Website: www.freelandplay.eu
*/

$_lir = null;
$_linr = null;
if (isset($_GET['p']) === true) {
	$file = 'm_' . $_GET['p'] . '.php';
	if (file_exists($file) === true) {
		include($file);
	} else if ($_GET['p'] === "home") {
		include('index.php');
	} else {
		include("index.php");
	}
} else {
	include('index.php');
}
