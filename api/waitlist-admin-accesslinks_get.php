<?php
include_once "../db/fetch_multiple_authed.php";

query("
SELECT CONCAT('https://service.reisishot.pictures/waitlist/', email, '/', access_key) AS url,
       firstname                                                                      AS firstname,
       lastname                                                                       AS lastname,
       email,
       birthday
FROM waitlist_person
");
