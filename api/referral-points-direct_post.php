<?php
include_once "../header/required.php";
include_once "../utils/security.php";
include_once "../utils/files.php";
include_once "../utils/sql.php";
include_once "../feature/referral/index.php";

$pdo = createMysqlConnection();
$pdo->beginTransaction();

$headers = getallheaders();

$email = strtolower(trim($headers["Email"]));
$access_key = trim($headers["Accesskey"]);

$json = read_body_json();

$person = trim($json['email']);
$action = trim($json['action']);

if (!checkUserInsert($pdo, $email, $access_key))
    throw new Exception("Invalid username / PWD");

addReferralPointsDirect($pdo, $person, $action);

$pdo->commit();
