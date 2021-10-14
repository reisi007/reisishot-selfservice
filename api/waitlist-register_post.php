<?php
include_once "../header/json.php";
include_once "../utils/sql.php";
include_once "../utils/mail.php";
include_once "../utils/files.php";

$json = read_body_json();

$email = trim($json['email']);
$firstname = trim($json["firstName"]);
$lastname = trim($json["lastName"]);
$birthday = trim($json["birthday"]);
$availability = trim($json["availability"]);
$phone_number = trim($json["phone_number"]);
$website = trim($json["website"]);


$pdo = createMysqlConnection();

$access_key = uuid($pdo);

$statement = $pdo->prepare("INSERT INTO waitlist_person(email, firstname, lastname, birthday, availability, phone_number, website, access_key) VALUES (:email,:firstname,:lastname,:birthday,:availability,:phone_number,:website,:access_key)");
$statement->bindParam("email", $email);
$statement->bindParam("firstname", $firstname);
$statement->bindParam("lastname", $lastname);
$statement->bindParam("birthday", $birthday);
$statement->bindParam("availability", $availability);
$statement->bindParam("phone_number", $phone_number);
$statement->bindParam("website", $website);
$statement->bindParam("access_key", $access_key);

$statement->execute();

if ($statement->rowCount() != 1)
    throw new Exception("Could not insert new person");
$pdo->commit();

$url = "https://service.reisishot.pictures/waitlist/$email/$access_key";
sendMail("waitlist@reisishot.pictures", $email, "Zugang zur Warteliste", "
<h1>Zugang zur Warteliste</h1>
<p><a='$url'>Bitte klicke hier, um dich für Shootings anzumelden</a></p>
$url
");
