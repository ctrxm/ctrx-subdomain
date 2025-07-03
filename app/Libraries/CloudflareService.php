<?php

namespace App\Libraries;

class CloudflareService
{
    private ?string $apiToken;
    private ?string $apiKey;
    private ?string $email;
    private string $baseUrl = 'https://api.cloudflare.com/client/v4/';

    public function __construct()
    {
        $this->apiToken = env('cloudflare.apiToken');
        $this->apiKey   = env('cloudflare.apiKey');
        $this->email    = env('cloudflare.email');
    }

    public function createDnsRecord(string $zoneId, string $type, string $name, string $content, bool $proxied = true): ?object
    {
        $payload = [
            'type'    => $type,
            'name'    => $name,
            'content' => $content,
            'ttl'     => 1, // 1 = Auto TTL
            'proxied' => $proxied,
        ];

        return $this->sendRequest("zones/{$zoneId}/dns_records", 'POST', $payload);
    }

    public function deleteDnsRecord(string $zoneId, string $recordId): ?object
    {
        return $this->sendRequest("zones/{$zoneId}/dns_records/{$recordId}", 'DELETE');
    }

    private function sendRequest(string $endpoint, string $method = 'GET', array $data = []): ?object
    {
        try {
            $client = \Config\Services::curlrequest([
                'baseURI' => $this->baseUrl,
                'timeout' => 20,
            ]);

            $headers = ['Content-Type' => 'application/json'];

            // Mengadopsi logika otentikasi dari kode lama Anda yang sudah terbukti
            if (!empty($this->apiToken)) {
                $headers['Authorization'] = 'Bearer ' . $this->apiToken;
            } elseif (!empty($this->apiKey) && !empty($this->email)) {
                $headers['X-Auth-Email'] = $this->email;
                $headers['X-Auth-Key']   = $this->apiKey;
            } else {
                // Tidak ada kredensial sama sekali
                return (object)[
                    'success' => false,
                    'errors' => [(object)['code' => 'NO_CREDENTIALS', 'message' => 'Kredensial API Cloudflare belum diatur di file .env.']]
                ];
            }

            $response = $client->request($method, $endpoint, [
                'headers' => $headers,
                'json'    => $data,
            ]);

            $statusCode = $response->getStatusCode();
            $body = $response->getBody();
            $result = json_decode($body);

            // Meniru error handling dari kode lama Anda yang lebih baik
            if ($statusCode >= 400) {
                 log_message('error', "[CloudflareService] Error ({$statusCode}): " . $body);
                 return (object)[
                    'success' => false,
                    'errors'  => $result->errors ?? [(object)['code' => $statusCode, 'message' => $result->message ?? 'HTTP Error']],
                ];
            }

            return $result;

        } catch (\Exception $e) {
            log_message('error', '[CloudflareService] cURL Exception: ' . $e->getMessage());
            return (object)[
                'success' => false,
                'errors'  => [(object) ['code' => 'CURL_EXCEPTION', 'message' => $e->getMessage()]],
            ];
        }
    }
}
