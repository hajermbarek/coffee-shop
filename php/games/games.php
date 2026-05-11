<?php
session_start();
require_once '../cnx.php';

$category = $_GET['category'] ?? 'ALL';
$search   = trim($_GET['search'] ?? '');
$date = $_SESSION['reservationDate'] ?? date('Y-m-d');
$heure    = ($_SESSION['reservationTime'] ?? date('H:i')) . ':00';

$sql    = "SELECT * FROM game WHERE 1=1";
$params = [];

if ($category !== 'ALL') {
    $sql .= " AND category = :category";
    $params[':category'] = $category;
}

if ($search !== '') {
    $sql .= " AND (name LIKE :search OR description LIKE :search)";
    $params[':search'] = '%' . $search . '%';
}

$sql .= " ORDER BY name ASC";

$stmt = $pdo->prepare($sql);
$stmt->execute($params);
$games = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Calcul dynamique du stock par jeu selon date et heure
foreach ($games as &$game) {
    $stmt2 = $pdo->prepare("
        SELECT COUNT(*) 
        FROM reservation_jeux rj
        JOIN reservations r ON rj.id_reservation = r.id_reservation
        WHERE rj.id_game = ?
        AND r.date_reservation = ?
        AND r.statut = 'confirmee'
        AND r.heure_reservation BETWEEN 
            SUBTIME(?, '03:00:00') AND ADDTIME(?, '03:00:00')
    ");
    $stmt2->execute([$game['id'], $date, $heure, $heure]);
    $reservations = $stmt2->fetchColumn();
    $game['stock_dispo'] = max(0, $game['exemplaires_total'] - $reservations);
}
unset($game);

$categories = ['ALL', 'FUN', 'STRATEGY', 'CLASSIC GAME'];
?>
<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <title>Cozy Café – Nos Jeux</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" />
    <link rel="stylesheet" href="../style.css" />
    <link rel="stylesheet" href="games.css" />
    <link href="https://fonts.googleapis.com/css2?family=Manrope:wght@400;500;600;700&display=swap" rel="stylesheet" />
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" rel="stylesheet" />
</head>
<body>

<!-- Navbar -->
<nav class="navbar navbar-expand-lg bg-body-tertiary">
    <div class="container-fluid">
        <a class="navbar-brand" href="#">Coffee Shop</a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav">
                <li class="nav-item"><a class="nav-link" href="../page1.php">Home</a></li>
                <li class="nav-item"><a class="nav-link" href="../menu/menu.php">Full menu</a></li>
                <li class="nav-item"><a class="nav-link" href="#">Our Story</a></li>
                <li class="nav-item"><a class="nav-link active" href="games.php">Games</a></li>
            </ul>
        </div>
    </div>
</nav>

<!-- Main -->
<section class="container py-5">

    <!-- Back + Titre -->
    <div class="page-header">
        <a href="javascript:history.back()" class="btn-back">
            <i class="fa-solid fa-arrow-left"></i> Back
        </a>
        <h2>Our Games</h2>
        <span></span>
    </div>

    <!-- Recherche + Filtres -->
    <div class="filter-bar">
        <form method="GET" action="games.php" class="search-form" id="searchForm">
            <div class="search-wrapper">
                <i class="fa-solid fa-magnifying-glass search-icon"></i>
                <input
                    type="text"
                    name="search"
                    id="searchInput"
                    placeholder="Search a game..."
                    value="<?= htmlspecialchars($search) ?>"
                    autocomplete="off"
                />
                <?php if ($search): ?>
                    <button type="button" class="clear-search" onclick="clearSearch()">
                        <i class="fa-solid fa-xmark"></i>
                    </button>
                <?php endif; ?>
            </div>
            <?php if ($category !== 'ALL'): ?>
                <input type="hidden" name="category" value="<?= htmlspecialchars($category) ?>" />
            <?php endif; ?>
        </form>

        <!-- Boutons de filtre -->
        <div class="filter-buttons">
            <?php
            $icons = [
                'ALL'          => 'fa-table-cells',
                'FUN'          => 'fa-face-laugh-beam',
                'STRATEGY'     => 'fa-chess-knight',
                'CLASSIC GAME' => 'fa-star',
            ];
            $colors = [
                'ALL'          => '#5c4033',
                'FUN'          => '#a0837f',
                'STRATEGY'     => '#859fb9',
                'CLASSIC GAME' => '#d9822b',
            ];
            foreach ($categories as $cat):
                $isActive = ($category === $cat) ? 'active' : '';
                $color    = $colors[$cat];
                $icon     = $icons[$cat];
                $url      = 'games.php?category=' . urlencode($cat) . ($search ? '&search=' . urlencode($search) : '');
            ?>
                <a href="<?= $url ?>"
                   class="filter-btn <?= $isActive ?>"
                   style="--badge-color: <?= $color ?>">
                    <i class="fa-solid <?= $icon ?>"></i> <?= $cat ?>
                </a>
            <?php endforeach; ?>
        </div>
    </div>

    <!-- Compteur -->
    <p class="results-count">
        <?= count($games) ?> game<?= count($games) !== 1 ? 's' : '' ?> found
        <?php if ($category !== 'ALL'): ?>
            in <strong><?= htmlspecialchars($category) ?></strong>
        <?php endif; ?>
        <?php if ($search): ?>
            for "<strong><?= htmlspecialchars($search) ?></strong>"
        <?php endif; ?>
    </p>

    <!-- Grille -->
    <?php if (empty($games)): ?>
        <div class="no-results">
            <i class="fa-solid fa-dice fa-3x"></i>
            <p>No game found. Try another search or filter.</p>
            <a href="games.php" class="btn-reserve" style="display:inline-block;padding:10px 28px;">
                See all games
            </a>
        </div>
    <?php else: ?>
        <div class="row g-4">
            <?php foreach ($games as $game): ?>
                <div class="col-12 col-md-6 col-lg-4">
                    <div class="game-card <?= $game['stock_dispo'] === 0 ? 'card-indispo' : '' ?>">

                        <!-- Badge stock -->
                        <div class="stock-badge <?= $game['stock_dispo'] > 0 ? 'stock-dispo' : 'stock-indispo' ?>">
                            <?php if ($game['stock_dispo'] > 0): ?>
                                ✅ <?= $game['stock_dispo'] ?> available
                            <?php else: ?>
                                ❌ Unavailable
                            <?php endif; ?>
                        </div>

                        <!-- Badge catégorie -->
                        <div class="game-badge" style="background: <?= htmlspecialchars($game['category_color']) ?>;">
                            <?= htmlspecialchars($game['category']) ?>
                        </div>

                        <img src="<?= htmlspecialchars($game['image_path']) ?>" alt="<?= htmlspecialchars($game['name']) ?>">

                        <div class="game-content">
                            <h3><?= htmlspecialchars($game['name']) ?></h3>
                            <p class="game-desc"><?= htmlspecialchars($game['description']) ?></p>
                            <div class="game-info">
                                <span>👥 <?= htmlspecialchars($game['players']) ?></span>
                                <span>⏳ <?= htmlspecialchars($game['duration']) ?></span>
                            </div>
                            <div class="game-buttons">
                                <?php if ($game['stock_dispo'] > 0): ?>
                                    <a class="btn-reserve"
                                       href="../reservation.php?game=<?= urlencode($game['name']) ?>&game_id=<?= $game['id'] ?>">
                                        Book
                                    </a>
                                <?php else: ?>
                                    <button class="btn-reserve btn-disabled" disabled>Unavailable</button>
                                <?php endif; ?>

                                <button class="btn-rules"
                                        onclick="openRules(<?= htmlspecialchars($game['rules']) ?>, '<?= addslashes($game['name']) ?>')">
                                    Rules
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>

</section>

<!-- Modal Règles -->
<div id="rulesModal" class="modal" onclick="closeOnBackdrop(event)">
    <div class="modal-content">
        <span class="close" onclick="closeRules()">&times;</span>
        <h2 id="rulesTitle"></h2>
        <ul id="rulesText"></ul>
    </div>
</div>

<!-- Footer -->
<footer class="site-footer">
    <div class="footer-main">
        <div class="footer-columns">
            <div class="footer-brand">
                <h3>COZY CAFÉ</h3>
                <p>A warm place for great brews & good moments</p>
            </div>
            <div class="footer-group">
                <h4>ABOUT</h4>
                <ul>
                    <li><a href="#">Our Story</a></li>
                    <li><a href="#">Location</a></li>
                    <li><a href="#">Careers</a></li>
                </ul>
            </div>
            <div class="footer-group">
                <h4>MENU</h4>
                <ul>
                    <li><a href="../menu/menu.php">Hot Drinks</a></li>
                    <li><a href="../menu/menu.php">Cold Brews</a></li>
                    <li><a href="../menu/menu.php">Pastries & Snacks</a></li>
                </ul>
            </div>
            <div class="footer-group">
                <h4>MORE</h4>
                <ul>
                    <li><a href="#">Contact Us</a></li>
                    <li><a href="#">Reservations</a></li>
                    <li><a href="#">FAQs</a></li>
                </ul>
            </div>
        </div>
    </div>
    <div class="footer-bottom">
        <p>© 2026 Cozy Café • Centre Urbain Nord, Tunis • All rights reserved</p>
        <p><a href="#">Privacy Policy</a> • <a href="#">Terms of Use</a></p>
    </div>
</footer>

<script src="games.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>