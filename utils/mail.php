<?php

const primary = '#2A9D8F';
const onPrimary = '#ffffff';

const newline = "\n";

function sendMail(string $from, string $to, string|null $bcc, string $subject, string $body): bool
{
    $headers = 'MIME-Version: 1.0' . newline;
    // Create email headers
    $headers .= 'From: ' . $from . newline .
        'Reply-To: ' . $from . newline .
        'Content-Type: text/html; charset=UTF-8' .
        'X-Mailer: ReisishotSelfservice/2023';

    if ($bcc != null) {
        $headers .= newline .
            'Bcc: ' . $bcc;
    }

    $message = "<!DOCTYPE html><html lang='de'><head><title>" . $subject . "</title></head><body style='background: " . primary . ';color: ' . onPrimary . ";border-bottom: solid 40px " . primary . "'>";
    $message .= '<div style="text-align: center"><img style="margin: 0 auto; border-radius: 1rem; display: inline-block; object-fit: scale-down" src="https://reisinger.pictures/apple-touch-icon.png"  alt="Florian Reisinger Photography Logo"/></div>';
    $message .= $body . '</body></html>';

    return mail(
        $to,
        '=?utf-8?B?' . base64_encode($subject) . '?=',
        $message,
        $headers
    );
}


function p(string $content)
{
    return "<p style='border: solid 20px " . primary . ";'>$content</p>";
}

function button(string $link, string $content)
{
    $textStyle = 'background-color: ' . onPrimary . ';color:' . primary . ' ';
    return "<table style='" . $textStyle . ";width: 100%;'>"
        . "<td style='display:table-cell;" . $textStyle . "; box-sizing: border-box;Padding: 0.5rem 1rem 0.5rem 1rem;border-radius: 0.25rem;font-size: 1rem;text-align: center'>"
        . '<a href="' . $link . '" style="display: block;' . $textStyle . '">' . $content . '</a>'
        . '</td></table>';
}
