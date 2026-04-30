<?php

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;

require_once __DIR__ . '/../lib/PHPMailer/Exception.php';
require_once __DIR__ . '/../lib/PHPMailer/SMTP.php';
require_once __DIR__ . '/../lib/PHPMailer/PHPMailer.php';

class BookManager {
    private $pdo;

    public function __construct($pdo) {
        $this->pdo = $pdo;
    }

    // ─── Get all books ───────────────────────────────────────────────────────
    public function getAllBooks() {
        $stmt = $this->pdo->query("SELECT * FROM livres ORDER BY titre ASC");
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // ─── Get single book by ID ────────────────────────────────────────────────
    public function getBookById($id) {
        $stmt = $this->pdo->prepare("SELECT * FROM livres WHERE id_livre = ?");
        $stmt->execute([$id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    // ─── Check reservation status ─────────────────────────────────────────────
    public function isBookReserved($bookId) {
        $stmt = $this->pdo->prepare("
            SELECT rl.code, rl.id_reservation,
                   DATE_ADD(r.date_reservation, INTERVAL 7 DAY) AS expiry_date,
                   r.date_reservation
            FROM reservation_livres rl
            JOIN reservations r ON rl.id_reservation = r.id_reservation
            WHERE rl.id_livre = ?
              AND r.statut = 'confirmee'
              AND r.date_reservation >= DATE_SUB(CURDATE(), INTERVAL 7 DAY)
            ORDER BY r.date_reservation DESC
            LIMIT 1
        ");
        $stmt->execute([$bookId]);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($row) {
            return [
                'reserved'   => true,
                'until_date' => date('d/m/Y', strtotime($row['expiry_date'])),
                'code'       => $row['code'],
            ];
        }
        return ['reserved' => false, 'until_date' => null, 'code' => null];
    }

    // ─── Release expired reservations ────────────────────────────────────────
    public function releaseExpiredReservations() {
        $stmt = $this->pdo->query("
            SELECT rl.id_livre, rl.id_reservation
            FROM reservation_livres rl
            JOIN reservations r ON rl.id_reservation = r.id_reservation
            WHERE r.statut = 'confirmee'
              AND r.date_reservation < DATE_SUB(CURDATE(), INTERVAL 7 DAY)
        ");
        $expired = $stmt->fetchAll(PDO::FETCH_ASSOC);

        foreach ($expired as $row) {
            $this->pdo->prepare("
                UPDATE livres SET exemplaires_disponibles = exemplaires_disponibles + 1
                WHERE id_livre = ?
            ")->execute([$row['id_livre']]);

            $this->pdo->prepare("
                UPDATE reservations SET statut = 'expiree'
                WHERE id_reservation = ?
            ")->execute([$row['id_reservation']]);
        }

        return count($expired);
    }

    // ─── Generate 6-digit code ────────────────────────────────────────────────
    public function generateCode() {
        do {
            $code = str_pad(random_int(0, 999999), 6, '0', STR_PAD_LEFT);
            $stmt = $this->pdo->prepare("
                SELECT COUNT(*) FROM reservation_livres WHERE code = ?
            ");
            $stmt->execute([$code]);
        } while ($stmt->fetchColumn() > 0);

        return $code;
    }

    // ─── Verify a reservation code ────────────────────────────────────────────
    public function verifyReservationCode($code) {
        $stmt = $this->pdo->prepare("
            SELECT rl.code, rl.id_livre, l.titre,
                   r.date_reservation,
                   DATE_ADD(r.date_reservation, INTERVAL 7 DAY) AS expiry_date,
                   r.statut
            FROM reservation_livres rl
            JOIN reservations r ON rl.id_reservation = r.id_reservation
            JOIN livres l       ON rl.id_livre = l.id_livre
            WHERE rl.code = ?
            ORDER BY r.date_reservation DESC
            LIMIT 1
        ");
        $stmt->execute([$code]);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$row) {
            return ['valid' => false];
        }

        $expired = (strtotime($row['expiry_date']) < time())
                   || $row['statut'] === 'expiree';

        return [
            'valid'       => true,
            'expired'     => $expired,
            'book_title'  => $row['titre'],
            'book_id'     => $row['id_livre'],
            'expiry_date' => date('d/m/Y', strtotime($row['expiry_date'])),
        ];
    }

    // ─── Send reservation email ───────────────────────────────────────────────
    public function sendReservationEmail($to, $bookTitle, $code, $expiryDate) {
        $mail = new PHPMailer(true);

        try {
            $mail->isSMTP();
            $mail->Host       = 'smtp.gmail.com';
            $mail->SMTPAuth   = true;
            $mail->Username   = 'emnaelloumi00@gmail.com'; // ← ton Gmail
            $mail->Password   = 'mmix rwef eyzr jshf'; // ← mot de passe d'application Gmail
            $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
            $mail->Port       = 587;

            $mail->setFrom('emnaelloumi00@gmail.com', 'Cozy Café');
            $mail->addAddress($to);
            $mail->isHTML(true);
            $mail->CharSet = 'UTF-8';
            $mail->Subject = 'Votre code de réservation - Cozy Café';
            $mail->Body    = "
            <html>
            <body style='font-family:Arial,sans-serif;background:#f5f1ee;padding:30px;'>
              <div style='max-width:500px;margin:auto;background:white;border-radius:15px;padding:30px;'>
                <h2 style='color:#6f4e37;text-align:center;'>☕ Cozy Café</h2>
                <h3 style='text-align:center;'>Réservation confirmée !</h3>
                <p>Votre livre <strong>«{$bookTitle}»</strong> est réservé.</p>
                <div style='background:#f0e8e0;border-radius:10px;padding:20px;text-align:center;margin:20px 0;'>
                  <p style='margin:0;color:#6f4e37;font-size:14px;'>Votre code à 6 chiffres :</p>
                  <p style='font-size:42px;font-weight:bold;color:#6f4e37;margin:10px 0;letter-spacing:8px;'>{$code}</p>
                  <p style='margin:0;font-size:13px;color:#999;'>Valable jusqu'au {$expiryDate}</p>
                </div>
                <p style='font-size:13px;color:#666;'>
                  Présentez ce code à votre arrivée pour récupérer votre livre.<br>
                  Le livre vous est réservé pendant <strong>7 jours</strong>.
                </p>
              </div>
            </body>
            </html>";

            $mail->send();

        } catch (Exception $e) {
    error_log("Email error: " . $mail->ErrorInfo);
    file_put_contents(__DIR__ . '/email_error.txt', $mail->ErrorInfo);
}
    }
}