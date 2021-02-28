<?php
include_once "../header/required.php";
include_once "../utils/security.php";
include_once "../utils/sql.php";
include_once "../utils/mail.php";
include_once "../utils/files.php";

$pdo = createMysqlConnection();
$pdo->beginTransaction();

$json = read_body_json();

$email = trim($_GET["email"]);
$access_key = trim($_GET["access_key"]);

$action = trim($json["action"]);
$base_url = trim($json["baseUrl"]);

$stmt = $pdo->prepare("
SELECT ca.contract_id, ci.hash_value
FROM contract_access ca
         JOIN contract_instances ci ON ca.contract_id = ci.id
WHERE email = :email
  AND access_key = :access_key
  AND CURRENT_TIMESTAMP <= ci.due_date
  AND NOT EXISTS(SELECT *
                 FROM contract_log cl
                 WHERE email = :email
                   AND ca.contract_id = cl.contract_id);
");
$stmt->bindParam("email", $email);
$stmt->bindParam("access_key", $access_key);
$stmt->execute();
$data = $stmt->fetch(PDO::FETCH_ASSOC);

if ($data === false)
    return;

$insert = $pdo->prepare("INSERT INTO contract_log(contract_id, email, log_type, hash_value) VALUES (:id,:email,:type,:hash)");
$insert->bindParam("id", $data["contract_id"]);
$insert->bindParam("email", $email);
$insert->bindParam("type", $action);
$insert->bindParam("hash", $data["hash_value"]);
$insert->execute();

$pdo->commit();

sendMail("contracts@reisishot.pictures", $email, $action . " - Aktion bei deinem Vertrag", "
<h1>Zugriff zu deinem Vertrag</h1>
 <p>
  Bitte benutze den folgenden Link, um zu deinem Vertrag zu kommen: <a href='$base_url/contracts/$email/$access_key'>Link zum Vertrag</a>
</p>
");
