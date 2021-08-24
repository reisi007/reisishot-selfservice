<?php
include_once "../db/fetch_multiple_secured.php";

db("
SELECT id,
       image_id,
       title,
       description,
       available_from,
       available_to,
       (SELECT max_waiting -COUNT(*) FROM waitlist_entry WHERE item_id = id) AS max_waiting,
       (SELECT COUNT(*) > 0
        FROM waitlist_entry
        WHERE item_id = id
          AND email = :email
          AND secret = :access_key
          AND done_customer) AS done_customer,
       (SELECT COUNT(*) > 0
        FROM waitlist_entry
        WHERE item_id = id
          AND email = :email
          AND secret = :access_key
          AND done_internal) AS done_internal,
         (SELECT COUNT(*) > 0
        FROM waitlist_entry
        WHERE item_id = id
          AND email = :email
          AND secret = :access_key) AS registered
FROM waitlist_item wi
WHERE available_from <= CURRENT_DATE
  AND (available_to IS NULL OR available_to >= CURRENT_DATE)
ORDER BY sort_index
");
