<?php

const KEY_state = "state";
const KEY_kw = "kw";
include_once "../header/json.php";
include_once "../github/iCal.php";
include_once '../config/calender.conf.php';
include_once "../utils/caching.php";

/**
 * @param $resultEntryCreator
 * @param $mergeEntries
 * @return void
 */
function accessCalendar($resultEntryCreator, $mergeEntries, string $validity): void
{
    $url = cache_access_url("shooting_calendar", calendar_shooting, $validity);

    $iCal = new iCal($url);
    $curDt = new DateTime('-1 month');

    $events = $iCal->events();

    $result = array();

    foreach ($events as $event) {
        $dateStart = $event->dateStart;
        $dateEnd = $event->dateEnd;
        $heading = $event->summary;

        $state = computeState($heading);

        $startsDt = new DateTime($dateStart);
        $endDt = new DateTime($dateEnd);

        if ($endDt >= $curDt) {
            $endDt->modify("-1 second");
            $startKw = intval($startsDt->format("W"));
            $endKw = intval($endDt->format("W"));

            for ($kw = $startKw; $kw <= $endKw; $kw++) {
                $cur = $resultEntryCreator($kw, $state, $event);
                if (array_key_exists($kw, $result)) {
                    $cur = $mergeEntries($result[$kw], $cur);
                }
                $result[$kw] = $cur;
            }
        }
    }
    echo json_encode(array_values($result), JSON_THROW_ON_ERROR);
}

const STATE_FREE = "FREE";
const STATE_BUSY = "BUSY";
const STATE_TAKEN = "TAKEN";
const STATE_BLOCKED = "BLOCKED";

/**
 * @param $original
 * @return string
 */
function computeState($original): string
{
    $searchString = strtolower($original);
    if (str_contains($searchString, "pause")) {
        return STATE_BLOCKED;
    }
    if (str_contains($searchString, "beschäftigt")) {
        return STATE_BUSY;
    }
    return STATE_TAKEN;
}

function merge_states($a, $b)
{
    $haystack = array($a, $b);

    if (in_array(STATE_BLOCKED, $haystack)) {
        return STATE_BLOCKED;
    }
    if (in_array(STATE_TAKEN, $haystack)) {
        return STATE_TAKEN;
    }
    if (in_array(STATE_BUSY, $haystack)) {
        return STATE_BUSY;
    }
    return STATE_FREE;
}
