<?php

const KEY_text = "text";
include_once "../utils/calendar.php";
include_once "../utils/authed_only.php";

$createEntry = function (int $kw, bool $isShooting, object $event): array {
    return array(
        KEY_kw => $kw,
        KEY_isShooting => $isShooting,
        KEY_text => $event->summary
    );
};

$mergeEntry = function ($original, $new) {
    $new[KEY_isShooting] = $new[KEY_isShooting] && $original[KEY_isShooting];
    $new[KEY_text] = $new[KEY_text] . ' && ' . $original[KEY_text];
    return $new;
};

accessCalendar($createEntry, $mergeEntry, "+30 minutes");
