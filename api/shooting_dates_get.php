<?php

include_once "../utils/calendar.php";

$createEntry = function (int $kw, string $state, object $rawEvent): array {
    return array(
        KEY_kw => $kw,
        KEY_state => $state
    );
};

$mergeEntry = function ($original, $new) {
    $new[KEY_state] = merge_states($new[KEY_state], $original[KEY_state]);
    return $new;
};

accessCalendar($createEntry, $mergeEntry, "+6 hours");
