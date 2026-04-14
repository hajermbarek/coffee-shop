<?php
include '../config.php';

$category_slug = 'hot-drinks';

$stmt = $pdo->prepare("SELECT * FROM menu_items 
                       WHERE category_slug = ? 
                       ORDER BY id ASC"); 

$stmt->execute([$category_slug]);
$items = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Hot Drinks - Cozy Café</title>
    <link rel="stylesheet" href="Menu_Style.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>

    <?php include '../navbar.php'; ?>

    <header>
        <h1>Cozy Café</h1>
        <p>Your daily dose of happiness</p>
    </header>

    <section style="padding: 40px 20px; text-align: center;">
        <h2>Hot Drinks</h2>
        
        <div class="menu">
            <?php if (empty($items)): ?>
                <p style="color: red; font-size: 1.2rem;">No items found. Please make sure you inserted the data into the menu_items table.</p>
            <?php else: ?>
                <?php foreach ($items as $item): ?>
                    <div class="item <?= $item['is_popular'] ? 'popular' : '' ?>">
                        <?php if ($item['is_popular']): ?>
                            <span class="badge">Popular</span>
                        <?php endif; ?>

                        <img src="<?= htmlspecialchars($item['image_url']) ?>" 
                             alt="<?= htmlspecialchars($item['name']) ?>" 
                             class="drink-img">

                        <h3><?= htmlspecialchars($item['name']) ?></h3>
                        <p><?= htmlspecialchars($item['description']) ?></p>
                        <p class="price"><?= htmlspecialchars($item['price']) ?></p>

                        <button class="order-btn" 
                                onclick="alert('Ordering <?= addslashes($item['name']) ?>...')">
                            Order Now
                        </button>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>
    </section>

    <?php include '../footer.php'; ?>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>