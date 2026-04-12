const tables = document.querySelectorAll(".table-top");
const selectedBox = document.getElementById("slcttable");
const dateInput = document.getElementById("date");
const timeSelect = document.getElementById("timeSelect");
const reservations = {
  "2026-03-26_09:00": [2, 5, 10],
  "2026-03-26_10:00": [1, 7, 13],
};
function updateAvailability() {
  const date = dateInput.value;
  const time = timeSelect.value;
  if (!date || !time) return;

  // reset all tables first
  tables.forEach((table) => {
    table.disabled = false;
    table.classList.remove("reserved", "selected");
  });
  selectedTable = null;
  selectedBox.textContent = "";
  const key = `${date}_${time}`;
  const reserved = reservations[key] || [];

  tables.forEach((table) => {
    const number = parseInt(table.querySelector(".number").textContent);
    if (reserved.includes(number)) {
      table.disabled = true;
      table.classList.add("reserved");
    }
  });
}
let selectedTable = null;

tables.forEach((table) => {
  table.addEventListener("click", () => {
    if (!dateInput.value || !timeSelect.value) {
      alert("Pick a date and time first.");
      return;
    }
    if (table.disabled) return;
    tables.forEach((t) => t.classList.remove("selected"));
    table.classList.add("selected");
    const number = parseInt(table.querySelector(".number").textContent);
    selectedTable = number;
    const seats = table.querySelector(".seats").textContent;
    selectedBox.textContent = `Table ${number} · ${seats}`;
  });
});
dateInput.addEventListener("change", updateAvailability);
timeSelect.addEventListener("change", updateAvailability);
