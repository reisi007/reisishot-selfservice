<?php
include_once "../header/json.php";
include_once "../utils/sql.php";

function db(string $sql)
{
    $email = $_GET['email'];
    $accessKey = $_GET['access_key'];

    $pdo = createMysqlConnection();

    $statement = $pdo->prepare($sql);
    $statement->bindParam("email", $email);
    $statement->bindParam("access_key", $accessKey);
    $statement->execute();
    $result = $statement->fetch(PDO::FETCH_ASSOC);
    echo json_encode($result, JSON_THROW_ON_ERROR);
}
