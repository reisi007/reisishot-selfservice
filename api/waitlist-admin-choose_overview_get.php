<?php
include_once "../utils/authed_only.php";
include_once "../header/json.php";
include_once "../db/fetch_multiple.php";

$path = "../../.choose";

$scan = array_slice(scandir($path), 2);

$result = [];

$pdo = createMysqlConnection();

foreach ($scan as $item) {
    $filename = $path . '/' . $item;
    if (is_dir($filename)) {
        $cur = array();
        $cur["name"] = $item;
        $cur["cnt"] = count(json_decode(file_get_contents($filename . "/meta.json")));

        $cur["access"] = select("
SELECT id,
       email,
       firstname       AS 'firstName',
       lastname        AS 'lastName',
       birthday
FROM review_pictures_access JOIN waitlist_person wp ON wp.id = review_pictures_access.person where folder = '$item'
", $pdo);
        $result[] = $cur;
    }
}
echo json_encode($result, JSON_THROW_ON_ERROR);

