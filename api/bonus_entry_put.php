<?php
include_once '../header/required.php';
include_once '../utils/authed_only.php';
include_once '../utils/files.php';
include_once '../utils/sql.php';

$json = read_body_json();

$id = trim($json['id']);
$text = trim($json['text']);
$value = intval(trim($json['value']));
$expireAt = trim($json['expireAt']);

$pdo = createMysqlConnection();
$pdo->beginTransaction();

$insert = $pdo->prepare("INSERT INTO bonuscard_entries_raw(bonus, text, value, expire_at) VALUE (:id,:text,:value,:expireAt)");

$insert->bindParam('id', $id);
$insert->bindParam('text', $text);
$insert->bindParam('value', $value);
$insert->bindParam('expireAt', $expireAt);

$insert->execute();

$pdo->commit();
