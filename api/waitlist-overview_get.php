<?php
include_once "../header/json.php";
include_once "../utils/sql.php";
include_once "../db/fetch_multiple_secured.php";

$pdo = createMysqlConnection();

$headers = getallheaders();
$email = trim($headers['Email']);
$accessKey = trim($headers['Accesskey']);

$user = $pdo->prepare("SELECT  id FROM  waitlist_person WHERE email = :email
          AND access_key = :access_key");

$user->bindParam("email", $email);
$user->bindParam("access_key", $accessKey);
$user->execute();

$person = $user->fetchColumn();

if ($person === false) {
    throw new Exception("Person not found");
}

query("
SELECT id,
       short,
       image_id,
       title,
       description,
       available_from,
       available_to,
       IF(
               ISNULL(max_waiting),
               NULL,
               (SELECT max_waiting - COUNT(*) FROM waitlist_entry WHERE item_id = id)) AS max_waiting,
       (SELECT COUNT(*) > 0
        FROM waitlist_entry
        WHERE item_id = id
          AND person = (SELECT id
                        FROM waitlist_person
                        WHERE email = :email
                          AND access_key = :access_key))                               AS registered
FROM waitlist_item wi
WHERE available_from <= CURRENT_DATE
  AND (available_to IS NULL OR available_to >= CURRENT_DATE)
ORDER BY sort_index
");
