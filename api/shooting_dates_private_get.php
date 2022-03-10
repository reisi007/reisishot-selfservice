<?php

include_once "../utils/calendar.php";
include_once "../utils/authed_only.php";


$simpleEntry = function (int $kw, bool $isShooting, object $event): array {
    return array(
        "kw" => $kw,
        "isShooting" => $isShooting,
        "text" => $event->summary
    );
};

accessCalendar($simpleEntry);
