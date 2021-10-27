<?php
include_once "../header/json.php";
include_once "../utils/sql.php";
include_once "../db/fetch_multiple_authed.php";

query("
SELECT access_key, email, rating, name, creation_date, review_private, review_public
FROM reviews
ORDER BY creation_date DESC
");

