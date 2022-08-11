<?php

include_once "../header/required.php";
include_once "../utils/authed.php";


$folder = $_GET['folder'];
$file = $_GET['file'];

if (str_contains($file, "..")) {
    exit("Illegal filename...");
}

$isAuthed = isAuthed();

$pdo = createMysqlConnection();


if (!$isAuthed) {
    $headers = getallheaders();
    $user = trim($_GET['email']);
    $pwd = trim($_GET['accesskey']);

    $stmt = $pdo->prepare("
SELECT COUNT(*)
FROM review_pictures_access rpa
         JOIN waitlist_person wp ON wp.id = rpa.person
WHERE email = :email
  AND access_key = :accessKey
AND folder = :folder
  ");

    $stmt->bindParam("email", $user);
    $stmt->bindParam("accessKey", $pwd);
    $stmt->bindParam("folder", $folder);

    $isAuthed = $stmt->fetchColumn() === "1";

}

if (!$isAuthed) {
    throw new Exception("User is not authed");
}

if (str_contains($file, "json")) {
    header("Content-Type: application/json");
} else {
    header("Content-Type: image/jpeg");
}

readfile("../../.choose/$folder/$file");
