<?php

require_once __DIR__ . '/JWT.php';
require_once __DIR__ . '/Response.php';

header('Content-Type: application/json');

$cData = file_get_contents('php://input');
$aData = json_decode($cData, true);

$headers = getallheaders();
$authorization = isset($headers['Authorization']) ? $headers['Authorization'] : '';

if (empty($authorization)) {
    Response::error('Bearer token is missing', 401);
}

$secret = $aData['secret'];
$token = str_replace('Bearer ', '', $authorization);

$isValid = (new JWT())->isValid($token, $secret);

$message = $isValid ? 'Token is valid.' : 'Token is invalid';

if ($isValid) {
    Response::success('', 200, array(
        'status' => $isValid,
        'message' => $message,
    ));
}

Response::error('', 401, array(
    'status' => $isValid,
    'message' => $message,
));
