<?php
/**
 * ZhipuAI Integration Class
 * Handles communication with Zhipu AI (BigModel) API for image generation
 * Used for virtual try-on feature
 */

class ZhipuAI {
    private $apiKey;
    private $baseUrl = 'https://open.bigmodel.cn/api/paas/v4';
    private $timeout = 120;

    public function __construct($apiKey = null) {
        // Use provided API key or get from environment/config
        $this->apiKey = $apiKey ?: getenv('ZHIPUAI_API_KEY') ?: '';
    }

    /**
     * Generate image using CogView-3 model
     * @param string $prompt - Text description of the image to generate
     * @param string $size - Image size (1024x1024, 768x768, etc.)
     * @return array - Response with image URL or error
     */
    public function generateImage($prompt, $size = '1024x1024') {
        $endpoint = $this->baseUrl . '/images/generations';

        $payload = [
            'model' => 'cogview-3-plus',
            'prompt' => $prompt,
            'size' => $size,
            'quality' => 'standard',
            'style' => 'natural'
        ];

        return $this->makeRequest('POST', $endpoint, $payload);
    }

    /**
     * Generate virtual try-on image
     * Since CogView is text-to-image, we construct a detailed prompt
     * describing the person wearing the clothing item
     *
     * @param string $userImagePath - Path to user's uploaded image (for reference)
     * @param array $productInfo - Product information (name, description, color, etc.)
     * @return array - Response with generated image URL or error
     */
    public function generateTryOnImage($userImagePath, $productInfo) {
        // Construct a detailed prompt for the virtual try-on
        $prompt = $this->buildTryOnPrompt($userImagePath, $productInfo);

        return $this->generateImage($prompt);
    }

    /**
     * Build detailed prompt for virtual try-on generation
     * @param string $userImagePath - User's image path (for metadata if available)
     * @param array $productInfo - Product details
     * @return string - Formatted prompt
     */
    private function buildTryOnPrompt($userImagePath, $productInfo) {
        $productName = $productInfo['name'] ?? 'clothing item';
        $productDesc = $productInfo['description'] ?? '';
        $color = $productInfo['color'] ?? '';
        $style = $productInfo['style'] ?? '';

        // Build detailed prompt for virtual try-on
        $prompt = "A photorealistic full-body portrait of a young person standing in a studio setting, ";
        $prompt .= "wearing a {$color} {$productName}. ";

        if (!empty($productDesc)) {
            $prompt .= "{$productDesc}. ";
        }

        if (!empty($style)) {
            $prompt .= "Style: {$style}. ";
        }

        $prompt .= "High quality, professional photography, natural lighting, ";
        $prompt .= "neutral gray background, retail fashion photography, ";
        $prompt .= "sharp focus, detailed, 8k quality, front-facing pose";

        return $prompt;
    }

    /**
     * Make HTTP request to Zhipu AI API
     * @param string $method - HTTP method (GET, POST, etc.)
     * @param string $endpoint - API endpoint
     * @param array $data - Request data
     * @return array - Response data
     */
    private function makeRequest($method, $endpoint, $data = []) {
        $ch = curl_init();

        $headers = [
            'Authorization: Bearer ' . $this->apiKey,
            'Content-Type: application/json'
        ];

        curl_setopt($ch, CURLOPT_URL, $endpoint);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_TIMEOUT, $this->timeout);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);

        if ($method === 'POST') {
            curl_setopt($ch, CURLOPT_POST, true);
            curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
        }

        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $error = curl_error($ch);

        curl_close($ch);

        if ($error) {
            return [
                'success' => false,
                'error' => 'cURL Error: ' . $error
            ];
        }

        $responseData = json_decode($response, true);

        if ($httpCode === 200 && isset($responseData['data'])) {
            return [
                'success' => true,
                'data' => $responseData['data']
            ];
        } else {
            return [
                'success' => false,
                'error' => isset($responseData['error']) ? $responseData['error']['message'] : 'API Error',
                'http_code' => $httpCode
            ];
        }
    }

    /**
     * Download image from URL and save to local file
     * @param string $url - Image URL
     * @param string $savePath - Local path to save image
     * @return bool - Success status
     */
    public function downloadImage($url, $savePath) {
        $ch = curl_init($url);
        $fp = fopen($savePath, 'wb');

        curl_setopt($ch, CURLOPT_FILE, $fp);
        curl_setopt($ch, CURLOPT_HEADER, 0);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
        curl_setopt($ch, CURLOPT_TIMEOUT, 60);

        $success = curl_exec($ch);
        curl_close($ch);
        fclose($fp);

        return $success && file_exists($savePath);
    }
}

/**
 * Alternative: Replicate API Integration (for better VTON results)
 * Uncomment and use if you have Replicate API key
 * Replicate has IDM-VTON which is specifically designed for virtual try-on
 */
class ReplicateVTON {
    private $apiToken;

    public function __construct($apiToken = null) {
        $this->apiToken = $apiToken ?: getenv('REPLICATE_API_TOKEN') ?: '';
    }

    /**
     * Run IDM-VTON model for virtual try-on
     * This provides much better results than text-to-image models
     *
     * @param string $personImagePath - Path to person's image
     * @param string $garmentImagePath - Path to garment image
     * @return array - Response with generated image or error
     */
    public function runVirtualTryOn($personImagePath, $garmentImagePath) {
        // Convert local images to base64 or upload to temp URL
        // For now, this is a placeholder - you'd need to implement
        // image upload to a public URL or base64 encoding

        $endpoint = 'https://api.replicate.com/v1/predictions';

        // This would use the IDM-VTON model
        // You'll need to implement the full workflow
        return ['success' => false, 'error' => 'Replicate integration not fully implemented'];
    }
}
