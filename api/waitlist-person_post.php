<?php
include_once "../header/required.php";
include_once "../utils/sql.php";
include_once "../utils/files.php";

$pdo = createMysqlConnection();

$json = read_body_json();
$headers = getallheaders();

$email = trim($headers['Email']);
$firstname = trim($json["firstName"]);
$lastname = trim($json["lastName"]);
$birthday = trim($json["birthday"]);
$availability = trim($json["availability"]);
$phone_number = trim($json["phone_number"]);
$website = trim($json["website"]);
$accessKey = trim($headers['Accesskey']);

$statement = $pdo->prepare("
UPDATE waitlist_person
SET firstname=:firstname,
    lastname=:lastname,
    birthday=:birthday,
    availability =:availability,
    phone_number=:phone_number,
    website=:website
WHERE email = :email
  AND access_key = :access_key
   ");

$statement->bindParam("firstname", $firstname);
$statement->bindParam("lastname", $lastname);
$statement->bindParam("birthday", $birthday);
$statement->bindParam("availability", $availability);
$statement->bindParam("phone_number", $phone_number);
$statement->bindParam("website", $website);
$statement->bindParam("email", $email);
$statement->bindParam("access_key", $accessKey);

$statement->execute();

if ($statement->rowCount() != 1) {
    throw new Exception("Too much changed...");
}

$pdo->commit();
