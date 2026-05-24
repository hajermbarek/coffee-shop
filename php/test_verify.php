<?php
session_start();
require_once 'cnx.php';

// Tester la connexion
try {
    $pdo->query("SELECT 1");
    echo "✅ Connexion BDD OK<br>";
} catch (Exception $e) {
    echo "❌ Connexion BDD échouée: " . $e->getMessage() . "<br>";
}

// Chercher un code existant dans la table reservation_livres
$stmt = $pdo->query("SELECT code, date_expiration, id_livre FROM reservation_livres LIMIT 5");
$codes = $stmt->fetchAll();

echo "<h3>Codes dans la base de données:</h3>";
if (empty($codes)) {
    echo "Aucun code trouvé dans reservation_livres<br>";
} else {
    foreach ($codes as $c) {
        echo "Code: " . $c['code'] . " | Expire: " . $c['date_expiration'] . " | Livre ID: " . $c['id_livre'] . "<br>";
    }
}

// Tester avec un code spécifique (mettez un vrai code qui existe)
$testCode = "123456"; // REMPLACEZ PAR UN VRAI CODE DE VOTRE BDD
echo "<hr>";
echo "<h3>Test avec le code: " . $testCode . "</h3>";

$stmt = $pdo->prepare("
    SELECT rl.*, r.id_reservation, l.titre as book_title, r.statut
    FROM reservation_livres rl
    JOIN reservations r ON rl.id_reservation = r.id_reservation
    JOIN livres l ON rl.id_livre = l.id_livre
    WHERE rl.code = ?
");
$stmt->execute([$testCode]);
$result = $stmt->fetch();

if ($result) {
    echo "✅ Code trouvé!<br>";
    echo "Livre: " . $result['book_title'] . "<br>";
    echo "Statut réservation: " . $result['statut'] . "<br>";
    echo "Date expiration: " . $result['date_expiration'] . "<br>";
} else {
    echo "❌ Code non trouvé";
}
?>