<?php

function combineMd(string $base, string $additionalText)
{
    $placeholder = '%platzhalter%';
    if (str_contains($base, $placeholder)) {
        return str_replace($placeholder, "\r\n{$additionalText}", $base);
    } else {
        return $base . "\r\n" . $additionalText;
    }
}
