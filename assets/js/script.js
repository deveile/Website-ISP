function openLogoutModal() {
    document
        .getElementById('logoutModal')
        .classList.add('show');
}

function closeLogoutModal() {
    document
        .getElementById('logoutModal')
        .classList.remove('show');
}

const metode = document.getElementById('metodePembayaran');
const bankBox = document.getElementById('bankBox');
const qrisBox = document.getElementById('qrisBox');

if (metode) {
    metode.addEventListener('change', function () {
        if (this.value == 'Transfer Bank') {
            bankBox.style.display = 'block';
            qrisBox.style.display = 'none';
        } else if (this.value == 'QRIS') {
            bankBox.style.display = 'none';
            qrisBox.style.display = 'block';
        } else {
            bankBox.style.display = 'none';
            qrisBox.style.display = 'none';
        }
    });
}