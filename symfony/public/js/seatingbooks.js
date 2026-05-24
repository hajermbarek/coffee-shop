document.addEventListener("DOMContentLoaded", function () {
    const tables = document.querySelectorAll(".table-top");
    const selectedBox = document.getElementById("slcttable");
    const tableInput = document.getElementById("table_id_input");
    const dateInput = document.getElementById("date");
    const timeSelect = document.getElementById("timeSelect");
    const form = document.getElementById("reservationForm");

    let selectedTable = null;
    const jsData = document.getElementById("js-data");
    const availabilityUrl = jsData.dataset.availabilityUrl;
    async function updateAvailability() {
        const date = dateInput.value;
        const time = timeSelect.value;
        if (!date || !time) return;

        tables.forEach((t) => {
            t.disabled = false;
            t.classList.remove("reserved", "selected", "selected-table");
        });
        if (selectedBox) selectedBox.textContent = "—";
        selectedTable = null;
        if (tableInput) tableInput.value = "";

        try {
            const res = await fetch(
                `${availabilityUrl}?date=${encodeURIComponent(date)}&time=${encodeURIComponent(time)}`,
            );
            const data = await res.json();

            if (data.error) {
                console.error("Availability check error:", data.error);
                return;
            }

            const reserved = data.reserved || [];
            tables.forEach((table) => {
                const num = parseInt(table.dataset.table);
                if (reserved.includes(num)) {
                    table.disabled = true;
                    table.classList.add("reserved");
                }
            });
        } catch (err) {
            console.error("Failed to fetch availability:", err);
        }
    }

    tables.forEach((table) => {
        table.addEventListener("click", () => {
            if (!dateInput.value || !timeSelect.value) {
                alert("Please select a date and time first.");
                return;
            }
            if (table.disabled) return;

            tables.forEach((t) =>
                t.classList.remove("selected", "selected-table"),
            );
            table.classList.add("selected", "selected-table");

            selectedTable = parseInt(table.dataset.table);
            const seats = table.dataset.seats || "";

            if (selectedBox)
                selectedBox.textContent = `Table ${selectedTable} · ${seats}`;
            if (tableInput) tableInput.value = selectedTable;
        });
    });
    dateInput.addEventListener("change", updateAvailability);
    timeSelect.addEventListener("change", updateAvailability);

    if (form) {
        form.addEventListener("submit", function (e) {
            const date = dateInput.value;
            const time = timeSelect.value;

            if (!selectedTable) {
                alert("Please select a table.");
                e.preventDefault();
                return;
            }
            if (!date) {
                alert("Please choose a date.");
                e.preventDefault();
                return;
            }
            if (!time) {
                alert("Please choose a time slot.");
                e.preventDefault();
                return;
            }
        });
    }
});
