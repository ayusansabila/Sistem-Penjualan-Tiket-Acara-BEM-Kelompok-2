<?php
require_once 'config.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $conn = getConnection();
    
    // Ambil data dari form
    $id_pembeli = $_POST['id_pembeli'];
    $id_tiket = $_POST['id_tiket'];
    $jumlah = $_POST['jumlah'];
    
    // Validasi input
    if (empty($id_pembeli) || empty($id_tiket) || empty($jumlah) || $jumlah < 1) {
        header("Location: index.php?error=invalid_input");
        exit();
    }
    
    // Ambil data tiket
    $sql_tiket = "SELECT * FROM tiket WHERE id_tiket = ?";
    $stmt = $conn->prepare($sql_tiket);
    $stmt->bind_param("i", $id_tiket);
    $stmt->execute();
    $result_tiket = $stmt->get_result();
    $tiket = $result_tiket->fetch_assoc();
    
    if (!$tiket) {
        header("Location: index.php?error=tiket_not_found");
        exit();
    }
    
    // Cek stok
    if ($tiket['stok'] < $jumlah) {
        header("Location: index.php?error=stok_tidak_cukup");
        exit();
    }
    
    $harga_satuan = $tiket['harga'];
    $kategori = $tiket['kategori'];
    $diskon = 0;
    $keterangan = '';
    
    // LOGIKA DISKON UNTUK VIP (Setiap pembelian VIP ke-5 dapat diskon Rp20.000)
    if ($kategori == 'VIP') {
        $diskon = cekDiskonVIP($conn);
        if ($diskon > 0) {
            $keterangan = 'Diskon pembeli VIP ke-5';
        }
    }
    
    // Hitung total
    $subtotal = $harga_satuan * $jumlah;
    $total_bayar = $subtotal - $diskon;
    
    // Mulai transaksi database
    $conn->begin_transaction();
    
    try {
        // Insert transaksi
        $sql_insert = "INSERT INTO transaksi (id_pembeli, id_tiket, jumlah, harga_satuan, diskon, total_bayar, keterangan) 
                        VALUES (?, ?, ?, ?, ?, ?, ?)";
        $stmt_insert = $conn->prepare($sql_insert);
        $stmt_insert->bind_param("iiiddds", $id_pembeli, $id_tiket, $jumlah, $harga_satuan, $diskon, $total_bayar, $keterangan);
        $stmt_insert->execute();
        
        $id_transaksi = $conn->insert_id;
        
        // Update stok tiket
        $stok_baru = $tiket['stok'] - $jumlah;
        $sql_update_stok = "UPDATE tiket SET stok = ? WHERE id_tiket = ?";
        $stmt_update = $conn->prepare($sql_update_stok);
        $stmt_update->bind_param("ii", $stok_baru, $id_tiket);
        $stmt_update->execute();
        
        // Commit transaksi
        $conn->commit();
        
        // Redirect ke halaman sukses
        header("Location: detail_transaksi.php?id=" . $id_transaksi);
        exit();
        
    } catch (Exception $e) {
        // Rollback jika ada error
        $conn->rollback();
        header("Location: index.php?error=transaksi_gagal");
        exit();
    }
    
    $conn->close();
    
} else {
    header("Location: index.php");
    exit();
}
?>