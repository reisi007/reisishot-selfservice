<?php
include_once "sql.php";
include_once "security.php";

$headers = getallheaders();
$user = trim($headers['Email']);
$pwd = trim($headers['Accesskey']);

$pdo = createMysqlConnection();

$statement = $pdo->prepare("UPDATE permission_session SET last_used = CURRENT_TIMESTAMP WHERE user_id = :user AND hash = :hash");
$statement->bindParam("user", $user);
$statement->bindParam("hash", $pwd);
$statement->execute();
if ($statement->rowCount() !== 1) {
    throw new Exception("Access not allowed");
}
