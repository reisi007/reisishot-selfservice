<?php

const boundary = '------------090303020209010600070908';
const logoBase64 = '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';

const primaryColor = '#27ae60';

const newline = "\n";
function sendMail(string $from, string $to, string|null $bcc, string $subject, string $body): bool
{
    $headers = 'MIME-Version: 1.0' . newline;
    // Create email headers
    $headers .= 'From: ' . $from . newline .
        'Reply-To: ' . $from . newline .
        'Content-Type: multipart/related;boundary=' . substr(boundary, 2) . newline .
        'X-Mailer: ReisishotSelfservice/2022';
    if ($bcc != null) {
        $headers .= newline .
            'Bcc: ' . $bcc;
    }
    $message = "This is multipart message using MIME" . newline;
    $message .= boundary . newline;
    $message .= 'Content-Type: text/html;charset=utf-8' . newline;
    $message .= 'Content-Transfer-Encoding: 7bit' . newline . newline;
    $message .= "<html lang='de'><body style='background: " . primaryColor . "'>";
    $message .= '<div style="padding: 1rem;">';
    $message .= '<div style="text-align: center"><img style="margin: 0 auto; border-radius: 1rem; display: inline-block;" src="cid:logo"  alt="Reisishot Logo"/></div>';
    $message .= '<br style="display:none;"/>';
    $message .= "<div style='background-color: #ffffff; border-radius: 0.5rem;margin: 0.5rem;padding: 1rem; box-sizing: border-box'><table width='100%' style='background-color: #ffffff'>$body</table></div>";
    $message .= '</div></body></html>';
    $message .= newline . boundary . newline;
    $message .= 'Content-Type: image/jpeg;name="logo.png"' . newline . 'Content-Transfer-Encoding: base64' . newline . 'Content-ID: <logo>' . newline . 'Content-Disposition: inline;filename="logo.png"' . newline . newline . logoBase64 . newline . boundary;
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
