<?php
require_once 'cnx.php';

// Récupère l'ID d'un jeu à partir de son nom 
function getGameIdByName($pdo, $nomJeu) {
    $stmt = $pdo->prepare("SELECT id_jeux FROM jeux WHERE nom = ?");
    $stmt->execute([$nomJeu]);
    $id = $stmt->fetchColumn();
    return $id;
}

// Récupère l'ID d'un livre à partir de son titre
function getBookIdByTitle($pdo, $titre) {
    $stmt = $pdo->prepare("SELECT id_livre FROM livres WHERE titre = ?");
    $stmt->execute([$titre]);
    $id = $stmt->fetchColumn();
    return $id;
}

// Vérifie si un client existe déjà via son email
function getClientByEmail($pdo, $email) {
    $stmt = $pdo->prepare("SELECT id_client FROM clients WHERE email = ?");
    $stmt->execute([$email]);
    return $stmt->fetchColumn();
}

// Insère un nouveau client
function insertClient($pdo, $prenom, $nom, $email, $telephone) {
    $stmt = $pdo->prepare("INSERT INTO clients (prenom, nom, email, telephone) VALUES (?, ?, ?, ?)");
    $stmt->execute([$prenom, $nom, $email, $telephone]);
    return $pdo->lastInsertId();
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


?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Confirmation de Réservation</title>
    <link rel="stylesheet" href="reservation.css">
</head>
<body>
    <header>
        <h1>Cozy Café</h1>
        <p>Your daily dose of happiness</p>
    </header>

    <form id="finalReservationForm" class="reservation-form" action="process_reservation.php" method="POST">
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

        <label>Activité spécifique :</label>
        <input type="text" id="activityDisplay" name="activity_display" value="<?= htmlspecialchars($activity) ?>" readonly>

        <label for="allergies">Allergies alimentaires :</label>
        <textarea id="allergies" name="allergies" placeholder="Ex: Noix, gluten..."></textarea>

        <label for="comments">Commentaires supplémentaires :</label>
        <textarea id="comments" name="comments" placeholder="Demandes particulières..."></textarea>

        <input type="hidden" name="table_id" value="<?= $tableId ?>">
        <input type="hidden" name="reservation_date" value="<?= htmlspecialchars($dateResa) ?>">
        <input type="hidden" name="reservation_time" value="<?= htmlspecialchars($heureResa) ?>">
        <input type="hidden" name="activity_type" value="<?= $activityType ?>">
        <input type="hidden" name="activity_id" value="<?= $activityId ?>">

        <button type="button" onclick="goBack()">Modifier le choix</button>
        <button type="submit">Confirmer la réservation</button>
    </form>

    <script>
        function goBack() {
            let zone = "<?= addslashes($zone) ?>";
            if (zone.includes('Quiet')) {
                window.location.href = 'books.php';
            } else if (zone.includes('Fun')) {
                window.location.href = 'jouer.php';
            } else {
                window.history.back();
            }
        }
    </script>
</body>
</html>


<?php

// Récupération des données du formulaire
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

// Validation minimale
if (!$email || !$dateResa || !$heureResa || !$idTable) {
    die("Données manquantes. Veuillez recommencer la réservation.");
}

try {
    $pdo->beginTransaction();

    // Insert d'information de client 
    $idClient = getClientByEmail($pdo, $email);
    if (!$idClient) {
        $idClient = insertClient($pdo, $prenom, $nom, $email, $telephone);
    } else {
        // mettre à jour les coordonnées si elles changent
        $stmt = $pdo->prepare("UPDATE clients SET prenom=?, nom=?, telephone=? WHERE id_client=?");
        $stmt->execute([$prenom, $nom, $telephone, $idClient]);
    }

    // Insertion de la réservation
    $statut = 'confirmee'; 
    $stmt = $pdo->prepare("
        INSERT INTO reservations (id_client, id_table, date_reservation, heure_reservation, nb_personnes, allergies, commentaires, statut)
        VALUES (?, ?, ?, ?, ?, ?, ?, ?)
    ");
    $stmt->execute([$idClient, $idTable, $dateResa, $heureResa, $nbPersonnes, $allergies, $commentaires, $statut]);
    $idReservation = $pdo->lastInsertId();

    // Gestion de l'activité (livre ou jeu)
    if ($activityType === 'book' && $activityId) {
        // Vérifier si le livre est déjà emprunté (réservation active dans les 7 jours)
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
                // Même client : on met à jour l'id_reservation de la ligne existante
                $stmtUpdate = $pdo->prepare("UPDATE reservation_livres SET id_reservation = ? WHERE id_RL = ?");
                $stmtUpdate->execute([$idReservation, $existing['id_RL']]);
                // Pas de décrémentation des exemplaires
            } else {
                // Autre client : refus
                throw new Exception("Ce livre est déjà emprunté par un autre client.");
            }
        } else {
            // Aucun emprunt actif : insertion normale
            $codePret = 'BK-' . strtoupper(uniqid());
            $stmt = $pdo->prepare("INSERT INTO reservation_livres (id_reservation, id_livre, code) VALUES (?, ?, ?)");
            $stmt->execute([$idReservation, $activityId, $codePret]);

            $stmt = $pdo->prepare("UPDATE livres SET exemplaires_disponibles = exemplaires_disponibles - 1 WHERE id_livre = ? AND exemplaires_disponibles > 0");
            $stmt->execute([$activityId]);
        }

    } elseif ($activityType === 'game' && $activityId) {
        $stmt = $pdo->prepare("INSERT INTO reservation_jeux (id_reservation, id_jeux) VALUES (?, ?)");
        $stmt->execute([$idReservation, $activityId]);

        $stmt = $pdo->prepare("UPDATE jeux SET exemplaires_disponibles = exemplaires_disponibles - 1 WHERE id_jeux = ? AND exemplaires_disponibles > 0");
        $stmt->execute([$activityId]);
    }

    $pdo->commit();

    session_destroy();

    echo "<script>
        alert('Réservation confirmée pour $prenom $nom !\\nZone : $zone\\nTable : $tableNum\\nDate : $dateResa à $heureResa\\nActivité : $activity\\nNombre de personnes : $nbPersonnes\\nNous vous attendons au Cozy Café.');
        window.location.href = 'index.php';
    </script>";
    exit;

} catch (Exception $e) {
    $pdo->rollBack();
    die("Erreur lors de l'enregistrement : " . $e->getMessage());
}
?>