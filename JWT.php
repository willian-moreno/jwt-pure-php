<?php

date_default_timezone_set('America/Sao_Paulo');

/**
 * Exception thrown when JWT token is invalid or unauthorized
 */
class UnauthorizedJWTException extends Exception
{
    /**
     * Construct UnauthorizedJWTException
     *
     * @param string $message Exception message
     * @param int $statusCode HTTP status code
     * @param \Throwable|null $previous Previous exception
     */
    public function __construct(
        string $message = 'Token inválido ou inexistente.',
        int $statusCode = 401,
        ?\Throwable $previous = null
    ) {
        parent::__construct(
            message: $message,
            code: $statusCode,
            previous: $previous
        );
    }
}

/**
 * Interface for JSON Web Token service operations
 */
interface IJsonWebToken
{
    /**
     * Create a new JWT token
     *
     * @param array $payload Additional payload data to include in token
     * @param string|null $secret Secret key for signing (uses JWT_SECRET env var if null)
     * @return string Generated JWT token
     */
    public function create(array $payload = [], ?string $secret = null): string;

    /**
     * Invalidate a JWT token (mark as blacklisted)
     *
     * @param string $token JWT token to invalidate
     * @return void
     */
    public function invalidate(string $token): void;

    /**
     * Validate if a JWT token is valid and not expired
     *
     * @param string $token JWT token to validate
     * @param string|null $secret Secret key for verification (uses JWT_SECRET env var if null)
     * @return bool True if token is valid, false otherwise
     */
    public function isValid(string $token, ?string $secret = null): bool;

    /**
     * Extract payload content from JWT token without validation
     *
     * @param string $token JWT token to decode
     * @return array Decoded payload data
     * @throws UnauthorizedJWTException If token format is invalid
     */
    public static function payloadContent(string $token): array;

    /**
     * Extract Bearer token from Authorization header
     *
     * @return string Bearer token or empty string if not found
     */
    public static function bearerToken(): string;
}

/**
 * JSON Web Token service implementation with fingerprinting security
 * 
 * This service provides JWT token creation, validation, and management with additional
 * security features including client fingerprinting to prevent token reuse across
 * different clients/browsers.
 * 
 * Security Features:
 * - Client fingerprinting based on IP, User-Agent, and browser headers
 * - Token expiration validation
 * - HMAC-SHA256 signature verification
 * - Protection against token replay attacks through fingerprinting
 */
class JWT implements IJsonWebToken
{
    /**
     * Create a new JWT token with standard claims and fingerprinting
     *
     * Creates a JWT token with standard claims (iss, aud, exp, iat) and includes
     * a client fingerprint for additional security. The token is automatically
     * added to the Authorization header.
     *
     * Security Note: The generated token includes a fingerprint that ties it to
     * the specific client making the request, preventing token reuse from different browsers/IPs.
     *
     * @param array $payload Additional payload data to include in token
     * @param string|null $secret Secret key for signing (uses JWT_SECRET env var if null)
     * @return string Generated JWT token
     */
    public function create(array $payload = [], ?string $secret = null): string
    {
        if (empty($secret)) {
            $secret = $_ENV['JWT_SECRET'];
        }

        $header = self::base64url_encode(
            json_encode([
                'alg' => 'HS256',
                'typ' => 'JWT'
            ])
        );

        $payload = self::base64url_encode(json_encode([
            'iss' => 'API App JWT',
            'aud' => 'App JWT',
            'exp' => date('Y-m-d H:i:s', time() + (60 * 60 * 24)),
            'iat' => date('Y-m-d H:i:s'),
            'fingerprint' => self::fingerprint(),
            ...$payload
        ]));

        $signature = self::base64url_encode(
            hash_hmac(
                algo: 'sha256',
                data: "{$header}.{$payload}",
                key: $secret,
                binary: true
            )
        );

        $jwt = "$header.$payload.$signature";

        header('Authorization: Bearer ' . $jwt);

        return $jwt;
    }

    /**
     * Encode data using base64url encoding (RFC 7515 compliant)
     *
     * Converts data to base64url format by replacing '+' with '-', '/' with '_',
     * and removing padding '=' characters as required by JWT specification.
     *
     * @param string $data Data to encode
     * @return string Base64url encoded string
     */
    private static function base64url_encode(string $data): string
    {
        return rtrim(strtr(base64_encode($data), '+/', '-_'), '=');
    }

    /**
     * Decode base64url encoded data (RFC 7515 compliant)
     *
     * Converts base64url format back to original data by reversing the
     * base64url encoding process and adding necessary padding.
     *
     * @param string $data Base64url encoded data to decode
     * @return string Decoded data
     */
    private static function base64url_decode(string $data): string
    {
        $remainder = strlen($data) % 4;

        if ($remainder) {
            $addlen = 4 - $remainder;
            $data .= str_repeat('=', $addlen);
        }

        return base64_decode(strtr($data, '-_', '+/'));
    }

    /**
     * Invalidate a JWT token (placeholder implementation)
     *
     * Note: Current implementation is empty. To properly invalidate tokens,
     * consider implementing a token blacklist mechanism.
     *
     * @param string $token JWT token to invalidate
     * @return void
     */
    public function invalidate(string $token): void {}

    /**
     * Validate JWT token integrity, expiration, and client fingerprint
     *
     * Performs comprehensive token validation including:
     * - Token format validation
     * - Signature verification using HMAC-SHA256
     * - Expiration time check
     * - Client fingerprint verification (security feature)
     *
     * Security Note: The fingerprint validation ensures the token can only be used
     * by the same client (IP + browser) that originally requested it, preventing
     * token hijacking and cross-client usage.
     *
     * @param string $token JWT token to validate
     * @param string|null $secret Secret key for verification (uses JWT_SECRET env var if null)
     * @return bool True if token is valid and not expired, false otherwise
     */
    public function isValid(string $token, ?string $secret = null): bool
    {
        if (empty($token)) {
            return false;
        }

        if (empty($secret)) {
            $secret = $_ENV['JWT_SECRET'];
        }

        $matches = [];

        if (!preg_match("/^(.+?)\.(.+?)\.(.+?)$/", $token, $matches)) {
            return false;
        }

        [, $header, $payload, $signature] = $matches;

        $signatureToCompare = self::base64url_encode(
            hash_hmac(
                algo: 'sha256',
                data: "{$header}.{$payload}",
                key: $secret,
                binary: true
            )
        );

        if ($signatureToCompare !== $signature) {
            return false;
        }

        $payloadBase64Decoded = self::base64url_decode($payload);

        if (empty($payloadBase64Decoded)) {
            return false;
        }

        $payloadJsonDecode = json_decode($payloadBase64Decoded, true);

        if (!is_array($payloadJsonDecode) || empty($payloadJsonDecode)) {
            return false;
        }

        $payloadFingerprint = $payloadJsonDecode['fingerprint'];
        $fingerprintToCompare = self::fingerprint();

        if ($fingerprintToCompare !== $payloadFingerprint) {
            return false;
        }

        $payloadExpiration = $payloadJsonDecode['exp'];
        $payloadExpirationTimestamp = strtotime($payloadExpiration);

        if (!is_numeric($payloadExpirationTimestamp)) {
            return false;
        }

        if (time() > $payloadExpirationTimestamp) {
            return false;
        }

        return true;
    }

    /**
     * Extract and decode payload content from JWT token
     *
     * Extracts the payload section from a JWT token and returns the decoded
     * data without performing signature validation or expiration checks.
     *
     * Warning: This method does not validate the token's integrity or expiration.
     * Use isValid() method for secure token validation before trusting payload data.
     *
     * @param string $token JWT token to decode
     * @return array Decoded payload data
     * @throws UnauthorizedJWTException If token format is invalid or payload is empty
     */
    public static function payloadContent(string $token): array
    {
        if (empty($token)) {
            throw new UnauthorizedJWTException;
        }

        $matches = [];

        if (!preg_match("/^(.+?)\.(.+?)\.(.+?)$/", $token, $matches)) {
            throw new UnauthorizedJWTException;
        }

        [,, $payload] = $matches;

        if (empty($payload)) {
            throw new UnauthorizedJWTException;
        }

        $payloadBase64Decoded = self::base64url_decode($payload);
        $payloadJsonDecode = json_decode($payloadBase64Decoded, true);

        if (empty($payloadJsonDecode)) {
            throw new UnauthorizedJWTException;
        }

        return $payloadJsonDecode;
    }

    /**
     * Extract Bearer token from HTTP Authorization header
     *
     * Parses the Authorization header to extract the Bearer token value,
     * removing the "Bearer " prefix and any extra whitespace.
     *
     * @return string Bearer token or empty string if Authorization header not found
     */
    public static function bearerToken(): string
    {
        $headers = getallheaders();

        if (empty($headers)) {
            return '';
        }

        $authorization = $headers['Authorization'] ?? '';
        $token = preg_replace(['/Bearer+/i', '/\s+/'], '', $authorization);

        return $token;
    }

    /**
     * Generate client fingerprint for token binding security
     *
     * Creates a unique fingerprint based on client characteristics including:
     * - IP address (REMOTE_ADDR)
     * - User Agent string
     * - Security headers (Sec-CH-UA, Sec-CH-UA-Platform)
     * - Accept-Language header
     *
     * The fingerprint is used to bind tokens to specific clients, preventing
     * token reuse across different browsers or IP addresses.
     *
     * Security Note: This fingerprint helps mitigate token hijacking attacks
     * by ensuring tokens can only be used from the same client environment
     * where they were originally issued.
     *
     * @return string MD5 hash of concatenated client characteristics
     */
    public static function fingerprint(): string
    {
        $items = [
            'ip' => $_SERVER['REMOTE_ADDR'] ?? '0.0.0.0',
            'userAgent' => $_SERVER['HTTP_USER_AGENT'] ?? '',
            'secChUa' => $_SERVER['HTTP_SEC_CH_UA'] ?? '',
            'secChUaPlataform' => $_SERVER['HTTP_SEC_CH_UA_PLATFORM'] ?? '',
            'acceptLanguage' => $_SERVER['HTTP_ACCEPT_LANGUAGE'] ?? ''
        ];

        return md5(implode(separator: '', array: $items));
    }
}
