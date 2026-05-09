<?php
session_start();
require_once 'cnx.php';

header('Content-Type: application/json');

$code = $_POST['code'] ?? '';

if (!preg_match('/^\d{6}$/', $code)) {
    echo json_encode(['valid' => false]);
    exit;
}

// Rechercher la réservation avec ce code
$stmt = $pdo->prepare("
    SELECT rl.*, r.id_reservation, l.titre as book_title
    FROM reservation_livres rl
    JOIN reservations r ON rl.id_reservation = r.id_reservation
    JOIN livres l ON rl.id_livre = l.id_livre
    WHERE rl.code = ? 
    AND r.statut = 'confirmee'
    ORDER BY r.date_reservation DESC
    LIMIT 1
");
$stmt->execute([$code]);
$reservation = $stmt->fetch();

if (!$reservation) {
    echo json_encode(['valid' => false]);
    exit;
}

// Vérifier si le code est expiré
$today = date('Y-m-d');
$expired = ($reservation['date_expiration'] < $today);

echo json_encode([
    'valid' => !$expired,
    'expired' => $expired,
    'expiry_date' => date('d/m/Y', strtotime($reservation['date_expiration'])),
    'book_title' => htmlspecialchars($reservation['book_title']),
    'code' => $code
]);
?>