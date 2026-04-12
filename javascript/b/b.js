// Ce script est à inclure dans chaque page de détail d'un livre (dossier b/)
document.addEventListener('DOMContentLoaded', function() {
  // Récupère le nom du livre stocké depuis books.html
  let bookName = sessionStorage.getItem('selectedBookName');
  
  // Fallback : si absent, on prend le titre affiché dans la page
  if (!bookName) {
    const titleElement = document.querySelector('div > h1');
    if (titleElement) {
      bookName = titleElement.innerText.trim();
    } else {
      bookName = 'Livre inconnu';
    }
  }

  const reserveBtn = document.querySelector('.reserve-btn');
  if (reserveBtn) {
    reserveBtn.addEventListener('click', function(e) {
      e.preventDefault();
      sessionStorage.setItem('activity', bookName);
      window.location.href = '../reservation.html';
    });
  }
});