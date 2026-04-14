document.addEventListener("DOMContentLoaded", function() {
    // --- LOGIKA OTOMATIS REDIRECT ---
    if (window.showSuccessMessage) {
        let timeLeft = 3;
        const timerDisplay = document.getElementById('timer');
        const countdown = setInterval(function() {
            timeLeft--;
            if(timerDisplay) timerDisplay.textContent = timeLeft;
            if (timeLeft <= 0) {
                clearInterval(countdown);
                window.location.href = window.redirectRoute;
            }
        }, 1000);
    }

    // --- LOGIKA SIGNATURE PAD ---
    const canvas = document.getElementById('signature-pad');
    if(canvas) {
        const signaturePad = new SignaturePad(canvas, {
            backgroundColor: 'rgba(255, 255, 255, 0)',
            penColor: 'rgb(0, 0, 0)'
        });

        function resizeCanvas() {
            const ratio = Math.max(window.devicePixelRatio || 1, 1);
            canvas.width = canvas.offsetWidth * ratio;
            canvas.height = canvas.offsetHeight * ratio;
            canvas.getContext("2d").scale(ratio, ratio);
            signaturePad.clear();
        }

        window.onresize = resizeCanvas;
        resizeCanvas();

        const clearBtn = document.getElementById('clear-btn');
        if (clearBtn) {
            clearBtn.addEventListener('click', function() {
                signaturePad.clear();
            });
        }

        const form = document.getElementById('signature-form');
        if (form) {
            form.addEventListener('submit', function(e) {
                if (signaturePad.isEmpty()) {
                    e.preventDefault();
                    Swal.fire({
                        icon: 'warning',
                        title: 'Tanda Tangan Kosong',
                        text: "Silakan tanda tangan terlebih dahulu!",
                        confirmButtonColor: '#0058a8'
                    });
                } else {
                    const dataURL = signaturePad.toDataURL('image/png');
                    const sigValue = document.getElementById('signature-value');
                    if (sigValue) sigValue.value = dataURL;
                }
            });
        }
    }
});
