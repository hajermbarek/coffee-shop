<?php
session_start();
require_once 'cnx.php';

// ============================================
// RÉCUPÉRATION DES DONNÉES DU FORMULAIRE
// ============================================

$prenom      = trim($_POST['firstname'] ?? '');
$nom         = trim($_POST['name'] ?? '');
$email       = trim($_POST['email'] ?? '');
$telephone   = trim($_POST['phone'] ?? '');
$nbPersonnes = (int)($_POST['people'] ?? 1);
$allergies   = trim($_POST['allergies'] ?? '');
$commentaires= trim($_POST['comments'] ?? '');
$idTable     = (int)($_POST['table_id'] ?? 0);
$dateResa    = $_POST['reservation_date'] ?? '';
$heureResa   = $_POST['reservation_time'] ?? '';
$activityType= $_POST['activity_type'] ?? null;
$activityId  = (int)($_POST['activity_id'] ?? 0);
$zoneName    = $_POST['zone_name'] ?? 'Non spécifiée';
$tableNumber = $_POST['table_number'] ?? '—';
$reservationCode = trim($_POST['reservation_code'] ?? '');
$hasCode     = isset($_POST['has_code']) && $_POST['has_code'] == 1;

// Validation basique
$errors = [];
if (empty($prenom)) $errors[] = "Le prénom est requis.";
if (empty($nom)) $errors[] = "Le nom est requis.";
if (empty($email)) $errors[] = "L'email est requis.";
if (empty($telephone)) $errors[] = "Le téléphone est requis.";
if (!$idTable) $errors[] = "Aucune table sélectionnée.";
if (empty($dateResa)) $errors[] = "La date est requise.";
if (empty($heureResa)) $errors[] = "L'heure est requise.";

// ============================================
// RÉCUPÉRATION DES INFOS DU CODE (si existant)
// ============================================

$codeInfo = null;
$activityName = '';
$dateExpiration = '';

if ($hasCode && !empty($reservationCode)) {
    $stmt = $pdo->prepare("
        SELECT rl.*, l.titre as book_title, l.id_livre
        FROM reservation_livres rl
        JOIN livres l ON rl.id_livre = l.id_livre
        WHERE rl.code = ?
    ");
    $stmt->execute([$reservationCode]);
    $codeInfo = $stmt->fetch();
    
    if ($codeInfo) {
        $activityName = $codeInfo['book_title'];
        $dateExpiration = $codeInfo['date_expiration'];
        $activityId = $codeInfo['id_livre'];
        $activityType = 'book';
    } else {
        $errors[] = "Code invalide. Veuillez vérifier votre code.";
    }
} else {
    // Si pas de code, récupérer le nom de l'activité normalement
    if ($activityType === 'book' && $activityId) {
        $stmt = $pdo->prepare("SELECT titre FROM livres WHERE id_livre = ?");
        $stmt->execute([$activityId]);
        $activityName = $stmt->fetchColumn();
    } elseif ($activityType === 'game' && $activityId) {
        $stmt = $pdo->prepare("SELECT name FROM game WHERE id = ?");
        $stmt->execute([$activityId]);
        $activityName = $stmt->fetchColumn();
    }
}

// S'il y a des erreurs, afficher et rediriger
if (!empty($errors)) {
    echo "<script>
        alert('Erreurs : " . addslashes(implode("\\n", $errors)) . "');
        window.history.back();
    </script>";
    exit;
}

// ============================================
// VÉRIFIER SI LE CLIENT EXISTE DÉJÀ
// ============================================

try {
    // Vérifier si le client existe
    $stmt = $pdo->prepare("SELECT id_client FROM clients WHERE email = ?");
    $stmt->execute([$email]);
    $idClient = $stmt->fetchColumn();
    
    if (!$idClient) {
        // Créer un nouveau client
        $stmt = $pdo->prepare("INSERT INTO clients (prenom, nom, email, telephone) VALUES (?, ?, ?, ?)");
        $stmt->execute([$prenom, $nom, $email, $telephone]);
        $idClient = $pdo->lastInsertId();
    } else {
        // Mettre à jour les infos du client existant
        $stmt = $pdo->prepare("UPDATE clients SET prenom = ?, nom = ?, telephone = ? WHERE id_client = ?");
        $stmt->execute([$prenom, $nom, $telephone, $idClient]);
    }
    
    // Créer la nouvelle réservation de table
    $stmt = $pdo->prepare("
        INSERT INTO reservations (id_client, id_table, date_reservation, heure_reservation, nb_personnes, allergies, commentaires, statut)
        VALUES (?, ?, ?, ?, ?, ?, ?, 'confirmee')
    ");
    $stmt->execute([$idClient, $idTable, $dateResa, $heureResa, $nbPersonnes, $allergies, $commentaires]);
    $idReservation = $pdo->lastInsertId();
    
    // Si on a un code, on lie la nouvelle réservation au code existant (sans le modifier)
    if ($hasCode && $codeInfo) {
        // Mettre à jour la réservation associée au code
        $stmt = $pdo->prepare("UPDATE reservation_livres SET id_reservation = ? WHERE id_RL = ?");
        $stmt->execute([$idReservation, $codeInfo['id_RL']]);
        
        // Ne PAS modifier exemplaires_disponibles car c'est déjà fait
        // Ne PAS générer de nouveau code
        // Ne PAS modifier date_expiration
    } elseif ($activityType === 'book' && $activityId && !$hasCode) {
        // Cas normal : nouvelle réservation de livre (génère un code)
        $newCode = str_pad(random_int(0, 999999), 6, '0', STR_PAD_LEFT);
        $dateExpiration = date('Y-m-d', strtotime($dateResa . ' +7 days'));
        
        $stmt = $pdo->prepare("
            INSERT INTO reservation_livres (id_reservation, id_livre, code, date_expiration) 
            VALUES (?, ?, ?, ?)
        ");
        $stmt->execute([$idReservation, $activityId, $newCode, $dateExpiration]);
        
        // Diminuer le nombre d'exemplaires disponibles
        $stmt = $pdo->prepare("UPDATE livres SET exemplaires_disponibles = 0 WHERE id_livre = ? AND exemplaires_disponibles = 1");
        $stmt->execute([$activityId]);
        
        $reservationCode = $newCode;
        $activityName = $activityName ?: "Livre réservé";
    } elseif ($activityType === 'game' && $activityId) {
        // Réservation de jeu
        $stmt = $pdo->prepare("INSERT INTO reservation_jeux (id_reservation, id_game) VALUES (?, ?)");
        $stmt->execute([$idReservation, $activityId]);
        
        $stmt = $pdo->prepare("UPDATE game SET exemplaires_disponibles = exemplaires_disponibles - 1 WHERE id = ? AND exemplaires_disponibles > 0");
        $stmt->execute([$activityId]);
        
        $stmt = $pdo->prepare("SELECT name FROM game WHERE id = ?");
        $stmt->execute([$activityId]);
        $activityName = $stmt->fetchColumn();
    }
    
    // Nettoyer la session
    unset($_SESSION['activity']);
    unset($_SESSION['activity_type']);
    unset($_SESSION['activity_id']);
    unset($_SESSION['reservationTable']);
    unset($_SESSION['reservationDate']);
    unset($_SESSION['reservationTime']);
    unset($_SESSION['table_id']);
    unset($_SESSION['reservationZone']);
    
} catch (Exception $e) {
    echo "<script>
        alert('Erreur : " . addslashes($e->getMessage()) . "');
        window.history.back();
    </script>";
    exit;
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Confirmation finale - Cozy Café</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        body {
            font-family: 'Georgia', 'Manrope', serif;
            background: linear-gradient(135deg, #f5f1ee 0%, #e8ddd0 100%);
            min-height: 100vh;
            display: flex;
            justify-content: center;
            align-items: center;
            padding: 20px;
        }
        .confirmation-card {
            max-width: 600px;
            width: 100%;
            background: white;
            border-radius: 24px;
            padding: 40px;
            box-shadow: 0 20px 40px rgba(0,0,0,0.15);
            animation: fadeIn 0.5s ease;
        }
        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(20px); }
            to { opacity: 1; transform: translateY(0); }
        }
        .checkmark {
            width: 80px;
            height: 80px;
            background: #d4edda;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 20px;
        }
        .checkmark svg {
            width: 50px;
            height: 50px;
            fill: #155724;
        }
        h1 {
            color: #6f4e37;
            font-size: 28px;
            margin-bottom: 10px;
            text-align: center;
        }
        .subtitle {
            color: #999;
            margin-bottom: 30px;
            text-align: center;
        }
        .code-container {
            background: #f0e8e0;
            padding: 20px;
            border-radius: 16px;
            margin: 20px 0;
            text-align: center;
        }
        .code-label {
            font-size: 14px;
            color: #6f4e37;
            text-transform: uppercase;
            letter-spacing: 2px;
            margin-bottom: 10px;
        }
        .code {
            font-size: 48px;
            font-weight: bold;
            color: #6f4e37;
            letter-spacing: 10px;
            font-family: 'Courier New', monospace;
            word-break: break-all;
        }
        .expiry {
            margin-top: 10px;
            font-size: 14px;
            color: #d4a373;
        }
        .info-row {
            display: flex;
            justify-content: space-between;
            padding: 12px 0;
            border-bottom: 1px solid #eee;
            text-align: left;
        }
        .info-label {
            font-weight: 600;
            color: #6f4e37;
        }
        .info-value {
            color: #555;
        }
        .warning-box {
            background: #fff3cd;
            border-left: 4px solid #ffc107;
            padding: 15px;
            margin: 20px 0;
            text-align: left;
            border-radius: 8px;
        }
        .warning-box p {
            margin: 5px 0;
            color: #856404;
        }
        .btn {
            display: inline-block;
            padding: 14px 28px;
            background: #6f4e37;
            color: white;
            text-decoration: none;
            border-radius: 40px;
            margin-top: 20px;
            transition: all 0.3s ease;
            font-weight: 600;
            border: none;
            cursor: pointer;
        }
        .btn:hover {
            background: #8b634b;
            transform: translateY(-2px);
        }
        .btn-secondary {
            background: transparent;
            color: #6f4e37;
            border: 2px solid #6f4e37;
            margin-left: 10px;
        }
        .btn-secondary:hover {
            background: #6f4e37;
            color: white;
            transform: translateY(-2px);
        }
        .footer-text {
            margin-top: 30px;
            font-size: 12px;
            color: #999;
            text-align: center;
        }
        .success-icon {
            text-align: center;
            font-size: 48px;
            margin-bottom: 20px;
        }
        @media (max-width: 500px) {
            .confirmation-card { padding: 25px; }
            .code { font-size: 32px; letter-spacing: 6px; }
        }
    </style>
</head>
<body>
    <div class="confirmation-card">
        <div class="success-icon">🎉</div>
        
        <h1>✅ Réservation confirmée !</h1>
        <p class="subtitle">Votre place au Cozy Café est réservée</p>
        
        <?php if ($hasCode && $codeInfo): ?>
            <!-- Cas avec code existant (pas de nouveau code généré) -->
            <div class="code-container">
                <div class="code-label">🔑 VOTRE CODE EXISTANT</div>
                <div class="code"><?= htmlspecialchars($reservationCode) ?></div>
                <div class="expiry">📅 Valable jusqu'au <?= date('d/m/Y', strtotime($dateExpiration)) ?></div>
                <small style="display:block; margin-top:10px; color:#6f4e37;">
                    ⚠️ Ce code a été conservé - Date d'expiration inchangée
                </small>
            </div>
        <?php elseif ($activityType === 'book'): ?>
            <!-- Nouvelle réservation de livre avec nouveau code -->
            <div class="code-container">
                <div class="code-label">🔑 VOTRE CODE UNIQUE</div>
                <div class="code"><?= htmlspecialchars($reservationCode) ?></div>
                <div class="expiry">📅 Valable jusqu'au <?= date('d/m/Y', strtotime($dateExpiration)) ?></div>
            </div>
        <?php endif; ?>
        
        <div style="text-align: left; margin: 20px 0;">
            <div class="info-row">
                <span class="info-label"><?= $activityType === 'book' ? '📖 Livre réservé' : '🎮 Jeu réservé' ?></span>
                <span class="info-value"><?= htmlspecialchars($activityName) ?></span>
            </div>
            <div class="info-row">
                <span class="info-label">👤 Client</span>
                <span class="info-value"><?= htmlspecialchars($prenom) ?> <?= htmlspecialchars($nom) ?></span>
            </div>
            <div class="info-row">
                <span class="info-label">📧 Email</span>
                <span class="info-value"><?= htmlspecialchars($email) ?></span>
            </div>
            <div class="info-row">
                <span class="info-label">📞 Téléphone</span>
                <span class="info-value"><?= htmlspecialchars($telephone) ?></span>
            </div>
            <div class="info-row">
                <span class="info-label">🪑 Table</span>
                <span class="info-value">Table n°<?= $idTable ?> (<?= htmlspecialchars($zoneName) ?>)</span>
            </div>
            <div class="info-row">
                <span class="info-label">📅 Date & Heure</span>
                <span class="info-value"><?= date('d/m/Y', strtotime($dateResa)) ?> à <?= $heureResa ?></span>
            </div>
            <div class="info-row">
                <span class="info-label">👥 Nombre de personnes</span>
                <span class="info-value"><?= $nbPersonnes ?></span>
            </div>
        </div>
        
        <div class="warning-box">
            <p><strong>⚠️ À conserver précieusement</strong></p>
            <p>Présentez ce code à l'accueil du Cozy Café.</p>
            <?php if ($hasCode && $codeInfo): ?>
            <p>Le code expire le <strong><?= date('d/m/Y', strtotime($dateExpiration)) ?></strong> (inchangé).</p>
            <?php else: ?>
            <p>Le code expire automatiquement après 7 jours.</p>
            <?php endif; ?>
        </div>
        
        <div style="text-align: center;">
            <a href="page1.php" class="btn">🏠 Retour à l'accueil</a>
            <button onclick="window.print()" class="btn btn-secondary">🖨️ Imprimer</button>
        </div>
        
        <div class="footer-text">
            <p>Cozy Café - Centre Urbain Nord, Tunis</p>
            <p>Réservation confirmée le <?= date('d/m/Y à H:i') ?></p>
        </div>
    </div>
    
    <script>
        // Optionnel : enregistrer dans localStorage pour consultation ultérieure
        <?php if ($reservationCode): ?>
        localStorage.setItem('cozy_last_code', '<?= addslashes($reservationCode) ?>');
        localStorage.setItem('cozy_last_activity', '<?= addslashes($activityName) ?>');
        localStorage.setItem('cozy_last_expiry', '<?= addslashes($dateExpiration) ?>');
        <?php endif; ?>
    </script>
</body>
</html>