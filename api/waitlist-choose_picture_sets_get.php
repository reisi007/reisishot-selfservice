<?php
include_once "../db/fetch_multiple_secured.php";

query("SELECT folder
FROM waitlist_person wp
         JOIN review_pictures_access rpa ON wp.id = rpa.person
WHERE email = :email
  AND access_key = :access_key");

