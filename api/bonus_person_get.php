<?php
include_once "../utils/sql.php";
include_once "../header/json.php";

$id = intval($_GET['id']);
$pin = intval($_GET['pin']);

$pdo = createMysqlConnection();

$statement = $pdo->prepare("
SELECT id,
       email,
       firstname AS 'firstName',
       lastname  AS 'lastName',
       birthday,
       tel,
       pin,
       sum AS 'total'
FROM bonuscard_summed wp WHERE id = :id AND pin = :pin
");

$statement->bindParam("id", $id);
$statement->bindParam("pin", $pin);

$statement->execute();
$result = $statement->fetch(PDO::FETCH_ASSOC);

if ($result === false) {
    header("HTTP/1.1 401 Unauthorized");
    return;
}


echo json_encode($result, JSON_THROW_ON_ERROR);
