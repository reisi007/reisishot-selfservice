<?php
include_once "../header/required.php";
include_once "../utils/authed_only.php";
include_once "../utils/sql.php";
include_once "../utils/files.php";

$json = read_body_json();
$headers = getallheaders();

$itemId = trim($json["itemId"]);
$personId = trim($json["personId"]);
$value = $json["value"] ? 1 : 0;


$pdo = createMysqlConnection();
$pdo->beginTransaction();

$statement = $pdo->prepare("UPDATE waitlist_entry SET date_assigned = :value WHERE item_id = :itemId AND person = :personId");
$statement->bindParam("itemId", $itemId);
$statement->bindParam("personId", $personId);
$statement->bindParam("value", $value, PDO::PARAM_INT);
$statement->execute();

if ($statement->rowCount() != 1) {
    throw new Exception("Statement did not affect one row...");
}

$pdo->commit();
