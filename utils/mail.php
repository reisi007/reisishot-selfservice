<?php

function sendMail(string $from, string $to, string $subject, string $body): bool
{
    $from = utf8_decode($from);
    $to = utf8_decode($to);
    $subject = utf8_decode($subject);
    $body = utf8_decode($body);


    $headers = 'MIME-Version: 1.0' . "\r\n";
    $headers .= 'Content-type: text/html; charset=iso-8859-1' . "\r\n";
    // Create email headers
    $headers .= 'From: ' . $from . "\r\n" .
        'Reply-To: ' . $from . "\r\n" .
        'X-Mailer: PHP/' . phpversion();

    $message = '<html lang="de"><body>';
    $message .= $body;
    $message .= '</body></html>';
    return mail($to, $subject, $message, $headers);
}
