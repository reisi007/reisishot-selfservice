<?php
include_once "../db/fetch_multiple_authed.php";

query("
SELECT id,
       email,
       firstname AS 'firstName',
       lastname  AS 'lastName',
       birthday,
       availability,
       phone_number,
       website,
       access_key
FROM waitlist_person
ORDER BY firstname, lastname, email, birthday DESC 
");
