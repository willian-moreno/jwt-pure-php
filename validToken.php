<?php

require_once('./JWT.php');

header('Content-Type: application/json');

$cData = file_get_contents('php://input');
$aData = json_decode($cData, true);

$token = $aData['token'];
$secret = $aData['secret'];

$isValid = JWT::isValid($token, $secret);

echo json_encode(array(
    'status' => $isValid,
    'message' => $isValid === 1 ? 'Token is valid.' : 'Token is invalid',
));
