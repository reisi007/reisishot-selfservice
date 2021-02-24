<?php
include_once "../header/required.php";
include_once "../utils/security.php";
include_once "../utils/sql.php";
include_once "../utils/mail.php";
include_once "../utils/files.php";

$pdo = createMysqlConnection();

$json = read_body_json();

$email = $_GET["email"];
$access_key = $_GET["access_key"];

$action = $json["action"];
$base_url = $json["baseUrl"];

$stmt = $pdo->prepare("
SELECT contract_id, hash_value
FROM contract_access ca
         JOIN contract_data cd ON cd.id = ca.contract_id
WHERE email = :email
  AND access_key = :access_key

");
$stmt->bindParam("email", $email);
$stmt->bindParam("access_key", $access_key);
$stmt->execute();
$data = $stmt->fetch(PDO::FETCH_ASSOC);

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
