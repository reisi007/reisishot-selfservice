<?php
include_once "../header/json.php";
include_once "../utils/sql.php";

$email = $_GET['email'];
$accessKey = $_GET['access_key'];

$sql = "
SELECT access_key, email, firstname, lastname, birthday,  markdown, hash_algo, hash_value, due_date
FROM contract_access ca
     JOIN contract_instances ci ON ca.contract_id = ci.contract_id
         JOIN contract_data cd ON ca.contract_id = cd.id
WHERE email = :email
  AND access_key = :access_key
";

$pdo = createMysqlConnection();

$statement = $pdo->prepare($sql);
$statement->bindParam("email", $email);
$statement->bindParam("access_key", $accessKey);
$statement->execute();
$result = $statement->fetch(PDO::FETCH_ASSOC);
echo json_encode($result, JSON_THROW_ON_ERROR);
