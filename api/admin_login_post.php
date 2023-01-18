<?php
include_once "../header/required.php";
include_once "../utils/sql.php";
include_once "../utils/security.php";
include_once "../utils/uuid.php";

$headers = getallheaders();
$user = trim($headers['Email']);
$pwd = trim($headers['Accesskey']);

$pdo = createMysqlConnection();

// Check if user is known
if (!checkIsAdmin($pdo, $user, $pwd)) {
    header("HTTP/1.1 401 Unauthorized");
    exit();
}

include_once "../vendor/autoload.php";
include_once "../config/jwt.conf.php";

use Firebase\JWT\JWT;

header('Content-Type: text/plain');

$issuedAt = new DateTimeImmutable();
$expired = $issuedAt->modify("+14 days");

$payload = [
    'iss' => 'https://api.reisinger.pictures',
    'aud' => 'https://reisinger.pictures',
    'iat' => $issuedAt->getTimestamp(),
    'nbf' => $issuedAt->getTimestamp(),
    "exp" => $expired->getTimestamp()
];

/**
 * IMPORTANT:
 * You must specify supported algorithms for your application. See
 * https://tools.ietf.org/html/draft-ietf-jose-json-web-algorithms-40
 * for a list of spec-compliant algorithms.
 */
echo JWT::encode($payload, jwt_key, 'HS512');
