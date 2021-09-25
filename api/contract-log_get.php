<?php
include_once "../db/fetch_multiple_secured.php";

query("
SELECT email, timestamp, log_type, hash_value
FROM contract_log cl
WHERE contract_id = (
    SELECT ca.contract_id FROM contract_access ca WHERE ca.email = :email AND ca.access_key = :access_key
)
ORDER BY timestamp DESC, email
");
