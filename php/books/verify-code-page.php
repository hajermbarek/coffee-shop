<?php
require_once '../cnx.php';
require_once '../lib/BookManager.php';

session_start();

$bookManager = new BookManager($pdo);
$bookId = isset($_GET['book_id']) ? (int)$_GET['book_id'] : 0;
$book   = $bookId ? $bookManager->getBookById($bookId) : null;
$status = $book   ? $bookManager->isBookReserved($bookId) : null;

// If book is no longer reserved, redirect to detail page
if ($book && $status && !$status['reserved']) {
    header("Location: book-detail.php?id={$bookId}");
    exit;
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8"/>
    <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
    <title>Vérification du code – Cozy Café</title>
    <link rel="stylesheet" href="books.css"/>
    <style>
        .verify-page {
            max-width: 480px;
            margin: 60px auto;
            background: white;
            border-radius: 16px;
            padding: 40px;
            box-shadow: 0 12px 36px rgba(111,78,55,0.12);
            text-align: center;
        }
        .book-preview-sm img {
            max-width: 110px;
            border-radius: 10px;
            box-shadow: 0 4px 12px rgba(0,0,0,0.12);
            margin-bottom: 12px;
        }
        .book-preview-sm h3 { color:#3b2f2f; margin:0 0 4px; }
        .book-preview-sm p  { color:#888; font-size:0.85rem; margin:0 0 20px; }

        .reserved-info {
            background: #fff3e0;
            border-radius: 10px;
            padding: 14px 18px;
            margin: 0 0 28px;
            font-size: 0.9rem;
            color: #7a4f00;
        }

        .verify-page h2 { color:#6f4e37; margin-bottom:8px; }
        .verify-page p.sub { color:#888; font-size:0.9rem; margin-bottom:24px; }

        .code-input-group {
            display: flex;
            gap: 10px;
            justify-content: center;
            margin-bottom: 10px;
        }
        .code-input-group input {
            width: 180px;
            padding: 12px 14px;
            border-radius: 10px;
            border: 2px solid #e0d4cc;
            font-size: 1.5rem;
            letter-spacing: 8px;
            text-align: center;
            transition: border-color 0.2s;
        }
        .code-input-group input:focus {
            outline: none;
            border-color: #6f4e37;
        }
        .code-input-group button {
            padding: 12px 20px;
            background: #6f4e37;
            color: white;
            border: none;
            border-radius: 10px;
            cursor: pointer;
            font-size: 0.95rem;
            font-weight: 700;
            transition: background 0.2s;
        }
        .code-input-group button:hover { background: #8b634b; }

        .msg { margin-top:14px; padding:12px; border-radius:9px; font-size:0.9rem; display:none; }
        .msg.error   { background:#f8d7da; color:#721c24; display:block; }
        .msg.success { background:#d4edda; color:#155724; display:block; }

        .back-link {
            display:block; margin-top:22px;
            color:#6f4e37; font-size:0.9rem;
        }
    </style>
</head>
<body>
    <?php include '../navbar.php'; ?>
    <header>
        <h1>Cozy Café</h1>
        <p>Your daily dose of happiness</p>
    </header>

    <div class="verify-page">

        <?php if ($book): ?>
        <div class="book-preview-sm">
            <img src="<?= htmlspecialchars($book['image'] ?? 'images/default-book.png') ?>"
                 alt="<?= htmlspecialchars($book['titre']) ?>"
                 onerror="this.src='images/default-book.png'">
            <h3><?= htmlspecialchars($book['titre']) ?></h3>
            <p><?= htmlspecialchars($book['auteur'] ?? '') ?></p>
        </div>

        <?php if ($status && $status['reserved']): ?>
        <div class="reserved-info">
            🔒 Ce livre est réservé jusqu'au <strong><?= $status['until_date'] ?></strong>
        </div>
        <?php endif; ?>
        <?php endif; ?>

        <h2>🔑 Entrez votre code</h2>
        <p class="sub">Entrez votre code à 6 chiffres pour accéder à ce livre</p>

        <div class="code-input-group">
            <input type="text" id="codeInput" maxlength="6"
                   placeholder="000000" autocomplete="off">
            <button onclick="checkCode()">Vérifier</button>
        </div>

        <div id="message" class="msg"></div>

        <a href="books.php" class="back-link">← Retour aux livres</a>
    </div>

    <?php include '../footer.php'; ?>

    <script>
    const bookId = <?= $bookId ?: 'null' ?>;

    function checkCode() {
        const code = document.getElementById('codeInput').value.trim();
        const msgDiv = document.getElementById('message');
        msgDiv.className = 'msg';
        msgDiv.innerHTML = '';

        if (!/^\d{6}$/.test(code)) {
            msgDiv.className = 'msg error';
            msgDiv.innerHTML = '⚠️ Veuillez entrer un code valide à 6 chiffres.';
            return;
        }

        fetch('verify-code.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
            body: 'code=' + encodeURIComponent(code)
                + (bookId ? '&book_id=' + bookId : '')
        })
        .then(r => r.json())
        .then(data => {
            if (!data.valid) {
                msgDiv.className = 'msg error';
                msgDiv.innerHTML = '❌ Code invalide. Ce code ne correspond pas à ce livre.';
            } else if (data.expired) {
                msgDiv.className = 'msg error';
                msgDiv.innerHTML = '⚠️ Ce code a expiré le ' + data.expiry_date + '.';
            } else {
                msgDiv.className = 'msg success';
                msgDiv.innerHTML =
                    '✅ Code valide ! <strong>' + data.book_title + '</strong><br>' +
                    'Valide jusqu\'au ' + data.expiry_date + '<br>' +
                    '<a href="reserve-book.php?code=' + encodeURIComponent(code) +
                    '" style="color:#155724;font-weight:bold;">→ Réserver ma table</a>';
            }
        })
        .catch(() => {
            msgDiv.className = 'msg error';
            msgDiv.innerHTML = 'Erreur réseau. Réessayez.';
        });
    }

    document.getElementById('codeInput').addEventListener('keydown', e => {
        if (e.key === 'Enter') checkCode();
    });
    </script>
</body>
</html>