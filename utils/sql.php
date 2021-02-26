<?php
include_once '../config/sql.conf.php';

function createMysqlConnection(): PDO
{
    $pdo = new \PDO('mysql:host=' . mysql_host . ';port=' . mysql_port . ';dbname=' . mysql_db, mysql_user, mysql_pwd);
    $pdo->setAttribute(PDO::ATTR_AUTOCOMMIT, 0);
    return $pdo;
}

function uuid(PDO $pdo)
{
    $stmt = $pdo->query("SELECT UUID()");
    return $stmt->fetchColumn();
}
