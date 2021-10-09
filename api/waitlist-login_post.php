<?php
include_once "../header/json.php";
include_once "../utils/sql.php";
include_once "../utils/mail.php";
include_once "../utils/files.php";

$json = read_body_json();
$email = trim($json['email']);

$pdo = createMysqlConnection();

$access_key = uuid($pdo);

$statement = $pdo->prepare("UPDATE waitlist_person SET access_key = :access_key WHERE email = :email");
$statement->bindParam("email", $email);
$statement->bindParam("access_key", $access_key);

$statement->execute();

if ($statement->rowCount() != 1)
    throw new Exception("Could not insert new person");

$pdo->commit();
sendMail("waitlist@reisishot.pictures", $email, "Zugang zur Warteliste", "
<h1>Zugang zur Warteliste</h1>
<p><a='https://service.reisishot.pictures/waitlist/$email/$access_key'>Bitte klicke hier, um dich für Shootings anzumelden</a></p>
");
