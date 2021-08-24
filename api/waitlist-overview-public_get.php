<?php
include_once "../db/fetch_multiple.php";

db("
SELECT id,
       image_id,
       title,
       description,
       available_from,
       available_to,
     (SELECT max_waiting -COUNT(*) FROM waitlist_entry WHERE item_id = id) AS max_waiting,
       0 AS done_customer,
       0 AS done_internal,
       0 AS registered
FROM waitlist_item
WHERE available_from <= CURRENT_DATE
  AND (available_to IS NULL OR available_to >= CURRENT_DATE)
ORDER BY sort_index
");
