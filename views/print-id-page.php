<script nonce="<?php echo htmlspecialchars($GLOBALS['nonce'] ?? '', ENT_QUOTES, 'UTF-8'); ?>">

window.addEventListener('DOMContentLoaded', () => {
    const storedData = localStorage.getItem('print_applicant_data');
    
    if (storedData) {
        const data = JSON.parse(storedData);
        
        // የሥራ ፈላጊውን መታወቂያ ቁጥር በገጹ ላይ ባለ ኤለመንት ውስጥ እናስገባዋለን
        const idElement = document.getElementById('applicant-id-display');
        if (idElement) {
            idElement.textContent = data.id;
        }

        // ሌሎች መረጃዎችንም እንደ አስፈላጊነቱ መጠቀም ትችላለህ
        console.log("Applicant ID:", data.id);
        console.log("Full Info:", data.info);

        // ስራው ካለቀ በኋላ ከ localStorage ማጽዳት ይቻላል
        // localStorage.removeItem('print_applicant_data');
    }
});
</script>