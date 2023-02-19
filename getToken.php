<?php

require_once('./JWT.php');

header('Content-Type: application/json');

$cData = file_get_contents('php://input');
$aData = json_decode($cData, true);

$secret = $aData['secret'];

$expiration = date('Y-m-d H:i:s', time() + 60);

$payload = array(
    'exp' => $expiration
);

$jwt = JWT::create($payload, $secret);

echo json_encode(array(
    'token' => $jwt,
));
