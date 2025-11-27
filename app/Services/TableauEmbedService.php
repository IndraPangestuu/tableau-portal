<?php

namespace App\Services;

use Illuminate\Support\Facades\Log;

class TableauEmbedService
{
    protected $server;
    protected $apiVersion;
    protected $siteId;
    protected $token;
    protected $siteContentUrl;

    public function __construct()
    {
        $this->server = config('tableau.server');
        $this->apiVersion = config('tableau.api_version');
        $this->siteId = config('tableau.site_id');
    }

    public function getTrustedUrl(string $username, string $viewPath)
    {
        $url = $this->server . '/trusted';
        $data = ['username' => $username];

        Log::info('Tableau Trusted Auth Request', [
            'url' => $url,
            'username' => $username,
            'viewPath' => $viewPath
        ]);

        $ch = curl_init($url);
        curl_setopt_array($ch, [
            CURLOPT_POST            => true,
            CURLOPT_POSTFIELDS      => http_build_query($data),
            CURLOPT_RETURNTRANSFER  => true,
            CURLOPT_TIMEOUT         => 15,
            CURLOPT_SSL_VERIFYHOST  => false,
            CURLOPT_SSL_VERIFYPEER  => false,
        ]);

        $ticket    = curl_exec($ch);
        $httpCode  = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $curlError = curl_error($ch);
        curl_close($ch);

        $ticket = trim($ticket);

        $failed = false;
        $errorMessage = '';

        if ($httpCode !== 200 || $curlError) {
            $failed = true;
            $ticket = '-1';
            $errorMessage = "HTTP $httpCode — $curlError";
            
            Log::error('Tableau Trusted Auth Failed - HTTP Error', [
                'http_code' => $httpCode,
                'curl_error' => $curlError,
                'url' => $url,
                'username' => $username
            ]);
        } elseif ($ticket === '-1') {
            $failed = true;
            $errorMessage = "Trusted Auth gagal. IP server belum jadi Trusted Host di Tableau.";
            
            Log::error('Tableau Trusted Auth Failed - Invalid Ticket', [
                'ticket' => $ticket,
                'url' => $url,
                'username' => $username,
                'message' => 'IP server belum terdaftar sebagai Trusted Host'
            ]);
        } else {
            Log::info('Tableau Trusted Auth Success', [
                'ticket' => substr($ticket, 0, 10) . '...',
                'username' => $username
            ]);
        }

        $embedUrl = $this->server . "/trusted/" . $ticket . $viewPath;

        return [
            'failed' => $failed,
            'ticket' => $ticket,
            'embed_url' => $embedUrl,
            'error_message' => $errorMessage
        ];
    }

    /**
     * Login ke Tableau REST API dan dapatkan token
     */
    public function signIn($siteContentUrl = null)
    {
        $username = config('tableau.admin_username');
        $password = config('tableau.admin_password');

        if (empty($username) || empty($password)) {
            Log::warning('Tableau API: Credentials not configured');
            return ['success' => false, 'error' => 'Tableau admin credentials belum dikonfigurasi di .env'];
        }

        $site = $siteContentUrl ?? $this->siteId ?? '';
        $url = $this->server . "/api/{$this->apiVersion}/auth/signin";

        Log::info('Tableau API Sign In', [
            'url' => $url,
            'username' => $username,
            'site' => $site ?: '(default)'
        ]);

        $body = json_encode([
            'credentials' => [
                'name' => $username,
                'password' => $password,
                'site' => ['contentUrl' => $site]
            ]
        ]);

        $response = $this->apiRequest($url, 'POST', $body);

        if (isset($response['credentials'])) {
            $this->token = $response['credentials']['token'];
            $this->siteContentUrl = $response['credentials']['site']['id'];
            
            Log::info('Tableau API Sign In Success', [
                'site_id' => $this->siteContentUrl,
                'site_name' => $response['credentials']['site']['contentUrl'] ?? $site
            ]);

            return [
                'success' => true,
                'token' => $this->token,
                'site_id' => $this->siteContentUrl,
                'site_name' => $response['credentials']['site']['contentUrl'] ?? $site
            ];
        }

        Log::error('Tableau API Sign In Failed', [
            'url' => $url,
            'site' => $site,
            'response' => $response
        ]);

        return ['success' => false, 'error' => $response['error'] ?? 'Login gagal. Periksa Site ID dan credentials.'];
    }

    /**
     * Ambil semua workbooks dari Tableau Server
     */
    public function getWorkbooks()
    {
        $auth = $this->signIn();
        if (!$auth['success']) {
            return $auth;
        }

        $url = $this->server . "/api/{$this->apiVersion}/sites/{$this->siteContentUrl}/workbooks";
        
        Log::info('Tableau API Get Workbooks', ['url' => $url]);
        
        $response = $this->apiRequest($url, 'GET', null, $this->token);

        if (isset($response['workbooks']['workbook'])) {
            $count = count($response['workbooks']['workbook']);
            Log::info('Tableau API Get Workbooks Success', ['count' => $count]);
            return ['success' => true, 'workbooks' => $response['workbooks']['workbook']];
        }

        Log::error('Tableau API Get Workbooks Failed', ['response' => $response]);
        return ['success' => false, 'error' => 'Gagal mengambil workbooks', 'workbooks' => []];
    }

    /**
     * Ambil semua views dari sebuah workbook
     */
    public function getViewsFromWorkbook($workbookId)
    {
        if (!$this->token) {
            $auth = $this->signIn();
            if (!$auth['success']) {
                return $auth;
            }
        }

        $url = $this->server . "/api/{$this->apiVersion}/sites/{$this->siteContentUrl}/workbooks/{$workbookId}/views";
        
        Log::info('Tableau API Get Views from Workbook', [
            'url' => $url,
            'workbook_id' => $workbookId
        ]);
        
        $response = $this->apiRequest($url, 'GET', null, $this->token);

        if (isset($response['views']['view'])) {
            $count = count($response['views']['view']);
            Log::info('Tableau API Get Views Success', ['count' => $count]);
            return ['success' => true, 'views' => $response['views']['view']];
        }

        Log::error('Tableau API Get Views Failed', ['response' => $response]);
        return ['success' => false, 'error' => 'Gagal mengambil views', 'views' => []];
    }

    /**
     * Ambil semua views dari Tableau Server
     */
    public function getAllViews($siteContentUrl = null)
    {
        $auth = $this->signIn($siteContentUrl);
        if (!$auth['success']) {
            return $auth;
        }

        $url = $this->server . "/api/{$this->apiVersion}/sites/{$this->siteContentUrl}/views";
        
        Log::info('Tableau API Get All Views', [
            'url' => $url,
            'site' => $siteContentUrl ?: '(default)'
        ]);
        
        $response = $this->apiRequest($url, 'GET', null, $this->token);

        if (isset($response['views']['view'])) {
            $views = $response['views']['view'];
            $formattedViews = array_map(function ($view) {
                return [
                    'id' => $view['id'],
                    'name' => $view['name'],
                    'contentUrl' => $view['contentUrl'],
                    'viewPath' => '/views/' . str_replace('/sheets/', '/', $view['contentUrl']),
                    'workbook' => $view['workbook']['name'] ?? 'Unknown'
                ];
            }, $views);
            
            Log::info('Tableau API Get All Views Success', [
                'count' => count($formattedViews),
                'site_name' => $auth['site_name'] ?? ''
            ]);

            return [
                'success' => true,
                'views' => $formattedViews,
                'site_name' => $auth['site_name'] ?? ''
            ];
        }

        Log::error('Tableau API Get All Views Failed', [
            'url' => $url,
            'response' => $response
        ]);

        return ['success' => false, 'error' => 'Gagal mengambil views', 'views' => []];
    }

    /**
     * Ambil daftar semua sites yang tersedia
     */
    public function getSites()
    {
        $auth = $this->signIn();
        if (!$auth['success']) {
            return $auth;
        }

        $url = $this->server . "/api/{$this->apiVersion}/sites";
        
        Log::info('Tableau API Get Sites', ['url' => $url]);
        
        $response = $this->apiRequest($url, 'GET', null, $this->token);

        if (isset($response['sites']['site'])) {
            $sites = array_map(function ($site) {
                return [
                    'id' => $site['id'],
                    'name' => $site['name'],
                    'contentUrl' => $site['contentUrl'],
                    'state' => $site['state'] ?? 'Active'
                ];
            }, $response['sites']['site']);
            
            Log::info('Tableau API Get Sites Success', ['count' => count($sites)]);
            return ['success' => true, 'sites' => $sites];
        }

        Log::error('Tableau API Get Sites Failed', ['response' => $response]);
        return ['success' => false, 'error' => 'Gagal mengambil daftar sites', 'sites' => []];
    }

    /**
     * Sign out dari Tableau REST API
     */
    public function signOut()
    {
        if (!$this->token) {
            return;
        }

        $url = $this->server . "/api/{$this->apiVersion}/auth/signout";
        $this->apiRequest($url, 'POST', null, $this->token);
        
        Log::info('Tableau API Sign Out');
        $this->token = null;
    }

    /**
     * Helper untuk API request
     */
    protected function apiRequest($url, $method = 'GET', $body = null, $token = null)
    {
        $ch = curl_init($url);

        $headers = ['Content-Type: application/json', 'Accept: application/json'];
        if ($token) {
            $headers[] = "X-Tableau-Auth: {$token}";
        }

        curl_setopt_array($ch, [
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_TIMEOUT => 30,
            CURLOPT_SSL_VERIFYHOST => false,
            CURLOPT_SSL_VERIFYPEER => false,
            CURLOPT_HTTPHEADER => $headers,
        ]);

        if ($method === 'POST') {
            curl_setopt($ch, CURLOPT_POST, true);
            if ($body) {
                curl_setopt($ch, CURLOPT_POSTFIELDS, $body);
            }
        }

        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $error = curl_error($ch);
        curl_close($ch);

        if ($error) {
            Log::error('Tableau API cURL Error', [
                'url' => $url,
                'method' => $method,
                'error' => $error,
                'http_code' => $httpCode
            ]);
            return ['error' => $error];
        }

        if ($httpCode >= 400) {
            Log::error('Tableau API HTTP Error', [
                'url' => $url,
                'method' => $method,
                'http_code' => $httpCode,
                'response' => substr($response, 0, 500)
            ]);
        }

        $decoded = json_decode($response, true);
        
        if ($decoded === null && !empty($response)) {
            Log::error('Tableau API Invalid JSON Response', [
                'url' => $url,
                'response' => substr($response, 0, 500)
            ]);
            return ['error' => 'Invalid JSON response from Tableau Server'];
        }

        return $decoded ?? ['error' => 'Empty response'];
    }
}
