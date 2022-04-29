<?php
include_once "../header/required.php";
include_once "../utils/files.php";
include_once "../utils/authed_only.php";

$pdo = createMysqlConnection();
$pdo->beginTransaction();

$json = read_body_json();

$itemId = $json["itemId"];
$isMinor = $json["isMinor"];
$isGroup = $json["isGroup"];

$statement = $pdo->prepare("INSERT INTO shooting_statistics(item_id, isminor, isgroup) VALUES (:item,:minor,:group)");
$statement->bindParam("item", $itemId);
$statement->bindParam("minor", $isMinor);
$statement->bindParam("group", $isGroup);
$success = $statement->execute();

if (!$success) {
    throw new Exception("Could not store shooting for $itemId (minor=$isMinor, group=$isGroup)");
}

$pdo->commit();
