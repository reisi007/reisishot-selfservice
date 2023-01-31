<?php
include_once '../header/required.php';
include_once '../utils/authed_only.php';
include_once '../utils/files.php';
include_once '../utils/sql.php';

$json = read_body_json();

$id = strtoupper(trim($json['rawId']));

$pdo = createMysqlConnection();
$pdo->beginTransaction();

$insert = $pdo->prepare("
UPDATE bonuscard_entries_raw
SET used = 1
WHERE id = :id AND used = 0
");

$insert->bindParam('id', $id);

$insert->execute();

if ($insert->rowCount() !== 1) {
    exit("More than one or zero entires changed");
}

$pdo->commit();
