<?php
include_once "../header/json.php";
include_once "../utils/files.php";
include_once "../utils/authed_only.php";

$pdo = createMysqlConnection();
$json = read_body_json();

$year = intval($json["year"]);

$statement = $pdo->prepare("
SELECT referrer, SUM(points) AS points
FROM referral_points_intermediate
WHERE year = :year
GROUP BY referrer
ORDER BY points DESC
");

$statement->bindParam("year", $year);

$statement->execute();
echo json_encode($statement->fetchAll(PDO::FETCH_ASSOC));
