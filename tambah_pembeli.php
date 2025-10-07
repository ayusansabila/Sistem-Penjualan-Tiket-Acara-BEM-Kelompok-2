<?php
require_once 'config.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $conn = getConnection();
    
    $nama = $_POST['nama'];
    $email = $_POST['email'];
    $no_telepon = $_POST['no_telepon'];
    
    // Validasi
    if (empty($nama) || empty($email)) {
        $error = "Nama dan email harus diisi!";
    } else {
        // Cek email sudah ada atau belum
        $sql_cek = "SELECT * FROM pembeli WHERE email = ?";
        $stmt_cek = $conn->prepare($sql_cek);
        $stmt_cek->bind_param("s", $email);
        $stmt_cek->execute();
        $result_cek = $stmt_cek->get_result();
        
        if ($result_cek->num_rows > 0) {
            $error = "Email sudah terdaftar!";
        } else {
            // Insert pembeli baru
            $sql_insert = "INSERT INTO pembeli (nama, email, no_telepon) VALUES (?, ?, ?)";
            $stmt_insert = $conn->prepare($sql_insert);
            $stmt_insert->bind_param("sss", $nama, $email, $no_telepon);
            
            if ($stmt_insert->execute()) {
                header("Location: index.php?success=pembeli_ditambahkan");
                exit();
            } else {
                $error = "Gagal menambahkan pembeli!";
            }
        }
    }
    
    $conn->close();
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tambah Pembeli - Sistem Tiket BEM</title>
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
            max-width: 500px;
            width: 100%;
        }
        
        .card {
            background: white;
            padding: 40px;
            border-radius: 15px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.3);
        }
        
        .card h1 {
            color: #667eea;
            margin-bottom: 10px;
            text-align: center;
        }
        
        .card p {
            color: #666;
            text-align: center;
            margin-bottom: 30px;
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
        
        .form-group input {
            width: 100%;
            padding: 12px;
            border: 2px solid #e0e0e0;
            border-radius: 8px;
            font-size: 14px;
            transition: border-color 0.3s;
        }
        
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
            margin-bottom: 10px;
        }
        
        .btn:hover {
            transform: translateY(-2px);
        }
        
        .btn-secondary {
            background: #6c757d;
        }
        
        .alert {
            padding: 15px;
            border-radius: 8px;
            margin-bottom: 20px;
            text-align: center;
        }
        
        .alert-error {
            background: #f8d7da;
            color: #721c24;
            border: 1px solid #f5c6cb;
        }
        
        .back-link {
            text-align: center;
            margin-top: 20px;
        }
        
        .back-link a {
            color: #667eea;
            text-decoration: none;
            font-weight: 600;
        }
        
        .back-link a:hover {
            text-decoration: underline;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="card">
            <h1>➕ Tambah Pembeli Baru</h1>
            <p>Daftarkan pembeli baru untuk sistem tiket</p>
            
            <?php if (isset($error)): ?>
                <div class="alert alert-error">
                    <?php echo $error; ?>
                </div>
            <?php endif; ?>
            
            <form method="POST" action="">
                <div class="form-group">
                    <label for="nama">Nama Lengkap: *</label>
                    <input type="text" name="nama" id="nama" required 
                           placeholder="Masukkan nama lengkap"
                           value="<?php echo isset($_POST['nama']) ? htmlspecialchars($_POST['nama']) : ''; ?>">
                </div>
                
                <div class="form-group">
                    <label for="email">Email: *</label>
                    <input type="email" name="email" id="email" required 
                           placeholder="contoh@email.com"
                           value="<?php echo isset($_POST['email']) ? htmlspecialchars($_POST['email']) : ''; ?>">
                </div>
                
                <div class="form-group">
                    <label for="no_telepon">No. Telepon:</label>
                    <input type="text" name="no_telepon" id="no_telepon" 
                           placeholder="081234567890"
                           value="<?php echo isset($_POST['no_telepon']) ? htmlspecialchars($_POST['no_telepon']) : ''; ?>">
                </div>
                
                <button type="submit" class="btn">Simpan Pembeli</button>
                <button type="button" class="btn btn-secondary" onclick="window.location.href='index.php'">
                    Kembali ke Halaman Utama
                </button>
            </form>
        </div>
    </div>
</body>
</html>