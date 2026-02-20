<?php
/**
 * Virtual Try-On Handler (Updated for IDM-VTON)
 */
session_start();
header('Content-Type: application/json');

// Disable error reporting for cleaner JSON output
error_reporting(0);
ini_set('display_errors', 0);

require_once __DIR__ . '/includes/db.php';
require_once __DIR__ . '/includes/config.php';
require_once __DIR__ . '/includes/ZhipuAI.php'; // Includes ReplicateVTON class

$uploadDir = __DIR__ . '/uploads/tryon/';
$generatedDir = __DIR__ . '/uploads/generated/';

// Create directories with full permissions
if (!is_dir($uploadDir)) mkdir($uploadDir, 0777, true);
if (!is_dir($generatedDir)) mkdir($generatedDir, 0777, true);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    
    // 1. Validate Input
    $productId = $_POST['product_id'] ?? null;
    if (!$productId) die(json_encode(['success' => false, 'message' => 'Product ID missing']));

    // 2. Fetch Product
    try {
        $stmt = $pdo->prepare("SELECT name, description, category FROM products WHERE id = ?");
        $stmt->execute([$productId]);
        $product = $stmt->fetch(PDO::FETCH_ASSOC);
    } catch (Exception $e) {
        die(json_encode(['success' => false, 'message' => 'Database error: ' . $e->getMessage()]));
    }

    if (!$product) die(json_encode(['success' => false, 'message' => 'Product not found']));

    // 3. Handle User Image Upload
    if (!isset($_FILES['user_image']) || $_FILES['user_image']['error'] !== UPLOAD_ERR_OK) {
        die(json_encode(['success' => false, 'message' => 'Image upload failed. Error Code: ' . $_FILES['user_image']['error']]));
    }

    $ext = pathinfo($_FILES['user_image']['name'], PATHINFO_EXTENSION);
    $userFilename = 'user_' . time() . '.' . $ext;
    $userPath = $uploadDir . $userFilename;
    
    if (!move_uploaded_file($_FILES['user_image']['tmp_name'], $userPath)) {
        die(json_encode(['success' => false, 'message' => 'Failed to save image to server']));
    }

    // 4. Map Product to a Real Garment Image URL
    // These URLs must be publicly accessible and reliable
    $garmentUrl = getSampleGarmentImage($product['name'], $product['category']);

    // 5. Call Replicate IDM-VTON
    $replicate = new ReplicateVTON();
    
    // Determine category (upper_body, lower_body, dresses)
    $vtonCategory = 'upper_body'; // Default
    if (stripos($product['category'], 'pant') !== false || stripos($product['name'], 'jean') !== false) {
        $vtonCategory = 'lower_body';
    }
    
    $result = $replicate->runVirtualTryOn($userPath, $garmentUrl, $vtonCategory);

    if ($result['success']) {
        // Save the result locally
        $generatedFilename = 'vton_' . time() . '_' . $productId . '.png';
        $savePath = $generatedDir . $generatedFilename;
        
        // Try to download, otherwise return the remote URL
        if ($replicate->downloadImage($result['image_url'], $savePath)) {
            // Clean up user upload to save space
            @unlink($userPath);
            
            echo json_encode([
                'success' => true, 
                'image_url' => 'uploads/generated/' . $generatedFilename
            ]);
        } else {
            echo json_encode([
                'success' => true, 
                'image_url' => $result['image_url'],
                'note' => 'Local save failed, using remote URL'
            ]);
        }
    } else {
        echo json_encode(['success' => false, 'message' => $result['error']]);
    }
}

// Helper: Reliable Garment Images for AI Processing
function getSampleGarmentImage($name, $category) {
    $name = strtolower($name);
    
    // Use Wikimedia Commons or reliable CDNs to avoid hotlink blocking
    
    // Hoodies
    if (strpos($name, 'hoodie') !== false) {
        return "https://upload.wikimedia.org/wikipedia/commons/2/23/Blue_Hoodie.jpg"; 
    } 
    // Jackets
    elseif (strpos($name, 'jacket') !== false) {
        return "https://upload.wikimedia.org/wikipedia/commons/thumb/a/ac/A_black_bomber_jacket.jpg/640px-A_black_bomber_jacket.jpg";
    } 
    // T-Shirts (Tees)
    elseif (strpos($name, 'tee') !== false || strpos($name, 't-shirt') !== false) {
        // Simple Black Tee
        return "https://upload.wikimedia.org/wikipedia/commons/2/24/Blue_Tshirt.jpg"; 
    } 
    // Bags/Totes (Note: VTON handles clothes best, bags might be experimental)
    elseif (strpos($name, 'tote') !== false) {
        return "https://upload.wikimedia.org/wikipedia/commons/6/6b/Bag_%28clothing%29.jpg";
    }
    
    // Default fallback (Simple Shirt)
    return "https://upload.wikimedia.org/wikipedia/commons/2/24/Blue_Tshirt.jpg";
}
?>