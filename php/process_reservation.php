<?php
require_once 'cnx.php';
session_start();

// ============================================
// PARTIE 1 : FONCTIONS UTILITAIRES (INVISIBLE)
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
// PARTIE 2 : RÉCUPÉRATION ET VALIDATION (INVISIBLE)
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
$activityType= $_SESSION['activity_type'] ?? null;
$activityId  = (int)($_SESSION['activity_id'] ?? 0);

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
// PARTIE 3 : TRAITEMENT BDD (INVISIBLE)
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
    
    // 2. Insertion de la réservation
    $stmt = $pdo->prepare("
        INSERT INTO reservations (id_client, id_table, date_reservation, heure_reservation, nb_personnes, allergies, commentaires, statut)
        VALUES (?, ?, ?, ?, ?, ?, ?, 'confirmee')
    ");
    $stmt->execute([$idClient, $idTable, $dateResa, $heureResa, $nbPersonnes, $allergies, $commentaires]);
    $idReservation = $pdo->lastInsertId();
    
    // 3. Gestion du livre et génération du code
    $code = generateCode();
    $dateExpiration = date('Y-m-d', strtotime($dateResa . ' +7 days'));
    
    $stmt = $pdo->prepare("
        INSERT INTO reservation_livres (id_reservation, id_livre, code, date_expiration) 
        VALUES (?, ?, ?, ?)
    ");
    $stmt->execute([$idReservation, $activityId, $code, $dateExpiration]);
    
    // 4. Diminuer le nombre d'exemplaires disponibles
    $stmt = $pdo->prepare("UPDATE livres SET exemplaires_disponibles = exemplaires_disponibles - 1 WHERE id_livre = ?");
    $stmt->execute([$activityId]);
    
    // 5. Récupérer le titre du livre pour l'affichage
    $stmt = $pdo->prepare("SELECT titre FROM livres WHERE id_livre = ?");
    $stmt->execute([$activityId]);
    $bookTitle = $stmt->fetchColumn();
    
    // 6. Valider la transaction
    $pdo->commit();
    
    // 7. Nettoyer la session
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
// PARTIE 4 : PAGE DE CONFIRMATION (VISIBLE)
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
        .footer {
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
        
        <div class="code-container">
            <div class="code-label">🔑 VOTRE CODE UNIQUE</div>
            <div class="code"><?= $code ?></div>
            <div class="expiry">📅 Valable jusqu'au <?= date('d/m/Y', strtotime($dateExpiration)) ?></div>
        </div>
        
        <div style="text-align: left; margin: 20px 0;">
            <div class="info-row">
                <span class="info-label">📖 Livre réservé</span>
                <span class="info-value"><?= htmlspecialchars($bookTitle) ?></span>
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
                <span class="info-value">Table n°<?= $idTable ?></span>
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
            <p>Présentez ce code à l'accueil du Cozy Café pour récupérer votre livre.</p>
            <p>Le code expire automatiquement après 7 jours.</p>
        </div>
        
        <div>
            <a href="index.php" class="btn">🏠 Retour à l'accueil</a>
            <button onclick="window.print()" class="btn btn-secondary">🖨️ Imprimer</button>
        </div>
        
        <div class="footer">
            <p>Cozy Café - Centre Urbain Nord, Tunis</p>
            <p>Code : <strong><?= $code ?></strong> | Expire le <?= date('d/m/Y', strtotime($dateExpiration)) ?></p>
        </div>
    </div>
    
    <script>
        // Sauvegarde dans localStorage pour consultation ultérieure
        localStorage.setItem('cozy_last_code', '<?= $code ?>');
        localStorage.setItem('cozy_last_book', '<?= addslashes($bookTitle) ?>');
        localStorage.setItem('cozy_last_expiry', '<?= $dateExpiration ?>');
    </script>
</body>
</html>