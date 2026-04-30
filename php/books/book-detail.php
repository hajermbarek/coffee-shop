<?php
require_once '../cnx.php';
require_once '../lib/BookManager.php';

session_start();

$bookManager = new BookManager($pdo);

$bookId = isset($_GET['id']) ? (int)$_GET['id'] : 0;
$book   = $bookManager->getBookById($bookId);

// Book not found
if (!$book) {
    header('Location: books.php');
    exit;
}

// Book already reserved → redirect
$status = $bookManager->isBookReserved($bookId);
if ($status['reserved']) {
    header('Location: books.php?error=book_reserved');
    exit;
}

// Store book in session so seatingbooks can use it
$_SESSION['pending_book_id']    = $bookId;
$_SESSION['pending_book_title'] = $book['titre'];
$_SESSION['pending_book_image'] = $book['image'] ?? 'images/default-book.png';

// Redirect to seating page
header('Location: ../../javascript/seatingbooks.html');
exit;