<?php
include_once "../header/required.php";
include_once "../utils/security.php";
include_once "../utils/sql.php";
include_once "../utils/mail.php";
include_once "../utils/files.php";

$json = read_body_json();
$headers = getallheaders();

$email = trim($headers['Email']);
$access_key = trim($headers['Accesskey']);

$item_id = trim($json["item_id"]);
$text = trim($json["text"]);

$pdo = createMysqlConnection();

$check = $pdo->prepare("SELECT id, firstname, lastname, birthday, availability, phone_number, website FROM waitlist_person WHERE email = :email AND access_key = :access_key");
$check->bindParam("email", $email);
$check->bindParam("access_key", $access_key);
$check->execute();

$person = $check->fetch(PDO::FETCH_ASSOC);

if ($person === false) {
    throw new Exception("Person not found!");
}

$personId = $person["id"];
$statement = $pdo->prepare("INSERT INTO waitlist_entry(item_id, person, text) VALUES (:item_id, :person, :text)");

$statement->bindParam("item_id", $item_id);
$statement->bindParam("person", $personId);
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

if ($cnt > $maxWaiting) {
    throw new Exception("Too many registrations...");
}

$pdo->commit();

$firstname = $person['firstname'];
$lastname = $person['lastname'];
$birthday = $person['birthday'];
$website = $person['website'];
$availability = $person['availability'];
$phone = $person['phone_number'];

sendMail("waitlist@reisishot.pictures", "florian@reisishot.pictures", null, "[NEU] $firstname $lastname - $title",
    "
    <h1>Neue Registrierung für $title</h1>
    <p>$firstname $lastname</p>
    <p>$birthday</p>
    <p>$website</p>
    <p>$availability</p>
    <p>$phone</p>
    <p><b>$text</b></p>
    ");
