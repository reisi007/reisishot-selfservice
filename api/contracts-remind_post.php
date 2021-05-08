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
SELECT email, access_key, cta.contract_id, due_date, signed_self, cnt_persons = cnt_signatures AS signed_all
FROM (SELECT access_key,
             ca.email,
             ca.contract_id
      FROM contract_access ca
      WHERE ca.email = :email) cta
         JOIN
     (SELECT '1' AS signed_self, cl.contract_id
      FROM contract_log cl
      WHERE cl.email = :email
        AND log_type = 'SIGN') ctl ON cta.contract_id = ctl.contract_id
         JOIN (
    SELECT COUNT(*) AS cnt_persons, ca.contract_id FROM contract_access ca GROUP BY ca.contract_id
) cnt_persons ON cta.contract_id = cnt_persons.contract_id
         JOIN (SELECT COUNT(*) AS cnt_signatures, cl.contract_id
               FROM contract_log cl
               WHERE log_type = 'SIGN'
               GROUP BY cl.contract_id) cnt_signers ON cta.contract_id = cnt_signers.contract_id
         JOIN contract_instances ci ON ci.id = cta.contract_id
ORDER BY ci.due_date DESC
");
$statement->bindParam("email", $to);
$res = $statement->execute();

function yesNoHtml(string $zeroOrOne)
{
    if ($zeroOrOne == '1')
        return '<span style="color: #27ae60">Ja</span>';
    else
        return '<span style="color: #c20114">Nein</span>';
}

$body = "
<h1>Zugriff zu deinen Verträgen</h1>
 <p>
  Bitte benutze einen der folgenden Links, um zu deinem Vertrag zu kommen:
</p>
<table border='1'>
<tr>
<th>Vertrag</th><th>Zu unterschreiben bis</th><th>Selbst unterschrieben</th><th>Alle unterschrieben</th>
</tr>";
if ($res !== false) {
    $idx = 0;
    while (($data = $statement->fetch(PDO::FETCH_ASSOC)) !== false) {
        $idx++;
        $body .= '<tr>' .
            "<td><a href='$base_url/contracts/$to/" .
            $data['access_key'] .
            "'>Vertrag $idx ansehen</a></td><td>" .
            $data['due_date'] . '</td><td>' .
            yesNoHtml($data['signed_self']) .
            '</td><td>' .
            yesNoHtml($data['signed_all']) .
            '</td>' .
            '</tr>';
    }
}

$body .= "</table>";
sendMail(
    "contracts@reisishot.pictures",
    $to,
    "Zugriff zu deinen Verträgen",
    $body
);


