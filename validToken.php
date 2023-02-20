<?php

require_once('./JWT.php');
require_once('./Response.php');

header('Content-Type: application/json');

$cData = file_get_contents('php://input');
$aData = json_decode($cData, true);

$headers = getallheaders();
$authorization = isset($headers['Authorization']) ? $headers['Authorization'] : '';

if (!empty($authorization)) {
    $secret = $aData['secret'];
    $token = str_replace('Bearer ', '', $authorization);

    $isValid = JWT::isValid($token, $secret);

    $message = $isValid === 1 ? 'Token is valid.' : 'Token is invalid';
    if ($isValid === 1) {
        Response::success('', 200, array(
            'status' => $isValid,
            'message' => $message,
        ));
    } else {
        Response::error('', 401, array(
            'status' => $isValid,
            'message' => $message,
        ));
    }
} else {
    Response::error('Bearer token is missing', 401);
}
