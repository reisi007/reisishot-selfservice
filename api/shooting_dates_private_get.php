<?php

const KEY_text = "text";
include_once "../utils/calendar.php";
include_once "../utils/authed_only.php";

$createEntry = function (int $kw, string $state, object $event): array {
    return array(
        KEY_kw => $kw,
        KEY_state => $state,
        KEY_text => $event->summary
    );
};

$mergeEntry = function ($original, $new) {
    $new[KEY_state] = merge_states($new[KEY_state], $original[KEY_state]);
    $new[KEY_text] = $new[KEY_text] . ' && ' . $original[KEY_text];
    return $new;
};

accessCalendar($createEntry, $mergeEntry, "+2 hours");
