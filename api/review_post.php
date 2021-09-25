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
    $review = $json["review"];

    $done = false;

    if ($access_key != null) {
        // Try update
        $statement = $pdo->prepare("UPDATE review SET rating=:rating, name=:name,review=:review WHERE email =:email AND access_key = :access_key");

        $statement->bindParam("rating", $rating);
        $statement->bindParam("name", $name);
        $statement->bindParam("review", $review);
        $statement->bindParam("email", $email);
        $statement->bindParam("access_key", $access_key);

        $statement->execute();

        if ($statement->rowCount() > 1)
            throw new Exception("Could not insert new user");
        if ($statement->rowCount() == 1)
            $done = true;
    }
    if (!$done) {
        $access_key = uuid($pdo);

        $statement = $pdo->prepare("INSERT INTO review(access_key, email, rating, name, review) VALUES (:access_key,:email,:rating,:name,:review)");

        $statement->bindParam("access_key", $access_key);
        $statement->bindParam("email", $email);
        $statement->bindParam("rating", $rating);
        $statement->bindParam("name", $name);
        $statement->bindParam("review", $review);

        $statement->execute();

        if ($statement->rowCount() != 1)
            throw new Exception("Could not insert new review");
    }

    $starCount = roundStars($rating / 20);

    echo "{\"access_key\":\"$access_key\"}";

    $pdo->commit();

    sendMail("review@reisishot.pictures", "review@reisishot.pictures", $starCount . ' ★ - Neue Bewertung', "
    <h1>$name ($email) - $starCount ★ <small>$rating / 100</small></h1>
    <p style='white-space: pre;'>$review</p>
    ");
})();

function roundStars($number)
{
    return round($number / 5, 1) * 5;
}
