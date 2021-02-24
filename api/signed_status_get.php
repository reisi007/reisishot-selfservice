<?php
include_once "../db/fetch_multiple.php";

db("
SELECT ca.email AS email, CAST(cl.contract_id IS NOT NULL AS INTEGER) AS signed
FROM (
         SELECT contract_id, email
         FROM contract_access
         WHERE contract_id =
               (SELECT contract_id FROM contract_access ca WHERE ca.email = :email AND ca.access_key = :access_key)
     ) ca
         LEFT OUTER JOIN (SELECT * FROM contract_log WHERE log_type = 'SIGN') cl
                         ON ca.contract_id = cl.contract_id
");
