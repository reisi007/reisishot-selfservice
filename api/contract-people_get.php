<?php
include_once "../db/fetch_multiple_authed.php";

query("
SELECT DISTINCT firstname AS 'firstName', lastname AS 'lastName', birthday, email
FROM contract_access
ORDER BY lastname, firstname, birthday, email
");
