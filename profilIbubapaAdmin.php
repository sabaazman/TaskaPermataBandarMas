<?php
session_start();
require_once 'config.php'; // Sambungan ke database

// Semak jika pentadbir sedang log masuk
if (!isset($_SESSION['id_pentadbir'])) {
    header("Location: login.php");
    exit();
}

// Semak jika parameter 'id_ibubapa' ada dalam URL
if (isset($_GET['id_ibubapa'])) {
    $id_ibubapa = $_GET['id_ibubapa'];

    // Query untuk mendapatkan maklumat ibu bapa dan anak-anak berdasarkan id_ibubapa
    $query = "
    SELECT 
        ibubapa.id_ibubapa,
        ibubapa.ic_bapa,
        ibubapa.nama_bapa,
        ibubapa.pekerjaan_bapa,
        ibubapa.pendapatan_bapa,
        ibubapa.email_bapa,
        ibubapa.no_bapa,
        ibubapa.ic_ibu,
        ibubapa.nama_ibu,
        ibubapa.pekerjaan_ibu,
        ibubapa.pendapatan_ibu,
        ibubapa.EmailIbu,
        ibubapa.no_ibu,
        ibubapa.pengesahan,
        pelajar.ic_pelajar,
        pelajar.nama_pelajar,
        pelajar.jantina,
        pelajar.alamat_semasa,
        pelajar.umur,
        pelajar.gambar_pelajar,
        pelajar.Alahan,
        pelajar.tahun_pengajian,
        pelajar.sijilLahir_pelajar
    FROM ibubapa
    LEFT JOIN pelajar ON pelajar.ibubapa_id = ibubapa.id_ibubapa
    WHERE ibubapa.id_ibubapa = ?";

    $stmt = $conn->prepare($query);
    if (!$stmt) {
        die("Prepare statement failed: " . $conn->error);
    }

    // Bind parameter: 'i' untuk integer id_ibubapa
    $stmt->bind_param("i", $id_ibubapa);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows == 0) {
        die("Ibu bapa tidak dijumpai.");
    }

    // Simpan data ibu bapa (sama untuk semua baris)
    $ibu_bapa_data = null;
    $anak_data = [];

    while ($row = $result->fetch_assoc()) {
        if (!$ibu_bapa_data) {
            $ibu_bapa_data = [
                'ic_bapa' => $row['ic_bapa'],
                'nama_bapa' => $row['nama_bapa'],
                'pekerjaan_bapa' => $row['pekerjaan_bapa'],
                'pendapatan_bapa' => $row['pendapatan_bapa'],
                'email_bapa' => $row['email_bapa'],
                'no_bapa' => $row['no_bapa'],
                'ic_ibu' => $row['ic_ibu'],
                'nama_ibu' => $row['nama_ibu'],
                'pekerjaan_ibu' => $row['pekerjaan_ibu'],
                'pendapatan_ibu' => $row['pendapatan_ibu'],
                'EmailIbu' => $row['EmailIbu'],
                'no_ibu' => $row['no_ibu'],
                'pengesahan' => $row['pengesahan'],
            ];
        }
        if ($row['ic_pelajar']) {
            $anak_data[] = [
                'ic_pelajar' => $row['ic_pelajar'],
                'nama_pelajar' => $row['nama_pelajar'],
                'jantina' => $row['jantina'],
                'alamat_semasa' => $row['alamat_semasa'],
                'umur' => $row['umur'],
                'gambar_pelajar' => $row['gambar_pelajar'],
                'Alahan' => $row['Alahan'],
                'tahun_pengajian' => $row['tahun_pengajian'],
                'sijilLahir_pelajar' => $row['sijilLahir_pelajar'],
            ];
        }
    }

    $stmt->close();
} else {
    die("ID Ibu bapa tidak disediakan.");
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Maklumat Pelajar</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <link rel="stylesheet" href="css/dashboard.css">
    <style>
       .profile-container {
        width: 70%;
        margin: 20px auto;
        background: #fff;
        padding: 20px;
        border-radius: 8px;
        box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
    }

    .profile-container h1 {
        font-weight: bold;
        text-align: center;
        margin-bottom: 20px;
    }

    /* Standardized Table Styling */
    .profile-container table {
        width: 100%;
        border-collapse: collapse;
        table-layout: fixed; /* Ensures consistent column widths */
    }

    .profile-container table th,
    .profile-container table td {
        padding: 12px 15px;
        border-bottom: 1px solid #ddd;
        word-wrap: break-word; /* Handles long content */
    }

    .profile-container table th {
            width: 30%;
            text-align: left;
            background-color: #3a4065; /* Warna biru tua yang Anda mau */
            color: white; /* Warna teks putih untuk kontras */
            font-weight: 600;
            padding: 12px 15px;
        }

    .profile-container table td {
        width: 70%; /* Fixed width for all data cells */
    }

    .student-photo {
        text-align: center;
        margin-bottom: 20px;
    }

    .student-photo img {
        width: 150px;
        height: 150px;
        border-radius: 50%;
        object-fit: cover;
        border: 2px solid #ddd;
    }

    .crud-buttons {
        display: flex;
        justify-content: center;
        gap: 10px;
        margin-top: 20px;
    }

    .crud-buttons button {
        padding: 10px 20px;
        background: #666;
        color: white;
        border: none;
        border-radius: 5px;
        cursor: pointer;
    }

    .crud-buttons button:hover {
        background: #444;
    }

    h3 {
        font-weight: bold;
        margin-top: 30px;
        color: #3a4065;
        padding-bottom: 8px;
        border-bottom: 2px solid #eee;
    }

    /* Responsive adjustments */
    @media (max-width: 768px) {
        .profile-container {
            width: 95%;
            padding: 15px;
        }
        
        .profile-container table th,
        .profile-container table td {
            display: block;
            width: 100%;
            box-sizing: border-box;
        }
        
        .profile-container table th {
            width: 30%;
            text-align: left;
            background-color: #3a4065; /* Warna biru tua yang Anda mau */
            color: white; /* Warna teks putih untuk kontras */
            font-weight: 600;
            padding: 12px 15px;
        }
    }

        .modal-body h5 {
            font-weight: bold;
            margin-top: 20px;
            margin-bottom: 10px;
        }

        .modal-body .mb-3 {
            margin-bottom: 15px;
        }
        /* Base styles */
        body {
            margin: 0;
            font-family: 'Segoe UI', system-ui, -apple-system, sans-serif;
            background: #f8f9fa;
            color: #333;
        }

        .container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 0 20px;
        }

        /* Header styling */
        header {
            background: linear-gradient(135deg, #3a4065 0%, #4e54c8 100%);
            padding: 1rem;
            display: flex;
            justify-content: space-between;
            align-items: center;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
        }

        header a {
            color: white !important;
            text-decoration: none;
            font-weight: 600;
            transition: all 0.3s ease;
        }

        header a:hover {
            opacity: 0.9;
            transform: translateY(-1px);
        }


        /* Navigation dropdown styling */
        /* Navigation styling */
        nav {
            background: #ffffff !important;
            border-radius: 8px;
            margin: 1rem 0;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.05);
        }

        nav ul {
            background: transparent !important;
            list-style-type: none;
            padding: 0 !important;
            margin: 0;
            display: flex;
            gap: 0.5rem;
        }

        nav ul li a {
            text-decoration: none;
            color: #3a4065 !important;
            display: block;
            padding: 1rem 1.5rem;
            transition: all 0.3s ease;
            border-radius: 6px;
        }

        /* Dropdown styling */
        .dropdown {
            background-color: #ffffff !important;
            border: 1px solid rgba(58, 64, 101, 0.1);
        }

        .dropdown li a {
            color: #3a4065 !important;
        }

        /* Submenu styling */
        .submenu {
            background-color: #ffffff !important;
            border: 1px solid rgba(58, 64, 101, 0.1);
        }

        .submenu li a {
            color: #3a4065 !important;
        }

        /* Hover effects */
        nav ul li a:hover,
        .dropdown li a:hover,
        .submenu li a:hover {
            background: rgba(58, 64, 101, 0.1);
            color: #4e54c8 !important;
        }
        nav ul li .dropdown-icon {
            margin-left: 3px;
            font-size: 8px;
        }

        nav ul .dropdown {
            display: none;
            position: absolute;
            left: 0;
            top: 100%;
            background-color: #ffffff;
            box-shadow: 0 8px 24px rgba(0, 0, 0, 0.15);
            padding: 0.5rem;
            margin: 0;
            list-style-type: none;
            border-radius: 8px;
            overflow: visible;
            min-width: 200px;
            z-index: 1000;
            border: 1px solid rgba(58, 64, 101, 0.1);
        }

        nav ul li:hover > ul.dropdown {
            display: block;
        }

        nav ul .dropdown li {
            padding: 0;
            width: 100%;
            margin: 0;
        }

        nav ul .dropdown li a {
            padding: 0.75rem 1rem;
            color: #3a4065 !important;
            display: block;
            border-radius: 4px;
            margin: 2px;
        }

        nav ul .dropdown li a:hover {
            background: rgba(58, 64, 101, 0.1);
            color: #4e54c8 !important;
        }

        nav ul .dropdown-parent > a {
            cursor: pointer;
        }

        nav ul .submenu {
            display: none;
            position: absolute;
            left: 100%;
            top: 0;
            background-color: #ffffff;
            padding: 0.5rem;
            list-style-type: none;
            border-radius: 8px;
            box-shadow: 0 8px 24px rgba(0, 0, 0, 0.15);
            min-width: 200px;
            z-index: 1100;
            border: 1px solid rgba(58, 64, 101, 0.1);
        }

        nav ul li.dropdown-parent:hover > ul.submenu {
            display: block;
        }

        nav ul .submenu li {
            padding: 0;
        }

        nav ul .submenu li a {
            color: #3a4065 !important;
            display: block;
            padding: 0.75rem 1rem;
            border-radius: 4px;
        }

        nav ul .submenu li a:hover {
            background: rgba(58, 64, 101, 0.1);
            color: #4e54c8 !important;
        }

        nav ul.dropdown > li {
            display: block;
        }
        
        /* Style for the 'Back' button */
        .back-button {
            display: block;
            margin: 20px auto; /* Centering the button */
            padding: 10px 40px; /* Adjust padding for better size */
            background-color: #007bff;/* Blue color */
            color: white;
            text-align: center;
            font-size: 16px;
            border-radius: 5px;
            cursor: pointer;
            text-decoration: none;
            width: auto; /* Let the button width adjust to content */
            max-width: 150px; /* Adjust max-width to make the button smaller */
        }

        .back-button:hover {
            background-color:  #0056b3;/* Darker blue on hover */
        }

    </style>
</head>
<body class="dashboard-container">
    <div class="container">
        <!-- Header -->
        <header>
            <a href="pentadbir_dashboard.php"><span>Dashboard Pentadbir</span></a>
            <a href="logout.php">Logout</a>
        </header>

        <!-- Navigation -->
                <nav>
                <ul>
                    <li class="dropdown-parent">
                    <a href="#">Senarai Pendaftar <span class="dropdown-icon">&#x25BC;</span></a>
                    <ul class="dropdown">
                        <li class="dropdown-parent">
                        <a href="#">Pengesahan <span class="dropdown-icon">&#x25BC;</span></a>
                        <ul class="submenu">
                            <li><a href="pengesahan_pelajar.php">Pelajar</a></li>
                            <li><a href="pengesahan_pendidik.php">Pendidik</a></li>
                        </ul>
                        </li>
                        <li><a href="admin_pelajar.php">Pelajar</a></li>
                        <li><a href="admin_pendidik.php">Pendidik</a></li>
                    </ul>
                </li>
                <li><a href="yuranPentadbir.php">Yuran</a></li>
                <li><a href="admin_jadual.php">Jadual Pelajar</a></li>
                <li><a href="pentadbirRPA.php">RPA</a></li>
                <li><a href="pentadbirLaporan.php">Laporan</a></li>
            </ul>
        </nav>
        <main>
          <div class="profile-container">
        <h1>Maklumat Ibu Bapa</h1>
        <table>
            <tr><th>No MyKad Bapa</th><td><?= htmlspecialchars($ibu_bapa_data['ic_bapa']) ?></td></tr>
            <tr><th>Nama Bapa</th><td><?= htmlspecialchars($ibu_bapa_data['nama_bapa']) ?></td></tr>
            <tr><th>Pekerjaan Bapa</th><td><?= htmlspecialchars($ibu_bapa_data['pekerjaan_bapa']) ?></td></tr>
            <tr><th>Pendapatan Bapa</th><td><?= htmlspecialchars($ibu_bapa_data['pendapatan_bapa']) ?></td></tr>
            <tr><th>Email Bapa</th><td><?= htmlspecialchars($ibu_bapa_data['email_bapa']) ?></td></tr>
            <tr><th>No Telefon Bapa</th><td><?= htmlspecialchars($ibu_bapa_data['no_bapa']) ?></td></tr>

            <tr><th>No MyKad Ibu</th><td><?= htmlspecialchars($ibu_bapa_data['ic_ibu']) ?></td></tr>
            <tr><th>Nama Ibu</th><td><?= htmlspecialchars($ibu_bapa_data['nama_ibu']) ?></td></tr>
            <tr><th>Pekerjaan Ibu</th><td><?= htmlspecialchars($ibu_bapa_data['pekerjaan_ibu']) ?></td></tr>
            <tr><th>Pendapatan Ibu</th><td><?= htmlspecialchars($ibu_bapa_data['pendapatan_ibu']) ?></td></tr>
            <tr><th>Email Ibu</th><td><?= htmlspecialchars($ibu_bapa_data['EmailIbu']) ?></td></tr>
            <tr><th>No Telefon Ibu</th><td><?= htmlspecialchars($ibu_bapa_data['no_ibu']) ?></td></tr>
        </table>

        <?php if (count($anak_data) > 0): ?>
            <?php foreach ($anak_data as $data): ?>
                <h1>Maklumat Pelajar</h1>
                <div class="student-photo">
                    <?php if (!empty($data['gambar_pelajar']) && file_exists("uploads/" . $data['gambar_pelajar'])): ?>
                        <img src="uploads/<?= htmlspecialchars($data['gambar_pelajar']) ?>" alt="Gambar Pelajar">
                    <?php else: ?>
                        <img src="uploads/default.png" alt="Default Photo">
                    <?php endif; ?>
                </div>

                <table>
                    <tr><th>No MyKid Pelajar</th><td><?= htmlspecialchars($data['ic_pelajar']) ?></td></tr>
                    <tr><th>Nama Pelajar</th><td><?= htmlspecialchars($data['nama_pelajar']) ?></td></tr>
                    <tr><th>Tahun Pengajian</th><td><?= htmlspecialchars($data['tahun_pengajian']) ?></td></tr>
                    <tr><th>Jantina</th><td><?= htmlspecialchars($data['jantina']) ?></td></tr>
                    <tr><th>Alamat Semasa</th><td><?= htmlspecialchars($data['alamat_semasa']) ?></td></tr>
                    <tr><th>Umur</th><td><?= htmlspecialchars($data['umur']) ?></td></tr>
                    <tr><th>Alahan</th><td><?= htmlspecialchars($data['Alahan']) ?></td></tr>
                    <tr><th>Sijil Lahir</th><td>
                        <?php if (!empty($data['sijilLahir_pelajar']) && file_exists("uploads/" . $data['sijilLahir_pelajar'])): ?>
                            <a href="uploads/<?= htmlspecialchars($data['sijilLahir_pelajar']) ?>" target="_blank">Lihat Sijil Lahir</a>
                        <?php else: ?>
                            Tiada sijil lahir tersedia
                        <?php endif; ?>
                    </td></tr>
                </table>
            <?php endforeach; ?>
        <?php else: ?>
            <p>Tiada maklumat anak yang didaftarkan.</p>
        <?php endif; ?>

        <a href="javascript:history.back()" class="back-button">Kembali</a>
    </div>
    </main>
</body>
</html>