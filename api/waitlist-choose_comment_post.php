<?php

include_once "../header/required.php";
include_once "../db/fetch_single_secured.php";
include_once "../utils/files.php";

$pdo = createMysqlConnection();
$pdo->beginTransaction();

$json = read_body_json();
$headers = getallheaders();

$email = strtolower(trim($headers["Email"]));
$access_key = trim($headers["Accesskey"]);

$folder = trim($json["folder"]);
$image = trim($json["image"]);
$comment = $json["data"];

$person = select("SELECT id FROM waitlist_person WHERE email = :email AND  access_key = :access_key", $pdo);

$id = $person["id"];

$insert = $pdo->prepare("REPLACE INTO review_pictures_comment(person, folder, filename, comment) VALUES(:p,:fo,:fi,:c) ");
$insert->bindParam("p", $id);
$insert->bindParam("fo", $folder);
$insert->bindParam("fi", $image);
$insert->bindParam("c", $comment);

$insert->execute();

if ($insert->rowCount() != 1) {
    throw new Exception('Could not insert/replace rating');
}

$pdo->commit();
