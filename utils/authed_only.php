<?php
include_once "sql.php";

$headers = getallheaders();
$user = trim($headers['Email']);
$pwd = trim($headers['Accesskey']);

$pdo = createMysqlConnection();

// Check if user is allowed to insert
if (!checkUserInsert($pdo, $user, $pwd)) {
    throw new Exception("Wrong PWD");
}
