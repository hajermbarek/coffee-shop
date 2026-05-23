document.addEventListener("DOMContentLoaded", function () {
    const tables       = document.querySelectorAll(".table4");
    const tableInput   = document.getElementById("table_id_input");
    const numInput     = document.getElementById("table_numero_input");
    const tableDisplay = document.getElementById("table");
    const dateInput    = document.getElementById("date");
    const timeInput    = document.getElementById("time");
    const form         = document.getElementById("reservationForm");
    let selectedTable  = null;

    async function updateAvailability() {
        const date = dateInput.value;
        const time = timeInput.value;
        if (!date || !time) return;

        tables.forEach(function(t) {
            t.classList.remove("reserved", "selected");
            t.style.pointerEvents = "auto";
            t.style.opacity = "1";
        });
        if (tableDisplay) tableDisplay.value = "";
        if (tableInput)   tableInput.value   = "";
        if (numInput)     numInput.value      = "";
        selectedTable = null;

        try {
            const res  = await fetch(
                `seating_games.php?check_availability=1&date=${encodeURIComponent(date)}&time=${encodeURIComponent(time)}`
            );
            const data = await res.json();
            const reserved = data.reserved || [];

            tables.forEach(function(table) {
                const id = parseInt(table.dataset.id);
                if (reserved.includes(id)) {
                    table.classList.add("reserved");
                    table.style.pointerEvents = "none";
                }
            });
        } catch (err) {
            console.error("Failed to fetch availability:", err);
        }
    }

    tables.forEach(function(table) {
        table.addEventListener("click", function() {
            if (!dateInput.value || !timeInput.value) {
                alert("Please select a date and time first.");
                return;
            }
            if (table.classList.contains("reserved")) return;

            tables.forEach(function(t) { t.classList.remove("selected"); });
            table.classList.add("selected");

            const id     = table.dataset.id;
            const numero = table.dataset.numero;
            const seats  = table.dataset.seats;

            selectedTable = id;
            if (tableDisplay) tableDisplay.value = "Table " + numero + " (" + seats + " seats)";
            if (tableInput)   tableInput.value   = id;
            if (numInput)     numInput.value      = numero;
        });
    });

    dateInput.addEventListener("change", updateAvailability);
    timeInput.addEventListener("change", updateAvailability);

    if (form) {
        form.addEventListener("submit", function(e) {
            if (!selectedTable) {
                alert("Please select a table.");
                e.preventDefault();
                return;
            }
            if (!dateInput.value) {
                alert("Please choose a date.");
                e.preventDefault();
                return;
            }
            if (!timeInput.value) {
                alert("Please choose a time.");
                e.preventDefault();
                return;
            }
        });
    }
});