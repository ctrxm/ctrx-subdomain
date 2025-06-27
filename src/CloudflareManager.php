<?php

namespace App;

class CloudflareManager
{
    private $zoneId;
    private $apiToken;
    private $apiEmail;
    private $baseUrl;
    private $headers;

    public function __construct($apiToken, $zoneId, $apiEmail = null)
    {
        $this->apiToken = $apiToken;
        $this->zoneId = $zoneId;
        $this->apiEmail = $apiEmail;
        $this->baseUrl = "https://api.cloudflare.com/client/v4/zones/{$this->zoneId}/";

        $this->headers = [
            "Authorization: Bearer {$this->apiToken}",
            "Content-Type: application/json",
        ];

        // Jika Anda menggunakan Global API Key dan butuh email/key khusus
        // Cloudflare API Token biasanya cukup dengan Authorization: Bearer
        // Jika API Token Anda adalah Global API Key (berbentuk panjang), maka gunakan ini:
        // if ($this->apiEmail && strpos($this->apiToken, 'GFB-') !== 0) {
        //     $this->headers = [
        //         "X-Auth-Email: {$this->apiEmail}",
        //         "X-Auth-Key: {$this->apiToken}",
        //         "Content-Type: application/json",
        //     ];
        // }
    }

    private function makeRequest($method, $endpoint, $payload = [])
    {
        $url = $this->baseUrl . $endpoint;
        $ch = curl_init($url);

        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $this->headers);
        curl_setopt($ch, CURLOPT_TIMEOUT, 10); //

        if ($method === 'POST') {
            curl_setopt($ch, CURLOPT_POST, true);
            curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($payload));
        } elseif ($method === 'DELETE') {
            curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "DELETE");
        } elseif ($method === 'PUT') {
            curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "PUT");
            curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($payload));
        }

        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $error = curl_error($ch);
        curl_close($ch);

        if ($response === false) {
            error_log("cURL Error: " . $error);
            return ['success' => false, 'message' => "Network or cURL error: " . $error];
        }

        $data = json_decode($response, true);

        if ($httpCode >= 400 || !(isset($data['success']) && $data['success'])) {
            $errorMessage = "Cloudflare API Error ({$httpCode}): " . (isset($data['errors'][0]['message']) ? $data['errors'][0]['message'] : 'Unknown error');
            error_log($errorMessage . " Response: " . $response);
            return ['success' => false, 'message' => $errorMessage];
        }

        return $data;
    }

    public function getDnsRecords()
    {
        $result = $this->makeRequest('GET', 'dns_records');
        return $result['success'] ? $result['result'] : false;
    }

    public function addDnsRecord($type, $name, $content, $proxied = true, $ttl = 120)
    {
        $payload = [
            'type'    => $type,
            'name'    => $name,
            'content' => $content,
            'ttl'     => $ttl,
            'proxied' => $proxied,
        ];
        return $this->makeRequest('POST', 'dns_records', $payload);
    }

    
    public function updateDnsRecord($recordId, $type, $name, $content, $proxied = true, $ttl = 120)
    {
        $payload = [
            'type'    => $type,
            'name'    => $name,
            'content' => $content,
            'ttl'     => $ttl,
            'proxied' => $proxied,
        ];
        // Panggil makeRequest secara internal dengan method PUT
        return $this->makeRequest('PUT', "dns_records/{$recordId}", $payload);
    }

    public function deleteDnsRecord($recordId)
    {
        return $this->makeRequest('DELETE', "dns_records/{$recordId}");
    }
}