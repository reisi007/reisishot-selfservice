<?php
include_once "../header/required.php";

header("Content-Type: text/plain");

readfile('../assets/contracts/' . $_GET['filename']);
