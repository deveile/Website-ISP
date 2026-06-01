<?php
require_once __DIR__ . '/../../auth/cek_login.php';
require_once __DIR__ . '/../../koneksi.php';

$id_transaksi = $_GET['id'] ?? '';

$sql = "
SELECT t.*,
    CASE WHEN t.jenis_transaksi='upgrade' THEN p_baru.nama_paket ELSE p_lama.nama_paket END AS nama_paket,
    CASE WHEN t.jenis_transaksi='upgrade' THEN p_baru.kecepatan ELSE p_lama.kecepatan END AS kecepatan,
    CASE WHEN t.jenis_transaksi='upgrade' THEN p_baru.harga ELSE p_lama.harga END AS harga
FROM tb_transaksi t
LEFT JOIN tb_langganan l ON t.id_langganan = l.id_langganan
LEFT JOIN tb_paket p_lama ON l.id_paket = p_lama.id_paket
LEFT JOIN tb_paket p_baru ON t.id_paket_baru = p_baru.id_paket
WHERE t.id_transaksi = '$id_transaksi'
";

$query = mysqli_query($koneksi, $sql);
$data = mysqli_fetch_assoc($query);
$js_script = "";

if (!$data) {
    echo "<script>
        document.addEventListener('DOMContentLoaded', function() {
            showPhpAlert('Data Tidak Ditemukan', 'Riwayat transaksi tidak valid.', 'index.php', 'error');
        });
    </script>";
    exit;
}

if ($data['status_pembayaran'] == 'lunas' || $data['status_pembayaran'] == 'menunggu_verifikasi') {
    echo "<script>
        document.addEventListener('DOMContentLoaded', function() {
            showPhpAlert('Tagihan Sudah Diproses', 'Transaksi ini sudah selesai atau sedang ditinjau.', 'index.php', 'warning');
        });
    </script>";
    exit;
}

if (isset($_POST['submit'])) {
    $metode = $_POST['metode_pembayaran'] ?? '';

    if (empty($metode) || $_FILES['bukti_pembayaran']['name'] == '') {
        echo "<script>
            document.addEventListener('DOMContentLoaded', function() {
                showPhpAlert('Data Belum Lengkap', 'Silakan pilih metode dan unggah bukti bayar.', '', 'warning');
            });
        </script>";
    } else {
        $bukti = $_FILES['bukti_pembayaran']['name'];
        $ext = pathinfo($bukti, PATHINFO_EXTENSION);
        $nama_file = "BUKTI-" . $data['kode_invoice'] . "-" . time() . "." . $ext;
        $allowed = ['jpg', 'jpeg', 'png', 'webp'];

        if (!in_array(strtolower($ext), $allowed)) {
            echo "<script>
                document.addEventListener('DOMContentLoaded', function() {
                    showPhpAlert('Format Salah', 'Format file harus JPG, PNG, atau WEBP!', '', 'error');
                });
            </script>";
            exit;
        }
        
        if (move_uploaded_file($_FILES['bukti_pembayaran']['tmp_name'], '../../assets/uploads/bukti/' . $nama_file)) {
            mysqli_begin_transaction($koneksi);
            try {
                $upd = "UPDATE tb_transaksi SET 
                        metode_pembayaran = '$metode', 
                        bukti_pembayaran = '$nama_file', 
                        status_pembayaran = 'menunggu_verifikasi' 
                        WHERE id_transaksi = '$id_transaksi'";
                mysqli_query($koneksi, $upd);
                mysqli_commit($koneksi);
                
                $js_script = "
                    <script>
                        document.addEventListener('DOMContentLoaded', function() {
                            const modalHtml = `
                                <div class='custom-overlay'>
                                    <div class='custom-modal-box content-pop'>
                                        <i class='bi bi-check-circle-fill icon-success'></i>
                                        <h2>Upload Berhasil!</h2>
                                        <p>Bukti pembayaran telah dikirim ke sistem. Mohon tunggu verifikasi oleh admin.</p>
                                        <a href='index.php' class='btn-modal-action btn-success-color'>Ke Dashboard</a>
                                    </div>
                                </div>
                            `;
                            document.body.insertAdjacentHTML('beforeend', modalHtml);
                        });
                    </script>
                ";
            } catch (Exception $e) {
                mysqli_rollback($koneksi);
                echo "<script>
                    document.addEventListener('DOMContentLoaded', function() {
                        showPhpAlert('Gagal Sistem', 'Terjadi kesalahan database internal.', '', 'error');
                    });
                </script>";
            }
        }
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pembayaran</title>
    <link rel="icon" type="image/png" href="../../assets/images/logo.png">
    <link rel="stylesheet" href="../../assets/css/style.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <style>
        .payment-method-grid {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 15px;
            margin-bottom: 20px;
        }
        .method-card {
            border: 2px solid #e2e8f0;
            border-radius: 12px;
            padding: 15px;
            text-align: center;
            cursor: pointer;
            transition: all 0.25s ease;
            position: relative;
            background: #fff;
        }
        .method-card input[type="radio"] {
            position: absolute;
            top: 12px; right: 12px;
            accent-color: #ff6b00;
            transform: scale(1.2);
        }
        .method-card i {
            font-size: 32px; color: #64748b;
            display: block; margin-bottom: 8px;
            transition: color 0.2s;
        }
        .method-card span { font-weight: 600; font-size: 14px; color: #334155; }
        .method-card.selected {
            border-color: #ff6b00; background-color: #fffaf7;
            box-shadow: 0 4px 12px rgba(255, 107, 0, 0.1);
        }
        .method-card.selected i, .method-card.selected span { color: #ff6b00; }
        .payment-box {
            background: #f8fafc; border: 1px dashed #cbd5e1;
            border-radius: 12px; padding: 15px; margin-bottom: 20px;
            animation: fadeIn 0.3s ease;
        }
        .payment-box h4 { margin: 0 0 5px 0; color: #1e293b; font-size: 15px; }
        .payment-box p { margin: 0; color: #64748b; font-size: 13px; line-height: 1.6; }
        #qrisBox { display: none; flex-direction: column; align-items: center; justify-content: center; text-align: center; }
        #qrisBox img {
            width: 100%; max-width: 180px; height: auto; margin-top: 12px;
            border-radius: 12px; border: 1px solid #e2e8f0; padding: 8px;
            background: #fff; box-shadow: 0 4px 10px rgba(0, 0, 0, 0.05);
        }
        .custom-file-upload {
            border: 2px dashed #cbd5e1; border-radius: 12px;
            padding: 25px 15px; text-align: center; background: #fff;
            cursor: pointer; transition: all 0.2s ease; display: block;
        }
        .custom-file-upload:hover { border-color: #ff6b00; background: #fffaf7; }
        .custom-file-upload i { font-size: 40px; color: #94a3b8; display: block; margin-bottom: 10px; }
        .custom-file-upload p { margin: 0; color: #64748b; font-size: 14px; }
        .custom-file-upload span { display: block; font-size: 11px; color: #94a3b8; margin-top: 5px; }
        .custom-file-upload .file-name-preview { margin-top: 8px; font-weight: 600; color: #ff6b00; font-size: 13px; display: none; }
        .hidden-file-input { display: none !important; }
        .custom-overlay {
            position: fixed; top: 0; left: 0; width: 100%; height: 100%;
            background: rgba(0, 0, 0, 0.6); backdrop-filter: blur(5px);
            display: flex; justify-content: center; align-items: center; z-index: 9999;
        }
        .custom-modal-box {
            background: #fff; padding: 30px; border-radius: 15px; width: 90%; max-width: 400px;
            text-align: center; box-shadow: 0 10px 25px rgba(0,0,0,0.2);
        }
        .content-pop { animation: popIn 0.3s ease-in-out; }
        .custom-modal-box i { font-size: 65px; display: block; margin-bottom: 15px; }
        .icon-success { color: #22c55e; }
        .icon-warning { color: #f59e0b; }
        .icon-error { color: #ef4444; }
        .custom-modal-box h2 { margin: 0 0 10px; color: #333; font-size: 22px; }
        .custom-modal-box p { color: #666; font-size: 14px; margin-bottom: 25px; line-height: 1.5; }
        .btn-modal-action {
            display: inline-block; padding: 12px 30px; color: #fff; width: 100%;
            text-decoration: none; border-radius: 8px; font-weight: 600; transition: 0.2s; border: none; cursor: pointer;
        }
        .btn-success-color { background: #22c55e; }
        .btn-success-color:hover { background: #16a34a; }
        .btn-warning-color { background: #f59e0b; }
        .btn-warning-color:hover { background: #d97706; }
        @keyframes fadeIn { from { opacity: 0; transform: translateY(-5px); } to { opacity: 1; transform: translateY(0); } }
        @keyframes popIn { 0% { transform: scale(0.7); opacity: 0; } 100% { transform: scale(1); opacity: 1; } }
    </style>
</head>
<body>

<div class="auth-container">
    <div class="payment-merge-card">
        <div class="payment-left">
            <h2>Ringkasan</h2>
            <div class="payment-summary-list">
                <div class="payment-summary-item">
                    <span>Invoice</span><strong><?= $data['kode_invoice'] ?? '-'; ?></strong>
                </div>
                <div class="payment-summary-item">
                    <span>Paket</span><strong><?= $data['nama_paket'] ?? '-'; ?></strong>
                </div>
                <div class="payment-summary-item">
                    <span>Total</span>
                    <strong class="payment-price">Rp <?= number_format($data['harga'] ?? 0, 0, ',', '.'); ?></strong>
                </div>
            </div>
        </div>

        <div class="payment-right">
            <h2>Pembayaran</h2>
            <form method="POST" enctype="multipart/form-data" id="paymentForm">
                <div class="form-group">
                    <label style="margin-bottom: 10px; display:block;">Pilih Metode Pembayaran</label>
                    <div class="payment-method-grid">
                        <label class="method-card" id="card-transfer">
                            <input type="radio" name="metode_pembayaran" value="transfer" id="radio-transfer">
                            <i class="bi bi-bank"></i>
                            <span>Transfer Bank</span>
                        </label>
                        <label class="method-card" id="card-qris">
                            <input type="radio" name="metode_pembayaran" value="qris" id="radio-qris">
                            <i class="bi bi-qr-code-scan"></i>
                            <span>QRIS / E-Wallet</span>
                        </label>
                    </div>
                </div>

                <div id="bankBox" class="payment-box" style="display:none;">
                    <h4>Informasi Rekening Bank</h4>
                    <p><strong>BCA:</strong> 1234567890 (a.n Anuwani Media)<br><strong>Mandiri:</strong> 9876543210 (a.n Anuwani Media)</p>
                </div>

                <div id="qrisBox" class="payment-box">
                    <h4>Scan Kode QRIS</h4>
                    <img src="../../assets/images/qris.png" alt="QRIS Code">
                </div>

                <div class="form-group">
                    <label style="margin-bottom: 10px; display:block;">Bukti Pembayaran</label>
                    <label class="custom-file-upload">
                        <input type="file" name="bukti_pembayaran" id="file-upload-input" class="hidden-file-input" accept="image/*">
                        <i class="bi bi-cloud-arrow-up"></i>
                        <p id="upload-box-text">Klik untuk telusuri foto bukti bayar</p>
                        <span>Format didukung: JPG, PNG, WEBP</span>
                        <div class="file-name-preview" id="file-name-preview"></div>
                    </label>
                </div>

                <button type="submit" name="submit" class="btn-payment" style="margin-top: 10px;">Kirim Konfirmasi</button>
            </form>
        </div>
    </div>
</div>

<script>
const cards = document.querySelectorAll('.method-card');
const bankBox = document.getElementById('bankBox');
const qrisBox = document.getElementById('qrisBox');

cards.forEach(card => {
    card.addEventListener('click', function() {
        cards.forEach(c => c.classList.remove('selected'));
        const radio = this.querySelector('input[type="radio"]');
        radio.checked = true;
        this.classList.add('selected');
        
        if (radio.value === 'transfer') {
            bankBox.style.display = 'block';
            qrisBox.style.display = 'none';
        } else if (radio.value === 'qris') {
            bankBox.style.display = 'none';
            qrisBox.style.display = 'flex'; 
        }
    });
});

const fileInput = document.getElementById('file-upload-input');
const textContainer = document.getElementById('upload-box-text');
const namePreview = document.getElementById('file-name-preview');

fileInput.addEventListener('change', function() {
    if (this.files && this.files.length > 0) {
        const fileName = this.files[0].name;
        textContainer.innerHTML = "<span style='color:#22c55e;'><i class='bi bi-file-earmark-check-fill' style='font-size:20px; display:inline; margin-right:5px;'></i>File Terpilih</span>";
        namePreview.textContent = fileName;
        namePreview.style.display = 'block';
    } else {
        textContainer.textContent = "Klik untuk telusuri foto bukti bayar";
        namePreview.style.display = 'none';
    }
});

const paymentForm = document.getElementById('paymentForm');

paymentForm.addEventListener('submit', function(event) {
    const radioTransfer = document.getElementById('radio-transfer');
    const radioQris = document.getElementById('radio-qris');
    
    if (!radioTransfer.checked && !radioQris.checked) {
        event.preventDefault(); 
        showPhpAlert('Metode Belum Dipilih', 'Silakan pilih salah satu metode pembayaran terlebih dahulu.', '', 'warning');
        return;
    }
    
    if (fileInput.files.length === 0) {
        event.preventDefault(); 
        showPhpAlert('Bukti Belum Diunggah', 'Silakan masukkan atau upload foto bukti pembayaran Anda terlebih dahulu.', '', 'warning');
        return;
    }
});

function showPhpAlert(title, text, redirectUrl = '', type = 'warning') {
    let iconClass = 'bi-exclamation-triangle-fill icon-warning';
    let btnClass = 'btn-warning-color';
    if(type === 'error') {
        iconClass = 'bi-x-circle-fill icon-error';
        btnClass = 'btn-warning-color';
    }

    const modalTemplate = `
        <div class='custom-overlay' id='alertContainer'>
            <div class='custom-modal-box content-pop'>
                <i class='bi ${iconClass}'></i>
                <h2>${title}</h2>
                <p>${text}</p>
                <button type='button' class='btn-modal-action ${btnClass}' id='closeAlertBtn'>Mengerti</button>
            </div>
        </div>
    `;
    
    document.body.insertAdjacentHTML('beforeend', modalTemplate);
    
    document.getElementById('closeAlertBtn').addEventListener('click', function() {
        document.getElementById('alertContainer').remove();
        if (redirectUrl !== '') {
            window.location.href = redirectUrl;
        }
    });
}
</script>

<?php echo $js_script; ?>
</body>
</html>