<?php
include_once "../header/json.php";
include_once "../db/fetch_multiple_authed.php";
include_once "../utils/authed_only.php";

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
       done_customer,
       done_internal,
       email,
       firstname              AS 'firstName',
       lastname               AS 'lastName',
       birthday,
       availability,
       phone_number,
       website,
       access_key,
       COALESCE(rp.points, 0) AS points
FROM waitlist_entry we
         JOIN waitlist_person wp ON we.person = wp.id
         LEFT OUTER JOIN referral_points rp ON rp.referrer = wp.email
WHERE item_id = :id
ORDER BY done_internal DESC, points DESC 

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

echo json_encode($response, JSON_THROW_ON_ERROR);

