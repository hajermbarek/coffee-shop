<?php
session_start();
$zone  = 'Quiet Zone';
$error = $_GET['error'] ?? '';
?>
<!doctype html>
<html lang="en">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <link rel="stylesheet" href="seatingbooks.css" />
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
                <?php
                $tables = [
                    [1,  4, ['top', 'right', 'bottom', 'left']],
                    [2,  2, ['right', 'left']],
                    [3,  4, ['top', 'right', 'bottom', 'left']],
                    [4,  1, ['top']],
                    [5,  2, ['right', 'left']],
                    [6,  1, ['top']],
                    [7,  4, ['top', 'right', 'bottom', 'left']],
                    [8,  2, ['right', 'left']],
                    [9,  3, ['top', 'right', 'left']],
                    [10, 4, ['top', 'right', 'bottom', 'left']],
                    [11, 1, ['top']],
                    [12, 2, ['right', 'left']],
                    [13, 3, ['top', 'right', 'left']],
                    [14, 2, ['right', 'left']],
                    [15, 1, ['top']],
                ];
                foreach ($tables as [$id, $seats, $dots]):
                    $seatLabel = $seats === 1 ? '1 seat' : "$seats seats";
                ?>
                    <button class="table-top" data-table="<?= $id ?>" data-seats="<?= htmlspecialchars($seatLabel) ?>">
                        <?php foreach ($dots as $pos): ?>
                            <span class="dot <?= $pos ?>"></span>
                        <?php endforeach; ?>
                        <span class="number"><?= $id ?></span>
                        <span class="seats"><?= $seatLabel ?></span>
                    </button>
                <?php endforeach; ?>
            </div>
        </div>

        <div class="select">
            <form method="POST" action="set_session.php" id="reservationForm">
                <input type="hidden" name="table_id" id="table_id_input" value="" />
                <input type="hidden" name="zone" value="<?= htmlspecialchars($zone) ?>" />

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