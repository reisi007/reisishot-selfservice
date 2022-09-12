<?php
include_once "../header/required.php";
include_once "../utils/security.php";
include_once "../utils/sql.php";
include_once "../utils/mail.php";
include_once "../utils/files.php";

$pdo = createMysqlConnection();
$pdo->beginTransaction();

$json = read_body_json();
$headers = getallheaders();

$email = strtolower(trim($headers["Email"]));
$access_key = trim($headers["Accesskey"]);

$action = strtoupper(trim($json["action"]));
$base_url = trim($json["baseUrl"]);

$stmt = $pdo->prepare("
SELECT ca.contract_id, ci.hash_value
FROM contract_access ca
         JOIN contract_instances ci ON ca.contract_id = ci.id
WHERE email = :email
  AND access_key = :access_key
  AND CURRENT_TIMESTAMP <= ci.due_date
  AND NOT EXISTS(
        SELECT log_type
        FROM contract_log cl
        WHERE cl.email = :email
          AND ca.contract_id = cl.contract_id
          AND log_type = 'SIGN'
    )
");
$stmt->bindParam("email", $email);
$stmt->bindParam("access_key", $access_key);
$stmt->execute();
$data = $stmt->fetch(PDO::FETCH_ASSOC);

if ($data === false) {
    return;
}
$hash_value = $data["hash_value"];


$insert = $pdo->prepare("INSERT INTO contract_log(contract_id, email, log_type, hash_value) VALUES (:id,:email,:type,:hash)");
$insert->bindParam("id", $data["contract_id"]);
$insert->bindParam("email", $email);
$insert->bindParam("type", $action);

$insert->bindParam("hash", $hash_value);
$insert->execute();

$pdo->commit();

sendMail("contracts@reisinger.pictures", $email, null, $action . " - Aktion bei deinem Vertrag", "
<h1>Zugriff zu deinem Vertrag</h1>
 <p>
  Bitte benutze den folgenden Link, um zu deinem Vertrag zu kommen: <a href='$base_url'>Link zum Vertrag</a>
</p>
<p>
Hash: <span style='overflow-x: auto'>$hash_value</span>
</p>
");
