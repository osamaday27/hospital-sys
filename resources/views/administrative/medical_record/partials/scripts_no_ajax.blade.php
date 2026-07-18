<script>
    function calculateCompletionPercentage(formId) {
        const form = document.getElementById(formId);
        const totalFields = form.querySelectorAll('select, input[type="text"], input[type="date"], input[type="file"]').length;
        const inputs = form.querySelectorAll('select, input[type="text"], input[type="date"], input[type="file"]');
        let count = 0;
        inputs.forEach((input) => {
            if (input.type === 'file') {
                if (input.value) count++;
            } else if (String(input.value).trim() !== '') {
                count++;
            }
        });
        return totalFields ? (count / totalFields) * 100 : 0;
    }
    function get(){
        const formId = 'patientForm';
        const completion = Math.round(calculateCompletionPercentage(formId));
        const wi = document.getElementById('wi');
        const pr = document.getElementById('pr');
        if (wi) {
            wi.setAttribute('aria-valuenow', completion);
            wi.style.width = completion + '%';
        }
        if (pr) pr.textContent = completion + '%';
    }
</script>