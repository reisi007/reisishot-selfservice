<?php
include_once "../db/fetch_multiple_authed.php";

query("
SELECT id,
       email,
       firstname AS 'firstName',
       lastname  AS 'lastName',
       birthday,
       tel,
       pin,
       sum AS 'total'
FROM bonuscard_summed wp
");
