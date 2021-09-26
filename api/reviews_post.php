<?php
include_once "../header/json.php";
include_once "../utils/security.php";
include_once "../utils/sql.php";
include_once "../utils/mail.php";
include_once "../utils/files.php";

(function () {

    $pdo = createMysqlConnection();
    $pdo->beginTransaction();

    $json = read_body_json();
    $headers = getallheaders();

    $email = strtolower(trim($headers["Email"]));
    $access_key = trim($headers["Accesskey"]);

    if ($access_key == "")
        $access_key = null;

    $rating = $json["rating"];
    $name = $json["name"];
    $review_public = $json["review_public"];
    $review_private = $json["review_private"];

    if ($access_key != null) {
        // Try update
        $statement = $pdo->prepare("
UPDATE reviews
SET rating=:rating,
    name=:name,
    review_private=:review_private,
    review_public=:review_public
WHERE email = :email
  AND access_key = :access_key
  ");

        $statement->bindParam("rating", $rating);
        $statement->bindParam("name", $name);
        $statement->bindParam("review_private", $review_private);
        $statement->bindParam("review_public", $review_public);
        $statement->bindParam("email", $email);
        $statement->bindParam("access_key", $access_key);

        $statement->execute();

        if ($statement->rowCount() > 1)
            throw new Exception("Could not insert new rating");

        echo "{\"access_key\":\"$access_key\"}";
        return;
    }

    $access_key = uuid($pdo);

    $statement = $pdo->prepare("
INSERT INTO reviews(access_key, email, rating, name, review_private, review_public)
VALUES (:access_key, :email, :rating, :name, :review_private, :review_public)
");

    $statement->bindParam("access_key", $access_key);
    $statement->bindParam("email", $email);
    $statement->bindParam("rating", $rating);
    $statement->bindParam("name", $name);
    $statement->bindParam("review_private", $review_private);
    $statement->bindParam("review_public", $review_public);

    $statement->execute();

    if ($statement->rowCount() != 1)
        throw new Exception("Could not insert new review");


    $starCount = roundStars($rating / 20);

    echo "{\"access_key\":\"$access_key\"}";

    $pdo->commit();

    sendMail("review@reisishot.pictures", "review@reisishot.pictures", $starCount . ' ★ - Neue Bewertung', "
    <h1>$name ($email) - $starCount ★ <small>$rating / 100</small></h1>
    <p style='white-space: pre;'>$review_public</p>
    ");
})();

function roundStars($number)
{
    return round($number / 5, 1) * 5;
}
