<?php
include_once "../header/required.php";
include_once '../utils/string.php';

header("Content-Type: text/plain");

$filename = $_GET['filename'];
if (str_contains($filename, "/") || str_contains($filename, "\\")) {
    throw new Exception("Filename must not contain unsafe characters");
}

$contractText = file_get_contents('../assets/contracts/' . $filename);

echo combineMd($contractText, "");
