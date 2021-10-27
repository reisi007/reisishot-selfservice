<?php
include_once "../utils/sql.php";
include_once "../header/required.php";
include_once "../utils/security.php";
include_once "../utils/authed_only.php";


/**
 * @throws JsonException
 */
function query(string $sql, \PDO $pdo = null)
{
    include_once "../header/json.php";
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
function select(string $sql, \PDO $pdo): array
{
    $statement = $pdo->query($sql);
    return $statement->fetchAll(PDO::FETCH_ASSOC);
}
