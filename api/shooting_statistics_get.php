<?php
include_once "../header/json.php";
include_once "../utils/authed_only.php";
include_once "../db/fetch_multiple.php";

$pdo = createMysqlConnection();

$showMinors = !array_key_exists("showMinors", $_GET) || $_GET['showMinors'] === "true";
$showGroups = !array_key_exists("showGroups", $_GET) || $_GET['showGroups'] === "true";
$whereClause = "";
if (!$showMinors || !$showGroups) {
    $whereClause .= "where ";
}
if (!$showMinors) {
    $whereClause .= "isminor = FALSE ";
}
if (!$showMinors && !$showGroups) {
    $whereClause .= "and ";
}
if (!$showGroups) {
    $whereClause .= "isgroup = FALSE ";
}

$data = select("
SELECT year, wi.title, SUM(cnt) AS cnt
FROM (SELECT * FROM shooting_statistics_aggregated $whereClause) stats
         JOIN waitlist_item wi ON stats.item_id = wi.id
GROUP BY year, wi.title

", $pdo);

$result = [];

foreach ($data as $cur) {
    $key = $cur["year"];
    if (array_key_exists($key, $result)) {
        $year = $result[$key];
    } else {
        $year = array();
        $result[$key] = $year;
    }

    $year[$cur["title"]] = intval($cur["cnt"]);

    $result[$key] = $year;
}

echo json_encode($result, JSON_THROW_ON_ERROR);
