<?php
include_once "../utils/sql.php";
include_once "../header/required.php";

/**
 * @throws JsonException
 */
function query(string $sql, \PDO $pdo = null)
{
    include_once "../header/json.php";
    if ($pdo == null) {
        $pdo = createMysqlConnection();
    }
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
    $headers = getallheaders();
    $email = trim($headers['Email']);
    $accessKey = trim($headers['Accesskey']);

    $statement = $pdo->prepare($sql);
    $statement->bindParam("email", $email);
    $statement->bindParam("access_key", $accessKey);
    $statement->execute();
    return $statement->fetchAll(PDO::FETCH_ASSOC);
}
