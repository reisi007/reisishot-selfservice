<?php
include_once "../header/json.php";
//include_once "../utils/authed_only.php";
include_once "../db/fetch_multiple.php";

$pdo = createMysqlConnection();

$data = select("
SELECT title, MONTH(shooting_date) AS 'month', COUNT(*) AS cnt
FROM shooting_statistics s
         JOIN waitlist_item wi ON wi.id = s.item_id
GROUP BY title, MONTH(shooting_date)
    ", $pdo);

$result = [];

foreach ($data as $cur) {
    $key = $cur["month"];
    if (array_key_exists($key, $result)) {
        $month = $result[$key];
    } else {
        $month = array();
        $result[$key] = $month;
    }

    if (array_key_exists($key, $month)) {
        $month[$cur["title"]] += intval($cur["cnt"]);
    } else {
        $month[$cur["title"]] = intval($cur["cnt"]);
    }

    $result[$key] = $month;
}

echo json_encode($result, JSON_THROW_ON_ERROR);
