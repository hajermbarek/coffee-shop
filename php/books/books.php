<?php
require_once '../cnx.php';
require_once '../lib/BookManager.php';

session_start();

$bookManager = new BookManager($pdo);

// ── Auto-release expired reservations on every page load ──────────────────────
$bookManager->releaseExpiredReservations();

// ── Fetch all books with their reservation status ─────────────────────────────
$books = $bookManager->getAllBooks();
foreach ($books as &$book) {
    $book['reservation_status'] = $bookManager->isBookReserved($book['id_livre']);
}
unset($book);

// ── Error / success messages from redirects ───────────────────────────────────
$error   = $_GET['error']   ?? null;
$success = $_GET['success'] ?? null;
$errorMessages = [
    'invalid_code'  => '❌ Code invalide. Veuillez réessayer.',
    'expired_code'  => '⚠️ Ce code a expiré. Le livre est à nouveau disponible.',
    'book_reserved' => '⚠️ Ce livre vient d\'être réservé par quelqu\'un d\'autre.',
    'no_code'       => '❌ Aucun code fourni.',
];
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8"/>
    <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
    <title>Cozy Café – Books</title>
    <link rel="stylesheet" href="books.css"/>
    <style>
        /* ── Alert banners ── */
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

        /* ── Badge overlay on book card ── */
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

        /* ── Disabled button ── */
        .more.disabled {
            opacity: 0.55;
            background: #aaa;
            cursor: not-allowed;
            pointer-events: none;
        }

        /* ── Verify-code section ── */
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

        /* ── Button container ── */
        .button-container {
            display: flex;
            gap: 14px;
            justify-content: center;
            flex-wrap: wrap;
        }

        /* ── Discover button ── */
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

    <?php if ($error && isset($errorMessages[$error])): ?>
        <div class="alert error"><?= $errorMessages[$error] ?></div>
    <?php endif; ?>
    <?php if ($success === 'released'): ?>
        <div class="alert success">✅ Votre réservation a bien été libérée.</div>
    <?php endif; ?>

    <section>
        <h2>Books Available</h2>

        <div class="button-container">
            <a href="#reserve-section" class="how">How does reserving work?</a>
            <a href="../../javascript/books.html" class="how discover-btn">📖 Discover Our Books</a>
        </div>

        <!-- ── Verify existing code ── -->
        <div class="verify-section">
            <h3>📖 Vous avez déjà un code ?</h3>
            <p>Entrez votre code à 6 chiffres pour vérifier votre réservation</p>
            <input type="text" id="verifyCode" maxlength="6" placeholder="000000">
            <button onclick="verifyCode()">Vérifier</button>
            <div id="verifyMessage" class="vm"></div>
        </div>

        <br>

        <!-- ── Book grid ── -->
        <div class="books">
            <?php foreach ($books as $book): ?>
            <?php $status = $book['reservation_status']; ?>
            <div class="book">

                <?php if ($status['reserved']): ?>
                    <span class="reserved-badge">
                        Réservé<br>jusqu'au <?= $status['until_date'] ?>
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

                <?php if ($status['reserved']): ?>
                    <!-- Reserved → go to code verification page -->
                    <a href="verify-code-page.php?book_id=<?= $book['id_livre'] ?>"
                       class="more">
                       🔑 J'ai un code
                    </a>
                <?php else: ?>
                    <!-- Available → go to reservation form -->
                    <a href="book-detail.php?id=<?= $book['id_livre'] ?>" class="more">
                        Réserver
                    </a>
                <?php endif; ?>

            </div>
            <?php endforeach; ?>
        </div>
    </section>

    <!-- ── How it works ── -->
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

        fetch('verify-code.php', {
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
                    '<a href="reserve-book.php?code=' + encodeURIComponent(code) +
                    '" style="color:#6f4e37;font-weight:bold;">→ Réserver ma table</a>';
            }
        })
        .catch(() => {
            msgDiv.className = 'vm error';
            msgDiv.innerHTML = 'Erreur réseau. Réessayez.';
        });
    }

    // Allow pressing Enter in the code input
    document.getElementById('verifyCode').addEventListener('keydown', e => {
        if (e.key === 'Enter') verifyCode();
    });
    </script>
</body>
</html>