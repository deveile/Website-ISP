<?php
$host     = "localhost";
$user     = "root";
$password = "";
$database = "data_isp";

$koneksi = mysqli_connect(
    $host,
    $user,
    $password,
    $database
);

if(!$koneksi){
    die(
        "Koneksi database gagal : " .
        mysqli_connect_error()
    );
}

// PANGGIL AUTOLOAD PHPMAILER VERSI LAMA DI SINI
// Jalur disesuaikan: auth -> Mail\phpmailer -> PHPMailerAutoload.php
require 'auth/Mail/phpmailer/PHPMailerAutoload.php';

function kirimEmailPeringatan($koneksi, $id_customer, $sisa_tenggat) {
    // 1. Ambil data email dan nama customer dari tb_customer
    $query = mysqli_query($koneksi, "SELECT email_customer, nama_customer FROM tb_customer WHERE id_customer = '$id_customer' LIMIT 1");
    $customer = mysqli_fetch_assoc($query);

    if (!$customer || empty($customer['email_customer'])) {
        return false; 
    }

    $email_tujuan = $customer['email_customer'];
    $nama_customer = $customer['nama_customer'];

    // 2. PENGAMAN: Gunakan SESSIONS agar tidak spam email setiap refresh halaman
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }
    
    if (isset($_SESSION['email_terkirim_hari_ini']) && $_SESSION['email_terkirim_hari_ini'] == $sisa_tenggat) {
        return true; 
    }

    // 3. PROSES KONFIGURASI PHPMailer (Versi Lama)
    $mail = new PHPMailer; // Langsung panggil tanpa backslash atau namespace

    try {
        // Konfigurasi Server SMTP Gmail
        $mail->isSMTP();
        $mail->Host       = 'smtp.gmail.com';                     
        $mail->SMTPAuth   = true;                                 
        $mail->Username   = 'devdevv1864@gmail.com';                // Ganti dengan email ISP kamu
        $mail->Password   = 'knbd cgxe ggmj ezir';                // Ganti dengan 16 digit App Password Gmail
        $mail->SMTPSecure = 'tls';                                // Format versi lama langsung tulis 'tls' atau 'ssl'
        $mail->Port       = 587;                                  

        $mail->SMTPOptions = array(
            'ssl' => array(
                'verify_peer' => false,
                'verify_peer_name' => false,
                'allow_self_signed' => true
            )
        );
        // Penerima & Pengirim
        $mail->setFrom('devdevv1864@gmail.com', 'Anuwani ISP Internet');
        $mail->addAddress($email_tujuan, $nama_customer);         

        // Konten Email (Format HTML)
        $mail->isHTML(true);
        $mail->Subject = 'PENTING: Layanan Internet Ditangguhkan (Jatuh Tempo)';
        
        $mail->Body    = "
            <div style='font-family: Arial, sans-serif; max-width: 600px; border: 1px solid #e4e4e7; padding: 20px; border-radius: 8px;'>
                <h2 style='color: #ef4444;'>Pemberitahuan Masa Tangguhkan (Suspend)</h2>
                <p>Halo, <strong>" . htmlspecialchars($nama_customer) . "</strong>,</p>
                <p>Kami menginformasikan bahwa layanan internet Anda saat ini telah <strong>Ditangguhkan (Suspend)</strong> karena telah melewati batas tanggal jatuh tempo pembayaran.</p>
                
                <div style='background-color: #fffbeb; border-left: 4px solid #f59e0b; padding: 15px; margin: 20px 0;'>
                    <p style='margin: 0; color: #b45309;'>
                        <strong>Perhatian:</strong> Anda memiliki sisa waktu <strong>" . $sisa_tenggat . " hari</strong> lagi untuk melakukan pembayaran sebelum layanan kami <strong>dicabut secara permanen</strong> dari sistem.
                    </p>
                </div>
                
                <p>Silakan segera lakukan pembayaran melalui halaman Dashboard Customer Anda untuk mengaktifkan kembali layanan internet secara otomatis.</p>
                <br>
                <p>Salam hangat,<br><strong>Tim Anuwani ISP</strong></p>
            </div>
        ";

        $mail->send();
        
        // Catat di session
        $_SESSION['email_terkirim_hari_ini'] = $sisa_tenggat;
        return true;

    } catch (Exception $e) {
        error_log("Gagal mengirim email: {$mail->ErrorInfo}");
        return false;
    }
}

// ... Fungsi cekMasaTenggatLangganan ke bawah tetap sama ...
function cekMasaTenggatLangganan($koneksi, $id_customer) {
    $query = mysqli_query($koneksi, "SELECT id_langganan, tanggal_selesai, status_langganan 
                                     FROM tb_langganan 
                                     WHERE id_customer = '$id_customer' 
                                     AND status_langganan IN ('aktif', 'suspend', 'dicabut') 
                                     ORDER BY id_langganan DESC LIMIT 1");
    $data = mysqli_fetch_assoc($query);

    if (!$data) return ['status' => 'tidak_ada'];

    if ($data['status_langganan'] == 'dicabut') {
        return ['status' => 'dicabut', 'sisa_hari' => 0];
    }

    $tgl_selesai = new DateTime($data['tanggal_selesai']);
    $tgl_sekarang = new DateTime(date('Y-m-d'));
    
    $interval = $tgl_selesai->diff($tgl_sekarang);
    $selisih_hari = (int)$interval->format('%r%a');

    if ($selisih_hari < 0) {
        return ['status' => 'aman', 'sisa_hari' => abs($selisih_hari)];
    }

    $id_langganan = $data['id_langganan'];

    if ($selisih_hari >= 5) {
        if ($data['status_langganan'] != 'dicabut') {
            mysqli_query($koneksi, "UPDATE tb_langganan SET status_langganan = 'dicabut' WHERE id_langganan = '$id_langganan'");
        }
        return ['status' => 'dicabut', 'sisa_hari' => 0];

    } else {
        if ($data['status_langganan'] != 'suspend') {
            mysqli_query($koneksi, "UPDATE tb_langganan SET status_langganan = 'suspend' WHERE id_langganan = '$id_langganan'");
        }

        $sisa_tenggat_peringatan = 5 - $selisih_hari;

        kirimEmailPeringatan($koneksi, $id_customer, $sisa_tenggat_peringatan);

        return [
            'status' => 'peringatan_suspend', 
            'sisa_hari' => $sisa_tenggat_peringatan
        ];
    }
}
?>