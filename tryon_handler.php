<?php
/**
 * Virtual Try-On Handler
 * Backend script to handle try-on requests
 * Handles file uploads and communicates with Zhipu AI API
 */

// Start session if not already started
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Set headers for JSON response
header('Content-Type: application/json');

// Include required files
require_once __DIR__ . '/includes/db.php';
require_once __DIR__ . '/includes/config.php';
require_once __DIR__ . '/includes/ZhipuAI.php';

// Configuration
$uploadDir = __DIR__ . '/uploads/tryon/';
$generatedDir = __DIR__ . '/uploads/generated/';

// Create directories if they don't exist
if (!is_dir($uploadDir)) {
    mkdir($uploadDir, 0777, true);
}
if (!is_dir($generatedDir)) {
    mkdir($generatedDir, 0777, true);
}

/**
 * Handle the try-on request
 */
function handleTryOnRequest($pdo) {
    global $uploadDir, $generatedDir;

    // Check if it's an AJAX request
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        return jsonResponse(false, 'Invalid request method');
    }

    // Get API key from config or environment
    // Priority: 1. Environment variable, 2. Config constant
    $apiKey = getenv('ZHIPUAI_API_KEY') ?: '';
    if (empty($apiKey) && defined('ZHIPUAI_API_KEY')) {
        $apiKey = ZHIPUAI_API_KEY;
    }

    // Validate product selection
    $productId = $_POST['product_id'] ?? null;
    if (!$productId) {
        return jsonResponse(false, 'Please select a product to try on');
    }

    // Get product information from database
    $product = getProductById($productId, $pdo);
    if (!$product) {
        return jsonResponse(false, 'Product not found');
    }

    // Handle file upload
    $userImagePath = null;
    if (isset($_FILES['user_image']) && $_FILES['user_image']['error'] === UPLOAD_ERR_OK) {
        $uploadResult = handleImageUpload($_FILES['user_image'], $uploadDir);
        if (!$uploadResult['success']) {
            return jsonResponse(false, $uploadResult['error']);
        }
        $userImagePath = $uploadResult['path'];
    } else {
        return jsonResponse(false, 'Please upload your photo');
    }

    // Prepare product info for API
    $productInfo = [
        'name' => $product['product_name'] ?? 'clothing',
        'description' => $product['product_description'] ?? '',
        'color' => extractColor($product['product_name'] . ' ' . $product['product_description']),
        'style' => 'modern casual'
    ];

    // Check if we have a valid API key
    if (empty($apiKey)) {
        // Demo mode - return a placeholder response
        // In production, this would actually call the API
        return handleDemoMode($product, $userImagePath, $generatedDir);
    }

    // Call Zhipu AI API
    try {
        $zhipuAI = new ZhipuAI($apiKey);

        $response = $zhipuAI->generateTryOnImage($userImagePath, $productInfo);

        if ($response['success'] && isset($response['data'][0]['url'])) {
            $imageUrl = $response['data'][0]['url'];

            // Download and save the generated image
            $filename = 'tryon_' . time() . '_' . $productId . '.png';
            $localPath = $generatedDir . $filename;

            if ($zhipuAI->downloadImage($imageUrl, $localPath)) {
                // Clean up temporary user image
                if (file_exists($userImagePath)) {
                    unlink($userImagePath);
                }

                return jsonResponse(true, 'Image generated successfully', [
                    'image_url' => 'uploads/generated/' . $filename,
                    'product_name' => $product['product_name']
                ]);
            } else {
                return jsonResponse(true, 'Image generated', [
                    'image_url' => $imageUrl,
                    'product_name' => $product['product_name']
                ]);
            }
        } else {
            return jsonResponse(false, $response['error'] ?? 'Failed to generate image');
        }
    } catch (Exception $e) {
        return jsonResponse(false, 'API Error: ' . $e->getMessage());
    }
}

/**
 * Handle image upload
 */
function handleImageUpload($file, $uploadDir) {
    // Validate file type
    $allowedTypes = ['image/jpeg', 'image/png', 'image/jpg', 'image/webp'];
    $fileType = mime_content_type($file['tmp_name']);

    if (!in_array($fileType, $allowedTypes)) {
        return ['success' => false, 'error' => 'Invalid file type. Please upload JPG, PNG, or WebP images.'];
    }

    // Validate file size (max 5MB)
    if ($file['size'] > 5 * 1024 * 1024) {
        return ['success' => false, 'error' => 'File too large. Maximum size is 5MB.'];
    }

    // Generate unique filename
    $extension = pathinfo($file['name'], PATHINFO_EXTENSION);
    $filename = 'user_' . time() . '_' . bin2hex(random_bytes(4)) . '.' . $extension;
    $targetPath = $uploadDir . $filename;

    // Move uploaded file
    if (move_uploaded_file($file['tmp_name'], $targetPath)) {
        return ['success' => true, 'path' => $targetPath];
    } else {
        return ['success' => false, 'error' => 'Failed to upload image'];
    }
}

/**
 * Extract color from product name/description
 */
function extractColor($text) {
    $colors = ['red', 'blue', 'green', 'black', 'white', 'yellow', 'orange', 'purple', 'pink', 'gray', 'grey', 'navy', 'brown', 'beige', 'cream'];

    $textLower = strtolower($text);
    foreach ($colors as $color) {
        if (strpos($textLower, $color) !== false) {
            return $color;
        }
    }

    return ''; // Return empty if no color found
}

/**
 * Handle demo mode (when no API key is configured)
 */
function handleDemoMode($product, $userImagePath, $generatedDir) {
    // In demo mode, we'll use a placeholder approach
    // Copy the user image as a "result" for demonstration
    $filename = 'tryon_' . time() . '_' . ($product['id'] ?? 'demo') . '.png';
    $resultPath = $generatedDir . $filename;

    // Copy the uploaded image as demo result
    if (copy($userImagePath, $resultPath)) {
        return jsonResponse(true, 'Demo mode: Image processed (API key not configured)', [
            'image_url' => 'uploads/generated/' . $filename,
            'product_name' => $product['product_name'],
            'demo_mode' => true,
            'message' => 'Configure ZHIPUAI_API_KEY in tryon_handler.php for real AI generation'
        ]);
    } else {
        return jsonResponse(false, 'Demo mode failed');
    }
}

/**
 * Helper function to return JSON response
 */
function jsonResponse($success, $message, $data = []) {
    echo json_encode(array_merge([
        'success' => $success,
        'message' => $message
    ], $data));
    exit;
}

/**
 * Get available products for try-on
 */
function getTryOnProducts($pdo) {
    try {
        $stmt = $pdo->query("
            SELECT id, product_name, product_price, product_image, product_description
            FROM products
            ORDER BY id DESC
            LIMIT 20
        ");
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    } catch (PDOException $e) {
        return [];
    }
}

// Handle the request based on action
$action = $_POST['action'] ?? 'tryon';

switch ($action) {
    case 'tryon':
        echo json_encode(handleTryOnRequest($pdo));
        break;

    case 'get_products':
        $products = getTryOnProducts($pdo);
        echo json_encode(['success' => true, 'products' => $products]);
        break;

    default:
        echo json_encode(['success' => false, 'error' => 'Unknown action']);
}
