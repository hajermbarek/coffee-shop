<?php
require_once '../cnx.php';
session_start();

// Récupérer TOUS les livres depuis la BDD
$stmt = $pdo->query("SELECT * FROM livres ORDER BY titre");
$books = $stmt->fetchAll();

// Vérifier si chaque livre est réservé (optionnel)
foreach ($books as &$book) {
    // Vérifier si le livre est actuellement réservé
    $stmt = $pdo->prepare("
        SELECT rl.*, r.date_reservation 
        FROM reservation_livres rl
        JOIN reservations r ON rl.id_reservation = r.id_reservation
        WHERE rl.id_livre = ? 
        AND r.statut = 'confirmee'
        AND DATE_ADD(r.date_reservation, INTERVAL 7 DAY) > CURDATE()
    ");
    $stmt->execute([$book['id_livre']]);
    $reservation = $stmt->fetch();
    
    $book['reserved'] = ($reservation !== false);
    $book['expiry_date'] = $reservation ? date('Y-m-d', strtotime($reservation['date_reservation'] . ' +7 days')) : null;
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8"/>
    <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
    <title>Cozy Café – Books</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" />
    <link rel="stylesheet" href="../style.css" />
    <link rel="stylesheet" href="books.css"/>
    <style>
        /* Alert banners */
        .alert {
            max-width: 700px;
            margin: 20px auto 0;
            padding: 14px 20px;
            border-radius: 10px;
            font-size: 0.95rem;
            text-align: center;
        }
        .alert.error   { background:#f8d7da; color:#721c24; }
        .alert.success { background:#d4edda; color:#155724; }

        /* Badge overlay on book card */
        .book { position: relative; }

        .reserved-badge {
            background: #d4a373;
            color: white;
            padding: 4px 10px;
            border-radius: 12px;
            font-size: 0.7rem;
            position: absolute;
            top: 10px; right: 10px;
            z-index: 2;
            text-align: center;
            line-height: 1.4;
        }
        .available-badge {
            background: #6d7a53;
            color: white;
            padding: 4px 10px;
            border-radius: 12px;
            font-size: 0.7rem;
            position: absolute;
            top: 10px; right: 10px;
            z-index: 2;
        }

        .more.disabled {
            opacity: 0.55;
            background: #aaa;
            cursor: not-allowed;
            pointer-events: none;
        }

        .verify-section {
            background: #f0e8e0;
            padding: 30px;
            margin: 30px auto;
            max-width: 520px;
            border-radius: 15px;
            text-align: center;
            box-shadow: 0 4px 15px rgba(111,78,55,0.1);
        }
        .verify-section h3 { color:#6f4e37; margin-bottom:8px; }
        .verify-section p  { font-size:0.9rem; color:#666; margin-bottom:16px; }
        .verify-section input {
            padding: 10px 14px;
            width: 180px;
            border-radius: 8px;
            border: 1px solid #ccc;
            font-size: 1.1rem;
            letter-spacing: 4px;
            text-align: center;
        }
        .verify-section button {
            padding: 10px 22px;
            background: #6f4e37;
            color: white;
            border: none;
            border-radius: 8px;
            cursor: pointer;
            font-size: 0.95rem;
            margin-left: 8px;
            transition: background 0.2s;
        }
        .verify-section button:hover { background: #8b634b; }
        .vm { margin-top: 14px; padding: 10px; border-radius: 8px; display:none; }
        .vm.error   { background:#f8d7da; color:#721c24; display:block; }
        .vm.success { background:#d4edda; color:#155724; display:block; }

        .button-container {
            display: flex;
            gap: 14px;
            justify-content: center;
            flex-wrap: wrap;
        }

        .discover-btn {
            background: linear-gradient(135deg, #3b2f2f 0%, #5a3e2b 100%);
            box-shadow: 0 8px 24px rgba(59,47,47,0.3);
        }
        .discover-btn:hover {
            background: linear-gradient(135deg, #5a3e2b 0%, #6f4e37 100%);
            box-shadow: 0 14px 32px rgba(59,47,47,0.4);
        }
    </style>
</head>
<body>
    <?php include '../navbar.php'; ?>

    <header>
        <h1>Cozy Café</h1>
        <p>Your daily dose of happiness</p>
    </header>

    <section>
        <h2>Books Available</h2>

        <div class="button-container">
            <a href="#reserve-section" class="how">How does reserving work?</a>
            <a href="books.php" class="how discover-btn">📖 Discover Our Books</a>
        </div>

        <!-- Verify existing code -->
        <div class="verify-section">
            <h3>📖 Vous avez déjà un code ?</h3>
            <p>Entrez votre code à 6 chiffres pour vérifier votre réservation</p>
            <input type="text" id="verifyCode" maxlength="6" placeholder="000000">
            <button onclick="verifyCode()">Vérifier</button>
            <div id="verifyMessage" class="vm"></div>
        </div>

        <br>

        <!-- Book grid -->
        <div class="books">
            <?php if (empty($books)): ?>
                <p>Aucun livre disponible pour le moment.</p>
            <?php else: ?>
                <?php foreach ($books as $book): ?>
                <div class="book">
                    <?php if ($book['reserved']): ?>
                        <span class="reserved-badge">
                            Réservé<br>jusqu'au <?= $book['expiry_date'] ?>
                        </span>
                    <?php else: ?>
                        <span class="available-badge">Disponible</span>
                    <?php endif; ?>

                    <img src="<?= htmlspecialchars($book['image'] ?? 'images/default-book.png') ?>"
                         alt="<?= htmlspecialchars($book['titre']) ?>"
                         class="bookimg"
                         onerror="this.src='images/default-book.png'">

                    <p class="names"><?= htmlspecialchars($book['titre']) ?></p>
                    <p class="names" style="font-size:0.8rem;color:#999;margin-top:2px;">
                        <?= htmlspecialchars($book['auteur'] ?? '') ?>
                    </p>

                    <?php if ($book['reserved']): ?>
                        <a href="verify-code-page.php?book_id=<?= $book['id_livre'] ?>" class="more">
                           🔑 J'ai un code
                        </a>
                    <?php else: ?>
                        <a href="book-detail.php?id=<?= $book['id_livre'] ?>" class="more">
                            📖 Voir détails
                        </a>
                    <?php endif; ?>
                </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>
    </section>

    <!-- How it works -->
    <section id="reserve-section">
        <h1 style="text-align:center">How Our Book Reservation Works</h1>
        <p style="text-align:center;max-width:600px;margin:auto;line-height:1.8;">
            📚 <strong>Réservation gratuite pour 7 jours</strong><br><br>
            1️⃣ Choisissez un livre disponible<br>
            2️⃣ Remplissez le formulaire de réservation<br>
            3️⃣ Recevez un <strong>code à 6 chiffres</strong> par email<br>
            4️⃣ Présentez ce code à votre arrivée<br>
            5️⃣ Le livre est à vous pendant <strong>7 jours</strong> !<br><br>
            ⚠️ <strong>Important :</strong> Le livre doit être consulté sur place.<br>
            🔄 Après 7 jours, le livre redevient automatiquement disponible.
        </p>
    </section>

    <?php include '../footer.php'; ?>

    <script>
    function verifyCode() {
        const code = document.getElementById('verifyCode').value.trim();
        const msgDiv = document.getElementById('verifyMessage');
        msgDiv.className = 'vm';
        msgDiv.innerHTML = '';

        if (!/^\d{6}$/.test(code)) {
            msgDiv.className = 'vm error';
            msgDiv.innerHTML = 'Veuillez entrer un code valide à 6 chiffres.';
            return;
        }

        fetch('../verify_code.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
            body: 'code=' + encodeURIComponent(code)
        })
        .then(r => r.json())
        .then(data => {
            if (!data.valid) {
                msgDiv.className = 'vm error';
                msgDiv.innerHTML = '❌ Code invalide ou introuvable.';
            } else if (data.expired) {
                msgDiv.className = 'vm error';
                msgDiv.innerHTML = '⚠️ Code expiré le ' + data.expiry_date + '.';
            } else {
                msgDiv.className = 'vm success';
                msgDiv.innerHTML =
                    '✅ Code valide ! Livre : <strong>' + data.book_title + '</strong><br>' +
                    'Valable jusqu\'au ' + data.expiry_date + '<br>' +
                    '<a href="../seatingbooks.php?code=' + encodeURIComponent(code) +
                    '" style="color:#6f4e37;font-weight:bold;">→ Réserver ma table</a>';
            }
        })
        .catch(() => {
            msgDiv.className = 'vm error';
            msgDiv.innerHTML = 'Erreur réseau. Réessayez.';
        });
    }

    document.getElementById('verifyCode')?.addEventListener('keydown', e => {
        if (e.key === 'Enter') verifyCode();
    });
    </script>
</body>
</html>