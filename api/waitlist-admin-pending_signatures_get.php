<?php
include_once "../db/fetch_multiple_authed.php";

query("
SELECT ca.email, access_key, due_date
FROM contract_access ca
         JOIN contract_instances ci ON ci.id = ca.contract_id AND due_date > CURRENT_TIMESTAMP
WHERE email NOT IN (SELECT email FROM contract_log cl WHERE log_type = 'SIGN' AND cl.contract_id = ci.id)
ORDER BY due_date DESC, email
");
