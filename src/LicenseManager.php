<?php
namespace App;

class LicenseManager
{
    private $licenseServerApiUrl;
    private $currentDomain;
    private $db;

    public function __construct($licenseServerApiUrl, $currentDomain, Database $db)
    {
        $this->licenseServerApiUrl = $licenseServerApiUrl;
        $this->currentDomain = $currentDomain;
        $this->db = $db;
    }

    public function verifyLicenseWithServer($licenseCodeRaw)
    {

        $payload = [
            'license_code' => $licenseCodeRaw,
            'domain'       => $this->currentDomain,
        ];

        $ch = curl_init($this->licenseServerApiUrl);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($payload));
        curl_setopt($ch, CURLOPT_TIMEOUT, 15);

        $response = curl_exec($ch);
        $error = curl_error($ch);
        curl_close($ch);

        if ($response === false) {
            error_log("License Server API cURL Error: " . $error . " URL: " . $this->licenseServerApiUrl);
            return ['status' => false, 'message' => "Gagal terhubung ke server lisensi. Coba lagi nanti."];
        }

        $apiResponse = json_decode($response, true);

        if (json_last_error() !== JSON_ERROR_NONE) {
            error_log("License Server API JSON Decode Error: " . json_last_error_msg() . " Response: " . $response);
            return ['status' => false, 'message' => "Respons tidak valid dari server lisensi."];
        }

        if (isset($apiResponse['status']) && $apiResponse['status'] === true) {
            return [
                'status' => true,
                'message' => $apiResponse['message'] ?? 'Lisensi valid.',
                'data' => $apiResponse['data'] ?? []
            ];
        } else {
            return [
                'status' => false,
                'message' => $apiResponse['message'] ?? 'Lisensi tidak valid.',
                'reason' => $apiResponse['reason'] ?? 'unknown'
            ];
        }
    }


    public function getAndVerifyStoredLicense()
    {
        $storedCodeRow = $this->db->fetch("SELECT setting_value FROM app_settings WHERE setting_key = 'app_license_code'");

        if (!$storedCodeRow) {
            return ['status' => false, 'message' => 'Lisensi belum terpasang di database lokal.'];
        }

        $licenseCodeRaw = $storedCodeRow['setting_value'];

        $verificationResult = $this->verifyLicenseWithServer($licenseCodeRaw);

        if ($verificationResult['status']) {
            $verificationResult['code_raw'] = $licenseCodeRaw;
        } else {
            $this->removeLicense();
            $verificationResult['message'] .= " Lisensi lokal dihapus.";
        }
        return $verificationResult;
    }

    public function storeLicense($licenseCodeRaw)
    {
        try {
            $existingLicense = $this->db->fetch("SELECT setting_key FROM app_settings WHERE setting_key = 'app_license_code'");
            if ($existingLicense) {
                $this->db->execute("UPDATE app_settings SET setting_value = ? WHERE setting_key = 'app_license_code'", [$licenseCodeRaw]);
            } else {
                $this->db->execute("INSERT INTO app_settings (setting_key, setting_value) VALUES ('app_license_code', ?)", [$licenseCodeRaw]);
            }
            return true;
        } catch (\PDOException $e) {
            error_log("LicenseManager Error: Failed to store license in local DB: " . $e->getMessage());
            return false;
        }
    }


     
    public function removeLicense()
    {
        try {
            $this->db->execute("DELETE FROM app_settings WHERE setting_key = 'app_license_code'");
            return true;
        } catch (\PDOException $e) {
            error_log("LicenseManager Error: Failed to remove license from local DB: " . $e->getMessage());
            return false;
        }
    }
}
