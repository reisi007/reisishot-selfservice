<?php
include_once "../header/_cors.php";
include_once "sql.php";

$headers = getallheaders();
$user = trim($headers['Email']);
$pwd = trim($headers['Accesskey']);

$pdo = createMysqlConnection();
$pdo->beginTransaction();

$statement = $pdo->prepare("UPDATE permission_session SET last_used = CURRENT_TIMESTAMP WHERE user_id = :user AND hash = :hash");
$statement->bindParam("user", $user);
$statement->bindParam("hash", $pwd);
$statement->execute();

if ($statement->rowCount() !== 1) {
    header("HTTP/1.1 401 Unauthorized");
    $pdo->rollBack();
    exit();
}
$pdo->commit();
