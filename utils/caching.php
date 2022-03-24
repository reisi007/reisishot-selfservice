<?php

const CACHE_FOLDER = "../../.service-cache/";

function cache_get(string $cache_key, string $validity): string|null
{
    $path = computeCachePath($cache_key);
    if (!file_exists($path)) {
        return null;
    }

    $lastEditTime = filemtime($path);
    $cacheEditTimeString = date("Y-m-d H:i:s", $lastEditTime);
    $dateTime = new DateTime($cacheEditTimeString);
    $now = new DateTime();
    $dateTime->modify($validity);
    if ($now > $dateTime) {
        return null;
    }
    return $path;
}


function cache_put(string $cache_key, string|null $data): string|null
{
    $path = computeCachePath($cache_key);
    if ($data === null) {
        if (file_exists($path)) {
            unlink($path);
        }
        return null;
    } else {
        if (!file_exists(CACHE_FOLDER)) {
            mkdir(CACHE_FOLDER, recursive: true);
        }
        file_put_contents($path, $data, LOCK_EX);
    }
    return $path;
}

function cache_put_url(string $cache_key, string|null $data)
{
    if ($data == null) {
        return cache_put($cache_key, null);
    } else {
        return cache_put($cache_key, file_get_contents($data));
    }
}

/**
 * @param string $cache_key
 * @return string
 */
function computeCachePath(string $cache_key): string
{
    return CACHE_FOLDER . $cache_key . ".cached";
}
