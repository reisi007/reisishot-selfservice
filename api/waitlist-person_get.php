<?php
include_once "../db/fetch_single_secured.php";

query("
SELECT id,
       email,
       firstname                                                         AS 'firstName',
       lastname                                                          AS 'lastName',
       birthday,
       availability,
       phone_number,
       website,
       access_key,
       (SELECT SUM(points) FROM referral_points WHERE referrer = :email) AS points,
       (SELECT SUM(points)
        FROM referral_points_intermediate
        WHERE referrer = :email
          AND type NOT LIKE 'perk%')                                     AS sortable_points
FROM waitlist_person wp
WHERE email = :email
  AND access_key = :access_key

");
