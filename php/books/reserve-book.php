<?php
require_once '../cnx.php';
require_once '../lib/BookManager.php';

session_start();

$bookManager = new BookManager($pdo);

// Get code from URL or session
$code = trim($_GET['code'] ?? $_SESSION['book_reservation_code'] ?? '');

if (!$code) {
    header('Location: books.php?error=no_code');
    exit;
}

$verification = $bookManager->verifyReservationCode($code);

if (!$verification['valid']) {
    unset($_SESSION['book_reservation_code']);
    header('Location: books.php?error=invalid_code');
    exit;
}

if ($verification['expired']) {
    unset($_SESSION['book_reservation_code']);
    header('Location: books.php?error=expired_code');
    exit;
}

// Store in session for the seating page
$_SESSION['book_reservation_code'] = $code;
$_SESSION['book_title']            = $verification['book_title'];
$_SESSION['activity']              = $verification['book_title'];
$_SESSION['activity_type']         = 'book';
$_SESSION['activity_id']           = $verification['book_id'];
$_SESSION['reservationZone']       = 'Quiet Zone';

// Redirect to table selection
header('Location: ../../seatingbooks.html');
exit;