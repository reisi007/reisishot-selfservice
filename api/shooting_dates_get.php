<?php

include_once "../utils/calendar.php";

$createEntry = function (int $kw, bool $isShooting, object $event): array {
    return array(
        KEY_kw => $kw,
        KEY_isShooting => $isShooting
    );
};

$mergeEntry = function ($original, &$new) {
    $new[KEY_isShooting] = $new[KEY_isShooting] && $original[KEY_isShooting];
};

accessCalendar($createEntry, $mergeEntry);
