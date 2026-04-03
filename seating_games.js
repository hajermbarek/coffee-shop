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
