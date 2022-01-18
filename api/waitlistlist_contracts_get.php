<?php
include_once "../db/fetch_multiple_secured.php";

query("
SELECT ca.access_key AS access_key, due_date, COUNT(log_type) AS is_signed, due_date > CURRENT_TIMESTAMP AS can_sign
FROM contract_access ca
         JOIN contract_instances ci ON ci.id = ca.contract_id
         JOIN waitlist_person wp ON ca.email = wp.email
         LEFT JOIN (SELECT * FROM contract_log WHERE email = :email AND log_type = 'SIGN') cl ON cl.contract_id = ci.id
WHERE wp.email = :email
  AND wp.access_key = :access_key
GROUP BY ca.access_key, due_date
ORDER BY due_date DESC
");
