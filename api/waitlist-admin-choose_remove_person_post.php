<?php

include_once "../header/required.php";
include_once "../utils/authed_only.php";
include_once "../utils/sql.php";
include_once "../utils/mail.php";
include_once "../utils/files.php";

$pdo = createMysqlConnection();
$pdo->beginTransaction();

$json = read_body_json();

$p = $json["personId"];
$f = $json["folder"];

$stmt = $pdo->prepare("DELETE FROM review_pictures_access WHERE person = :p AND folder = :f");

$stmt->bindParam("p", $p);
$stmt->bindParam("f", $f);

$stmt->execute();

$pdo->commit();


