<?php
include_once "../header/required.php";
include_once "../utils/security.php";
include_once "../utils/sql.php";
include_once "../utils/files.php";

$json = read_body_json();

$item_id = trim($json["itemId"]);
$secret = $_GET['access_key'];
$email = $_GET['email'];
$firstname = trim($json["firstName"]);
$lastname = trim($json["lastName"]);
$birthday = trim($json["birthday"]);
$availability = trim($json["availability"]);
$phone_number = trim($json["phone_number"]);
$website = trim($json["website"]);
$text = trim($json["text"]);

$pdo = createMysqlConnection();

$statement = $pdo->prepare("INSERT INTO waitlist_entry(item_id, secret, email, firstname, lastname, birthday, availability, phone_number, website, text) VALUES (:item_id, :secret, :email, :firstname, :lastname, :birthday, :availability, :phone_number, :website, :text)");

$statement->bindParam("item_id", $item_id);
$statement->bindParam("secret", $secret);
$statement->bindParam("email", $email);
$statement->bindParam("firstname", $firstname);
$statement->bindParam("lastname", $lastname);
$statement->bindParam("birthday", $birthday);
$statement->bindParam("availability", $availability);
$statement->bindParam("phone_number", $phone_number);
$statement->bindParam("website", $website);
$statement->bindParam("text", $text);

$statement->execute();

$pdo->commit();
