<?php

include_once "../utils/calendar.php";


$simpleEntry = function (int $kw, bool $isShooting, object $event): array {
    return array(
        "kw" => $kw,
        "isShooting" => $isShooting
    );
};

accessCalendar($simpleEntry);
