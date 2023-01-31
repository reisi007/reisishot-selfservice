<?php
include_once '../utils/sql.php';
include_once '../header/json.php';

$id = intval($_GET['id']);
$pin = intval($_GET['pin']);

$pdo = createMysqlConnection();

$result = array();

$unused = $pdo->prepare("SELECT ber.id AS `rawId`, text, value, expire_at AS expireat
FROM bonuscard_entries_raw ber
         JOIN bonuscard b ON b.id = ber.bonus
WHERE b.id = :id
  AND b.pin = :pin
  AND ber.used = 0
ORDER BY expire_at ");

$unused->bindParam("id", $id);
$unused->bindParam("pin", $pin);

$unused->execute();

$result["unused"] = $unused->fetchAll(PDO::FETCH_ASSOC);


$used = $pdo->prepare('SELECT ber.id AS rawid, text, value, expire_at AS expireat
FROM bonuscard_entries_raw ber
         JOIN bonuscard b ON b.id = ber.bonus
WHERE b.id = :id
  AND b.pin = :pin
  AND ber.used = 1
ORDER BY expire_at ');


$used->bindParam('id', $id);
$used->bindParam('pin', $pin);

$used->execute();

$result['used'] = $used->fetchAll(PDO::FETCH_ASSOC);


echo json_encode($result, JSON_THROW_ON_ERROR);
