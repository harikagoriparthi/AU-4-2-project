<?php 
include 'data/products.php';
$id = isset($_GET['id']) ? $_GET['id'] : 1;
$item = $products[0];
foreach($products as $p) { if($p['id'] == $id) { $item = $p; break; } }
$pageTitle = "AU // " . strtoupper($item['name']);
include 'includes/header.php'; 
?>
<style>
    .pdp-grid { display: grid; grid-template-columns: 1fr 1fr; min-height: 80vh; }
    .pdp-images { background: #f4f4f4; display: flex; align-items: center; justify-content: center; border-right: var(--border-thick); position: relative; }
    .pdp-info { padding: 4rem; display: flex; flex-direction: column; justify-content: center; }
    .origin-label { display: inline-block; border: 1px solid #ccc; padding: 5px 10px; font-family: var(--font-tech); font-size: 0.8rem; margin-top: 2rem; }
    #tryon-modal { display: none; position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0,0,0,0.95); z-index: 5000; }
    .tryon-content { width: 90%; height: 90%; margin: 5vh auto; background: #000; border: 1px solid #333; display: grid; grid-template-columns: 300px 1fr 300px; color: #00f3ff; font-family: var(--font-tech); position: relative; }
    .tryon-close { position: absolute; top: 20px; right: 20px; background: none; border: 1px solid #00f3ff; color: #00f3ff; padding: 5px 15px; cursor: pointer; z-index: 10; }
    .scan-line { position: absolute; width: 100%; height: 2px; background: #00f3ff; top: 0; box-shadow: 0 0 10px #00f3ff; animation: scan 2s infinite linear; }
    @keyframes scan { 0% { top: 0; opacity: 1; } 100% { top: 100%; opacity: 0; } }
    @media(max-width: 768px) { .pdp-grid { grid-template-columns: 1fr; } .tryon-content { grid-template-columns: 1fr; overflow-y: scroll; } }
</style>
</head>
<body>

<?php include 'includes/navbar.php'; ?>

<div class="pdp-grid">
    <!-- FIXED: img_bg -> image_bg_color, img_text -> image_text -->
    <div class="pdp-images">
        <img src="<?php echo !empty($item['image']) ? str_replace('\\', '/', $item['image']) : 'uploads/products/default.jpg'; ?>" onerror="this.src='uploads/products/default.jpg';"alt="">
    </div>

    <div class="pdp-info">
        <p style="font-family: var(--font-tech); color: #666;">// <?php echo strtoupper($item['category']); ?> // ID: 00<?php echo $item['id']; ?></p>
        <h1 style="font-size: 3rem; line-height: 1; margin: 1rem 0;"><?php echo $item['name']; ?></h1>
        <div style="font-size: 1.2rem; margin-bottom: 2rem;">
            <span class="stars" style="color: var(--au-gold);">★★★★★</span> 
            <span><?php echo $item['rating']; ?> / 5.0</span>
        </div>
        <h2 style="font-size: 2rem; color: var(--au-blue); margin-bottom: 2rem;">₹<?php echo $item['price']; ?></h2>
        
        <!-- FIXED: long_desc -> long_description -->
        <p style="line-height: 1.8; margin-bottom: 2rem; max-width: 500px;"><?php echo $item['long_description']; ?></p>

        <div style="display: flex; gap: 1rem; margin-bottom: 2rem;">
            <select style="padding: 15px; border: var(--border-thick); font-family: var(--font-tech); min-width: 100px;">
                <option>SIZE: M</option><option>SIZE: L</option><option>SIZE: XL</option>
            </select>
            <input type="number" value="1" min="1" max="5" style="padding: 15px; border: var(--border-thick); width: 60px; text-align: center;">
        </div>

        <div style="display: flex; gap: 1rem; flex-wrap: wrap;">
            <a href="cart_action.php?action=add&id=<?php echo $item['id']; ?>" style="flex: 1;">
                <button class="btn btn-add-cart" style="background: var(--au-blue); color: var(--au-gold); width: 100%;">ADD TO CART</button>
            </a>
            <button class="btn" onclick="openTryOn()" style="background: #000; color: #00f3ff; border: 1px solid #00f3ff; flex: 1; min-width: 200px;">
                <span style="font-size: 1.2rem;">👁‍🗨</span> VIRTUAL TRY-ON
            </button>
        </div>

        <div class="origin-label">COUNTRY OF ORIGIN: <?php echo strtoupper($item['origin']); ?></div>
    </div>
</div>

<!-- Try-On Modal Code (Hidden) -->
<div id="tryon-modal">
    <div class="tryon-content">
        <button class="tryon-close" onclick="closeTryOn()">CLOSE_SIMULATION [X]</button>
        <div style="border-right: 1px solid #333; padding: 2rem;">
            <h3>> ASSET_LOADED</h3>
            <p style="margin-top: 1rem; color: #666;"><?php echo $item['name']; ?></p>
            <div style="margin-top: 2rem; border: 1px solid #333; padding: 10px;"><p>> MESH_QUALITY: HIGH</p><p>> TEXTURE: 4K</p></div>
        </div>
        <div style="position: relative; background: #050505; display: flex; align-items: center; justify-content: center;">
            <div class="scan-line"></div>
            <div style="text-align: center;"><h1 style="font-size: 8rem; opacity: 0.1; color: #fff;">AU</h1><p>[CAMERA_FEED_SIMULATION]</p></div>
        </div>
        <div style="border-left: 1px solid #333; padding: 2rem;">
            <h3>> FIT_ANALYSIS</h3>
            <p style="margin-top: 20px;">SIZE PREDICTION: L</p><div style="width: 100%; height: 5px; background: #333; margin-top: 5px;"><div style="width: 75%; height: 100%; background: #00f3ff;"></div></div>
            <p style="margin-top: 20px;">CONFIDENCE: 98%</p><div style="width: 100%; height: 5px; background: #333; margin-top: 5px;"><div style="width: 98%; height: 100%; background: #00f3ff;"></div></div>
        </div>
    </div>
</div>
<script>
function openTryOn() { document.getElementById('tryon-modal').style.display = 'block'; }
function closeTryOn() { document.getElementById('tryon-modal').style.display = 'none'; }
</script>

<?php include 'includes/footer.php'; ?>