document.addEventListener('DOMContentLoaded', function() {
  const tables = document.querySelectorAll(".table-top");
  const selectedBox = document.getElementById("slcttable");
  const dateInput = document.getElementById("date");
  const timeSelect = document.getElementById("timeSelect");
  const reserveLink = document.querySelector(".reserve");
  
  let selectedTable = null;
  const zone = sessionStorage.getItem('reservationZone') || 'Quiet Zone';

  // Exemple de réservations existantes (optionnel)
  const reservations = {
    "2026-03-26_09:00": [2, 5, 10],
    "2026-03-26_10:00": [1, 7, 13],
  };

  function updateAvailability() {
    const date = dateInput.value;
    const time = timeSelect.value;
    if (!date || !time) return;

    tables.forEach(table => {
      table.disabled = false;
      table.classList.remove("reserved", "selected");
    });
    if (selectedBox) selectedBox.textContent = "";
    selectedTable = null;

    const key = `${date}_${time}`;
    const reserved = reservations[key] || [];

    tables.forEach(table => {
      const number = parseInt(table.querySelector(".number").textContent);
      if (reserved.includes(number)) {
        table.disabled = true;
        table.classList.add("reserved");
      }
    });
  }

  // Sélection d'une table
  tables.forEach(table => {
    table.addEventListener("click", () => {
      if (!dateInput.value || !timeSelect.value) {
        alert("Please select a date and time first.");
        return;
      }
      if (table.disabled) return;
      
      tables.forEach(t => t.classList.remove("selected"));
      table.classList.add("selected");
      const number = parseInt(table.querySelector(".number").textContent);
      selectedTable = number;
      const seats = table.querySelector(".seats")?.textContent || "";
      if (selectedBox) selectedBox.textContent = `Table ${number} · ${seats}`;
    });
  });

  dateInput.addEventListener("change", updateAvailability);
  timeSelect.addEventListener("change", updateAvailability);

  // Clic sur "Reserve now"
  if (reserveLink) {
    reserveLink.addEventListener("click", function(e) {
      const date = dateInput.value;
      const time = timeSelect.value;

      if (!selectedTable) {
        alert('Please select a table.');
        e.preventDefault();
        return;
      }
      if (!date) {
        alert('Please choose a date.');
        e.preventDefault();
        return;
      }
      if (!time) {
        alert('Please choose a time slot.');
        e.preventDefault();
        return;
      }

      // Stockage dans sessionStorage
      sessionStorage.setItem('reservationDate', date);
      sessionStorage.setItem('reservationTime', time);
      sessionStorage.setItem('reservationTable', selectedTable);
      sessionStorage.setItem('reservationZone', zone);
      // La redirection se fait via href="books.html"
    });
  }
});