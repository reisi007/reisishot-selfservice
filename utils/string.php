<?php

function combineMd(string $base, string $additionalText, string $dsgvo = "")
{
    $placeholder = '%platzhalter%';
    if (str_contains($base, $placeholder)) {
        $fullText = str_replace($placeholder, "\r\n{$additionalText}", $base);
    } else {
        $fullText = "{$base}\r\n{$additionalText}";
    }

    if (!empty(trim($dsgvo))) {
        $fullText .= "\r\n{$dsgvo}";
    }

    return $fullText;
}
