<?php
require_once '../cnx.php';
session_start();

// Must have a code in session
if (empty($_SESSION['book_reservation_code'])) {
    header('Location: books.php');
    exit;
}

$code       = $_SESSION['book_reservation_code'];
$bookTitle  = $_SESSION['book_title']          ?? '';
$email      = $_SESSION['reservation_email']   ?? '';
$expiryDate = $_SESSION['reservation_expiry']  ?? '';
$table      = $_SESSION['reservation_table']   ?? '';
$date       = $_SESSION['reservation_date']    ?? '';
$time       = $_SESSION['reservation_time']    ?? '';

// Format date nicely
$formattedDate = $date
    ? date('l, d F Y', strtotime($date))
    : '';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8"/>
    <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
    <title>Reservation Confirmed – Cozy Café</title>
    <link rel="stylesheet" href="books.css"/>
    <style>
        .confirm-box {
            max-width: 480px;
            margin: 60px auto 80px;
            background: white;
            border-radius: 18px;
            padding: 42px 40px;
            text-align: center;
            box-shadow: 0 14px 40px rgba(111,78,55,0.14);
        }
        .confirm-icon { font-size: 3.2rem; margin-bottom: 10px; }
        .confirm-box h2 {
            color: #6f4e37;
            margin: 0 0 10px;
            font-size: 1.75rem;
        }
        .confirm-box .subtitle {
            color: #888;
            font-size: 0.92rem;
            line-height: 1.6;
            margin-bottom: 24px;
        }

        /* ── Booking summary ── */
        .booking-summary {
            background: #faf5f0;
            border-radius: 12px;
            padding: 16px 20px;
            margin-bottom: 24px;
            text-align: left;
            font-size: 0.9rem;
            color: #4a3020;
            line-height: 2;
        }
        .booking-summary span { float: right; font-weight: 600; color: #6f4e37; }

        /* ── Code display ── */
        .code-display {
            background: #f0e8e0;
            border-radius: 14px;
            padding: 26px;
            margin-bottom: 20px;
        }
        .code-display .label {
            font-size: 0.83rem;
            color: #999;
            margin-bottom: 8px;
        }
        .code-display .code {
            font-size: 3rem;
            font-weight: 800;
            color: #6f4e37;
            letter-spacing: 12px;
        }
        .code-display .expiry {
            font-size: 0.8rem;
            color: #bbb;
            margin-top: 8px;
        }

        .email-note {
            font-size: 0.84rem;
            color: #999;
            margin-bottom: 28px;
        }
        .email-note strong { color: #6f4e37; }

        /* ── Buttons ── */
        .btn-row {
            display: flex;
            gap: 12px;
            justify-content: center;
        }
        .btn {
            display: inline-block;
            padding: 12px 24px;
            border-radius: 10px;
            font-weight: 700;
            text-decoration: none;
            font-size: 0.92rem;
            transition: transform 0.15s;
        }
        .btn:hover { transform: translateY(-2px); }
        .btn-primary   { background:#6f4e37; color:white; }
        .btn-secondary { background:#e8d9c5; color:#4a2c1f; }
    </style>
</head>
<body>
    <?php include '../navbar.php'; ?>

    <header>
        <h1>Cozy Café</h1>
        <p>Your daily dose of happiness</p>
    </header>

    <div class="confirm-box">
        <div class="confirm-icon">✅</div>
        <h2>Reservation Confirmed!</h2>
        <p class="subtitle">
            <strong>«<?= htmlspecialchars($bookTitle) ?>»</strong>
            is reserved for you for <strong>7 days</strong>.
        </p>

        <!-- Booking details -->
        <?php if ($table || $formattedDate || $time): ?>
        <div class="booking-summary">
            <?php if ($table): ?>
                Table <span>#<?= htmlspecialchars($table) ?></span><br>
            <?php endif; ?>
            <?php if ($formattedDate): ?>
                Date <span><?= htmlspecialchars($formattedDate) ?></span><br>
            <?php endif; ?>
            <?php if ($time): ?>
                Time <span><?= htmlspecialchars($time) ?></span>
            <?php endif; ?>
        </div>
        <?php endif; ?>

        <!-- 6-digit code -->
        <div class="code-display">
            <div class="label">Your 6-digit code</div>
            <div class="code"><?= htmlspecialchars($code) ?></div>
            <div class="expiry">Valid until <?= htmlspecialchars($expiryDate) ?></div>
        </div>

        <p class="email-note">
            📧 A confirmation email has been sent to
            <strong><?= htmlspecialchars($email) ?></strong>
        </p>

        <div class="btn-row">
            <a href="books.php" class="btn btn-secondary">← Back to Books</a>
        </div>
    </div>

    <?php include '../footer.php'; ?>
</body>
</html>