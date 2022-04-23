<?php
include_once "../header/required.php";
include_once "../utils/sql.php";
include_once "../utils/security.php";
include_once "../utils/uuid.php";

$headers = getallheaders();
$user = trim($headers['Email']);
$pwd = trim($headers['Accesskey']);

$pdo = createMysqlConnection();

// Check if user is known
if (!checkIsAdmin($pdo, $user, $pwd)) {
    header("HTTP/1.1 401 Unauthorized");
    exit();
}

$uuid = uuid();

$statement = $pdo->prepare("INSERT INTO permission_session(user_id, hash, last_used) VALUE (:user,:hash,CURRENT_TIMESTAMP)");
$statement->bindValue("user", $user);
$statement->bindValue("hash", $uuid);
$statement->execute();
$pdo->commit();


include_once "../header/json.php";
echo json_encode(array("user" => $user, "hash" => $uuid));
