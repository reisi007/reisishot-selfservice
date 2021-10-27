<?php
include_once "../db/fetch_single_secured.php";

query("
SELECT id,
       email,
       firstname               AS 'firstName',
       lastname                AS 'lastName',
       birthday,
       availability,
       phone_number,
       website,
       access_key,
       COALESCE(rp.points, 0)  AS points,
       COALESCE(rpi.points, 0) AS points,
       COALESCE(rpi.points, 0) AS sortable_points
FROM waitlist_person wp
         LEFT OUTER JOIN referral_points rp
                         ON wp.email = rp.referrer
         LEFT OUTER JOIN (SELECT referrer, SUM(points) AS points
                          FROM referral_points_intermediate
                          WHERE type NOT LIKE 'perk%'
                          GROUP BY points) rpi
                         ON rp.referrer = rpi.referrer
WHERE email = :email
  AND access_key = :access_key
");
