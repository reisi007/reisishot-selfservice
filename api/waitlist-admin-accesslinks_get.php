<?php
include_once "../db/fetch_multiple_authed.php";

query("
SELECT CONCAT('/waitlist/', email, '/', access_key) AS url,
       firstname                                                                      AS 'firstName',
       lastname                                                                       AS 'lastName',
       email,
       birthday
FROM waitlist_person
");
