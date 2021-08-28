<?php
include_once "../header/required.php";
include_once "../utils/security.php";
include_once "../utils/sql.php";

$pdo = createMysqlConnection();
$pdo->beginTransaction();

$headers = getallheaders();

$email = strtolower(trim($headers["Email"]));
$access_key = trim($headers["Accesskey"]);

if (!checkUserInsert($pdo, $email, $access_key))
    throw new Exception("Wrong PWD");

$id = $_GET['id'];

$statement = $pdo->prepare("
UPDATE waitlist_entry
SET done_internal = 1
WHERE item_id = :id
");

$statement->bindParam("id", $id);
$statement->execute();

if ($statement->rowCount() != 1) {
    throw new Exception("Wrong numbe of rows changed");
}

$pdo->commit();
