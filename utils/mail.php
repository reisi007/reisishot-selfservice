<?php

function sendMail(string $from, string $to, string|null $bcc, string $subject, string $body): bool
{
    $headers = 'MIME-Version: 1.0' . "\r\n";
    $headers .= 'Content-type: text/html; charset=utf-8' . "\r\n";
    // Create email headers
    $headers .= 'From: ' . $from . "\r\n" .
        'Reply-To: ' . $from . "\r\n" .
        'X-Mailer: PHP/' . phpversion();
    if ($bcc != null) {
        $headers .= "\r\n" .
            'Bcc: ' . $bcc;
    }

    $message = '<html lang="de"><body>';
    $message .= $body;
    $message .= '</body></html>';
    return mail($to, '=?utf-8?B?' . base64_encode($subject) . '?=', $message, $headers);
}
