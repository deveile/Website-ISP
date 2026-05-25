function openLogoutModal() {
    document.getElementById('logoutModal').classList.add('show');
}
function closeLogoutModal() {
    document.getElementById('logoutModal').classList.remove('show');
}
function showUpdateModal() {
    document.getElementById('updateSuccessModal').classList.add('show');
}
function closeUpdateModal() {
    document.getElementById('updateSuccessModal').classList.remove('show');
    window.location.href = 'index.php'; 
}



const metode = document.getElementById('metodePembayaran');
const bankBox = document.getElementById('bankBox');
const qrisBox = document.getElementById('qrisBox');

if (metode) {
    metode.addEventListener('change', function () {
        if (this.value == 'transfer') {
            bankBox.style.display = 'block';
            qrisBox.style.display = 'none';
        } else if (this.value == 'qris') {
            bankBox.style.display = 'none';
            qrisBox.style.display = 'block';
        } else {
            bankBox.style.display = 'none';
            qrisBox.style.display = 'none';
        }
    });
}


function openNonaktifModal(id) {
    document.getElementById('nonaktifModal').classList.add('show');
    document.getElementById('btnNonaktif').href = 'nonaktif.php?id=' + id;
}
function closeNonaktifModal() {
    document.getElementById('nonaktifModal').classList.remove('show');
}
function openAktifModal(id) {
    document.getElementById('aktifModal').classList.add('show');
    document.getElementById('btnAktif').href = 'aktifkan.php?id=' + id;
}
function closeAktifModal() {
    document.getElementById('aktifModal').classList.remove('show');
}


function openTambahModal() {
    document
        .getElementById('tambahModal')
        .classList.add('show');
}

function closeTambahModal() {
    document
        .getElementById('tambahModal')
        .classList.remove('show');
}

function submitTambahPaket() {
    document
        .getElementById('formPaket')
        .submit();
}