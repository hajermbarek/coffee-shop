<?php
include '../cnx.php';

function getCategories($pdo, $section) {
    $stmt = $pdo->prepare("SELECT * FROM menu_categories WHERE section = ? ORDER BY id");
    $stmt->execute([$section]);
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

$bakeryItems = getCategories($pdo, 'bakery');
$beverageItems = getCategories($pdo, 'beverages');
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <link rel="stylesheet" href="menuStyle.css">
    <title>Cozy Café Menu</title>
    <link rel="stylesheet" href="style.css" />
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>

    <?php include '../navbar.php'; ?>   
    <!-- Bakery Section -->
<section class="bakery-section">
    <h2>Bakery</h2>
    <p>At Cozy Café, we offer a wide range of bakery items that are perfect for any occasion. 
       From our freshly baked breads to our decadent cakes and pastries, each of our products is made with 
       the finest ingredients and the utmost care.</p>
    
    <div class="bakery-items">
        <?php foreach ($bakeryItems as $item): ?>
            <a href="<?= htmlspecialchars($item['link']) ?>" class="category-link">
                <div class="bakery-item">
                    <img src="<?= htmlspecialchars($item['image_url']) ?>" 
                         alt="<?= htmlspecialchars($item['title']) ?>">
                    <p class="item-label"><?= htmlspecialchars($item['title']) ?></p>
                </div>
            </a>
        <?php endforeach; ?>
    </div>
</section>

<!-- Beverages Section -->
<section class="beverages-section">
    <h2>Beverages</h2>
    <p>From bold espresso to refreshing iced creations and fresh juices, 
       our drinks are crafted daily with the utmost care and the finest beans, leaves and fruits.</p>
    
    <div class="beverages-items">
        <?php foreach ($beverageItems as $item): ?>
            <a href="<?= htmlspecialchars($item['link']) ?>" class="category-link">
                <div class="beverage-item">
                    <img src="<?= htmlspecialchars($item['image_url']) ?>" 
                         alt="<?= htmlspecialchars($item['title']) ?>">
                    <p class="item-label"><?= htmlspecialchars($item['title']) ?></p>
                </div>
            </a>
        <?php endforeach; ?>
    </div>
</section>

    <?php include '../footer.php'; ?>  

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>