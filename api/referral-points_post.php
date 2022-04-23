<?php
include_once "../header/required.php";
include_once "../utils/authed_only.php";
include_once "../utils/files.php";
include_once "../feature/referral/index.php";

$pdo = createMysqlConnection();
$pdo->beginTransaction();

$json = read_body_json();

$person = trim($json['email']);
$action = trim($json['action']);

addReferralPoints($pdo, $person, $action);

$pdo->commit();
