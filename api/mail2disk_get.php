<?php
include_once '../header/required.php';
header('Content-Type: text/plain');

$path = '../../mailoutput/';
$files = scandir($path, SCANDIR_SORT_DESCENDING);
$newest_file = $files[0];

echo file_get_contents($path . $newest_file);
