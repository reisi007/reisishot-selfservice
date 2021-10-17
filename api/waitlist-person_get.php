<?php
include_once "../db/fetch_single_secured.php";

query("
SELECT id,
       email,
       firstname           AS 'firstName',
       lastname            AS 'lastName',
       birthday,
       availability,
       phone_number,
       website,
       access_key,
       COALESCE(points, 0) AS points
FROM waitlist_person wp
         LEFT OUTER JOIN referral_points rp ON wp.email = rp.referrer
WHERE email = :email
  AND access_key = :access_key
  ");
