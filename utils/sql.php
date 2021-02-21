<?php
include_once '../config/sql.conf.php';

function createMysqlConnection(): mysqli
{
    $connection = new mysqli(mysql_host, mysql_user, mysql_pwd, mysql_db, mysql_port);
    if ($connection->connect_error) {
        die("DB failed: " . $connection->connect_error);
    }
    $connection->autocommit(false);
    return $connection;
}
