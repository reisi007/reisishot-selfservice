<?php
include_once "../db/fetch_multiple.php";
include_once "../utils/security.php";

$connection = createMysqlConnection();

$headers = getallheaders();
$user = strtolower(trim($headers["Email"]));
$pwd = trim($headers["Accesskey"]);

if (!checkUserInsert($connection, $user, $pwd))
    throw new Exception("Wrong PWD");

query("
SELECT DISTINCT firstname AS 'firstName', lastname AS 'lastName', birthday, email
FROM contract_access
ORDER BY lastname, firstname, birthday, email
");
