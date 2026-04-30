<?php
require_once '../cnx.php';
require_once '../lib/BookManager.php';

session_start();

header('Content-Type: application/json');

$bookManager = new BookManager($pdo);
$code   = trim($_POST['code']    ?? '');
$bookId = (int)($_POST['book_id'] ?? 0);

// Basic format check
if (!preg_match('/^\d{6}$/', $code)) {
    echo json_encode(['valid' => false]);
    exit;
}

$result = $bookManager->verifyReservationCode($code);

// If a specific bookId was passed, verify the code belongs to that book
if ($result['valid'] && $bookId && isset($result['book_id'])) {
    if ((int)$result['book_id'] !== $bookId) {
        echo json_encode(['valid' => false]);
        exit;
    }
}

// Store valid, non-expired code in session
if ($result['valid'] && !$result['expired']) {
    $_SESSION['book_reservation_code'] = $code;
    $_SESSION['book_title']            = $result['book_title'];
}

echo json_encode($result);