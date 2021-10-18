<?php
include_once "../db/fetch_multiple.php";

query("
SELECT id, display, -value AS value
FROM referral_values
WHERE id LIKE 'perk_%'
ORDER BY value, id, display
    ");
