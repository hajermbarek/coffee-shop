<?php
require_once 'cnx.php';
session_start();

// ============================================
// FONCTIONS UTILITAIRES
// ============================================

function getClientByEmail($pdo, $email) {
    $stmt = $pdo->prepare("SELECT id_client FROM clients WHERE email = ?");
    $stmt->execute([$email]);
    return $stmt->fetchColumn();
}

function insertClient($pdo, $prenom, $nom, $email, $telephone) {
    $stmt = $pdo->prepare("INSERT INTO clients (prenom, nom, email, telephone) VALUES (?, ?, ?, ?)");
    $stmt->execute([$prenom, $nom, $email, $telephone]);
    return $pdo->lastInsertId();
}

function generateCode() {
    return str_pad(random_int(0, 999999), 6, '0', STR_PAD_LEFT);
}

// ============================================
// RÉCUPÉRATION ET VALIDATION DES DONNÉES
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

// Validation
$errors = [];
if (empty($prenom)) $errors[] = "Le prénom est requis.";
if (empty($nom)) $errors[] = "Le nom est requis.";
if (empty($email)) $errors[] = "L'email est requis.";
if (empty($telephone)) $errors[] = "Le téléphone est requis.";
if (!$idTable) $errors[] = "Aucune table sélectionnée.";
if (empty($dateResa)) $errors[] = "La date est requise.";
if (empty($heureResa)) $errors[] = "L'heure est requise.";
if (!$activityId) $errors[] = "Aucune activité sélectionnée.";

if (!empty($errors)) {
    echo "<script>alert('Erreurs : " . addslashes(implode("\\n", $errors)) . "'); window.history.back();</script>";
    exit;
}

// ============================================
// TRAITEMENT BASE DE DONNÉES
// ============================================

try {
    $pdo->beginTransaction();
    
    // 1. Gestion du client
    $idClient = getClientByEmail($pdo, $email);
    if (!$idClient) {
        $idClient = insertClient($pdo, $prenom, $nom, $email, $telephone);
    } else {
        $stmt = $pdo->prepare("UPDATE clients SET prenom = ?, nom = ?, telephone = ? WHERE id_client = ?");
        $stmt->execute([$prenom, $nom, $telephone, $idClient]);
    }
    
    // 2. Insertion de la réservation de table
    $stmt = $pdo->prepare("
        INSERT INTO reservations (id_client, id_table, date_reservation, heure_reservation, nb_personnes, allergies, commentaires, statut)
        VALUES (?, ?, ?, ?, ?, ?, ?, 'confirmee')
    ");
    $stmt->execute([$idClient, $idTable, $dateResa, $heureResa, $nbPersonnes, $allergies, $commentaires]);
    $idReservation = $pdo->lastInsertId();
    
    // 3. Gestion selon le type d'activité (LIVRE ou JEU)
    
    if ($activityType === 'book' && $activityId) {
        // ============================================
        // TRAITEMENT POUR LES LIVRES
        // ============================================
        
        // Vérifier si le livre est déjà emprunté
        $stmt = $pdo->prepare("
            SELECT rl.id_RL, r.id_reservation, r.id_client, r.date_reservation
            FROM reservation_livres rl
            JOIN reservations r ON rl.id_reservation = r.id_reservation
            WHERE rl.id_livre = ?
            AND r.statut = 'confirmee'
            AND r.date_reservation >= DATE_SUB(CURDATE(), INTERVAL 7 DAY)
            ORDER BY r.date_reservation DESC
            LIMIT 1
        ");
        $stmt->execute([$activityId]);
        $existing = $stmt->fetch();

        if ($existing) {
            // Un emprunt actif existe
            if ($existing['id_client'] == $idClient) {
                // Même client : on met à jour l'id_reservation
                $stmtUpdate = $pdo->prepare("UPDATE reservation_livres SET id_reservation = ? WHERE id_RL = ?");
                $stmtUpdate->execute([$idReservation, $existing['id_RL']]);
            } else {
                throw new Exception("Ce livre est déjà emprunté par un autre client.");
            }
        } else {
            // Aucun emprunt actif : insertion normale
            $code = generateCode();
            $dateExpiration = date('Y-m-d', strtotime($dateResa . ' +7 days'));
            
            $stmt = $pdo->prepare("
                INSERT INTO reservation_livres (id_reservation, id_livre, code, date_expiration) 
                VALUES (?, ?, ?, ?)
            ");
            $stmt->execute([$idReservation, $activityId, $code, $dateExpiration]);
            
            // Diminuer le nombre d'exemplaires disponibles
            $stmt = $pdo->prepare("UPDATE livres SET exemplaires_disponibles = exemplaires_disponibles - 1 WHERE id_livre = ? AND exemplaires_disponibles > 0");
            $stmt->execute([$activityId]);
        }
        
        // Récupérer le titre du livre
        $stmt = $pdo->prepare("SELECT titre FROM livres WHERE id_livre = ?");
        $stmt->execute([$activityId]);
        $activityName = $stmt->fetchColumn();



        
    } elseif ($activityType === 'game' && $activityId) {

    // Vérifier le stock dynamiquement à cette date
   $stmt = $pdo->prepare("
    SELECT COUNT(*) 
    FROM reservation_jeux rj
    JOIN reservations r ON rj.id_reservation = r.id_reservation
    WHERE rj.id_game = ?
    AND r.date_reservation = ?
    AND r.statut = 'confirmee'
    AND r.heure_reservation BETWEEN 
        SUBTIME(?, '03:00:00') AND ADDTIME(?, '03:00:00')
");
    $stmt->execute([$activityId, $dateResa, $heureResa, $heureResa]);
    $dejaPris = $stmt->fetchColumn();

    $stmtTotal = $pdo->prepare("SELECT exemplaires_total FROM game WHERE id = ?");
    $stmtTotal->execute([$activityId]);
    $total = $stmtTotal->fetchColumn();

    if ($dejaPris >= $total) {
        throw new Exception("Ce jeu n'est plus disponible à cette date.");
    }

    // Stock OK → insérer
    $stmt = $pdo->prepare("INSERT INTO reservation_jeux (id_reservation, id_game) VALUES (?, ?)");
    $stmt->execute([$idReservation, $activityId]);

    // Récupérer le nom du jeu
    $stmt = $pdo->prepare("SELECT name FROM game WHERE id = ?");
    $stmt->execute([$activityId]);
    $activityName = $stmt->fetchColumn();

    $code = null;
    $dateExpiration = null;
}
    // Valider la transaction
    $pdo->commit();
    
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
    $pdo->rollBack();
    echo "<script>alert('Erreur : " . addslashes($e->getMessage()) . "'); window.history.back();</script>";
    exit;
}

// ============================================
// PAGE DE CONFIRMATION
// ============================================
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Confirmation - Cozy Café</title>
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
            max-width: 550px;
            width: 100%;
            background: white;
            border-radius: 24px;
            padding: 40px;
            box-shadow: 0 20px 40px rgba(0,0,0,0.15);
            text-align: center;
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
        }
        .subtitle {
            color: #999;
            margin-bottom: 30px;
        }
        .code-container {
            background: #f0e8e0;
            padding: 20px;
            border-radius: 16px;
            margin: 20px 0;
        }
        .code-label {
            font-size: 14px;
            color: #6f4e37;
            text-transform: uppercase;
            letter-spacing: 2px;
            margin-bottom: 10px;
        }
        .code {
            font-size: 52px;
            font-weight: bold;
            color: #6f4e37;
            letter-spacing: 12px;
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
        }
        @media (max-width: 500px) {
            .confirmation-card { padding: 25px; }
            .code { font-size: 32px; letter-spacing: 8px; }
        }
    </style>
</head>
<body>
    <div class="confirmation-card">
        <div class="checkmark">
            <svg viewBox="0 0 24 24">
                <path d="M9 16.17L4.83 12l-1.42 1.41L9 19 21 7l-1.41-1.41L9 16.17z"/>
            </svg>
        </div>
        
        <h1>✅ Réservation confirmée !</h1>
        <p class="subtitle">Votre place au Cozy Café est réservée</p>
        
        <?php if ($activityType === 'book' && $code): ?>
        <div class="code-container">
            <div class="code-label">🔑 VOTRE CODE UNIQUE</div>
            <div class="code"><?= $code ?></div>
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
        
        <?php if ($activityType === 'book'): ?>
        <div class="warning-box">
            <p><strong>⚠️ À conserver précieusement</strong></p>
            <p>Présentez ce code à l'accueil du Cozy Café pour récupérer votre livre.</p>
            <p>Le code expire automatiquement après 7 jours.</p>
        </div>
        <?php else: ?>
        <div class="warning-box">
            <p><strong>🎲 Bon jeu !</strong></p>
            <p>Rendez-vous à l'accueil pour récupérer votre jeu.</p>
            <p>N'oubliez pas de le rendre avant de partir.</p>
        </div>
        <?php endif; ?>
        
        <div>
            <a href="page1.php" class="btn">🏠 Retour à l'accueil</a>
            <button onclick="window.print()" class="btn btn-secondary">🖨️ Imprimer</button>
        </div>
        
        <div class="footer-text">
            <p>Cozy Café - Centre Urbain Nord, Tunis</p>
            <?php if ($activityType === 'book' && $code): ?>
            <p>Code : <strong><?= $code ?></strong> | Expire le <?= date('d/m/Y', strtotime($dateExpiration)) ?></p>
            <?php endif; ?>
        </div>
    </div>
    
    <script>
        <?php if ($activityType === 'book' && $code): ?>
        localStorage.setItem('cozy_last_code', '<?= $code ?>');
        localStorage.setItem('cozy_last_activity', '<?= addslashes($activityName) ?>');
        localStorage.setItem('cozy_last_expiry', '<?= $dateExpiration ?>');
        <?php endif; ?>
    </script>
</body>
</html>