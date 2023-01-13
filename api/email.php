<?php
include_once '../header/required.php';
include_once '../utils/mail.php';

$inputJSON = file_get_contents('php://input');
$inputJSON = json_decode($inputJSON, true);
if (prepareEmail($inputJSON)) {
    http_response_code(204);
} else {
    http_response_code(555);
}

function prepareEmail($inputJSON)
{
    $from = safeString($inputJSON["email"]);
    $betreff = safeString($inputJSON["subject"]);
    $message = '<h1>Neue Nachricht vom Kontaktformular!</h1>';
    foreach ($inputJSON as $key => $value) {
        $message .= '<b>' . safeString($key) . '</b><br/>'
            . safeString($value) . "<br/>\n";
    }

    return sendMail($from, "florian@reisinger.pictures", null, $betreff, $message);
}

function safeString(string $string)
{
    return utf8_decode(htmlspecialchars($string, ENT_XML1));
}

?>
