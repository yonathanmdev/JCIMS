<script>
document.addEventListener("DOMContentLoaded", function () {
    const leaveDaysInput = document.getElementById("leave_days");
    const daysErrorMsg = document.getElementById("days_error_msg");
    const form = document.getElementById("addAnnualLeaveForm");
    const submitBtn = document.getElementById("submitLeaveBtn");

    if (!leaveDaysInput || !form) return;

    // 1. የ30 ቀን ጣሪያ መቆጣጠሪያ (Real-time Validation)
    leaveDaysInput.addEventListener("input", function () {
        const value = parseInt(this.value, 10);
        if (value > 30) {
            daysErrorMsg.style.display = "block";
            this.classList.add("is-invalid");
            if (submitBtn) submitBtn.disabled = true;
        } else {
            daysErrorMsg.style.display = "none";
            this.classList.remove("is-invalid");
            if (submitBtn) submitBtn.disabled = false;
        }
    });

    // 2. ፎርሙ ከመላኩ በፊት የመጨረሻ ማረጋገጫ (Form Submission Guard)
    form.addEventListener("submit", function (e) {
        const enteredDays = parseInt(leaveDaysInput.value, 10);
        
        if (isNaN(enteredDays) || enteredDays < 1 || enteredDays > 30) {
            e.preventDefault(); // ፎርሙ እንዳይላክ ማቆም
            
            if (typeof Swal !== 'undefined') {
                Swal.fire({ 
                    icon: 'error', 
                    title: 'የቀናት ስህተት', 
                    text: 'በአንድ በጀት አመት ከ 30 ቀን በላይ ወይም ከ 1 ቀን በታች መመዝገብ አይቻልም።' 
                });
            } else {
                alert("ስህተት፡ እባክዎ ከ 1 እስከ 30 ቀናት ባለው ክልል ውስጥ ያስገቡ።");
            }
            return;
        }

        // ለደህንነት ሲባል ከአንድ ጊዜ በላይ እንዳይጫኑት ቁልፉን ማሰናከል (Prevent Double Submission)
        if (submitBtn) {
            submitBtn.disabled = true;
            submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin mr-1"></i> በመመዝገብ ላይ...';
        }
    });
});
</script>