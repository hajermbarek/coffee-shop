<?php
ini_set('display_errors', 1);
error_reporting(E_ALL);
require_once '../cnx.php';
require_once '../lib/BookManager.php';

session_start();

header('Content-Type: application/json');

$bookManager = new BookManager($pdo);

// ── Get book from session ─────────────────────────────────────────────────────
$bookId = $_SESSION['pending_book_id'] ?? 0;

if (!$bookId) {
    echo json_encode(['success' => false, 'message' => 'No book selected. Please start over.']);
    exit;
}

// ── Collect & sanitize input ──────────────────────────────────────────────────
$firstname    = trim($_POST['firstname']     ?? '');
$name         = trim($_POST['name']          ?? '');
$email        = trim($_POST['email']         ?? '');
$phone        = trim($_POST['phone']         ?? '');
$notes        = trim($_POST['notes']         ?? '');
$tableNumber  = trim($_POST['table_number']  ?? '');
$date         = trim($_POST['date']          ?? '');
$time         = trim($_POST['time']          ?? '');

// ── Basic validation ──────────────────────────────────────────────────────────
if (!$firstname || !$name || !$email) {
    echo json_encode(['success' => false, 'message' => 'Required fields are missing.']);
    exit;
}
if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    echo json_encode(['success' => false, 'message' => 'Invalid email address.']);
    exit;
}

// ── Check book is still available ────────────────────────────────────────────
$status = $bookManager->isBookReserved($bookId);
if ($status['reserved']) {
    echo json_encode(['success' => false, 'message' => 'Sorry, this book was just reserved by someone else.']);
    exit;
}

$book = $bookManager->getBookById($bookId);
if (!$book) {
    echo json_encode(['success' => false, 'message' => 'Book not found.']);
    exit;
}

try {
    $pdo->beginTransaction();

    // ── Create or retrieve client ─────────────────────────────────────────────
    $stmt = $pdo->prepare("SELECT id_client FROM clients WHERE email = ?");
    $stmt->execute([$email]);
    $clientId = $stmt->fetchColumn();

    if (!$clientId) {
        $stmt = $pdo->prepare("
            INSERT INTO clients (prenom, nom, email, telephone, date_inscription)
            VALUES (?, ?, ?, ?, NOW())
        ");
        $stmt->execute([$firstname, $name, $email, $phone]);
        $clientId = $pdo->lastInsertId();
    } else {
        $pdo->prepare("UPDATE clients SET prenom=?, nom=?, telephone=? WHERE id_client=?")
            ->execute([$firstname, $name, $phone, $clientId]);
    }

    // ── Resolve table ID from table number ────────────────────────────────────
    $tableId = 1; // default fallback
    if ($tableNumber) {
        $stmt = $pdo->prepare("SELECT id_table FROM tables WHERE numero_table = ? LIMIT 1");
        $stmt->execute([$tableNumber]);
        $found = $stmt->fetchColumn();
        if ($found) $tableId = $found;
    }

    // ── Insert reservation ────────────────────────────────────────────────────
    $reservationDate = $date ?: date('Y-m-d');
    $reservationTime = $time ?: date('H:i:s');

    $stmt = $pdo->prepare("
        INSERT INTO reservations
            (id_client, id_table, date_reservation, heure_reservation,
             nb_personnes, commentaires, statut)
        VALUES (?, ?, ?, ?, 1, ?, 'confirmee')
    ");
    $stmt->execute([$clientId, $tableId, $reservationDate, $reservationTime, $notes]);
    $reservationId = $pdo->lastInsertId();

    // ── Generate unique 6-digit code ──────────────────────────────────────────
    $code = $bookManager->generateCode();

    // ── Link book to reservation ──────────────────────────────────────────────
    $pdo->prepare("
        INSERT INTO reservation_livres (id_reservation, id_livre, code)
        VALUES (?, ?, ?)
    ")->execute([$reservationId, $bookId, $code]);

    // ── Decrement available copies ────────────────────────────────────────────
    $pdo->prepare("
        UPDATE livres
        SET exemplaires_disponibles = exemplaires_disponibles - 1
        WHERE id_livre = ? AND exemplaires_disponibles > 0
    ")->execute([$bookId]);

    $pdo->commit();

    // ── Send confirmation email ───────────────────────────────────────────────
    $expiryDate = date('d/m/Y', strtotime('+7 days'));
    $bookManager->sendReservationEmail($email, $book['titre'], $code, $expiryDate);

    // ── Store in session for confirmation page ────────────────────────────────
    $_SESSION['book_reservation_code']  = $code;
    $_SESSION['book_title']             = $book['titre'];
    $_SESSION['reservation_email']      = $email;
    $_SESSION['reservation_expiry']     = $expiryDate;
    $_SESSION['reservation_table']      = $tableNumber;
    $_SESSION['reservation_date']       = $date;
    $_SESSION['reservation_time']       = $time;

    // Clear pending book from session
    unset($_SESSION['pending_book_id']);
    unset($_SESSION['pending_book_title']);
    unset($_SESSION['pending_book_image']);

    echo json_encode(['success' => true]);

} catch (Exception $e) {
    $pdo->rollBack();
    echo json_encode(['success' => false, 'message' => 'Database error: ' . $e->getMessage()]);
}