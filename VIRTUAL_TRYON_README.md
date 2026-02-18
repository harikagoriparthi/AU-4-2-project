# Virtual Try-On Implementation Guide

## Overview
This document explains how to set up and use the Virtual Try-On feature in your AU Heritage e-commerce project.

## Files Created/Modified

### New Files
1. **`includes/ZhipuAI.php`** - Zhipu AI API integration class
2. **`includes/config.php`** - Configuration file for API keys
3. **`tryon_handler.php`** - Backend handler for try-on requests
4. **`uploads/tryon/`** - Directory for user uploaded images
5. **`uploads/generated/`** - Directory for generated images

### Modified Files
1. **`tryon.php`** - Completely redesigned with full functionality

---

## Important: API Configuration

### Option 1: Zhipu AI (GLM/CogView)
- **Status**: Basic integration complete
- **Limitation**: CogView is primarily a text-to-image model, NOT a true virtual try-on model
- **Result**: It generates images from text prompts, NOT by overlaying clothes on your uploaded photo
- **Get API Key**: https://open.bigmodel.cn/

**To enable Zhipu AI:**
```php
// In includes/config.php
define('ZHIPUAI_API_KEY', 'your-api-key-here');
```

### Option 2: Replicate API (RECOMMENDED for Better Results)
**This is strongly recommended** - Replicate has **IDM-VTON**, which is specifically designed for virtual try-on and produces much better results.

1. Sign up at https://replicate.com/
2. Get your API token from your account settings
3. Update `includes/config.php`:
```php
define('REPLICATE_API_TOKEN', 'your-replicate-token');
```

---

## How to Run Without API Key (Demo Mode)

The system works in **demo mode** without an API key:
- Upload a photo
- Select a product
- Click "INITIATE_VIRTUAL_TRY_ON"
- Your uploaded image will be shown as the result (for UI testing)

---

## Setup Instructions

### 1. Database Setup
Ensure your database has products. The virtual try-on pulls products from the `products` table.

```sql
-- Check if products table exists and has data
SELECT * FROM products LIMIT 10;
```

### 2. Configure API Key
Edit `includes/config.php`:
```php
<?php
// Add your Zhipu AI API key
define('ZHIPUAI_API_KEY', 'your-api-key-here');
```

### 3. Test the Feature
1. Start your XAMPP/Apache server
2. Navigate to http://localhost/your-project/tryon.php
3. Upload a photo
4. Select a product
5. Click the try-on button

---

## User Flow

1. **Navigate to Virtual Try-On**: Click "Lab" in the navigation
2. **Upload Your Photo**: Drag & drop or click to upload a JPG/PNG image (max 5MB)
3. **Select a Product**: Click on a clothing item from the left panel
4. **Try On**: Click the "INITIATE_VIRTUAL_TRY_ON" button
5. **View Result**: The generated image appears in the center
6. **Download**: Click "DOWNLOAD" to save the result

---

## Technical Details

### API Integration Flow
1. User uploads photo → Saved to `uploads/tryon/`
2. User selects product → Product info fetched from database
3. PHP backend constructs prompt using product details
4. Backend calls Zhipu AI API (CogView-3)
5. Generated image saved to `uploads/generated/`
6. Frontend displays result

### Current Limitations
- **Zhipu AI**: Text-to-image generation, not true VTON
- **Demo Mode**: Without API key, copies uploaded image as result
- **Image Quality**: Results depend on AI model capabilities

---

## Troubleshooting

### "No products available"
- Check your database connection in `includes/db.php`
- Ensure the `products` table has data

### "Please upload your photo"
- Make sure you're uploading a valid image (JPG, PNG, WebP)
- Check file size is under 5MB

### API Errors
- Verify API key is correct
- Check API credits/balance
- Check network connectivity

---

## For Better Virtual Try-On Results

Consider integrating **Replicate's IDM-VTON** model which is specifically designed for virtual try-on. This would require:
1. Replicate API token
2. Modifying `tryon_handler.php` to use the ReplicateVTON class
3. Using the IDM-VTON model endpoint

Contact me if you need help implementing the Replicate integration for better results!
sss
s
