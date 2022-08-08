<?php
include_once "../db/fetch_multiple_authed.php";

query("
SELECT DISTINCT *
FROM (SELECT firstname AS 'firstName', lastname AS 'lastName', birthday, email
      FROM contract_access) ca
UNION
(SELECT firstname,
        lastname,
        birthday,
        email
 FROM waitlist_person)
ORDER BY lastname, firstname, birthday, email
");
