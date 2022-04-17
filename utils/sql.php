<?php
include_once '../config/sql.conf.php';

function createMysqlConnection(): PDO
{
    $pdo = new \PDO('mysql:host=' . mysql_host . ';port=' . mysql_port . ';dbname=' . mysql_db . ';charset=utf8', mysql_user, mysql_pwd);
    $pdo->setAttribute(PDO::ATTR_AUTOCOMMIT, 0);
    $pdo->setAttribute(PDO::ATTR_EMULATE_PREPARES, false);
    return $pdo;
}
