<?php
date_default_timezone_set('America/Sao_Paulo');

class JWT
{
    static function create($payload, $secret, $header = null)
    {
        if (!$header || empty($header)) {
            $header = array(
                'alg' => 'HS256',
                'typ' => 'JWT',
            );
        }
        $header = json_encode($header);
        $header = base64_encode($header);

        $payload = self::payloadValidation($payload);
        $payload = json_encode($payload);
        $payload = base64_encode($payload);

        $signature = hash_hmac(
            'sha256',
            "$header.$payload",
            $secret,
            true
        );
        $signature = base64_encode($signature);

        $jwt = "$header.$payload.$signature";

        return $jwt;
    }

    static private function payloadValidation($payload)
    {
        $oneDayDuration = time() + (60 * 60 * 24);
        $expiration = date('Y-m-d H:i:s', $oneDayDuration);
        $payloadExpiration = array('exp' => $expiration);

        if (!isset($payload['exp'])) return array_merge(
            $payload,
            $payloadExpiration
        );
        return $payload;
    }

    static function isValid($token, $secret)
    {
        $isValid = 0;

        $tokenParts = explode('.', $token);

        if (
            !$tokenParts ||
            empty($tokenParts) ||
            count($tokenParts) !== 3
        ) return $isValid;

        $header = $tokenParts[0];
        $payload = $tokenParts[1];
        $signature = $tokenParts[2];

        $signatureToCompare = hash_hmac(
            'sha256',
            "$header.$payload",
            $secret,
            true
        );
        $signatureToCompare = base64_encode($signatureToCompare);

        if ($signatureToCompare === $signature) {
            $isValid = 1;

            $payloadBase64Decoded = base64_decode($payload);
            $payloadJsonDecode = json_decode($payloadBase64Decoded, true);
            $payloadExpiration = $payloadJsonDecode['exp'];
            $payloadExpirationTimestamp = strtotime($payloadExpiration);

            if (time() > $payloadExpirationTimestamp) $isValid = 0;
        }

        return $isValid;
    }
}
