<?php
/**
 * Helper to make HTTP GET requests to the backend API.
 *
 * @param string $endpoint The API endpoint path (e.g. "/asientos/misAsiento?evento=li")
 * @param string $token The JWT token for authentication
 * @return array|null The decoded JSON response data, or null on failure
 */
function api_get(string $endpoint, string $token): ?array
{
    global $BASE_API_URL;

    $ch = curl_init();
    curl_setopt_array($ch, [
        CURLOPT_URL => $BASE_API_URL . $endpoint,
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_HTTPHEADER => [
            'Authorization: Bearer ' . $token
        ],
        CURLOPT_SSL_VERIFYPEER => false, // Known local development requirement
        CURLOPT_TIMEOUT => 10
    ]);

    $response = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);

    if ($response === false || $httpCode >= 400) {
        error_log("API GET Error [HTTP $httpCode] on endpoint $endpoint");
        return null;
    }

    return json_decode($response, true);
}
