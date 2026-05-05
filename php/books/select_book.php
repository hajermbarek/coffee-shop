<?php
session_start();

// Vérifier que le formulaire a été soumis via POST
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    
    // Récupérer les données du formulaire
    $book_id = (int)($_POST['book_id'] ?? 0);
    $book_title = trim($_POST['book_title'] ?? '');
    
    // Vérifier que les données sont valides
    if ($book_id > 0 && !empty($book_title)) {
        
        // 📚 STOCKER LES INFORMATIONS DU LIVRE EN SESSION
        $_SESSION['activity'] = $book_title;           // Nom du livre (ex: "A Little Life")
        $_SESSION['activity_type'] = 'book';           // Type = livre (vs 'game' pour les jeux)
        $_SESSION['activity_id'] = $book_id;           // ID du livre (ex: 1)
        
        // Optionnel : ajouter un message de confirmation
        $_SESSION['booking_message'] = "Livre sélectionné : " . $book_title;
        
        // 🔄 REDIRIGER VERS LE CHOIX DE TABLE
        header('Location: ../seatingbooks.php');
        exit;  // Toujours appeler exit après header()
        
    } else {
        // Données invalides : rediriger avec une erreur
        header('Location: books.php?error=invalid_book');
        exit;
    }
    
} else {
    // Pas de formulaire POST : rediriger vers la liste des livres
    header('Location: books.php');
    exit;
}
?>