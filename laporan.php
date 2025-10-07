<?php
require_once 'config.php';
$conn = getConnection();

// Ambil semua transaksi
$sql = "SELECT t.*, p.nama, p.email, tk.kategori
        FROM transaksi t
        JOIN pembeli p ON t.id_pembeli = p.id_pembeli
        JOIN tiket tk ON t.id_tiket = tk.id_tiket
        ORDER BY t.tanggal_transaksi DESC";
$result = $conn->query($sql);

// Statistik
$sql_stats = "SELECT 
              COUNT(*) as total_transaksi,
              SUM(total_bayar) as total_pendapatan,
              SUM(CASE WHEN tk.kategori = 'Reguler' THEN t.jumlah ELSE 0 END) as total_reguler,
              SUM(CASE WHEN tk.kategori = 'VIP' THEN t.jumlah ELSE 0 END) as total_vip,
              SUM(diskon) as total_diskon
              FROM transaksi t
              JOIN tiket tk ON t.id_tiket = tk.id_tiket";
$result_stats = $conn->query($sql_stats);
$stats = $result_stats->fetch_assoc();
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Laporan Transaksi - Sistem Tiket BEM</title>
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
            max-width: 1400px;
            margin: 0 auto;
        }
        
        .header {
            background: white;
            padding: 30px;
            border-radius: 15px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.3);
            margin-bottom: 30px;
        }
        
        .header h1 {
            color: #667eea;
            margin-bottom: 5px;
        }
        
        .header p {
            color: #666;
        }
        
        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 20px;
            margin-bottom: 30px;
        }
        
        .stat-card {
            background: white;
            padding: 25px;
            border-radius: 15px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.3);
            text-align: center;
        }
        
        .stat-card .icon {
            font-size: 40px;
            margin-bottom: 10px;
        }
        
        .stat-card h3 {
            color: #666;
            font-size: 14px;
            margin-bottom: 10px;
            text-transform: uppercase;
            letter-spacing: 1px;
        }
        
        .stat-card .value {
            font-size: 28px;
            font-weight: bold;
            color: #667eea;
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
        
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }
        
        table thead {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
        }
        
        table th {
            padding: 15px;
            text-align: left;
            font-weight: 600;
        }
        
        table td {
            padding: 15px;
            border-bottom: 1px solid #e0e0e0;
        }
        
        table tbody tr:hover {
            background: #f8f9fa;
        }
        
        .badge {
            display: inline-block;
            padding: 5px 15px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: 600;
        }
        
        .badge-reguler {
            background: #e3f2fd;
            color: #1976d2;
        }
        
        .badge-vip {
            background: #fff3e0;
            color: #f57c00;
        }
        
        .badge-diskon {
            background: #e8f5e9;
            color: #2e7d32;
        }
        
        .btn {
            display: inline-block;
            padding: 12px 25px;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            border: none;
            border-radius: 8px;
            font-size: 14px;
            font-weight: 600;
            cursor: pointer;
            text-decoration: none;
            transition: transform 0.2s;
        }
        
        .btn:hover {
            transform: translateY(-2px);
        }
        
        .btn-secondary {
            background: #6c757d;
        }
        
        .empty-state {
            text-align: center;
            padding: 60px 20px;
            color: #666;
        }
        
        .empty-state .icon {
            font-size: 80px;
            margin-bottom: 20px;
            opacity: 0.5;
        }
        
        @media (max-width: 768px) {
            table {
                font-size: 12px;
            }
            
            table th,
            table td {
                padding: 10px 5px;
            }
            
            .stat-card {
                padding: 15px;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>📊 Laporan Transaksi Tiket</h1>
            <p>Sistem Penjualan Tiket BEM - Rekap Semua Transaksi</p>
        </div>
        
        <div class="stats-grid">
            <div class="stat-card">
                <div class="icon">🎫</div>
                <h3>Total Transaksi</h3>
                <div class="value"><?php echo number_format($stats['total_transaksi'] ?? 0); ?></div>
            </div>
            
            <div class="stat-card">
                <div class="icon">💰</div>
                <h3>Total Pendapatan</h3>
                <div class="value"><?php echo formatRupiah($stats['total_pendapatan'] ?? 0); ?></div>
            </div>
            
            <div class="stat-card">
                <div class="icon">👥</div>
                <h3>Tiket Reguler</h3>
                <div class="value"><?php echo number_format($stats['total_reguler'] ?? 0); ?></div>
            </div>
            
            <div class="stat-card">
                <div class="icon">⭐</div>
                <h3>Tiket VIP</h3>
                <div class="value"><?php echo number_format($stats['total_vip'] ?? 0); ?></div>
            </div>
            
            <div class="stat-card">
                <div class="icon">🎉</div>
                <h3>Total Diskon</h3>
                <div class="value"><?php echo formatRupiah($stats['total_diskon'] ?? 0); ?></div>
            </div>
        </div>
        
        <div class="card">
            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px;">
                <h2 style="margin: 0; border: none; padding: 0;">Daftar Transaksi</h2>
                <a href="index.php" class="btn">← Kembali ke Halaman Utama</a>
            </div>
            
            <?php if ($result->num_rows > 0): ?>
                <div style="overflow-x: auto;">
                    <table>
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Tanggal</th>
                                <th>Pembeli</th>
                                <th>Email</th>
                                <th>Kategori</th>
                                <th>Jumlah</th>
                                <th>Harga</th>
                                <th>Diskon</th>
                                <th>Total</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php while ($row = $result->fetch_assoc()): ?>
                                <tr>
                                    <td><strong>#<?php echo str_pad($row['id_transaksi'], 6, '0', STR_PAD_LEFT); ?></strong></td>
                                    <td><?php echo date('d/m/Y H:i', strtotime($row['tanggal_transaksi'])); ?></td>
                                    <td><?php echo htmlspecialchars($row['nama']); ?></td>
                                    <td><?php echo htmlspecialchars($row['email']); ?></td>
                                    <td>
                                        <span class="badge <?php echo $row['kategori'] == 'VIP' ? 'badge-vip' : 'badge-reguler'; ?>">
                                            <?php echo $row['kategori']; ?>
                                        </span>
                                    </td>
                                    <td><?php echo $row['jumlah']; ?>x</td>
                                    <td><?php echo formatRupiah($row['harga_satuan']); ?></td>
                                    <td>
                                        <?php if ($row['diskon'] > 0): ?>
                                            <span class="badge badge-diskon">
                                                -<?php echo formatRupiah($row['diskon']); ?>
                                            </span>
                                        <?php else: ?>
                                            <span style="color: #999;">-</span>
                                        <?php endif; ?>
                                    </td>
                                    <td><strong><?php echo formatRupiah($row['total_bayar']); ?></strong></td>
                                </tr>
                            <?php endwhile; ?>
                        </tbody>
                    </table>
                </div>
            <?php else: ?>
                <div class="empty-state">
                    <div class="icon">📭</div>
                    <h3>Belum Ada Transaksi</h3>
                    <p>Silakan buat transaksi pertama Anda</p>
                    <a href="index.php" class="btn" style="margin-top: 20px;">Buat Transaksi</a>
                </div>
            <?php endif; ?>
        </div>
    </div>
</body>
</html>

<?php $conn->close(); ?>