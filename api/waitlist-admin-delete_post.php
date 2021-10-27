<?php
include_once "../header/required.php";
include_once "../utils/security.php";
include_once "../utils/sql.php";
include_once "../utils/files.php";
include_once "../utils/authed_only.php";

$json = read_body_json();

$person = trim($json['person']);
$itemId = trim($json['item']);

$pdo = createMysqlConnection();
$pdo->beginTransaction();

$statement = $pdo->prepare("
DELETE
FROM waitlist_entry
WHERE item_id = :id AND person = :person
");

$statement->bindParam("id", $itemId);
$statement->bindParam("person", $person);
$statement->execute();

if ($statement->rowCount() != 1) {
    throw new Exception("Wrong number of rows changed {$statement->rowCount()} - $id - $person");
}

$pdo->commit();
