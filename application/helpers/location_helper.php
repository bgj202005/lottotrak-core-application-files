<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Location Helper
 * 
 * Functions to help with IP geolocation and location detection
 */

if (!function_exists('get_real_ip_address')) {
    /**
     * Get the real IP address of the user
     * Handles various proxy configurations and headers
     */
    function get_real_ip_address() {
        // Check for various headers that might contain the real IP
        $ip_headers = array(
            'HTTP_CF_CONNECTING_IP',     // Cloudflare
            'HTTP_CLIENT_IP',            // Proxy
            'HTTP_X_FORWARDED_FOR',      // Load balancer/proxy
            'HTTP_X_FORWARDED',          // Proxy
            'HTTP_X_CLUSTER_CLIENT_IP',  // Cluster
            'HTTP_FORWARDED_FOR',        // Proxy
            'HTTP_FORWARDED',            // Proxy
            'REMOTE_ADDR'                // Standard
        );
        
        foreach ($ip_headers as $header) {
            if (!empty($_SERVER[$header])) {
                $ip_list = explode(',', $_SERVER[$header]);
                $ip = trim($ip_list[0]);
                
                // Validate IP address
                if (filter_var($ip, FILTER_VALIDATE_IP, FILTER_FLAG_NO_PRIV_RANGE | FILTER_FLAG_NO_RES_RANGE)) {
                    return $ip;
                }
            }
        }
        
        // Fallback to REMOTE_ADDR even if it's local/private
        return isset($_SERVER['REMOTE_ADDR']) ? $_SERVER['REMOTE_ADDR'] : '127.0.0.1';
    }
}

if (!function_exists('detect_location_by_ip')) {
    /**
     * Detect location based on IP address using free IP geolocation service
     * 
     * @param string $ip_address The IP address to lookup
     * @return array Location information
     */
    function detect_location_by_ip($ip_address = null) {
        if (!$ip_address) {
            $ip_address = get_real_ip_address();
        }
        
        // Default response
        $location = array(
            'ip' => $ip_address,
            'city' => 'Unknown',
            'region' => 'Unknown', 
            'country' => 'Unknown',
            'country_code' => 'XX',
            'detected_at' => date('Y-m-d H:i:s'),
            'success' => false
        );
        
        // Skip localhost and private IPs
        if ($ip_address === '127.0.0.1' || 
            $ip_address === '::1' || 
            strpos($ip_address, '192.168.') === 0 || 
            strpos($ip_address, '10.') === 0 ||
            strpos($ip_address, '172.') === 0) {
            
            $location['city'] = 'Localhost';
            $location['region'] = 'Local Development';
            $location['country'] = 'Local Machine';
            $location['country_code'] = 'LH';
            return $location;
        }
        
        try {
            // Use ip-api.com (free, no API key required, 1000 requests per hour)
            $url = "http://ip-api.com/json/{$ip_address}?fields=status,message,country,countryCode,region,regionName,city,query";
            
            $context = stream_context_create([
                'http' => [
                    'timeout' => 5,
                    'user_agent' => 'LottoTrak Location Service/1.0'
                ]
            ]);
            
            $response = @file_get_contents($url, false, $context);
            
            if ($response !== false) {
                $data = json_decode($response, true);
                
                if (isset($data['status']) && $data['status'] === 'success') {
                    $location = array(
                        'ip' => $ip_address,
                        'city' => isset($data['city']) ? $data['city'] : 'Unknown',
                        'region' => isset($data['regionName']) ? $data['regionName'] : 'Unknown',
                        'country' => isset($data['country']) ? $data['country'] : 'Unknown',
                        'country_code' => isset($data['countryCode']) ? $data['countryCode'] : 'XX',
                        'detected_at' => date('Y-m-d H:i:s'),
                        'success' => true
                    );
                }
            }
        } catch (Exception $e) {
            // Log error if needed, but continue with default location
            log_message('error', 'Location detection failed: ' . $e->getMessage());
        }
        
        return $location;
    }
}

if (!function_exists('format_location_display')) {
    /**
     * Format location information for display
     * 
     * @param array $location Location data
     * @return string Formatted location string
     */
    function format_location_display($location) {
        if (!is_array($location)) {
            return 'Unknown Location';
        }
        
        $parts = array();
        
        if (!empty($location['city']) && $location['city'] !== 'Unknown') {
            $parts[] = $location['city'];
        }
        
        if (!empty($location['region']) && $location['region'] !== 'Unknown') {
            $parts[] = $location['region'];
        }
        
        if (!empty($location['country']) && $location['country'] !== 'Unknown') {
            $parts[] = $location['country'];
        }
        
        return !empty($parts) ? implode(', ', $parts) : 'Unknown Location';
    }
}