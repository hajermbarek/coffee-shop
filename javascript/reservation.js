document.addEventListener('DOMContentLoaded', function() {
  // Récupération des données stockées
  const zone = sessionStorage.getItem('reservationZone') || 'Non spécifiée';
  const tableNum = sessionStorage.getItem('reservationTable') || '—';
  const date = sessionStorage.getItem('reservationDate') || '—';
  const time = sessionStorage.getItem('reservationTime') || '—';
  const activity = sessionStorage.getItem('activity') || 'Aucune activité choisie';

  // Remplir les champs en lecture seule (avec vérification d'existence)
  const zoneField = document.getElementById('zoneDisplay');
  if (zoneField) zoneField.value = zone;

  const tableField = document.getElementById('tableNumber');
  if (tableField) tableField.value = `Table ${tableNum}`;

  const dateField = document.getElementById('dateDisplay');
  if (dateField) dateField.value = date;

  const timeField = document.getElementById('timeDisplay');
  if (timeField) timeField.value = time;

  const activityField = document.getElementById('activityDisplay');
  if (activityField) activityField.value = activity;

  // Gestion du bouton "Modifier le choix"
  window.goBack = function() {
    if (zone.includes('Quiet')) {
      window.location.href = 'books.html';
    } else if (zone.includes('Fun')) {
      window.location.href = 'jouer.html';
    } else {
      window.history.back();
    }
  };

  // Soumission du formulaire
  const form = document.getElementById('finalReservationForm');
  if (form) {
    form.addEventListener('submit', function(e) {
      e.preventDefault();

      const firstName = document.getElementById('firstname')?.value || '';
      const lastName = document.getElementById('name')?.value || '';
      const email = document.getElementById('email')?.value || '';
      const phone = document.getElementById('phone')?.value || '';
      const people = document.getElementById('people')?.value || '1';
      const allergies = document.getElementById('allergies')?.value || '';
      const comments = document.getElementById('comments')?.value || '';

      alert(`Réservation confirmée pour ${firstName} ${lastName} !\nZone : ${zone}\nTable : ${tableNum}\nDate : ${date} à ${time}\nActivité : ${activity}\nNombre de personnes : ${people}\nNous vous attendons au Cozy Café.`);

      sessionStorage.clear();
      window.location.href = 'page1.html';
    });
  }
});