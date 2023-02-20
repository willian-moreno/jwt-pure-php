<?php
date_default_timezone_set('America/Sao_Paulo');

/**
 * Class to create and validate JSON Web Token. 
 */
class JWT
{
    /**
     * Create a Json Web Token, with a payload that can contain any information, a secret key, and an optional header with algorithm and token type.
     *
     * @param array $payload List that carries any information, such as an entity ID.
     * @param string $secret Secret key for token validation.
     * @param array $header List which typically consists of two parts: token type which is JWT and the signature algorithm being used such as HMAC SHA256 or RSA.
     * @return string
     */
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

    /**
     * Checks if payload contain expiration value. If not, add the 'exp' parameter with 1 day of validation.
     *
     * @param array $payload List that carries any information, such as an entity ID.
     * @return array
     */
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

    /**
     * Validation to determine whether the token is valid based on signature and expiration time.
     *
     * @param string $token Token value to be validated.
     * @param string $secret Secret key for token validation.
     * @return boolean
     */
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
