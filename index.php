<?php
require_once 'config.php';
$conn = getConnection();

// Ambil data tiket
$sql_tiket = "SELECT * FROM tiket";
$result_tiket = $conn->query($sql_tiket);

// Ambil data pembeli
$sql_pembeli = "SELECT * FROM pembeli ORDER BY nama";
$result_pembeli = $conn->query($sql_pembeli);

// Cek jumlah transaksi VIP untuk info diskon
$jumlah_vip = getJumlahTransaksiVIP($conn);
$sisa_untuk_diskon = 5 - ($jumlah_vip % 5);
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sistem Penjualan Tiket BEM</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            padding: 20px;
        }
        
        .container {
            max-width: 1200px;
            margin: 0 auto;
        }
        
        .header {
            background: white;
            padding: 30px;
            border-radius: 15px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.3);
            margin-bottom: 30px;
            text-align: center;
        }
        
        .header h1 {
            color: #667eea;
            margin-bottom: 10px;
        }
        
        .header p {
            color: #666;
        }
        
        .content {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 30px;
        }
        
        .card {
            background: white;
            padding: 30px;
            border-radius: 15px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.3);
        }
        
        .card h2 {
            color: #667eea;
            margin-bottom: 20px;
            border-bottom: 3px solid #667eea;
            padding-bottom: 10px;
        }
        
        .form-group {
            margin-bottom: 20px;
        }
        
        .form-group label {
            display: block;
            margin-bottom: 8px;
            color: #333;
            font-weight: 600;
        }
        
        .form-group select,
        .form-group input {
            width: 100%;
            padding: 12px;
            border: 2px solid #e0e0e0;
            border-radius: 8px;
            font-size: 14px;
            transition: border-color 0.3s;
        }
        
        .form-group select:focus,
        .form-group input:focus {
            outline: none;
            border-color: #667eea;
        }
        
        .btn {
            width: 100%;
            padding: 15px;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            border: none;
            border-radius: 8px;
            font-size: 16px;
            font-weight: 600;
            cursor: pointer;
            transition: transform 0.2s;
        }
        
        .btn:hover {
            transform: translateY(-2px);
        }
        
        .btn-secondary {
            background: #6c757d;
            margin-top: 10px;
        }
        
        .info-box {
            background: #f8f9fa;
            padding: 15px;
            border-radius: 8px;
            margin-bottom: 20px;
            border-left: 4px solid #667eea;
        }
        
        .info-box h3 {
            color: #667eea;
            margin-bottom: 10px;
            font-size: 16px;
        }
        
        .info-box p {
            color: #666;
            font-size: 14px;
            line-height: 1.6;
        }
        
        .ticket-info {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 15px;
            margin-bottom: 20px;
        }
        
        .ticket-card {
            background: linear-gradient(135deg, #f5f7fa 0%, #c3cfe2 100%);
            padding: 20px;
            border-radius: 10px;
            text-align: center;
        }
        
        .ticket-card h3 {
            color: #333;
            margin-bottom: 10px;
        }
        
        .ticket-card .price {
            font-size: 24px;
            font-weight: bold;
            color: #667eea;
        }
        
        .ticket-card .stock {
            margin-top: 10px;
            color: #666;
            font-size: 14px;
        }
        
        .alert {
            padding: 15px;
            border-radius: 8px;
            margin-bottom: 20px;
        }
        
        .alert-success {
            background: #d4edda;
            color: #155724;
            border: 1px solid #c3e6cb;
        }
        
        .alert-warning {
            background: #fff3cd;
            color: #856404;
            border: 1px solid #ffeaa7;
        }
        
        @media (max-width: 768px) {
            .content {
                grid-template-columns: 1fr;
            }
            
            .ticket-info {
                grid-template-columns: 1fr;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>🎫 Sistem Penjualan Tiket Seminar BEM</h1>
            <p>Universitas XYZ - Sistem Transaksi Tiket Acara</p>
        </div>
        
        <div class="content">
            <!-- Form Transaksi -->
            <div class="card">
                <h2>Form Pembelian Tiket</h2>
                
                <div class="info-box">
                    <h3>ℹ️ Info Diskon VIP</h3>
                    <p>Setiap pembelian VIP ke-5 mendapat diskon <strong>Rp20.000</strong></p>
                    <p>Transaksi VIP saat ini: <strong><?php echo $jumlah_vip; ?></strong></p>
                    <p>Sisa <?php echo $sisa_untuk_diskon; ?> transaksi lagi untuk diskon berikutnya!</p>
                </div>
                
                <form action="proses_transaksi.php" method="POST" id="formTransaksi">
                    <div class="form-group">
                        <label for="pembeli">Pilih Pembeli:</label>
                        <select name="id_pembeli" id="pembeli" required>
                            <option value="">-- Pilih Pembeli --</option>
                            <?php while ($pembeli = $result_pembeli->fetch_assoc()): ?>
                                <option value="<?php echo $pembeli['id_pembeli']; ?>">
                                    <?php echo $pembeli['nama']; ?> - <?php echo $pembeli['email']; ?>
                                </option>
                            <?php endwhile; ?>
                        </select>
                    </div>
                    
                    <div class="form-group">
                        <label for="tiket">Pilih Kategori Tiket:</label>
                        <select name="id_tiket" id="tiket" required onchange="updateHarga()">
                            <option value="">-- Pilih Tiket --</option>
                            <?php 
                            $result_tiket->data_seek(0); // Reset pointer
                            while ($tiket = $result_tiket->fetch_assoc()): 
                            ?>
                                <option value="<?php echo $tiket['id_tiket']; ?>" 
                                        data-harga="<?php echo $tiket['harga']; ?>"
                                        data-kategori="<?php echo $tiket['kategori']; ?>">
                                    <?php echo $tiket['kategori']; ?> - <?php echo formatRupiah($tiket['harga']); ?>
                                </option>
                            <?php endwhile; ?>
                        </select>
                    </div>
                    
                    <div class="form-group">
                        <label for="jumlah">Jumlah Tiket:</label>
                        <input type="number" name="jumlah" id="jumlah" min="1" value="1" required onchange="updateHarga()">
                    </div>
                    
                    <div class="info-box" id="ringkasan" style="display: none;">
                        <h3>Ringkasan Pembelian</h3>
                        <p id="ringkasan-text"></p>
                    </div>
                    
                    <button type="submit" class="btn">Proses Transaksi</button>
                    <button type="button" class="btn btn-secondary" onclick="window.location.href='tambah_pembeli.php'">
                        Tambah Pembeli Baru
                    </button>
                </form>
            </div>
            
            <!-- Info Tiket & Riwayat -->
            <div class="card">
                <h2>Informasi Tiket</h2>
                
                <div class="ticket-info">
                    <?php 
                    $result_tiket->data_seek(0);
                    while ($tiket = $result_tiket->fetch_assoc()): 
                    ?>
                        <div class="ticket-card">
                            <h3><?php echo $tiket['kategori']; ?></h3>
                            <div class="price"><?php echo formatRupiah($tiket['harga']); ?></div>
                            <div class="stock">Stok: <?php echo $tiket['stok']; ?> tiket</div>
                        </div>
                    <?php endwhile; ?>
                </div>
                
                <h2 style="margin-top: 30px;">Riwayat Transaksi Terakhir</h2>
                
                <?php
                $sql_riwayat = "SELECT t.*, p.nama, tk.kategori, t.tanggal_transaksi 
                               FROM transaksi t
                               JOIN pembeli p ON t.id_pembeli = p.id_pembeli
                               JOIN tiket tk ON t.id_tiket = tk.id_tiket
                               ORDER BY t.tanggal_transaksi DESC
                               LIMIT 5";
                $result_riwayat = $conn->query($sql_riwayat);
                
                if ($result_riwayat->num_rows > 0):
                ?>
                    <div style="margin-top: 20px;">
                        <?php while ($riwayat = $result_riwayat->fetch_assoc()): ?>
                            <div class="info-box" style="margin-bottom: 15px;">
                                <strong><?php echo $riwayat['nama']; ?></strong> - 
                                <?php echo $riwayat['kategori']; ?> (<?php echo $riwayat['jumlah']; ?>x)
                                <br>
                                <small style="color: #666;">
                                    <?php echo date('d/m/Y H:i', strtotime($riwayat['tanggal_transaksi'])); ?>
                                </small>
                                <br>
                                Total: <strong><?php echo formatRupiah($riwayat['total_bayar']); ?></strong>
                                <?php if ($riwayat['diskon'] > 0): ?>
                                    <span style="color: #28a745;"> (Diskon: <?php echo formatRupiah($riwayat['diskon']); ?>)</span>
                                <?php endif; ?>
                            </div>
                        <?php endwhile; ?>
                    </div>
                <?php else: ?>
                    <p style="color: #666; text-align: center; margin-top: 20px;">Belum ada transaksi</p>
                <?php endif; ?>
                
                <button type="button" class="btn" style="margin-top: 20px;" onclick="window.location.href='laporan.php'">
                    Lihat Semua Transaksi
                </button>
            </div>
        </div>
    </div>
    
    <script>
        function updateHarga() {
            const tiketSelect = document.getElementById('tiket');
            const jumlahInput = document.getElementById('jumlah');
            const ringkasan = document.getElementById('ringkasan');
            const ringkasanText = document.getElementById('ringkasan-text');
            
            if (tiketSelect.value && jumlahInput.value) {
                const selectedOption = tiketSelect.options[tiketSelect.selectedIndex];
                const harga = parseFloat(selectedOption.dataset.harga);
                const kategori = selectedOption.dataset.kategori;
                const jumlah = parseInt(jumlahInput.value);
                const subtotal = harga * jumlah;
                
                let diskon = 0;
                let keterangan = '';
                
                // Cek diskon untuk VIP
                if (kategori === 'VIP') {
                    const jumlahVIP = <?php echo $jumlah_vip; ?>;
                    if ((jumlahVIP + 1) % 5 === 0) {
                        diskon = 20000;
                        keterangan = '<br><span style="color: #28a745;">🎉 Selamat! Anda mendapat diskon Rp20.000 sebagai pembeli VIP ke-5!</span>';
                    }
                }
                
                const total = subtotal - diskon;
                
                ringkasanText.innerHTML = `
                    <strong>Kategori:</strong> ${kategori}<br>
                    <strong>Jumlah:</strong> ${jumlah} tiket<br>
                    <strong>Subtotal:</strong> Rp ${subtotal.toLocaleString('id-ID')}<br>
                    ${diskon > 0 ? `<strong>Diskon:</strong> Rp ${diskon.toLocaleString('id-ID')}<br>` : ''}
                    <strong>Total Bayar:</strong> <span style="font-size: 18px; color: #667eea;">Rp ${total.toLocaleString('id-ID')}</span>
                    ${keterangan}
                `;
                
                ringkasan.style.display = 'block';
            } else {
                ringkasan.style.display = 'none';
            }
        }
    </script>
</body>
</html>

<?php $conn->close(); ?>