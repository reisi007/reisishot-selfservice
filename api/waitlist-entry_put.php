<?php
include_once "../header/required.php";
include_once "../utils/security.php";
include_once "../utils/sql.php";
include_once "../utils/mail.php";
include_once "../utils/files.php";

$json = read_body_json();
$headers = getallheaders();

$item_id = trim($json["itemId"]);
$secret = trim($headers['Accesskey']);
$email = trim($headers['Email']);
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

// Check if valid
$check = $pdo->prepare("
SELECT COUNT(*)AS cnt, title, max_waiting
FROM waitlist_item wi
         JOIN waitlist_entry we ON wi.id = we.item_id
WHERE wi.id = :item_id
GROUP BY id, max_waiting,title
");

$check->bindParam("item_id", $item_id);
$check->execute();
$result = $check->fetch(PDO::FETCH_ASSOC);

$cnt = $result["cnt"];
$maxWaiting = $result["max_waiting"];
$title = $result["title"];

if ($cnt > $maxWaiting)
    throw new Exception("Too many registrations...");

$pdo->commit();

sendMail("waitlist@reisishot.pictures", "florian@reisishot.pictures", "Neue Registrierung für $title",
    "
    <h1>Neue Registrierung für $title</h1>
    <p>$firstname $lastname</p>
    <p>$birthday</p>
    <p>$website</p>
    <p>$availability</p>
    <p><b>$text</b></p>
    ");
