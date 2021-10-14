<?php
include_once "../header/required.php";
include_once "../utils/sql.php";
include_once "../utils/security.php";
include_once "../utils/files.php";

$json = read_body_json();
$headers = getallheaders();

$oldUser = strtolower(trim($headers["Email"]));
$oldPwd = trim($headers["Accesskey"]);

$newUser = strtolower(trim($json["user"]));
$newPwd = trim($json["pwd"]);

$connection = createMysqlConnection();
$connection->beginTransaction();

setPassword($connection, $oldUser, $oldPwd, $newUser, $newPwd);

$connection->commit();
