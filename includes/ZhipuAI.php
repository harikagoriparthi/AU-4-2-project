<?php
/**
 * AI Integration Class
 * Handles communication with Zhipu AI and Replicate
 */

class ZhipuAI {
    private $apiKey;
    
    public function __construct($apiKey = null) {
        $this->apiKey = $apiKey ?: getenv('ZHIPUAI_API_KEY') ?: '';
    }

    public function generateTryOnImage($userImagePath, $productInfo) {
        return ['success' => false, 'error' => 'Please use ReplicateVTON for true virtual try-on.'];
    }

    public function downloadImage($url, $savePath) {
        // Fallback download method
        $ch = curl_init($url);
        $fp = fopen($savePath, 'wb');
        if (!$fp) return false;
        
        curl_setopt($ch, CURLOPT_FILE, $fp);
        curl_setopt($ch, CURLOPT_HEADER, 0);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
        curl_setopt($ch, CURLOPT_TIMEOUT, 60);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        
        $success = curl_exec($ch);
        curl_close($ch);
        fclose($fp);
        return $success && file_exists($savePath);
    }
}

/**
 * Replicate API Integration for IDM-VTON
 */
class ReplicateVTON {
    private $apiToken;

    public function __construct($apiToken = null) {
        $this->apiToken = $apiToken ?: (defined('REPLICATE_API_TOKEN') ? REPLICATE_API_TOKEN : '');
    }

    public function runVirtualTryOn($personImagePath, $garmentImageUrl, $category = 'upper_body') {
        if (empty($this->apiToken)) {
            return ['success' => false, 'error' => 'Replicate API Token is missing.'];
        }

        // 1. Encode person image to Base64 (since it's local)
        $personImageBase64 = $this->fileToBase64($personImagePath);
        if (!$personImageBase64) {
            return ['success' => false, 'error' => 'Failed to process user image.'];
        }

        // 2. Prepare Payload for IDM-VTON
        // Note: We use the 'input' object directly. 
        // We do NOT specify 'version' here to avoid "version does not exist" errors.
        // Instead, we hit the model deployment endpoint directly.
        $payload = [
            "input" => [
                "human_img" => $personImageBase64,
                "garm_img" => $garmentImageUrl,
                "garment_des" => "clothing item",
                "category" => $category, // 'upper_body', 'lower_body', or 'dresses'
                "crop" => false,
                "seed" => 42,
                "steps" => 30
            ]
        ];

        // 3. Start Prediction using the Model Endpoint (Stable)
        // This endpoint automatically uses the latest version of the model
        $endpoint = 'https://api.replicate.com/v1/models/yisol/idm-vton/predictions';
        
        $response = $this->makeRequest('POST', $endpoint, $payload);

        if (isset($response['error'])) {
            return ['success' => false, 'error' => 'Replicate API Error: ' . $response['error']];
        }

        if (!isset($response['id'])) {
            // Debugging: return full response if ID is missing
            return ['success' => false, 'error' => 'Invalid response from Replicate.', 'debug' => $response];
        }

        $predictionId = $response['id'];
        
        // 4. Poll for results
        return $this->pollPrediction($predictionId);
    }

    private function pollPrediction($id, $maxAttempts = 45) {
        $attempts = 0;
        
        while ($attempts < $maxAttempts) {
            $response = $this->makeRequest('GET', "https://api.replicate.com/v1/predictions/" . $id);
            
            $status = $response['status'] ?? 'failed';
            
            if ($status === 'succeeded') {
                return [
                    'success' => true,
                    'image_url' => $response['output']
                ];
            } else if ($status === 'failed' || $status === 'canceled') {
                return [
                    'success' => false, 
                    'error' => 'Generation failed: ' . ($response['error'] ?? 'Unknown error')
                ];
            }

            // Wait 2 seconds before next poll
            sleep(2);
            $attempts++;
        }

        return ['success' => false, 'error' => 'Request timed out.'];
    }

    private function makeRequest($method, $url, $data = null) {
        $ch = curl_init($url);
        
        $headers = [
            'Authorization: Bearer ' . $this->apiToken, // Use Bearer for standard tokens
            'Content-Type: application/json',
            'Prefer: wait' // Ask Replicate to wait a bit before returning (reduces polling)
        ];

        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false); // Fix for some local setups
        
        if ($method === 'POST') {
            curl_setopt($ch, CURLOPT_POST, true);
            curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
        }

        $result = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $curlError = curl_error($ch);
        curl_close($ch);

        if ($curlError) {
            return ['error' => 'Connection Error: ' . $curlError];
        }

        $json = json_decode($result, true);
        
        if ($httpCode >= 400) {
            $detail = $json['detail'] ?? json_encode($json);
            return ['error' => 'HTTP ' . $httpCode . ': ' . $detail];
        }

        return $json;
    }

    private function fileToBase64($path) {
        if (!file_exists($path)) return false;
        $type = pathinfo($path, PATHINFO_EXTENSION);
        $data = file_get_contents($path);
        // Map jpg to jpeg for mime type
        if($type == 'jpg') $type = 'jpeg';
        return 'data:image/' . $type . ';base64,' . base64_encode($data);
    }

    public function downloadImage($url, $savePath) {
        $ch = curl_init($url);
        $fp = fopen($savePath, 'wb');
        if (!$fp) return false;

        curl_setopt($ch, CURLOPT_FILE, $fp);
        curl_setopt($ch, CURLOPT_HEADER, 0);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
        curl_setopt($ch, CURLOPT_TIMEOUT, 60);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        
        $success = curl_exec($ch);
        curl_close($ch);
        fclose($fp);
        return $success && file_exists($savePath);
    }
}
?>