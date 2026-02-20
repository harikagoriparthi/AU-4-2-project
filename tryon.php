<?php
$pageTitle = "AU | VIRTUAL_FIT_LAB";
include 'includes/header.php';
require_once __DIR__ . '/includes/db.php';

// Get products from DB - Fixed column selection to match your DB schema
try {
    $stmt = $pdo->query("SELECT id, name, price, description, image_bg_color FROM products ORDER BY id ASC LIMIT 20");
    $products = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (Exception $e) {
    $products = [];
}
?>

<style>
    body { background-color: #050505; color: #00f3ff; overflow-x: hidden; }
    .navbar { background: #000; border-bottom: 1px solid #333; }
    .nav-links a, .logo { color: #fff; }
    
    /* CRT Effect Overlay */
    .crt::before {
        content: " "; display: block; position: absolute; top: 0; left: 0; bottom: 0; right: 0;
        background: linear-gradient(rgba(18, 16, 16, 0) 50%, rgba(0, 0, 0, 0.25) 50%),
                    linear-gradient(90deg, rgba(255, 0, 0, 0.06), rgba(0, 255, 0, 0.02), rgba(0, 0, 255, 0.06));
        z-index: 2; background-size: 100% 2px, 3px 100%; pointer-events: none;
    }

    .lab-container { display: grid; grid-template-columns: 320px 1fr 320px; min-height: calc(100vh - 100px); }
    .panel { border-right: 1px solid #333; padding: 2rem; background: #0a0a0a; overflow-y: auto; max-height: calc(100vh - 100px); position: relative; z-index: 5; }
    .panel-right { border-left: 1px solid #333; border-right: none; }
    .panel-center { display: flex; flex-direction: column; align-items: center; justify-content: center; background: radial-gradient(circle at center, #111 0%, #000 100%); padding: 2rem; }

    /* Items */
    .garment-item { padding: 15px; border: 1px solid #333; margin-bottom: 10px; cursor: pointer; display: flex; gap: 12px; transition: 0.3s; }
    .garment-item:hover, .garment-item.selected { border-color: #00f3ff; background: rgba(0, 243, 255, 0.1); }
    .garment-thumb { width: 40px; height: 40px; background: #222; display: flex; align-items: center; justify-content: center; font-size: 1.2rem; }

    /* Upload */
    .upload-area { width: 100%; max-width: 400px; aspect-ratio: 3/4; border: 2px dashed #333; display: flex; flex-direction: column; align-items: center; justify-content: center; position: relative; background: #0a0a0a; overflow: hidden; }
    .upload-area.has-image { border-style: solid; border-color: #00f3ff; }
    .upload-preview { width: 100%; height: 100%; object-fit: cover; display: none; }
    .upload-area.has-image .upload-preview { display: block; }
    .upload-area input { position: absolute; width: 100%; height: 100%; opacity: 0; cursor: pointer; }

    /* Button */
    .tryon-btn { width: 100%; max-width: 400px; padding: 18px; margin-top: 2rem; background: transparent; border: 2px solid #00f3ff; color: #00f3ff; font-family: var(--font-tech); font-weight: bold; cursor: pointer; transition: 0.3s; }
    .tryon-btn:hover:not(:disabled) { background: #00f3ff; color: #000; box-shadow: 0 0 20px rgba(0, 243, 255, 0.5); }
    .tryon-btn:disabled { opacity: 0.4; cursor: not-allowed; }
    .tryon-btn.processing { animation: pulse 1s infinite; pointer-events: none; }

    @keyframes pulse { 0% { opacity: 1; } 50% { opacity: 0.5; } 100% { opacity: 1; } }

    /* Result */
    .result-container { margin-top: 2rem; display: none; width: 100%; max-width: 400px; animation: fadeIn 0.5s; }
    .result-image { width: 100%; border: 2px solid #00f3ff; }
    @keyframes fadeIn { from { opacity: 0; transform: translateY(20px); } to { opacity: 1; transform: translateY(0); } }
    
    @media (max-width: 1024px) { .lab-container { grid-template-columns: 1fr; } .panel { border: none; border-bottom: 1px solid #333; max-height: none; } }
</style>
</head>

<body class="crt">
    <!-- Cursor -->
    <div class="cursor-dot"></div><div class="cursor-outline"></div>

    <nav class="navbar">
        <div class="logo glitch" data-text="VIRTUAL_FIT_LAB">VIRTUAL_FIT_LAB v2.0</div>
        <ul class="nav-links"><li><a href="shop.php">EXIT LAB</a></li></ul>
    </nav>

    <div class="lab-container">
        <!-- Products Panel -->
        <div class="panel">
            <h3 style="font-family: var(--font-tech); margin-bottom: 1.5rem;">&gt; SELECT_ASSET</h3>
            <?php if (empty($products)): ?>
                <p style="color: #666;">Database Error: No products found.</p>
            <?php else: ?>
                <div id="garmentList">
                    <?php foreach ($products as $p): ?>
                        <div class="garment-item" data-id="<?php echo $p['id']; ?>" data-name="<?php echo htmlspecialchars($p['name']); ?>">
                            <div class="garment-thumb" style="background: <?php echo $p['image_bg_color']; ?>;">
                                <?php echo (stripos($p['name'], 'hoodie') !== false) ? '🧥' : '👕'; ?>
                            </div>
                            <div>
                                <div style="font-weight: bold; color: #fff;"><?php echo htmlspecialchars($p['name']); ?></div>
                                <div style="font-family: var(--font-tech); color: #00f3ff; font-size: 0.8rem;">₹<?php echo number_format($p['price']); ?></div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        </div>

        <!-- Interactive Panel -->
        <div class="panel-center">
            <div class="upload-area" id="uploadArea">
                <input type="file" id="userImageInput" accept="image/*">
                <img src="" id="uploadPreview" class="upload-preview">
                <div id="uploadPlaceholder" style="text-align: center;">
                    <div style="font-size: 3rem; color: #333;">📸</div>
                    <div style="color: #666; font-family: var(--font-tech); font-size: 0.8rem; margin-top: 10px;">
                        UPLOAD FULL BODY PHOTO<br><span style="color: #00f3ff;">JPG / PNG / WEBP</span>
                    </div>
                </div>
            </div>

            <button class="tryon-btn" id="tryOnBtn" disabled>&gt; INITIALIZE_FUSION</button>
            <div id="statusMsg" style="font-family: var(--font-tech); margin-top: 1rem; color: #00f3ff; display:none;">PROCESSING_DATA...</div>

            <div class="result-container" id="resultContainer">
                <img src="" id="resultImage" class="result-image">
                <a id="downloadLink" href="#" download="au_tryon.jpg" style="display: block; text-align: center; margin-top: 10px; color: #fff; text-decoration: underline;">DOWNLOAD_RESULT</a>
            </div>
        </div>

        <!-- Metrics Panel -->
        <div class="panel panel-right">
            <h3 style="font-family: var(--font-tech); margin-bottom: 1.5rem;">&gt; METRICS</h3>
            <div style="margin-bottom: 2rem;">
                <p style="color: #666; font-size: 0.8rem; margin-bottom: 5px;">MODEL_ID</p>
                <p style="color: #fff;">IDM-VTON (REPLICATE)</p>
            </div>
            <div style="margin-bottom: 2rem;">
                <p style="color: #666; font-size: 0.8rem; margin-bottom: 5px;">LATENCY</p>
                <p style="color: #00ff00;">ONLINE (API LINKED)</p>
            </div>
            <div style="border: 1px solid #333; padding: 1rem; font-size: 0.8rem; color: #888;">
                <strong>INSTRUCTIONS:</strong><br><br>
                1. Select a garment from the left.<br>
                2. Upload a clear, front-facing photo.<br>
                3. Ensure good lighting.<br>
                4. Wait 15-30s for rendering.
            </div>
        </div>
    </div>

    <script>
        const uploadArea = document.getElementById('uploadArea');
        const fileInput = document.getElementById('userImageInput');
        const preview = document.getElementById('uploadPreview');
        const placeholder = document.getElementById('uploadPlaceholder');
        const tryOnBtn = document.getElementById('tryOnBtn');
        const resultContainer = document.getElementById('resultContainer');
        const resultImg = document.getElementById('resultImage');
        const statusMsg = document.getElementById('statusMsg');
        
        let selectedId = null;
        let file = null;

        // File Upload
        fileInput.addEventListener('change', e => {
            if(e.target.files[0]) {
                file = e.target.files[0];
                const reader = new FileReader();
                reader.onload = ev => {
                    preview.src = ev.target.result;
                    uploadArea.classList.add('has-image');
                    placeholder.style.display = 'none';
                    checkReady();
                }
                reader.readAsDataURL(file);
            }
        });

        // Selection
        document.querySelectorAll('.garment-item').forEach(item => {
            item.addEventListener('click', () => {
                document.querySelectorAll('.garment-item').forEach(i => i.classList.remove('selected'));
                item.classList.add('selected');
                selectedId = item.dataset.id;
                checkReady();
            });
        });

        function checkReady() {
            tryOnBtn.disabled = !(selectedId && file);
        }

        // Action
        tryOnBtn.addEventListener('click', async () => {
            if(!selectedId || !file) return;

            tryOnBtn.disabled = true;
            tryOnBtn.classList.add('processing');
            tryOnBtn.innerText = "> FUSING_ASSETS...";
            statusMsg.style.display = 'block';
            statusMsg.innerText = "UPLOADING & PROCESSING (MAY TAKE 30s)...";
            resultContainer.style.display = 'none';

            const formData = new FormData();
            formData.append('product_id', selectedId);
            formData.append('user_image', file);

            try {
                const res = await fetch('tryon_handler.php', { method: 'POST', body: formData });
                const data = await res.json();

                if(data.success) {
                    resultImg.src = data.image_url;
                    document.getElementById('downloadLink').href = data.image_url;
                    resultContainer.style.display = 'block';
                    statusMsg.style.display = 'none';
                } else {
                    alert('ERROR: ' + data.message);
                    statusMsg.innerText = "FAILED: " + data.message;
                }
            } catch(e) {
                alert('System Error: ' + e.message);
            }

            tryOnBtn.classList.remove('processing');
            tryOnBtn.innerText = "> INITIALIZE_FUSION";
            tryOnBtn.disabled = false;
        });

        // Cursor Logic
        const cursorDot = document.querySelector(".cursor-dot");
        const cursorOutline = document.querySelector(".cursor-outline");
        if (cursorDot && cursorOutline) {
            window.addEventListener("mousemove", function (e) {
                cursorDot.style.left = `${e.clientX}px`; cursorDot.style.top = `${e.clientY}px`;
                cursorOutline.animate({ left: `${e.clientX}px`, top: `${e.clientY}px` }, { duration: 500, fill: "forwards" });
            });
        }
    </script>
</body>
</html>