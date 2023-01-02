<?php

const primaryColor = '#2A9D8F';

const newline = "\n";
function sendMail(string $from, string $to, string|null $bcc, string $subject, string $body): bool
{
    $headers = 'MIME-Version: 1.0' . newline;
    // Create email headers
    $headers .= 'From: ' . $from . newline .
        'Reply-To: ' . $from . newline .
        'Content-Type: text/html; charset=UTF-8' .
        'X-Mailer: ReisishotSelfservice/2022';
    if ($bcc != null) {
        $headers .= newline .
            'Bcc: ' . $bcc;
    }

    $message = "<html lang='de'><body style='background: " . primaryColor . ";word-wrap: break-word;'>";
    $message .= '<div style="padding: 1rem;">';
    $message .= '<div style="text-align: center"><img style="margin: 0 auto; border-radius: 1rem; display: inline-block; object-fit: scale-down" src="https://reisinger.pictures/apple-touch-icon.png"  alt="Florian Reisinger Photography Logo"/></div>';
    $message .= '<br style="display:none;"/>';
    $message .= "<div style='background-color: #ffffff; border-radius: 0.5rem;margin: 0.5rem;padding: 1rem; box-sizing: border-box'><table width='100%' style='background-color: #ffffff;word-wrap: break-word;'>$body</table></div>";
    $message .= '</div></body></html>';
    return mail($to, '=?utf-8?B?' . base64_encode($subject) . '?=', $message, $headers);
}


function insertMainLink(string $target, string $linkText)
{
    return "<table style='background-color: " . primaryColor . "; width: 100%;'>"
        . "<td style='display:table-cell;background-color: " . primaryColor . ";box-sizing: border-box;Padding: 0.5rem 1rem 0.5rem 1rem;border-radius: 0.25rem;font-size: 1rem;text-align: center'>"
        . "<a href='$target' style='display: block;color: #ffffff'>$linkText</a>"
        . "</td></table>"
        . "<small style='text-align: center'>Falls du den Link oben nicht klicken kannst, kopiere diesen Link: <a href='$target'>$target</a></small>";
}
