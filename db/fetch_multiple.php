<?php
include_once "../header/json.php";
include_once "../utils/sql.php";

function db(string $sql)
{
    $pdo = createMysqlConnection();
    $statement = $pdo->query($sql);
    $statement->execute();
    $result = $statement->fetchAll(PDO::FETCH_ASSOC);
    echo json_encode($result, JSON_THROW_ON_ERROR);
}
