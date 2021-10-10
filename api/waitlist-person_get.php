<?php
include_once "../db/fetch_single_secured.php";

query("
SELECT id, email, firstname AS 'firstName', lastname AS 'lastName', birthday, availability, phone_number, website, access_key
FROM waitlist_person
WHERE email = :email
  AND access_key = :access_key
  ");
