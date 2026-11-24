// =========================================================
// LEARNSPHERE INTERACTIVE SCRIPT - MODUL 4 JAVASCRIPT
// =========================================================

// 1. Terapkan tema tersimpan segera saat halaman dimuat
(function initTheme() {
    const savedTheme = localStorage.getItem("learnsphere_theme") || "light";
    document.documentElement.setAttribute("data-theme", savedTheme);
})();

document.addEventListener("DOMContentLoaded", function () {
    updateThemeButtonText();
});

function toggleTheme() {
    const current = document.documentElement.getAttribute("data-theme") || "light";
    const next = current === "light" ? "dark" : "light";
    document.documentElement.setAttribute("data-theme", next);
    localStorage.setItem("learnsphere_theme", next);
    updateThemeButtonText();
}

function updateThemeButtonText() {
    const btn = document.getElementById("btn-theme-toggle");
    if (!btn) return;
    const current = document.documentElement.getAttribute("data-theme") || "light";
    btn.textContent = current === "dark" ? "Mode Terang" : "Mode Gelap";
}

// 2. Pengerjaan & Koreksi Otomatis Contoh Soal pada Modul Materi
function periksaContohSoal(soalId, kunciBenar) {
    const pilihanTerpilih = document.querySelector('input[name="contoh_opsi_' + soalId + '"]:checked');

    if (!pilihanTerpilih) {
        alert("Silakan pilih salah satu jawaban (A, B, C, atau D) terlebih dahulu!");
        return;
    }

    const jawabanUser = pilihanTerpilih.value;
    const opsiHuruf = ["A", "B", "C", "D"];

    // Tandai opsi yang benar & kunci input agar tidak berubah sebelum di-reset
    opsiHuruf.forEach(function (huruf) {
        const label = document.getElementById("label-contoh-" + soalId + "-" + huruf);
        const radio = label ? label.querySelector('input[type="radio"]') : null;

        if (label) {
            label.classList.remove("is-correct", "is-wrong");
            if (huruf === kunciBenar) {
                label.classList.add("is-correct");
            }
        }
        if (radio) {
            radio.disabled = true;
        }
    });

    const feedbackBox = document.getElementById("feedback-contoh-" + soalId);
    const pembahasanBox = document.getElementById("pembahasan-" + soalId);
    const btnReset = document.getElementById("btn-reset-" + soalId);

    feedbackBox.classList.remove("feedback-success", "feedback-error");
    feedbackBox.style.display = "block";

    if (jawabanUser === kunciBenar) {
        feedbackBox.classList.add("feedback-success");
        feedbackBox.textContent = "Tepat sekali! Jawaban Anda (" + jawabanUser + ") benar.";
    } else {
        const labelSalah = document.getElementById("label-contoh-" + soalId + "-" + jawabanUser);
        if (labelSalah) {
            labelSalah.classList.add("is-wrong");
        }
        feedbackBox.classList.add("feedback-error");
        feedbackBox.textContent = "Kurang tepat. Anda memilih " + jawabanUser + ", sedangkan kunci jawaban adalah " + kunciBenar + ".";
    }

    // Tampilkan pembahasan dan tombol Coba Lagi
    if (pembahasanBox) pembahasanBox.style.display = "block";
    if (btnReset) btnReset.style.display = "inline-flex";
}

// Reset Contoh Soal agar dapat dikerjakan ulang
function resetContohSoal(soalId) {
    const opsiHuruf = ["A", "B", "C", "D"];
    opsiHuruf.forEach(function (huruf) {
        const label = document.getElementById("label-contoh-" + soalId + "-" + huruf);
        const radio = label ? label.querySelector('input[type="radio"]') : null;
        if (label) {
            label.classList.remove("is-correct", "is-wrong");
        }
        if (radio) {
            radio.checked = false;
            radio.disabled = false;
        }
    });

    const feedbackBox = document.getElementById("feedback-contoh-" + soalId);
    const pembahasanBox = document.getElementById("pembahasan-" + soalId);
    const btnReset = document.getElementById("btn-reset-" + soalId);

    if (feedbackBox) feedbackBox.style.display = "none";
    if (pembahasanBox) pembahasanBox.style.display = "none";
    if (btnReset) btnReset.style.display = "none";
}

// 3. Validasi Form Registrasi (Modul 4)
function validasiRegister() {
    const nama = document.getElementById("nama_lengkap").value.trim();
    const username = document.getElementById("username").value.trim();
    const pass = document.getElementById("password").value;
    const konfirmasi = document.getElementById("konfirmasi_password").value;

    if (nama === "" || username === "" || pass === "" || konfirmasi === "") {
        alert("Semua field pendaftaran wajib diisi!");
        return false;
    }

    const regexNama = /^[a-zA-Z\s.]+$/;
    if (!regexNama.test(nama)) {
        alert("Nama lengkap hanya boleh berisi huruf, spasi, atau titik!");
        document.getElementById("nama_lengkap").focus();
        return false;
    }

    if (pass.length < 6) {
        alert("Password minimal terdiri dari 6 karakter!");
        return false;
    }

    if (pass !== konfirmasi) {
        alert("Konfirmasi password tidak cocok!");
        document.getElementById("konfirmasi_password").focus();
        return false;
    }

    return true;
}

// 4. Validasi Form Login
function validasiLogin() {
    const user = document.getElementById("username").value.trim();
    const pass = document.getElementById("password").value.trim();

    if (user === "" || pass === "") {
        alert("Username dan Password tidak boleh kosong!");
        return false;
    }
    return true;
}

// 5. Character Counter untuk Diskusi Modul (Modul 4 Percobaan 6)
function hitungKarakter(textareaId, counterId, maks) {
    const field = document.getElementById(textareaId);
    const counter = document.getElementById(counterId);
    if (!field || !counter) return;

    let sisa = maks - field.value.length;
    if (sisa < 0) {
        alert("Maaf, batas maksimum pengisian adalah " + maks + " karakter!");
        field.value = field.value.substring(0, maks);
        sisa = 0;
    }
    counter.value = sisa;
}

// 6. Pelacak Progres Soal Terjawab pada Halaman Quiz
function updateProgresQuiz(totalSoal) {
    const progresEl = document.getElementById("progres-terjawab");
    if (!progresEl) return;
    const terjawab = document.querySelectorAll('#form-kerjakan-quiz input[type="radio"]:checked').length;
    progresEl.textContent = terjawab + " / " + totalSoal + " Terjawab";
}

// 7. Timer Otomatis Pengerjaan Quiz Pilihan Ganda
function mulaiTimerQuiz(durasiMenit) {
    const display = document.getElementById("waktu-tersisa");
    const formQuiz = document.getElementById("form-kerjakan-quiz");
    if (!display || !formQuiz) return;

    let totalDetik = durasiMenit * 60;

    const interval = setInterval(function () {
        let menit = Math.floor(totalDetik / 60);
        let detik = totalDetik % 60;

        menit = menit < 10 ? "0" + menit : menit;
        detik = detik < 10 ? "0" + detik : detik;

        display.textContent = menit + ":" + detik;

        if (totalDetik <= 0) {
            clearInterval(interval);
            alert("Waktu pengerjaan quiz telah habis! Jawaban Anda akan dikirim otomatis.");
            formQuiz.submit();
        }
        totalDetik--;
    }, 1000);
}