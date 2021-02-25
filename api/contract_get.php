<?php
include_once "../db/fetch_single.php";

db("
SELECT access_key, email, firstname, lastname, birthday,  CONCAT(markdown,'\r\n',additional_text) AS markdown, ci.hash_algo, ci.hash_value, due_date
FROM contract_access ca
     JOIN contract_instances ci ON ca.contract_id = ci.contract_id
         JOIN contract_data cd ON ca.contract_id = cd.id
WHERE email = :email
  AND access_key = :access_key
");
