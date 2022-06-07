<?php
include_once "../header/required.php";
include_once "../utils/authed_only.php";
include_once "../utils/files.php";
include_once "../feature/referral/index.php";


$pdo = createMysqlConnection();
$pdo->beginTransaction();

$headers = getallheaders();

$email = strtolower(trim($headers["Email"]));
$access_key = trim($headers["Accesskey"]);

$json = read_body_json();

$person = trim($json['email']);
$referrerAction = trim($json['referrerAction']);
$directAction = trim($json['directAction']);

if ($directAction) {
    addReferralPointsDirect($pdo, $person, $directAction);
}
if ($referrerAction) {
    addReferralPoints($pdo, $person, $directAction);
}

$pdo->commit();
