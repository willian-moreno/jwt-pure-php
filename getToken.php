<?php

require_once('./JWT.php');

header('Content-Type: application/json');

require_once('./Response.php');

$cData = file_get_contents('php://input');
$aData = json_decode($cData, true);

$secret = isset($aData['secret']) ? $aData['secret'] : '';

if (empty($secret)) {
    Response::error('', 400);
}
$expiration = date('Y-m-d H:i:s', time() + 60);

$payload = array(
    'exp' => $expiration
);

$jwt = JWT::create($payload, $secret);

Response::success('', 200, array(
    'token' => $jwt,
));
