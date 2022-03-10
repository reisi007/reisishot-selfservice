<?php

include_once "../header/json.php";
include_once "../github/iCal.php";
include_once '../config/calender.conf.php';

/**
 * @param  $resultEntryCreator
 * @return void
 */
function accessCalendar($resultEntryCreator): void
{
    $iCal = new iCal(calendar_shooting);

    $allEvents = $iCal->eventsByDateSince('-2 months');

    $result = array();
    foreach ($allEvents as $date => $events) {
        foreach ($events as $event) {
            $dateStart = $event->dateStart;
            $dateEnd = $event->dateEnd;
            $heading = $event->summary;
            $isShooting = !str_contains(strtolower($heading), "pause");

            $startsDt = new DateTime($dateStart);
            $endDt = new DateTime($dateEnd);
            $endDt->modify("-1 second");
            $startKw = intval($startsDt->format("W"));
            $endKw = intval($endDt->format("W"));

            for ($kw = $startKw; $kw <= $endKw; $kw++) {
                $result[] = $resultEntryCreator($kw, $isShooting, $event);
            }
        }
    }

    echo json_encode($result, JSON_THROW_ON_ERROR);
}
