<?php
session_start();
require_once '../cnx.php';

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
    <title>Confirmation de Réservation - Cozy Café</title>
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
        <input type="hidden" name="zone_name" value="<?= htmlspecialchars($zone) ?>">
        <input type="hidden" name="table_number" value="<?= htmlspecialchars($tableNum) ?>">

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
    </script>
</body>
</html>