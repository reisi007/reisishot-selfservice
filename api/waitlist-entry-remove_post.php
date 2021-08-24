<?php
include_once "../header/required.php";
include_once "../utils/security.php";
include_once "../utils/sql.php";
include_once "../utils/files.php";

$json = read_body_json();

$itemId = trim($json["item_id"]);
$email = $_GET['email'];
$secret = $_GET['access_key'];

$pdo = createMysqlConnection();
$pdo->beginTransaction();

$statement = $pdo->prepare("DELETE FROM waitlist_entry WHERE item_id = :itemId AND email = :email AND secret = :access_key");
$statement->bindParam("email", $email);
$statement->bindParam("access_key", $secret);
$statement->bindParam("itemId", $itemId);
$statement->execute();

if ($statement->rowCount() != 1)
    throw new Exception("Statement did not affect one row...");

$pdo->commit();
