<?php
include_once "../utils/authed_only.php";
include_once "../header/json.php";
include_once "../db/fetch_multiple_authed.php";

$headers = getallheaders();
$pdo = createMysqlConnection();

$item_result = $pdo->query("
SELECT id,
       short,
       image_id,
       title,
       description,
       available_from,
       available_to,
       max_waiting,
       sort_index
FROM waitlist_item
ORDER BY sort_index
");

$items = $item_result->fetchAll(PDO::FETCH_ASSOC);

$entries_statement = $pdo->prepare("
SELECT item_id,
       wp.id                  AS 'person_id',
       text,
       email,
       firstname              AS 'firstName',
       lastname               AS 'lastName',
       birthday,
       availability,
       phone_number,
       website,
       access_key,
       COALESCE(rp.points, 0) AS points,
       date_assigned,
       NOT (wp.ignore_until IS NULL OR wp.ignore_until < CURRENT_TIMESTAMP) AS ignored
FROM waitlist_entry we
         JOIN waitlist_person wp ON we.person = wp.id
         LEFT OUTER JOIN referral_points rp ON rp.referrer = wp.email
WHERE item_id = :id  
ORDER BY ignored ,date_assigned ,points DESC 
");

foreach ($items as $key => &$item) {
    $id = $item["id"];
    $entries_statement->bindParam("id", $id);
    $entries_statement->execute();
    $item["registrations"] = $entries_statement->fetchAll(PDO::FETCH_ASSOC);
}

$response = array();
$response["registrations"] = $items;
$response["leaderboard"] = select("SELECT referrer, points FROM referral_points", $pdo);
$response["pendingContracts"] = select("
SELECT ca.email, access_key, due_date
FROM contract_access ca
         JOIN contract_instances ci ON ci.id = ca.contract_id AND due_date > CURRENT_TIMESTAMP
WHERE email NOT IN (SELECT email FROM contract_log cl WHERE log_type = 'SIGN' AND cl.contract_id = ci.id)
ORDER BY due_date DESC, email
", $pdo);

$response["blocked"] = select("
SELECT firstname AS 'firstName', lastname AS 'lastName', email, birthday, ignore_until AS 'ignoredUntil'
FROM waitlist_person wp
WHERE wp.ignore_until IS NOT NULL
  AND ignore_until > CURRENT_DATE

", $pdo);

echo json_encode($response, JSON_THROW_ON_ERROR);

