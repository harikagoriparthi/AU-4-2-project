<?php 
$pageTitle = "AU ARCHIVES // SUPPLY";
include 'includes/header.php'; 
include 'data/products.php';

$category = isset($_GET['category']) ? $_GET['category'] : 'all';
?>
<style>
    .shop-layout {
        display: grid;
        grid-template-columns: 250px 1fr;
        min-height: 100vh;
    }

    .sidebar {
        padding: 2rem;
        border-right: var(--border-thick);
        background: var(--paper-white);
    }

    .filter-group { margin-bottom: 2rem; }

    .filter-title {
        font-family: var(--font-tech);
        font-weight: bold;
        margin-bottom: 1rem;
        display: block;
    }

    .filter-item {
        display: block;
        padding: 5px 0;
        font-family: var(--font-street);
        font-size: 0.9rem;
        cursor: pointer;
    }

    .filter-item:hover {
        color: var(--au-blue);
        text-decoration: underline;
    }

    .stars {
        color: var(--au-gold);
        letter-spacing: 2px;
    }

    /* ✅ PRODUCT GRID FIX */
    .product-grid {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(250px, 1fr));
        gap: 10px;
        padding: 10px;
    }

    .product-card {
    background: #fff;
    border: 1px solid #ddd;
    padding: 15px;
    transition: 0.3s;
}

.product-card:hover {
    transform: translateY(-5px);
}
/* ===== LAYOUT ===== */
.shop-layout {
    display: grid;
    grid-template-columns: 250px 1fr;
    min-height: 100vh;
}

/* ===== PRODUCT GRID (FIXED FOR DESKTOP) ===== */
.product-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(260px, 300px));
    justify-content: center;   /* 🔥 center grid on big screens */
    gap: 15px;
    padding: 15px;
}

/* ===== PRODUCT CARD ===== */
.product-card {
    background: #fff;
    border: 1px solid #ddd;
    padding: 15px;
    transition: 0.3s;
    border-radius: 10px;
}

.product-card:hover {
    transform: translateY(-5px);
}

/* ===== IMAGE FIX (IMPORTANT) ===== */
.card-img {
    width: 100%;
    height: 220px;
    background: #f5f5f5;
    display: flex;
    align-items: center;
    justify-content: center;
    overflow: hidden;
    border-radius: 8px;
}

/* 🔥 CHANGE HERE (THIS FIXES CROPPING ISSUE) */
.card-img img {
    max-width: 100%;
    max-height: 100%;
    object-fit: contain;   /* instead of cover */
}

/* ===== TEXT ===== */
.product-card h3 {
    font-size: 1.2rem;
    margin-top: 10px;
}

/* ===== MOBILE ===== */
@media (max-width: 768px) {
    .shop-layout {
        grid-template-columns: 1fr;
    }

    .sidebar {
        display: none;
    }

    .product-grid {
        grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
        padding: 15px;
    }
}
    /* ✅ IMAGE FIX */
    .card-img {
        width: 100%;
        height: 200px;
        overflow: hidden;
        background: #f5f5f5;
    }

    .card-img img {
        width: 100%;
        height: 100%;
        object-fit: cover;
    }

    @media (max-width: 768px) {
        .shop-layout { grid-template-columns: 1fr; }
        .sidebar { display: none; }
    }
</style>
</head>
<body>

<?php include 'includes/navbar.php'; ?>

<div class="shop-layout">

    <!-- SIDEBAR -->
    <aside class="sidebar">
        <div class="filter-group">
            <span class="filter-title">// CATEGORIES</span>
            <a href="shop.php?category=all" class="filter-item">[All Items]</a>
            <a href="shop.php?category=apparel" class="filter-item">[Apparel]</a>
            <a href="shop.php?category=accessories" class="filter-item">[Accessories]</a>
        </div>

        <div class="filter-group">
            <span class="filter-title">// PRICE RANGE</span>
            <input type="range" min="100" max="5000" style="width: 100%;">
            <div style="display: flex; justify-content: space-between; font-family: var(--font-tech); font-size: 0.8rem;">
                <span>₹100</span><span>₹5000</span>
            </div>
        </div>

        <div class="filter-group">
            <span class="filter-title">// RATING</span>
            <label class="filter-item">
                <input type="checkbox"> ★★★★☆ & Up
            </label>
        </div>
    </aside>

    <!-- PRODUCTS -->
    <main class="product-grid">

        <?php foreach ($products as $product): ?>

            <div class="product-card" onclick="window.location.href='product.php?id=<?php echo $product['id']; ?>'" style="cursor: pointer;">

                <?php if(!empty($product['tag'])): ?>
                    <span class="badge"><?php echo $product['tag']; ?></span>
                <?php endif; ?>

                <!-- ✅ ONLY ONE IMAGE BLOCK -->
                <div class="card-img">
                    <img src="<?php echo !empty($product['image']) 
                        ? str_replace('\\', '/', $product['image']) 
                        : 'uploads/products/default.jpg'; ?>" 
                        alt="<?php echo $product['name']; ?>">
                </div>

                <div>
                    <h3 style="font-size: 1.2rem;">
                        <?php echo $product['name']; ?>
                    </h3>

                    <div style="font-size: 0.8rem; margin: 5px 0;">
                        <span class="stars">
                            <?php echo str_repeat("★", round($product['rating'])); ?>
                        </span>
                        <span style="color: #666;">
                            (<?php echo $product['reviews']; ?>)
                        </span>
                    </div>

                    <div style="display: flex; justify-content: space-between; margin-top: 10px; font-family: var(--font-tech);">
                        <span><?php echo $product['brand']; ?></span>
                        <span style="font-weight: bold;">
                            ₹<?php echo $product['price']; ?>
                        </span>
                    </div>

                    <a href="cart_action.php?action=add&id=<?php echo $product['id']; ?>" onclick="event.stopPropagation();">
                        <button class="btn" style="width: 100%; margin-top: 15px;">
                            ADD TO CART
                        </button>
                    </a>
                </div>

            </div>

        <?php endforeach; ?>

    </main>
</div>

<?php include 'includes/footer.php'; ?>