<?php
require_once 'config.php';

if (!isset($_GET['id'])) {
    header("Location: index.php");
    exit();
}

$id_transaksi = $_GET['id'];
$conn = getConnection();

// Ambil detail transaksi
$sql = "SELECT t.*, p.nama, p.email, p.no_telepon, tk.kategori, tk.deskripsi
        FROM transaksi t
        JOIN pembeli p ON t.id_pembeli = p.id_pembeli
        JOIN tiket tk ON t.id_tiket = tk.id_tiket
        WHERE t.id_transaksi = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $id_transaksi);
$stmt->execute();
$result = $stmt->get_result();
$transaksi = $result->fetch_assoc();

if (!$transaksi) {
    header("Location: index.php?error=transaksi_not_found");
    exit();
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Bukti Transaksi - Sistem Tiket BEM</title>
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
            display: flex;
            align-items: center;
            justify-content: center;
        }
        
        .container {
            max-width: 600px;
            width: 100%;
        }
        
        .card {
            background: white;
            padding: 40px;
            border-radius: 15px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.3);
        }
        
        .success-icon {
            text-align: center;
            font-size: 60px;
            margin-bottom: 20px;
            animation: scaleIn 0.5s ease;
        }
        
        @keyframes scaleIn {
            from {
                transform: scale(0);
            }
            to {
                transform: scale(1);
            }
        }
        
        .card h1 {
            color: #28a745;
            text-align: center;
            margin-bottom: 10px;
        }
        
        .card p.subtitle {
            color: #666;
            text-align: center;
            margin-bottom: 30px;
        }
        
        .receipt {
            border: 2px dashed #667eea;
            padding: 25px;
            border-radius: 10px;
            margin-bottom: 20px;
            background: #f8f9fa;
        }
        
        .receipt-header {
            text-align: center;
            border-bottom: 2px solid #667eea;
            padding-bottom: 15px;
            margin-bottom: 20px;
        }
        
        .receipt-header h2 {
            color: #667eea;
            margin-bottom: 5px;
        }
        
        .receipt-header p {
            color: #666;
            font-size: 14px;
        }
        
        .receipt-row {
            display: flex;
            justify-content: space-between;
            padding: 10px 0;
            border-bottom: 1px solid #e0e0e0;
        }
        
        .receipt-row:last-child {
            border-bottom: none;
        }
        
        .receipt-label {
            color: #666;
            font-weight: 600;
        }
        
        .receipt-value {
            color: #333;
            text-align: right;
        }
        
        .total-section {
            background: #667eea;
            color: white;
            padding: 15px;
            border-radius: 8px;
            margin-top: 20px;
        }
        
        .total-section .receipt-row {
            border-bottom: 1px solid rgba(255,255,255,0.3);
        }
        
        .total-section .receipt-label,
        .total-section .receipt-value {
            color: white;
        }
        
        .total-section .grand-total {
            font-size: 24px;
            font-weight: bold;
            padding-top: 15px;
        }
        
        .discount-badge {
            display: inline-block;
            background: #28a745;
            color: white;
            padding: 5px 15px;
            border-radius: 20px;
            font-size: 12px;
            margin-left: 10px;
        }
        
        .btn {
            width: 100%;
            padding: 15px;
            border: none;
            border-radius: 8px;
            font-size: 16px;
            font-weight: 600;
            cursor: pointer;
            transition: transform 0.2s;
            margin-bottom: 10px;
        }
        
        .btn:hover {
            transform: translateY(-2px);
        }
        
        .btn-primary {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
        }
        
        .btn-secondary {
            background: #6c757d;
            color: white;
        }
        
        .btn-print {
            background: #28a745;
            color: white;
        }
        
        @media print {
            body {
                background: white;
                padding: 0;
            }
            
            .card {
                box-shadow: none;
            }
            
            .btn {
                display: none;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="card">
            <div class="success-icon">✅</div>
            <h1>Transaksi Berhasil!</h1>
            <p class="subtitle">Terima kasih atas pembelian Anda</p>
            
            <div class="receipt">
                <div class="receipt-header">
                    <h2>🎫 BUKTI PEMBELIAN TIKET</h2>
                    <p>BEM Universitas XYZ</p>
                    <p>ID Transaksi: #<?php echo str_pad($transaksi['id_transaksi'], 6, '0', STR_PAD_LEFT); ?></p>
                </div>
                
                <div class="receipt-row">
                    <span class="receipt-label">Tanggal & Waktu:</span>
                    <span class="receipt-value">
                        <?php echo date('d F Y, H:i', strtotime($transaksi['tanggal_transaksi'])); ?> WIB
                    </span>
                </div>
                
                <div class="receipt-row">
                    <span class="receipt-label">Nama Pembeli:</span>
                    <span class="receipt-value"><?php echo htmlspecialchars($transaksi['nama']); ?></span>
                </div>
                
                <div class="receipt-row">
                    <span class="receipt-label">Email:</span>
                    <span class="receipt-value"><?php echo htmlspecialchars($transaksi['email']); ?></span>
                </div>
                
                <?php if ($transaksi['no_telepon']): ?>
                <div class="receipt-row">
                    <span class="receipt-label">No. Telepon:</span>
                    <span class="receipt-value"><?php echo htmlspecialchars($transaksi['no_telepon']); ?></span>
                </div>
                <?php endif; ?>
                
                <div style="margin: 20px 0; padding: 20px 0; border-top: 2px solid #667eea; border-bottom: 2px solid #667eea;">
                    <div class="receipt-row">
                        <span class="receipt-label">Kategori Tiket:</span>
                        <span class="receipt-value">
                            <strong><?php echo $transaksi['kategori']; ?></strong>
                        </span>
                    </div>
                    
                    <div class="receipt-row">
                        <span class="receipt-label">Harga Satuan:</span>
                        <span class="receipt-value"><?php echo formatRupiah($transaksi['harga_satuan']); ?></span>
                    </div>
                    
                    <div class="receipt-row">
                        <span class="receipt-label">Jumlah:</span>
                        <span class="receipt-value"><?php echo $transaksi['jumlah']; ?> tiket</span>
                    </div>
                    
                    <div class="receipt-row">
                        <span class="receipt-label">Subtotal:</span>
                        <span class="receipt-value">
                            <?php echo formatRupiah($transaksi['harga_satuan'] * $transaksi['jumlah']); ?>
                        </span>
                    </div>
                    
                    <?php if ($transaksi['diskon'] > 0): ?>
                    <div class="receipt-row">
                        <span class="receipt-label">
                            Diskon 
                            <span class="discount-badge">🎉 PROMO</span>
                        </span>
                        <span class="receipt-value" style="color: #28a745;">
                            -<?php echo formatRupiah($transaksi['diskon']); ?>
                        </span>
                    </div>
                    
                    <?php if ($transaksi['keterangan']): ?>
                    <div style="background: #d4edda; padding: 10px; border-radius: 5px; margin-top: 10px; text-align: center; color: #155724;">
                        <strong>🎊 <?php echo htmlspecialchars($transaksi['keterangan']); ?>! 🎊</strong>
                    </div>
                    <?php endif; ?>
                    <?php endif; ?>
                </div>
                
                <div class="total-section">
                    <div class="receipt-row grand-total">
                        <span class="receipt-label">TOTAL BAYAR:</span>
                        <span class="receipt-value"><?php echo formatRupiah($transaksi['total_bayar']); ?></span>
                    </div>
                </div>
            </div>
            
            <button type="button" class="btn btn-print" onclick="window.print()">
                🖨️ Cetak Bukti
            </button>
            
            <button type="button" class="btn btn-primary" onclick="window.location.href='index.php'">
                🎫 Buat Transaksi Baru
            </button>
            
            <button type="button" class="btn btn-secondary" onclick="window.location.href='laporan.php'">
                📊 Lihat Semua Transaksi
            </button>
        </div>
    </div>
</body>
</html>

<?php $conn->close(); ?>