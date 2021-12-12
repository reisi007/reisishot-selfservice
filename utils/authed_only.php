<?php
include_once "sql.php";
include_once "security.php";

$headers = getallheaders();
$user = trim($headers['Email']);
$pwd = trim($headers['Accesskey']);

$pdo = createMysqlConnection();

// Check if user is allowed to insert
if (!checkUserInsert($pdo, $user, $pwd)) {
    throw new Exception("Wrong PWD");
}
