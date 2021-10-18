<?php
include_once "../db/fetch_single_secured.php";

query("
SELECT access_key,
       email,
       rating,
       name,
       creation_date,
       review_private,
       review_public
FROM reviews
WHERE email = :email
  AND access_key = :access_key
");
