<?php

const KEY_isShooting = "isShooting";
const KEY_kw = "kw";
include_once "../header/json.php";
include_once "../github/iCal.php";
include_once '../config/calender.conf.php';

/**
 * @param $resultEntryCreator
 * @param $mergeEntries
 * @return void
 */
function accessCalendar($resultEntryCreator, $mergeEntries): void
{
    $iCal = new iCal(calendar_shooting);
    $curDt = new DateTime('yesterday');

    $events = $iCal->events();

    $result = array();

    foreach ($events as $event) {
        $dateStart = $event->dateStart;
        $dateEnd = $event->dateEnd;
        $heading = $event->summary;

        $isShooting = !str_contains(strtolower($heading), "pause");

        $startsDt = new DateTime($dateStart);
        $endDt = new DateTime($dateEnd);

        if ($endDt >= $curDt) {
            $endDt->modify("-1 second");
            $startKw = intval($startsDt->format("W"));
            $endKw = intval($endDt->format("W"));

            for ($kw = $startKw; $kw <= $endKw; $kw++) {
                $cur = $resultEntryCreator($kw, $isShooting, $event);
                if (array_key_exists($kw, $result)) {
                    $cur = $mergeEntries($result[$kw], $cur);
                }
                $result[$kw] = $cur;
            }
        }
    }

    echo json_encode(array_values($result), JSON_THROW_ON_ERROR);
}
