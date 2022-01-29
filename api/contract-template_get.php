<?php
include_once "../header/required.php";

header("Content-Type: text/plain");

$filename = $_GET['filename'];
if (str_contains($filename, "/") || str_contains($filename, "\\")) {
    throw new Exception("Filename must not contain unsafe characters");
}

readfile('../assets/contracts/' . $filename);
