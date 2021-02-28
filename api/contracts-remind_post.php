<?php
include_once "../header/required.php";
include_once "../utils/sql.php";
include_once "../utils/mail.php";
include_once "../utils/files.php";

$json = read_body_json();

$to = trim($json["email"]);
$base_url = trim($json["baseUrl"]);

$pdo = createMysqlConnection();
$statement = $pdo->prepare("
SELECT access_key
FROM contract_access ca
         JOIN contract_instances ci ON ci.id = ca.contract_id
WHERE email = :email
ORDER BY due_date DESC
");
$statement->bindParam("email", $to);
$res = $statement->execute();

$body = "
<h1>Zugriff zu deinen Verträgen</h1>
 <p>
  Bitte benutze einen der folgenden Links, um zu deinem Vertrag zu kommen:
</p><ul>";
if ($res !== false) {
    $idx = 0;
    foreach ($statement->fetch(PDO::FETCH_ASSOC) as $accessKey) {
        $idx++;
        $body .= "<li><a href='$base_url/contracts/$to/$accessKey'>Vertrag $idx</a> </li>";
    }
}

$body .= "</ul>";
sendMail(
    "contracts@reisishot.pictures",
    $to,
    "Zugriff zu deinen Verträgen",
    $body
);
