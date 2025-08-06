<?php

class UpdateChecker {
    private $updateUrl = 'https://update.librewo.com';
    private $timeout = 5; // 5 seconds timeout
    
    /**
     * Check for updates if conditions are met
     * 
     * @return array|null Returns update info or null if no check performed
     */
    public function checkForUpdates() {
        // Don't check if update check is disabled
        if (!defined('UPDATE_CHECK_ENABLED') || !UPDATE_CHECK_ENABLED) {
            return null;
        }
        
        // Include version information
        $versionFile = defined('ROOT_PATH') ? ROOT_PATH . '/version.php' : dirname(__DIR__) . '/version.php';
        if (!file_exists($versionFile)) {
            return [
                'status' => 'error',
                'message' => 'Unable to check for updates'
            ];
        }
        
        include $versionFile;
        
        // Don't check for updates if channel is not 'release'
        if (!isset($channel) || $channel !== 'release') {
            return null;
        }
        
        if (!isset($version)) {
            return [
                'status' => 'error',
                'message' => 'Unable to check for updates'
            ];
        }
        
        try {
            return $this->performUpdateCheck($version);
        } catch (Exception $e) {
            // Return error state if check fails
            return [
                'status' => 'error',
                'message' => 'Unable to check for updates'
            ];
        }
    }
    
    /**
     * Perform the actual update check
     * 
     * @param string $currentVersion Current version number
     * @return array Update check result
     */
    private function performUpdateCheck($currentVersion) {
        $postData = json_encode(['current_version' => $currentVersion]);
        
        $context = stream_context_create([
            'http' => [
                'method' => 'POST',
                'header' => [
                    'Content-Type: application/json',
                    'Content-Length: ' . strlen($postData)
                ],
                'content' => $postData,
                'timeout' => $this->timeout
            ]
        ]);
        
        $response = @file_get_contents($this->updateUrl, false, $context);
        
        if ($response === false) {
            throw new Exception('Failed to connect to update server');
        }
        
        $data = json_decode($response, true);
        
        if (!isset($data['latest_version'])) {
            throw new Exception('Invalid response format');
        }
        
        // Validate version format (number.number.number)
        if (!preg_match('/^\d+\.\d+\.\d+$/', $data['latest_version'])) {
            throw new Exception('Invalid version format in response');
        }
        
        // Compare versions - update is available if versions are different
        $updateAvailable = version_compare($currentVersion, $data['latest_version'], '!=');
        
        return [
            'status' => 'success',
            'current_version' => $currentVersion,
            'latest_version' => $data['latest_version'],
            'update_available' => $updateAvailable
        ];
    }
}
