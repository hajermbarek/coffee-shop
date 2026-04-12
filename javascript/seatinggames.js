const tables=document.querySelectorAll('.table4');
tables.forEach(function(table) {
    table.addEventListener('click',function() {
        tables.forEach(function(t){
            t.classList.remove('selected');
        });
        table.classList.add('selected');
        const number=table.querySelector('.number').textContent;
        document.getElementById('table').value="Table "+number;
    });
});

let selectedTable = null;
const zone = sessionStorage.getItem('reservationZone') || 'Fun Zone';

// Sélection des tables (les divs .table4)
document.querySelectorAll('.table4').forEach(tableDiv => {
  tableDiv.addEventListener('click', function() {
    document.querySelectorAll('.table4').forEach(t => t.classList.remove('selected-table'));
    this.classList.add('selected-table');
    const num = this.querySelector('.number')?.innerText;
    if (num) selectedTable = num;
    document.getElementById('table').value = `Table ${selectedTable}`;
  });
});

// Stockage et redirection vers jouer.html
document.querySelector('.reserve').addEventListener('click', function(e) {
  e.preventDefault();
  const date = document.getElementById('date').value;
  const time = document.getElementById('time').value;

  if (!selectedTable) {
    alert('Veuillez cliquer sur une table pour la sélectionner.');
    return;
  }
  if (!date || !time) {
    alert('Veuillez choisir la date et l’heure.');
    return;
  }

  sessionStorage.setItem('reservationDate', date);
  sessionStorage.setItem('reservationTime', time);
  sessionStorage.setItem('reservationTable', selectedTable);
  sessionStorage.setItem('reservationZone', zone);
  window.location.href = 'jouer.html';
});