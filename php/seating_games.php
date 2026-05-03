<?php
session_start();
require_once 'cnx.php';

$zone = 'Fun Zone';
$error = $_GET['error'] ?? '';

// Fetch tables from DB
$stmt = $pdo->prepare("SELECT * FROM tables WHERE id_zone = ? ORDER BY numero");
$stmt->execute([2]);
$tables = $stmt->fetchAll();

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $table_id = $_POST['table_id'] ?? null;
    $numero   = $_POST['table_numero'] ?? null;
    $date     = $_POST['date'] ?? null;
    $time     = $_POST['time'] ?? null;

    if (!$table_id || !$date || !$time) {
        header("Location: seating_games.php?error=Please select a table, date and time.");
        exit;
    }

    // Check if table is already reserved at that date+time
    $stmt = $pdo->prepare("
        SELECT id_reservation FROM reservations
        WHERE id_table = ? AND date_reservation = ? AND heure_reservation = ?
        AND statut != 'annulee'
    ");
    $stmt->execute([$table_id, $date, $time]);

    if ($stmt->fetch()) {
        header("Location: seating_games.php?error=This table is already booked at that time.");
        exit;
    }

    // Save to session
    $_SESSION['reservationZone']  = $zone;
    $_SESSION['reservationTable'] = $numero;
    $_SESSION['table_id']         = $table_id;
    $_SESSION['reservationDate']  = $date;
    $_SESSION['reservationTime']  = $time;
    $_SESSION['activity']         = 'Aucune activité choisie';
    $_SESSION['activity_type']    = null;
    $_SESSION['activity_id']      = null;

    header("Location: reservation.php");
    exit;
}
?>

<!doctype html>
<html>
<head>
    <title>table game reservation</title>
    <link rel="stylesheet" href="seating_games.css">
</head>
<body>

<?php include 'navbar.php'; ?>

<div class="top">
    <h1>Reserve your seat!</h1>
    <h4>pick a table on the floor plan then choose time and date</h4>
</div>

<?php if ($error): ?>
    <div class="error-banner"><?= htmlspecialchars($error) ?></div>
<?php endif; ?>

<div class="container">

    <div class="dt">
        <form method="POST" action="seating_games.php" id="reservationForm">

            <input type="hidden" name="table_id" id="table_id_input" value="" />
            <input type="hidden" name="table_numero" id="table_numero_input" value="" />

            <div>
                <label for="table">Selected table:</label>
                <input type="text" id="table" readonly />
            </div>
            <div>
                <label for="date">Date:</label>
                <input type="date" id="date" name="date"
                    min="<?= date('Y-m-d') ?>" max="2026-12-31" required />
            </div>
            <div>
                <label for="time">Time:</label>
                <input type="time" id="time" name="time"
                    min="08:30" max="23:00" required />
            </div>

            <button type="submit" class="reserve_button">Reserve table</button>
        </form>
    </div>

    <div class="floor">
        <?php foreach ($tables as $t):
            $seats  = $t['places'];
            $numero = $t['numero'];
            $id     = $t['id_table'];
            // determine chair layout class based on seats
            $class  = $seats === 4 ? 'table4 four' : 'table4 six';
        ?>
            <div class="<?= $class ?>"
                 id="t<?= $numero ?>"
                 data-id="<?= $id ?>"
                 data-numero="<?= $numero ?>"
                 data-seats="<?= $seats ?>">

                <?php if ($seats === 6): ?>
                    <div class="korsi1"></div>
                    <div class="korsi2"></div>
                    <div class="korsi3"></div>
                    <div class="korsi4"></div>
                    <div class="korsi5"></div>
                    <div class="korsi6"></div>
                <?php else: ?>
                    <div class="korsi11"></div>
                    <div class="korsi22"></div>
                    <div class="korsi3"></div>
                    <div class="korsi4"></div>
                <?php endif; ?>

                <div class="number"><?= $numero ?></div>
            </div>
        <?php endforeach; ?>

        <div class="counter" id="counter">Counter</div>
    </div>

</div>

<?php include 'footer.php'; ?>

<script src="seating_games.js"></script>
</body>
</html>