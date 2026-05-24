<?php
session_start();
require_once '../cnx.php';

if (isset($_GET['check_availability'])) {
    header('Content-Type: application/json');
    $date = $_GET['date'] ?? '';
    $time = $_GET['time'] ?? '';

    if (!$date || !$time) {
        echo json_encode(['reserved' => []]);
        exit;
    }

    $stmt = $pdo->prepare(
        "SELECT id_table FROM reservations
        WHERE date_reservation = :date
        AND heure_reservation = :time
        AND statut != 'annulee'"
    );
    $stmt->execute([':date' => $date, ':time' => $time . ':00']);
    $rows = $stmt->fetchAll(PDO::FETCH_COLUMN);
    echo json_encode(['reserved' => array_map('intval', $rows)]);
    exit;
}

// Récupérer les paramètres GET depuis games.php
if (isset($_GET['game'])) {
    $_SESSION['activity'] = $_GET['game'];
    $_SESSION['activity_type'] = 'game';
    $_SESSION['activity_id'] = $_GET['game_id'] ?? 0;
}

// Récupérer les paramètres GET depuis books.php
if (isset($_GET['book'])) {
    $_SESSION['activity'] = $_GET['book'];
    $_SESSION['activity_type'] = 'book';
    $_SESSION['activity_id'] = $_GET['book_id'] ?? 0;
}

//recuperation de code de reservation 

$prefilledCode = '';
$codeActivity = '';
$codeActivityId = null;

if (isset($_GET['code'])) {
    $code = $_GET['code'];
    
    // Récupérer les infos liées à ce code
    $stmt = $pdo->prepare("
        SELECT rl.*, l.titre as book_title, l.id_livre
        FROM reservation_livres rl
        JOIN livres l ON rl.id_livre = l.id_livre
        WHERE rl.code = ?
        AND rl.date_expiration > CURDATE()
    ");
    $stmt->execute([$code]);
    $codeData = $stmt->fetch();
    
    if ($codeData) {
        $prefilledCode = $code;
        $codeActivity = $codeData['book_title'];
        $codeActivityId = $codeData['id_livre'];
        
        // Si aucune activité n'est encore en session, utiliser celle du code
        if (!isset($_SESSION['activity']) || $_SESSION['activity'] == 'Aucune activité choisie') {
            $_SESSION['activity'] = $codeActivity;
            $_SESSION['activity_type'] = 'book';
            $_SESSION['activity_id'] = $codeActivityId;
        }
    }
}

// Récupération des données de session 
$zone          = $_SESSION['reservationZone'] ?? 'Non spécifiée';
$tableNum      = $_SESSION['reservationTable'] ?? '—';
$tableId       = $_SESSION['table_id'] ?? null;       
$dateResa      = $_SESSION['reservationDate'] ?? '';
$heureResa     = $_SESSION['reservationTime'] ?? '';
$activity      = $_SESSION['activity'] ?? 'Aucune activité choisie';
$activityType  = $_SESSION['activity_type'] ?? null;  
$activityId    = $_SESSION['activity_id'] ?? null;

// Si on a un code mais pas d'activité en session, utiliser les données du code
if ($prefilledCode && !$activityId) {
    $activity = $codeActivity;
    $activityType = 'book';
    $activityId = $codeActivityId;
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Confirmation de Réservation - Cozy Café</title>
    <link rel="stylesheet" href="reservation.css">
    <style>
        /* Petit style pour le champ code */
        .code-field {
            background: #f0e8e0;
            border-radius: 8px;
            padding: 2px 10px;
        }
        .code-field input {
            font-family: 'Courier New', monospace;
            letter-spacing: 2px;
            font-weight: bold;
        }
    </style>
</head>
<body>
    <header>
        <h1>Cozy Café</h1>
        <p>Your daily dose of happiness</p>
    </header>

    <form id="finalReservationForm" class="reservation-form" action="reservation_finale.php" method="POST">
        <h2>Finalize Your Reservation</h2>

        <label for="firstname">Prénom :</label>
        <input type="text" id="firstname" name="firstname" required>

        <label for="name">Nom :</label>
        <input type="text" id="name" name="name" required>

        <label for="email">Email :</label>
        <input type="email" id="email" name="email" required>

        <label for="phone">Téléphone :</label>
        <input type="tel" id="phone" name="phone" required>

        <label>Date de réservation :</label>
        <input type="text" id="dateDisplay" name="date_display" value="<?= htmlspecialchars($dateResa) ?>" readonly>

        <label>Heure :</label>
        <input type="text" id="timeDisplay" name="time_display" value="<?= htmlspecialchars($heureResa) ?>" readonly>

        <label for="people">Nombre de personnes :</label>
        <input type="number" id="people" name="people" min="1" required>

        <label>Zone :</label>
        <input type="text" id="zoneDisplay" name="zone_display" value="<?= htmlspecialchars($zone) ?>" readonly>

        <label>N°table :</label>
        <input type="text" id="tableNumber" name="table_display" value="Table <?= htmlspecialchars($tableNum) ?>" readonly>


        <label>🔑 Code de réservation :</label>
        <div class="code-field">
            <input type="text" id="reservationCode" name="reservation_code" 
                value="<?= htmlspecialchars($prefilledCode) ?>"
                placeholder="Entrez votre code à 6 chiffres"
                <?= $prefilledCode ? 'readonly style="background:#e8e0d8;"' : '' ?>>
            <?php if (!$prefilledCode): ?>
                <small style="display:block; color:#999; margin-top:5px;">
                    Si vous avez déjà un code, entrez-le ici
                </small>
            <?php else: ?>
                <small style="display:block; color:#6f4e37; margin-top:5px;">
                    ✅ Code valide - Livre pré-sélectionné : <?= htmlspecialchars($codeActivity) ?>
                </small>
            <?php endif; ?>
        </div>

        <label>Activité spécifique :</label>
        <input type="text" id="activityDisplay" name="activity_display" value="<?= htmlspecialchars($activity) ?>" readonly>

        <label for="allergies">Allergies alimentaires :</label>
        <textarea id="allergies" name="allergies" placeholder="Ex: Noix, gluten..."></textarea>

        <label for="comments">Commentaires supplémentaires :</label>
        <textarea id="comments" name="comments" placeholder="Demandes particulières..."></textarea>

        <!-- Champs cachés existants -->
        <input type="hidden" name="table_id" value="<?= $tableId ?>">
        <input type="hidden" name="reservation_date" value="<?= htmlspecialchars($dateResa) ?>">
        <input type="hidden" name="reservation_time" value="<?= htmlspecialchars($heureResa) ?>">
        <input type="hidden" name="activity_type" value="<?= $activityType ?>">
        <input type="hidden" name="activity_id" value="<?= $activityId ?>">
        <input type="hidden" name="zone_name" value="<?= htmlspecialchars($zone) ?>">
        <input type="hidden" name="table_number" value="<?= htmlspecialchars($tableNum) ?>">
        
        <!-- NOUVEAU : Champ caché pour le code s'il est pré-rempli -->
        <?php if ($prefilledCode): ?>
        <input type="hidden" name="has_code" value="1">
        <?php endif; ?>

        <button type="button" onclick="goBack()">Modifier le choix</button>
        <button type="submit">Confirmer la réservation</button>
    </form>

    <script>
        function goBack() {
            let zone = "<?= addslashes($zone) ?>";
            if (zone.includes('Quiet')) {
                window.location.href = '../books/books.php';
            } else if (zone.includes('Fun')) {
                window.location.href = '../games/games.php';
            } else {
                window.history.back();
            }
        }
        
        // Vérification du code en temps réel (optionnel)
        const codeInput = document.getElementById('reservationCode');
        if (codeInput && !codeInput.readOnly) {
            codeInput.addEventListener('blur', function() {
                const code = this.value.trim();
                if (/^\d{6}$/.test(code)) {
                    fetch('verify_code.php', {
                        method: 'POST',
                        headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                        body: 'code=' + encodeURIComponent(code)
                    })
                    .then(r => r.json())
                    .then(data => {
                        if (data.valid && !data.expired) {
                            const activityField = document.getElementById('activityDisplay');
                            if (activityField && data.book_title) {
                                activityField.value = data.book_title;
                            }
                            // Ajouter un message de confirmation
                            let msgDiv = document.getElementById('codeMessage');
                            if (!msgDiv) {
                                msgDiv = document.createElement('div');
                                msgDiv.id = 'codeMessage';
                                codeInput.parentNode.appendChild(msgDiv);
                            }
                            msgDiv.innerHTML = '<span style="color:#155724;">✅ Code valide !</span>';
                            msgDiv.style.marginTop = '5px';
                        } else if (data.expired) {
                            alert('⚠️ Ce code a expiré le ' + data.expiry_date);
                        } else {
                            alert('❌ Code invalide');
                        }
                    })
                    .catch(() => {
                        alert('Erreur de vérification');
                    });
                }
            });
        }
    </script>
</body>
</html>