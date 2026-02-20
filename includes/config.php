<?php
/**
 * Configuration file for Virtual Try-On
 */

// Zhipu AI API Key (Legacy/Fallback)
define('ZHIPUAI_API_KEY', '');

// Replicate API Token for IDM-VTON (Primary)
define('REPLICATE_API_TOKEN', '');

// Maximum file size for uploads (in bytes)
define('MAX_UPLOAD_SIZE', 5 * 1024 * 1024); // 5MB

// Allowed image types
define('ALLOWED_IMAGE_TYPES', ['image/jpeg', 'image/jpg', 'image/png', 'image/webp']);
?>