<?php 
$pageTitle = "AU HERITAGE // HOME";
include 'includes/header.php'; 
?>
</head>
<body>

<?php include 'includes/navbar.php'; ?>

<!-- HERO (Same as before) -->
<header class="grid-2" style="min-height: 90vh; border-bottom: var(--border-thick);">
    <div style="padding: 4rem; display: flex; flex-direction: column; justify-content: center; border-right: var(--border-thick);">
        <div>
            <p style="font-family: var(--font-tech); color: #666; margin-bottom: 1rem; letter-spacing: 2px;">SERIES 004 -- "WALTAIR"</p>
            <h1 class="display-text">
                LOCAL<br>PRIDE<br>
                <span class="glitch" data-text="GLOBAL" style="display: inline-block;">GLOBAL</span><br>
                REACH.
            </h1>
            <p style="max-width: 450px; margin-top: 2rem; margin-bottom: 2rem; font-size: 1.1rem; line-height: 1.6;">
                Celebrating nearly a century of Andhra University excellence (Est. 1926). 
            </p>
            <a href="shop.php"><button class="btn">View Collection</button></a>
        </div>
    </div>
    <div style="background: url('https://images.unsplash.com/photo-1523381210434-271e8be1f52b?q=80&w=2070&auto=format&fit=crop') center/cover; position: relative; min-height: 400px;">
        <div style="position: absolute; bottom: 2rem; left: 2rem; background: white; padding: 1rem; border: 2px solid black;">
            <span style="font-family: var(--font-varsity); color: var(--au-blue); display: block;">FEATURED:</span>
            <span style="font-family: var(--font-tech);">The Senate Block Hoodie</span>
        </div>
    </div>
</header>

<!-- TOP CATEGORIES (NEW SECTION) -->
<section style="padding: 4rem 3%; border-bottom: var(--border-thick);">
    <h2 style="font-size: 2rem; margin-bottom: 2rem; text-align: center;">BROWSE ARCHIVES</h2>
    <div class="grid-2" style="gap: 2rem;">
        <div style="background: #f4f4f4; padding: 2rem; text-align: center; border: 1px solid #ddd; cursor: pointer;" onclick="window.location.href='shop.php?category=apparel'">
            <h3 style="font-size: 1.5rem;">APPAREL</h3>
            <p>Hoodies, Tees, Jackets</p>
        </div>
        <div style="background: #eee; padding: 2rem; text-align: center; border: 1px solid #ddd; cursor: pointer;" onclick="window.location.href='shop.php?category=accessories'">
            <h3 style="font-size: 1.5rem;">ACCESSORIES</h3>
            <p>Totes, Caps, Collectibles</p>
        </div>
    </div>
</section>

<!-- SHOWCASE -->
<section style="padding: 6rem 3%;">
    <h2 style="font-size: 3rem; margin-bottom: 3rem; text-align: center;">TRENDING NOW</h2>
    <div class="product-grid" style="border: var(--border-thick);">
        <!-- Static Preview Items -->
        <div class="product-card">
            <span class="badge">POPULAR</span>
            <div class="card-img">
                 <img src="uploads/products/hoodie.avif" alt="Hoodie">
            </div>           
            <div>
                <h3 style="font-size: 1.5rem;">Senate House '26</h3>
                <div style="display: flex; justify-content: space-between; margin-top: 10px; font-family: var(--font-tech);">
                    <span>Heavyweight Cotton</span><span>₹1,899</span>
                </div>
            </div>
        </div>
        <div class="product-card">
            <div class="card-img">
                <img src="uploads/products/T-shirt.webp" alt="Hoodie">
            </div>
            <div>
                <h3 style="font-size: 1.5rem;">Heritage Canvas</h3>
                <div style="display: flex; justify-content: space-between; margin-top: 10px; font-family: var(--font-tech);">
                    <span>Organic Canvas</span><span>₹599</span>
                </div>
            </div>
        </div>
    </div>
</section>

<?php include 'includes/footer.php'; ?>