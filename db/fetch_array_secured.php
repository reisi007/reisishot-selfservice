<?php
include_once "fetch_array_secured.php";

/**
 * @throws JsonException
 */
function query(array $sqls, \PDO $pdo = null)
{
    include_once "../header/json.php";
    $results = array();
    if ($pdo == null)
        $pdo = createMysqlConnection();
    foreach ($sqls as $name => $sql) {
        $results[$name] = select($sql, $pdo);
    }

    echo json_encode($results, JSON_THROW_ON_ERROR);
}
