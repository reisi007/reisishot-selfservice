<?php
include_once "../header/json.php";
include_once "../utils/sql.php";
include_once "../utils/security.php";


$headers = getallheaders();

$user = trim($headers["Email"]);
$pwd = trim($headers["Accesskey"]);

$pdo = createMysqlConnection();

// Check if user is allowed to insert
if (!checkUserInsert($pdo, $user, $pwd)) {
    throw new Exception("Wrong PWD");
}

$item_result = $pdo->query("SELECT * FROM waitlist_item ORDER BY sort_index");

$items = $item_result->fetchAll(PDO::FETCH_ASSOC);

$entries_statement = $pdo->prepare("
SELECT item_id,
       secret,
       email,
       firstname AS 'firstName',
       lastname  AS 'lastName',
       birthday,
       availability,
       phone_number,
       website,
       text,
       done_internal
FROM waitlist_entry
WHERE item_id = :id
  AND NOT done_internal
");

foreach ($items as $key => &$item) {
    $id = $item["id"];
    $entries_statement->bindParam("id", $id);
    $entries_statement->execute();
    $item["registrations"] = $entries_statement->fetchAll(PDO::FETCH_ASSOC);
}

echo json_encode($items, JSON_THROW_ON_ERROR);

