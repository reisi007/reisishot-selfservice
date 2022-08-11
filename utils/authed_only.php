<?php
include_once "../header/_cors.php";
include_once "authed.php";

$isAuthed = isAuthed();
if (!$isAuthed) {
    exit("Not logged in...");
}
