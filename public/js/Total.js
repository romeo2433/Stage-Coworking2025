document.addEventListener('DOMContentLoaded', function () {
    const selectAll = document.getElementById('select-all');
    const rowCheckboxes = document.querySelectorAll('.select-row');
    const selectedSumEl = document.getElementById('selected-sum');
    const totalPaidEl = document.getElementById('total-paid');

    function formatAr(amount) {
        const n = Math.round(amount);
        return n.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ' ') + ' Ar';
    }

    function updateSelectedSum() {
        let sum = 0;
        rowCheckboxes.forEach(cb => {
            if (cb.checked) {
                const amt = parseFloat(cb.dataset.amount) || 0;
                sum += amt;
            }
        });

        const anyChecked = Array.from(rowCheckboxes).some(cb => cb.checked);
        if (!anyChecked) {
            selectedSumEl.textContent = formatAr(0);
        } else {
            selectedSumEl.textContent = formatAr(sum);
        }
    }

    rowCheckboxes.forEach(cb => {
        cb.addEventListener('change', function () {
            const allChecked = Array.from(rowCheckboxes).every(c => c.checked);
            selectAll.checked = allChecked;
            updateSelectedSum();
        });
    });

    if (selectAll) {
        selectAll.addEventListener('change', function () {
            rowCheckboxes.forEach(cb => cb.checked = selectAll.checked);
            updateSelectedSum();
        });
    }

    updateSelectedSum();
});
