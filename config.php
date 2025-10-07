<?php
// config.php - Konfigurasi Database

define('DB_HOST', 'localhost');
define('DB_USER', 'root');
define('DB_PASS', '');
define('DB_NAME', 'sistem_tiket_bem');

// Fungsi koneksi database
function getConnection() {
    $conn = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
    
    if ($conn->connect_error) {
        die("Koneksi gagal: " . $conn->connect_error);
    }
    
    return $conn;
}

// Fungsi untuk mendapatkan jumlah transaksi VIP
function getJumlahTransaksiVIP($conn) {
    $sql = "SELECT COUNT(*) as total FROM transaksi t 
            JOIN tiket tk ON t.id_tiket = tk.id_tiket 
            WHERE tk.kategori = 'VIP'";
    $result = $conn->query($sql);
    $row = $result->fetch_assoc();
    return $row['total'];
}

// Fungsi untuk cek apakah dapat diskon (setiap pembelian VIP ke-5)
function cekDiskonVIP($conn) {
    $jumlah = getJumlahTransaksiVIP($conn);
    // Jika jumlah + 1 habis dibagi 5, maka dapat diskon
    if (($jumlah + 1) % 5 == 0) {
        return 20000; // Diskon Rp20.000
    }
    return 0;
}

// Format rupiah
function formatRupiah($angka) {
    return "Rp " . number_format($angka, 0, ',', '.');
}
?>