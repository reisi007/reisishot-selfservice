<?php
include_once "../header/json.php";
include_once "../utils/sql.php";
include_once "../utils/mail.php";
include_once "../utils/files.php";
include_once "../utils/uuid.php";
include_once "../feature/referral/index.php";

$json = read_body_json();

$referrer = isset($json['referrer']) ? trim($json['referrer']) : null;
$email = trim($json['email']);

$pdo = createMysqlConnection();
$pdo->beginTransaction();

$access_key = uuid();

$statement = $pdo->prepare("UPDATE waitlist_person SET access_key = :access_key WHERE email = :email");
$statement->bindParam("email", $email);
$statement->bindParam("access_key", $access_key);

$statement->execute();

if ($statement->rowCount() != 1)
    throw new Exception("Could not insert new person");


/** @noinspection PhpUnhandledExceptionInspection */
setReferral($pdo, $referrer, $email);

$pdo->commit();

$url = "https://service.reisishot.pictures/waitlist/$email/$access_key";

sendMail("waitlist@reisishot.pictures", $email, null, "Zugang zur Warteliste", "
<h1>Zugang zur Warteliste</h1>
<p><a='$url'>Bitte klicke hier, um dich für Shootings anzumelden</a></p>
");
