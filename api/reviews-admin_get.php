<?php
include_once "../header/json.php";
include_once "../utils/sql.php";
include_once "../db/fetch_multiple.php";
include_once "../utils/security.php";


$headers = getallheaders();

$user = trim($headers["Email"]);
$pwd = trim($headers["Accesskey"]);

$pdo = createMysqlConnection();

// Check if user is allowed to insert
if (!checkUserInsert($pdo, $user, $pwd)) {
    throw new Exception("Wrong PWD $user $pwd");
}

query("
SELECT access_key, email, rating, name, creation_date, review_private, review_public
FROM reviews
ORDER BY creation_date DESC
");

