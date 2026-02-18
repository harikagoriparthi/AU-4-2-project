<?php
$pageTitle = "AU | VIRTUAL_FIT_LAB";
include 'includes/header.php';

// Get products for the try-on selector
require_once __DIR__ . '/includes/db.php';
try {
    $stmt = $pdo->query("SELECT * FROM products ORDER BY id DESC LIMIT 20");
    $products = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (Exception $e) {
    $products = [];
}
?>

<style>
    body {
        background-color: #050505;
        color: #00f3ff;
        overflow-x: hidden;
    }

    .navbar {
        background: #000;
        border-bottom: 1px solid #333;
    }

    .nav-links a {
        color: #fff;
    }

    .logo {
        color: #fff;
    }

    .crt::before {
        content: " ";
        display: block;
        position: absolute;
        top: 0;
        left: 0;
        bottom: 0;
        right: 0;
        background: linear-gradient(rgba(18, 16, 16, 0) 50%, rgba(0, 0, 0, 0.25) 50%),
                    linear-gradient(90deg, rgba(255, 0, 0, 0.06), rgba(0, 255, 0, 0.02), rgba(0, 0, 255, 0.06));
        z-index: 2;
        background-size: 100% 2px, 3px 100%;
        pointer-events: none;
    }

    .lab-container {
        display: grid;
        grid-template-columns: 320px 1fr 320px;
        min-height: calc(100vh - 150px);
        gap: 0;
    }

    .panel {
        border-right: 1px solid #333;
        padding: 2rem;
        font-family: var(--font-tech);
        font-size: 0.8rem;
        z-index: 5;
        background: #0a0a0a;
        overflow-y: auto;
        max-height: calc(100vh - 150px);
    }

    .panel-right {
        border-left: 1px solid #333;
        border-right: none;
    }

    .panel-center {
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        background: radial-gradient(circle at center, #111 0%, #000 100%);
        position: relative;
        padding: 2rem;
    }

    .panel-title {
        color: #fff;
        margin-bottom: 1.5rem;
        font-family: var(--font-tech);
        font-size: 0.9rem;
        letter-spacing: 2px;
    }

    /* Garment Selector */
    .garment-list {
        list-style: none;
        padding: 0;
        margin: 0;
    }

    .garment-item {
        padding: 15px;
        border: 1px solid #333;
        margin-bottom: 10px;
        transition: all 0.3s ease;
        cursor: pointer;
        display: flex;
        align-items: center;
        gap: 12px;
    }

    .garment-item:hover {
        background: rgba(0, 243, 255, 0.1);
        border-color: #00f3ff;
        color: #fff;
    }

    .garment-item.selected {
        background: rgba(0, 243, 255, 0.15);
        border-color: #00f3ff;
        box-shadow: 0 0 15px rgba(0, 243, 255, 0.3);
    }

    .garment-thumb {
        width: 50px;
        height: 50px;
        background: #222;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.5rem;
    }

    .garment-info {
        flex: 1;
    }

    .garment-name {
        color: #fff;
        font-weight: bold;
        margin-bottom: 4px;
    }

    .garment-price {
        color: #00f3ff;
        font-size: 0.75rem;
    }

    /* Upload Area */
    .upload-area {
        width: 100%;
        max-width: 400px;
        aspect-ratio: 3/4;
        border: 2px dashed #333;
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        cursor: pointer;
        transition: all 0.3s ease;
        position: relative;
        overflow: hidden;
        background: #0a0a0a;
    }

    .upload-area:hover {
        border-color: #00f3ff;
        background: rgba(0, 243, 255, 0.05);
    }

    .upload-area.has-image {
        border-style: solid;
        border-color: #00f3ff;
    }

    .upload-area input[type="file"] {
        position: absolute;
        width: 100%;
        height: 100%;
        opacity: 0;
        cursor: pointer;
    }

    .upload-preview {
        width: 100%;
        height: 100%;
        object-fit: cover;
        display: none;
    }

    .upload-area.has-image .upload-preview {
        display: block;
    }

    .upload-icon {
        font-size: 3rem;
        color: #333;
        margin-bottom: 1rem;
    }

    .upload-text {
        color: #666;
        text-align: center;
        font-size: 0.8rem;
    }

    .upload-text span {
        color: #00f3ff;
    }

    /* Scan Animation */
    @keyframes scan {
        0% {
            top: 0%;
        }
        100% {
            top: 100%;
        }
    }

    .scan-line {
        position: absolute;
        width: 100%;
        height: 2px;
        background: #00f3ff;
        box-shadow: 0 0 15px #00f3ff;
        animation: scan 2s infinite alternate;
        display: none;
        z-index: 10;
    }

    .upload-area.scanning .scan-line {
        display: block;
    }

    /* Try On Button */
    .tryon-btn {
        width: 100%;
        max-width: 400px;
        padding: 18px 30px;
        margin-top: 2rem;
        background: transparent;
        border: 2px solid #00f3ff;
        color: #00f3ff;
        font-family: var(--font-tech);
        font-size: 1rem;
        font-weight: bold;
        text-transform: uppercase;
        letter-spacing: 3px;
        cursor: pointer;
        transition: all 0.3s ease;
        position: relative;
        overflow: hidden;
    }

    .tryon-btn:hover:not(:disabled) {
        background: #00f3ff;
        color: #000;
        box-shadow: 0 0 30px rgba(0, 243, 255, 0.5);
    }

    .tryon-btn:disabled {
        opacity: 0.4;
        cursor: not-allowed;
    }

    .tryon-btn.processing {
        pointer-events: none;
    }

    /* Status Bar */
    .status-container {
        margin-top: 2rem;
        width: 100%;
        max-width: 400px;
    }

    .status-text {
        color: #666;
        margin-bottom: 0.5rem;
        font-size: 0.75rem;
    }

    .status-bar {
        height: 4px;
        background: #222;
        width: 100%;
        position: relative;
        overflow: hidden;
    }

    .status-fill {
        height: 100%;
        background: #00f3ff;
        width: 0%;
        box-shadow: 0 0 10px #00f3ff;
        transition: width 0.3s ease;
    }

    .status-fill.processing {
        animation: processing 1.5s infinite;
    }

    @keyframes processing {
        0%, 100% {
            width: 0%;
            left: 0;
        }
        50% {
            width: 100%;
            left: 0;
        }
    }

    /* Result Display */
    .result-container {
        width: 100%;
        max-width: 400px;
        margin-top: 2rem;
        display: none;
    }

    .result-container.show {
        display: block;
        animation: fadeIn 0.5s ease;
    }

    @keyframes fadeIn {
        from {
            opacity: 0;
            transform: translateY(20px);
        }
        to {
            opacity: 1;
            transform: translateY(0);
        }
    }

    .result-image {
        width: 100%;
        border: 2px solid #00f3ff;
        box-shadow: 0 0 30px rgba(0, 243, 255, 0.3);
    }

    .result-info {
        margin-top: 1rem;
        text-align: center;
        color: #fff;
    }

    .result-actions {
        display: flex;
        gap: 1rem;
        margin-top: 1rem;
    }

    .result-btn {
        flex: 1;
        padding: 12px;
        background: transparent;
        border: 1px solid #333;
        color: #fff;
        font-family: var(--font-tech);
        font-size: 0.75rem;
        cursor: pointer;
        transition: all 0.3s ease;
        text-transform: uppercase;
    }

    .result-btn:hover {
        border-color: #00f3ff;
        color: #00f3ff;
    }

    /* Fit Metrics Panel */
    .metric-item {
        margin-bottom: 1.5rem;
    }

    .metric-label {
        color: #666;
        margin-bottom: 0.5rem;
        font-size: 0.75rem;
        display: flex;
        justify-content: space-between;
    }

    .metric-value {
        color: #fff;
    }

    .metric-bar {
        height: 6px;
        background: #222;
        position: relative;
    }

    .metric-fill {
        height: 100%;
        background: linear-gradient(90deg, #00f3ff, #00ff88);
        position: absolute;
        left: 0;
        top: 0;
    }

    /* System Status */
    .system-status {
        margin-top: 2rem;
        padding-top: 2rem;
        border-top: 1px solid #222;
    }

    .sys-item {
        display: flex;
        justify-content: space-between;
        margin-bottom: 0.5rem;
        color: #444;
        font-size: 0.7rem;
    }

    .sys-item.active {
        color: #00ff88;
    }

    /* Error/Success Messages */
    .message-box {
        width: 100%;
        max-width: 400px;
        padding: 1rem;
        margin-top: 1rem;
        display: none;
    }

    .message-box.show {
        display: block;
    }

    .message-box.error {
        background: rgba(255, 0, 0, 0.1);
        border: 1px solid #ff3333;
        color: #ff3333;
    }

    .message-box.success {
        background: rgba(0, 255, 136, 0.1);
        border: 1px solid #00ff88;
        color: #00ff88;
    }

    /* Responsive */
    @media (max-width: 1024px) {
        .lab-container {
            grid-template-columns: 1fr;
            grid-template-rows: auto auto auto;
        }

        .panel {
            max-height: none;
            border-right: none;
            border-bottom: 1px solid #333;
        }

        .panel-right {
            border-left: none;
        }
    }
</style>
</head>

<body class="crt">

    <!-- Custom Cursor Elements -->
    <div class="cursor-dot"></div>
    <div class="cursor-outline"></div>

    <nav class="navbar">
        <div class="logo glitch" data-text="VIRTUAL_FIT_LAB">VIRTUAL_FIT_LAB v2.0</div>
        <ul class="nav-links">
            <li><a href="shop.php">RETURN TO SHOP</a></li>
        </ul>
    </nav>

    <div class="lab-container">
        <!-- Left Panel: Product Selection -->
        <div class="panel">
            <h3 class="panel-title">&gt; SELECT_ITEM</h3>

            <?php if (empty($products)): ?>
                <div style="color: #666; text-align: center; padding: 2rem;">
                    <p style="margin-bottom: 1rem;">No products available</p>
                    <a href="shop.php" class="btn" style="font-size: 0.7rem; padding: 10px 20px;">Browse Shop</a>
                </div>
            <?php else: ?>
                <ul class="garment-list" id="garmentList">
                    <?php foreach ($products as $index => $product): ?>
                        <li class="garment-item" data-id="<?php echo $product['id']; ?>"
                            data-name="<?php echo htmlspecialchars($product['product_name']); ?>"
                            data-desc="<?php echo htmlspecialchars($product['product_description'] ?? ''); ?>">
                            <div class="garment-thumb">
                                <?php
                                // Display first letter or icon based on product type
                                $icon = '👕';
                                if (stripos($product['product_name'], 'cap') !== false || stripos($product['product_name'], 'hat') !== false) {
                                    $icon = '🧢';
                                } elseif (stripos($product['product_name'], 'hoodie') !== false) {
                                    $icon = '🧥';
                                } elseif (stripos($product['product_name'], 'pant') !== false || stripos($product['product_name'], 'jean') !== false) {
                                    $icon = '👖';
                                }
                                echo $icon;
                                ?>
                            </div>
                            <div class="garment-info">
                                <div class="garment-name"><?php echo htmlspecialchars($product['product_name']); ?></div>
                                <div class="garment-price">₹<?php echo number_format($product['product_price']); ?></div>
                            </div>
                        </li>
                    <?php endforeach; ?>
                </ul>
            <?php endif; ?>

            <div class="system-status">
                <div class="sys-item active">
                    <span>&gt; SYSTEM_READY</span>
                    <span>OK</span>
                </div>
                <div class="sys-item active">
                    <span>&gt; AI_ENGINE</span>
                    <span>ONLINE</span>
                </div>
                <div class="sys-item">
                    <span>&gt; API_STATUS</span>
                    <span><?php echo defined('ZHIPUAI_API_KEY') ? 'CONNECTED' : 'DEMO MODE'; ?></span>
                </div>
            </div>
        </div>

        <!-- Center Panel: Upload & Result -->
        <div class="panel-center">
            <!-- Upload Area -->
            <div class="upload-area" id="uploadArea">
                <div class="scan-line"></div>
                <input type="file" id="userImageInput" accept="image/jpeg,image/png,image/jpg,image/webp">

                <img src="" alt="Preview" class="upload-preview" id="uploadPreview">

                <div class="upload-placeholder" id="uploadPlaceholder">
                    <div class="upload-icon">📸</div>
                    <div class="upload-text">
                        <span>DRAG & DROP</span><br>
                        or click to browse<br>
                        <small style="color: #444;">JPG, PNG, WebP - Max 5MB</small>
                    </div>
                </div>
            </div>

            <!-- Try On Button -->
            <button class="tryon-btn" id="tryOnBtn" disabled>
                &gt; INITIATE_VIRTUAL_TRY_ON
            </button>

            <!-- Message Box -->
            <div class="message-box" id="messageBox"></div>

            <!-- Processing Status -->
            <div class="status-container" id="statusContainer" style="display: none;">
                <div class="status-text" id="statusText">PROCESSING...</div>
                <div class="status-bar">
                    <div class="status-fill" id="statusFill"></div>
                </div>
            </div>

            <!-- Result Display -->
            <div class="result-container" id="resultContainer">
                <img src="" alt="Try On Result" class="result-image" id="resultImage">
                <div class="result-info" id="resultInfo"></div>
                <div class="result-actions">
                    <button class="result-btn" id="tryAgainBtn">TRY AGAIN</button>
                    <button class="result-btn" id="downloadBtn">DOWNLOAD</button>
                </div>
            </div>
        </div>

        <!-- Right Panel: Fit Metrics -->
        <div class="panel panel-right">
            <h3 class="panel-title">&gt; FIT_METRICS</h3>

            <div class="metric-item">
                <div class="metric-label">
                    <span>SHOULDER WIDTH</span>
                    <span class="metric-value" id="shoulderValue">--</span>
                </div>
                <div class="metric-bar">
                    <div class="metric-fill" id="shoulderFill" style="width: 0%"></div>
                </div>
            </div>

            <div class="metric-item">
                <div class="metric-label">
                    <span>CHEST CIRCUMFERENCE</span>
                    <span class="metric-value" id="chestValue">--</span>
                </div>
                <div class="metric-bar">
                    <div class="metric-fill" id="chestFill" style="width: 0%"></div>
                </div>
            </div>

            <div class="metric-item">
                <div class="metric-label">
                    <span>TORSO LENGTH</span>
                    <span class="metric-value" id="torsoValue">--</span>
                </div>
                <div class="metric-bar">
                    <div class="metric-fill" id="torsoFill" style="width: 0%"></div>
                </div>
            </div>

            <div class="metric-item">
                <div class="metric-label">
                    <span>AI CONFIDENCE</span>
                    <span class="metric-value" id="confidenceValue">--</span>
                </div>
                <div class="metric-bar">
                    <div class="metric-fill" id="confidenceFill" style="width: 0%"></div>
                </div>
            </div>

            <button class="btn" id="renderBtn" style="border-color: #00f3ff; color: #00f3ff; width: 100%; margin-top: 2rem;">
                &gt; RENDER_PREVIEW
            </button>

            <div style="margin-top: 2rem; padding: 1rem; background: rgba(0,243,255,0.05); border: 1px solid #333;">
                <p style="color: #666; font-size: 0.7rem; line-height: 1.6;">
                    <strong style="color: #00f3ff;">TIP:</strong><br>
                    For best results, use a full-body photo with good lighting and a neutral background.
                </p>
            </div>
        </div>
    </div>

    <script>
        // Virtual Try-On JavaScript
        document.addEventListener('DOMContentLoaded', function() {
            // Elements
            const uploadArea = document.getElementById('uploadArea');
            const userImageInput = document.getElementById('userImageInput');
            const uploadPreview = document.getElementById('uploadPreview');
            const uploadPlaceholder = document.getElementById('uploadPlaceholder');
            const tryOnBtn = document.getElementById('tryOnBtn');
            const messageBox = document.getElementById('messageBox');
            const statusContainer = document.getElementById('statusContainer');
            const statusText = document.getElementById('statusText');
            const statusFill = document.getElementById('statusFill');
            const resultContainer = document.getElementById('resultContainer');
            const resultImage = document.getElementById('resultImage');
            const resultInfo = document.getElementById('resultInfo');
            const garmentItems = document.querySelectorAll('.garment-item');
            const tryAgainBtn = document.getElementById('tryAgainBtn');
            const downloadBtn = document.getElementById('downloadBtn');
            const renderBtn = document.getElementById('renderBtn');

            // State
            let selectedProductId = null;
            let selectedProductName = '';
            let userImageFile = null;

            // Cursor handling
            const cursorDot = document.querySelector(".cursor-dot");
            const cursorOutline = document.querySelector(".cursor-outline");

            if (cursorDot && cursorOutline) {
                window.addEventListener('mousemove', function(e) {
                    const posX = e.clientX;
                    const posY = e.clientY;
                    cursorDot.style.left = `${posX}px`;
                    cursorDot.style.top = `${posY}px`;
                    cursorOutline.animate({
                        left: `${posX}px`,
                        top: `${posY}px`
                    }, {
                        duration: 500,
                        fill: "forwards"
                    });
                });
            }

            // Image upload handling
            userImageInput.addEventListener('change', function(e) {
                const file = e.target.files[0];
                if (file) {
                    handleImageUpload(file);
                }
            });

            // Drag and drop
            uploadArea.addEventListener('dragover', function(e) {
                e.preventDefault();
                uploadArea.style.borderColor = '#00f3ff';
            });

            uploadArea.addEventListener('dragleave', function(e) {
                e.preventDefault();
                uploadArea.style.borderColor = '#333';
            });

            uploadArea.addEventListener('drop', function(e) {
                e.preventDefault();
                uploadArea.style.borderColor = '#333';

                const file = e.dataTransfer.files[0];
                if (file && file.type.startsWith('image/')) {
                    handleImageUpload(file);
                }
            });

            function handleImageUpload(file) {
                // Validate file
                const validTypes = ['image/jpeg', 'image/jpg', 'image/png', 'image/webp'];
                if (!validTypes.includes(file.type)) {
                    showMessage('Please upload a valid image (JPG, PNG, WebP)', 'error');
                    return;
                }

                if (file.size > 5 * 1024 * 1024) {
                    showMessage('File too large. Maximum size is 5MB', 'error');
                    return;
                }

                userImageFile = file;

                // Preview
                const reader = new FileReader();
                reader.onload = function(e) {
                    uploadPreview.src = e.target.result;
                    uploadArea.classList.add('has-image');
                    uploadPlaceholder.style.display = 'none';
                    checkTryOnReady();
                };
                reader.readAsDataURL(file);
            }

            // Product selection
            garmentItems.forEach(item => {
                item.addEventListener('click', function() {
                    // Deselect all
                    garmentItems.forEach(i => i.classList.remove('selected'));

                    // Select this one
                    this.classList.add('selected');
                    selectedProductId = this.dataset.id;
                    selectedProductName = this.dataset.name;

                    checkTryOnReady();
                });
            });

            // Check if ready for try-on
            function checkTryOnReady() {
                if (selectedProductId && userImageFile) {
                    tryOnBtn.disabled = false;
                } else {
                    tryOnBtn.disabled = true;
                }
            }

            // Try On Button
            tryOnBtn.addEventListener('click', async function() {
                if (!selectedProductId || !userImageFile) return;

                // Show processing state
                setProcessingState(true);
                hideMessage();
                resultContainer.classList.remove('show');

                // Create FormData
                const formData = new FormData();
                formData.append('action', 'tryon');
                formData.append('product_id', selectedProductId);
                formData.append('user_image', userImageFile);

                try {
                    const response = await fetch('tryon_handler.php', {
                        method: 'POST',
                        body: formData
                    });

                    const data = await response.json();

                    if (data.success) {
                        showResult(data.image_url, data.product_name);

                        // Update metrics with random values for demo
                        updateMetrics();

                        if (data.demo_mode) {
                            showMessage('Demo Mode: ' + data.message, 'success');
                        }
                    } else {
                        showMessage(data.message || 'Failed to generate image', 'error');
                    }
                } catch (error) {
                    showMessage('Error: ' + error.message, 'error');
                } finally {
                    setProcessingState(false);
                }
            });

            // Set processing state
            function setProcessingState(processing) {
                if (processing) {
                    tryOnBtn.classList.add('processing');
                    tryOnBtn.textContent = '> PROCESSING...';
                    statusContainer.style.display = 'block';
                    statusFill.classList.add('processing');
                    uploadArea.classList.add('scanning');
                } else {
                    tryOnBtn.classList.remove('processing');
                    tryOnBtn.textContent = '> INITIATE_VIRTUAL_TRY_ON';
                    statusContainer.style.display = 'none';
                    statusFill.classList.remove('processing');
                    uploadArea.classList.remove('scanning');
                }
            }

            // Show result
            function showResult(imageUrl, productName) {
                resultImage.src = imageUrl;
                resultInfo.innerHTML = `<strong>${productName}</strong><br><span style="color:#666;font-size:0.8rem;">Virtual Try-On Complete</span>`;
                resultContainer.classList.add('show');
            }

            // Update fit metrics
            function updateMetrics() {
                // Random values for demo
                const shoulder = Math.floor(Math.random() * 20) + 40;
                const chest = Math.floor(Math.random() * 20) + 85;
                const torso = Math.floor(Math.random() * 15) + 55;
                const confidence = Math.floor(Math.random() * 15) + 80;

                document.getElementById('shoulderValue').textContent = shoulder + 'cm';
                document.getElementById('chestValue').textContent = chest + 'cm';
                document.getElementById('torsoValue').textContent = torso + 'cm';
                document.getElementById('confidenceValue').textContent = confidence + '%';

                document.getElementById('shoulderFill').style.width = (shoulder / 70 * 100) + '%';
                document.getElementById('chestFill').style.width = (chest / 110 * 100) + '%';
                document.getElementById('torsoFill').style.width = (torso / 80 * 100) + '%';
                document.getElementById('confidenceFill').style.width = confidence + '%';
            }

            // Show message
            function showMessage(message, type) {
                messageBox.textContent = message;
                messageBox.className = 'message-box show ' + type;
            }

            // Hide message
            function hideMessage() {
                messageBox.classList.remove('show');
            }

            // Try Again
            tryAgainBtn.addEventListener('click', function() {
                resultContainer.classList.remove('show');
                userImageFile = null;
                uploadPreview.src = '';
                uploadArea.classList.remove('has-image');
                uploadPlaceholder.style.display = 'flex';

                garmentItems.forEach(i => i.classList.remove('selected'));
                selectedProductId = null;
                selectedProductName = '';

                checkTryOnReady();

                // Reset metrics
                document.getElementById('shoulderValue').textContent = '--';
                document.getElementById('chestValue').textContent = '--';
                document.getElementById('torsoValue').textContent = '--';
                document.getElementById('confidenceValue').textContent = '--';
                document.getElementById('shoulderFill').style.width = '0%';
                document.getElementById('chestFill').style.width = '0%';
                document.getElementById('torsoFill').style.width = '0%';
                document.getElementById('confidenceFill').style.width = '0%';
            });

            // Download
            downloadBtn.addEventListener('click', function() {
                if (resultImage.src) {
                    const link = document.createElement('a');
                    link.href = resultImage.src;
                    link.download = 'virtual-tryon-' + Date.now() + '.jpg';
                    link.click();
                }
            });

            // Render Preview Button (demo)
            renderBtn.addEventListener('click', function() {
                if (!selectedProductId) {
                    showMessage('Please select a product first', 'error');
                    return;
                }
                showMessage('Click "INITIATE_VIRTUAL_TRY_ON" to generate', 'success');
            });
        });
    </script>
</body>
</html>
