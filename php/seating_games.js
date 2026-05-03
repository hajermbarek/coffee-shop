const tables = document.querySelectorAll('.table4');

tables.forEach(function(table) {
    table.addEventListener('click', function() {
        // Remove selected from all
        tables.forEach(function(t) {
            t.classList.remove('selected');
        });

        // Mark this one as selected
        table.classList.add('selected');

        // Read data from the div (set by PHP)
        const numero = table.dataset.numero;
        const id     = table.dataset.id;
        const seats  = table.dataset.seats;

        // Fill the visible input
        document.getElementById('table').value = 'Table ' + numero + ' (' + seats + ' seats)';

        // Fill the hidden inputs (needed for form submission)
        document.getElementById('table_id_input').value     = id;
        document.getElementById('table_numero_input').value = numero;
    });
});