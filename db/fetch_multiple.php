<?php
include_once "../header/json.php";
include_once "../utils/sql.php";

/**
 * @throws JsonException
 */
function query(string $sql, \PDO $pdo = null)
{
    if ($pdo == null)
        $pdo = createMysqlConnection();
    $result = select($sql, $pdo);
    echo json_encode($result, JSON_THROW_ON_ERROR);
}

/**
 * @param string $sql
 * @param PDO $pdo
 * @return array|false
 */
function select(string $sql, \PDO $pdo)
{
    $statement = $pdo->query($sql);
    $statement->execute();
    return $statement->fetchAll(PDO::FETCH_ASSOC);
}
