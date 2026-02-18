<?php
/**
 * Configuration file for Virtual Try-On
 * Add your API keys here
 */

// Zhipu AI API Key
// Get your API key from: https://open.bigmodel.cn/
// Define your API key here (or use environment variable)
define('ZHIPUAI_API_KEY', '');

// Alternative: Use Replicate for better VTON results
// Get your API token from: https://replicate.com/
// Replicate's IDM-VTON model provides much better virtual try-on results
define('REPLICATE_API_TOKEN', '');

/**
 * IMPORTANT NOTES FOR VIRTUAL TRY-ON:
 *
 * 1. ZHIPU AI (GLM/CogView):
 *    - CogView is primarily a text-to-image model
 *    - It doesn't have true virtual try-on (keeping user's face/body)
 *    - It generates images from scratch based on text prompts
 *    - Results may not preserve user's identity
 *
 * 2. FOR BETTER RESULTS - Use Replicate (IDM-VTON):
 *    - IDM-VTON is specifically designed for virtual try-on
 *    - It preserves the user's face and body while changing clothes
 *    - Sign up at https://replicate.com/ to get an API token
 *
 * 3. Demo Mode:
 *    - Without API key, the system runs in demo mode
 *    - It will copy your uploaded image as the "result"
 *    - This is useful for testing the UI
 *
 * 4. To Get Zhipu AI API Key:
 *    - Visit https://open.bigmodel.cn/
 *    - Create an account
 *    - Go to API Keys section
 *    - Create a new API key
 *    - Add credits to your account
 */

// Maximum file size for uploads (in bytes)
define('MAX_UPLOAD_SIZE', 5 * 1024 * 1024); // 5MB

// Allowed image types
define('ALLOWED_IMAGE_TYPES', ['image/jpeg', 'image/jpg', 'image/png', 'image/webp']);
