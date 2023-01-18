<?php
include_once "sql.php";
include_once '../vendor/autoload.php';
include_once '../config/jwt.conf.php';

use Firebase\JWT\JWT;
use Firebase\JWT\Key;

function isAuthed(): bool
{
    try {
        $jwt = getallheaders()['Authorization'];
        $jwt = explode(' ', $jwt)[1];
        JWT::decode($jwt, new Key(jwt_key, 'HS512'));

        return true;
    } catch (Exception $e) {
        return false;
    }
}
