<?php
session_start();
require_once 'cnx.php';

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
           AND statut = 'confirmee'"
    );
    $stmt->execute([':date' => $date, ':time' => $time . ':00']);
    $rows = $stmt->fetchAll(PDO::FETCH_COLUMN);
    echo json_encode(['reserved' => array_map('intval', $rows)]);
    exit;
}

$tablesStmt = $pdo->prepare(
    "SELECT t.id_table, t.numero, t.places, z.nom AS zone_name
    FROM tables t
    JOIN zones z ON t.id_zone = z.id_zone
    WHERE t.id_zone = 1 
    ORDER BY t.numero"
);
$tablesStmt->execute();
$dbTables = $tablesStmt->fetchAll(PDO::FETCH_ASSOC);

function getDotsForSeats(int $seats): array
{
    return match (true) {
        $seats >= 4 => ['top', 'right', 'bottom', 'left'],
        $seats == 3 => ['top', 'right', 'left'],
        $seats == 2 => ['right', 'left'],
        default     => ['top'],
    };
}


if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $table_id = isset($_POST['table_id']) ? (int)$_POST['table_id'] : 0;
    $date     = trim($_POST['date'] ?? '');
    $time     = trim($_POST['time'] ?? '');
    $validIds = array_column($dbTables, 'id_table');

    $errors = [];
    if ($table_id < 1 || $table_id > 15)             $errors[] = 'Please select a valid table.';
    if (!preg_match('/^\d{4}-\d{2}-\d{2}$/', $date)) $errors[] = 'Please choose a date.';
    if (!preg_match('/^\d{2}:\d{2}$/', $time))        $errors[] = 'Please choose a time slot.';
    if ($date < date('Y-m-d'))                        $errors[] = 'You cannot reserve a table in the past.';

    if (empty($errors)) {
        $stmt = $pdo->prepare(
            "SELECT id_reservation FROM reservations
            WHERE id_table = :tid
            AND date_reservation = :date
            AND heure_reservation = :time
            AND statut = 'confirmee'"
        );
        $stmt->execute([':tid' => $table_id, ':date' => $date, ':time' => $time . ':00']);
        if ($stmt->fetch()) {
            $errors[] = 'Sorry, that table is already reserved at this time. Please choose another.';
        }
    }

    if (!empty($errors)) {
        $error = implode(' ', $errors);
    } else {
        $selectedTable = array_values(array_filter($dbTables, fn($t) => $t['id_table'] === $table_id))[0];
        $zoneName = $selectedTable['zone_name'] ?? 'Quiet Zone';

        $_SESSION['reservationZone']  = $zoneName;
        $_SESSION['reservationTable'] = $table_id;
        $_SESSION['table_id']         = $table_id;
        $_SESSION['reservationDate']  = $date;
        $_SESSION['reservationTime']  = $time;

        if (!isset($_SESSION['activity']))      $_SESSION['activity']      = 'Aucune activité choisie';
        if (!isset($_SESSION['activity_type'])) $_SESSION['activity_type'] = null;
        if (!isset($_SESSION['activity_id']))   $_SESSION['activity_id']   = null;

        header('Location: books/books.php');
        exit;
    }
}

$error = $error ?? $_GET['error'] ?? '';
?>
<!doctype html>
<html lang="en">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <link rel="stylesheet" href="style.css" />
    <link rel="stylesheet" href="seatingbooks.css" />
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" />
    <link href="https://fonts.googleapis.com/css2?family=Manrope:wght@400;500;600;700&display=swap" rel="stylesheet" />
    <title>Cozy Café - Quiet Zone</title>
</head>

<body>

    <header>
        <h1>Reservation</h1>
        <p class="title">pick a table on the floor plan then choose the time and date</p>
    </header>

    <?php if ($error): ?>
        <div class="error-banner"><?= htmlspecialchars($error) ?></div>
    <?php endif; ?>

    <section>
        <div class="arrange">
            <p>Book a Table</p>
            <div class="table-surf">
                <?php foreach ($dbTables as $table):
                    $seatLabel = $table['places'] === 1 ? '1 seat' : "{$table['places']} seats";
                    $dots      = getDotsForSeats((int)$table['places']);
                ?>
                    <button class="table-top" data-table="<?= $table['id_table'] ?>" data-seats="<?= htmlspecialchars($seatLabel) ?>">
                        <?php foreach ($dots as $pos): ?>
                            <span class="dot <?= $pos ?>"></span>
                        <?php endforeach; ?>
                        <span class="number"><?= $table['numero'] ?></span>
                        <span class="seats"><?= $seatLabel ?></span>
                    </button>
                <?php endforeach; ?>
            </div>
        </div>
        <div class="select">
            <form method="POST" action="seatingbooks.php" id="reservationForm">
                <input type="hidden" name="table_id" id="table_id_input" value="" />

                <div class="booking">
                    <label>Selected table :</label>
                    <div class="selected-box">
                        <p id="slcttable">—</p>
                    </div>
                </div>

                <div class="date">
                    <label for="date">Pick a day :</label>
                    <input type="date" id="date" name="date" min="<?= date('Y-m-d') ?>" required />
                </div>

                <div class="time">
                    <label for="timeSelect">Pick a time :</label>
                    <select id="timeSelect" name="time" size="5" required>
                        <?php
                        $slots = [
                            '09:00',
                            '09:30',
                            '10:00',
                            '10:30',
                            '11:00',
                            '11:30',
                            '12:00',
                            '12:30',
                            '13:00',
                            '13:30',
                            '14:00',
                            '14:30',
                            '15:00',
                            '15:30',
                            '16:00',
                            '16:30',
                            '17:00',
                            '17:30'
                        ];
                        foreach ($slots as $slot):
                        ?>
                            <option value="<?= $slot ?>"><?= $slot ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <button type="submit" class="reserve">Reserve now</button>
            </form>
        </div>
    </section>

    <?php include 'footer.php'; ?>

    <script src="seatingbooks.js"></script>
</body>

</html>