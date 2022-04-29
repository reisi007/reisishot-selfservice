<?php
include_once "../db/fetch_multiple.php";

query("
SELECT id,
       short,
       image_id,
       title,
       description,
       available_from,
       available_to,
       IF(
               ISNULL(max_waiting),
               NULL,
               (SELECT max_waiting - COUNT(*)
                FROM waitlist_entry we
                         JOIN waitlist_person wp ON wp.id = we.person
                WHERE item_id = id
                  AND (wp.ignore_until IS NULL OR wp.ignore_until < CURRENT_TIMESTAMP))
           ) AS max_waiting,
       0     AS registered
FROM waitlist_item
WHERE available_from <= CURRENT_DATE
  AND (available_to IS NULL OR available_to >= CURRENT_DATE)
ORDER BY sort_index
");
