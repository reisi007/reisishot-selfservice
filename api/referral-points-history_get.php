<?php
include_once "../db/fetch_multiple_secured.php";

query("
SELECT rv.value      AS points,
       rv.id         AS 'key',
       rv.display    AS display,
       rpr.timestamp AS timestamp
FROM referral_points_raw rpr
         JOIN waitlist_person wp ON wp.email = rpr.referrer
         JOIN referral_values rv ON rv.id = rpr.type
WHERE wp.email = :email
  AND wp.access_key = :access_key
ORDER BY timestamp DESC
");
