<?php
include_once '../utils/sql.php';
include_once '../header/json.php';

$pdo = createMysqlConnection();


$folder = $_GET["folder"];

$result = array(
    "ratings" => select("SELECT filename, rating
FROM waitlist_person wp
         JOIN review_pictures_rating r ON wp.id = r.person
WHERE email = :email
  AND access_key = :access_key
  AND  folder = :folder
", $folder, $pdo),
    "comments" => select("SELECT filename, comment
FROM waitlist_person wp
         JOIN review_pictures_comment c ON wp.id = c.person
WHERE email = :email
  AND access_key = :access_key
  AND  folder = :folder
", $folder, $pdo)
);


echo json_encode($result, JSON_THROW_ON_ERROR);

function select(string $sql, string $folder, \PDO $pdo): array
{
    $headers = getallheaders();

    $email = trim($headers['Email']);
    $accessKey = trim($headers['Accesskey']);

    $statement = $pdo->prepare($sql);
    $statement->bindParam('email', $email);
    $statement->bindParam('access_key', $accessKey);
    $statement->bindParam('folder', $folder);
    $statement->execute();
    return $statement->fetchAll(PDO::FETCH_ASSOC);
}
