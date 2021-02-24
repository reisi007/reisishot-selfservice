<?php
include_once "../db/fetch_multiple.php";

db("
SELECT email, timestamp, log_type, hash_value
FROM contract_log
WHERE contract_id = (
    SELECT contract_id FROM contract_access ca WHERE ca.email = :email AND ca.access_key = :access_key
)
");
