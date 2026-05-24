<?php
session_start();
require_once '../cnx.php';

// Récupérer l'ID du livre depuis l'URL
$book_id = (int)($_GET['id'] ?? 0);

if (!$book_id) {
    header('Location: books.php');
    exit;
}

// Récupérer les infos du livre depuis la BDD
$stmt = $pdo->prepare("SELECT * FROM livres WHERE id_livre = ?");
$stmt->execute([$book_id]);
$book = $stmt->fetch();

// Si le livre n'existe pas
if (!$book) {
    header('Location: books.php');
    exit;
}

// Vérifier si le livre est disponible
$isAvailable = $book['exemplaires_disponibles'] > 0;

// Vérifier si le livre est actuellement réservé
$stmt = $pdo->prepare("
    SELECT rl.*, r.date_reservation 
    FROM reservation_livres rl
    JOIN reservations r ON rl.id_reservation = r.id_reservation
    WHERE rl.id_livre = ? 
    AND r.statut = 'confirmee'
    AND DATE_ADD(r.date_reservation, INTERVAL 7 DAY) > CURDATE() 
    
");
$stmt->execute([$book_id]);
$reservation = $stmt->fetch();

$isReserved = ($reservation !== false);
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($book['titre']) ?> - Cozy Café</title>
    <link rel="stylesheet" href="books.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" />
    <link rel="stylesheet" href="style.css" />
    <style>
        .book-detail-container {
            max-width: 1000px;
            margin: 40px auto;
            padding: 20px;
            background: #fff;
            border-radius: 20px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.1);
            display: flex;
            gap: 40px;
            flex-wrap: wrap;
        }
        .book-cover {
            flex: 1;
            min-width: 250px;
            text-align: center;
        }
        .book-cover img {
            max-width: 100%;
            max-height: 400px;
            border-radius: 12px;
            box-shadow: 0 5px 15px rgba(0,0,0,0.2);
            object-fit: cover;
        }
        .book-info {
            flex: 2;
            min-width: 300px;
        }
        .book-info h1 {
            color: #6f4e37;
            font-size: 2rem;
            margin-bottom: 10px;
        }
        .book-author {
            color: #999;
            font-size: 1.1rem;
            margin-bottom: 20px;
            font-style: italic;
        }
        .book-description {
            line-height: 1.8;
            color: #444;
            margin: 20px 0;
        }
        .availability {
            display: inline-block;
            padding: 5px 12px;
            border-radius: 20px;
            font-size: 0.85rem;
            margin: 15px 0;
        }
        .available {
            background: #d4edda;
            color: #155724;
        }
        .unavailable {
            background: #f8d7da;
            color: #721c24;
        }
        .reserved {
            background: #fff3cd;
            color: #856404;
        }
        .reserve-now-btn {
            display: inline-block;
            padding: 12px 30px;
            background: #6f4e37;
            color: white;
            text-decoration: none;
            border-radius: 30px;
            font-weight: bold;
            margin-top: 20px;
            transition: all 0.3s ease;
            border: none;
            cursor: pointer;
        }
        .reserve-now-btn:hover {
            background: #8b634b;
            transform: translateY(-2px);
        }
        .reserve-now-btn.disabled {
            background: #ccc;
            cursor: not-allowed;
            transform: none;
        }
        .back-link {
            display: inline-block;
            margin-top: 20px;
            color: #6f4e37;
            text-decoration: none;
            margin-left: 15px;
        }
        .back-link:hover {
            text-decoration: underline;
        }
        hr {
            margin: 20px 0;
            border: none;
            border-top: 1px solid #eee;
        }
        .copies-info {
            font-size: 0.9rem;
            color: #666;
            margin-top: 10px;
        }
    </style>
</head>
<body>
    <?php include '../navbar.php'; ?>

    <div class="book-detail-container">
        <div class="book-cover">
            <img src="<?= htmlspecialchars($book['image'] ?? 'images/default-book.png') ?>"
                 alt="<?= htmlspecialchars($book['titre']) ?>"
                 onerror="this.onerror=null; this.src='images/default-book.png'">
        </div>
        
        <div class="book-info">
            <h1><?= htmlspecialchars($book['titre']) ?></h1>
            <div class="book-author">Par <?= htmlspecialchars($book['auteur'] ?? 'Auteur inconnu') ?></div>
            
            <?php if ($isReserved): ?>
                <span class="availability reserved">
                    📖 Réservé jusqu'au <?= date('d/m/Y', strtotime($reservation['date_reservation'] . ' +7 days')) ?>
                </span>
            <?php elseif ($isAvailable): ?>
                <span class="availability available">
                    📚 Disponible (<?= $book['exemplaires_disponibles'] ?> exemplaire(s))
                </span>
            <?php else: ?>
                <span class="availability unavailable">
                    ❌ Indisponible 
                </span>
            <?php endif; ?>
            
            <div class="book-description">
                <strong>📖 Description :</strong>
                <p><?= nl2br(htmlspecialchars($book['description'] ?? 'Aucune description disponible.')) ?></p>
            </div>
            
            
            
            <hr>
            
            <?php if ($isReserved): ?>
                <button class="reserve-now-btn disabled" disabled>
                    🔒 Déjà réservé pour le moment
                </button>
                <a href="books.php" class="back-link">← Choisir un autre livre</a>
            <?php elseif ($isAvailable): ?>
                <form method="POST" action="select_book.php">
                    <input type="hidden" name="book_id" value="<?= $book['id_livre'] ?>">
                    <input type="hidden" name="book_title" value="<?= htmlspecialchars($book['titre']) ?>">
                    <button type="submit" class="reserve-now-btn">📚 Réserver ce livre</button>
                    <a href="books.php" class="back-link">← Retour à la liste</a>
                </form>
            <?php else: ?>
                <button class="reserve-now-btn disabled" disabled>❌ Indisponible pour le moment</button>
                <a href="books.php" class="back-link">← Retour à la liste</a>
            <?php endif; ?>
        </div>
    </div>

    <?php include '../footer.php'; ?>
</body>
</html>