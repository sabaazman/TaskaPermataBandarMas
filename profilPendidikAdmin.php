<?php
session_start();
require_once 'config.php';

if (!isset($_SESSION['id_pentadbir'])) {
    header("Location: login.php");
    exit();
}

if (!isset($_GET['ic'])) {
    die("IC tidak diberikan.");
}

$ic_pendidik = $_GET['ic'];

$sql = "SELECT * FROM pendidik WHERE ic_pendidik = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("s", $ic_pendidik);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    die("Rekod tidak dijumpai.");
}

$row = $result->fetch_assoc();
$stmt->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Profil Pendidik</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="css/dashboard.css">
    <style>
        /* Profile container styling */
        .profile-container {
            width: 80%;
            margin: 20px auto;
            background: #fff;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.05);
        }

        .profile-container h1 {
            font-weight: bold;
            text-align: center;
            margin-bottom: 20px;
            color: #3a4065;
        }

       /* Table styling adjustments */
        table {
            width: 50%;
            border-collapse: collapse;
            margin: 1rem 0;
            background: white;
            border-radius: 8px;
            overflow: hidden;
            table-layout: fixed; /* Add this for consistent column widths */
        }

        table th,
        table td {
            padding: 0.75rem 1rem; /* Reduced padding */
            text-align: left;
            border-bottom: 1px solid #eee;
            vertical-align: top; /* Better alignment for multi-line content */
        }

        table th {
            background: rgba(58, 64, 101, 0.05);
            font-weight: 600;
            color: #3a4065;
            width: 30%; /* Set width for headers */
            min-width: 150px; /* Minimum width to prevent squeezing */
        }

        table td {
            width: 70%; /* Set width for data cells */
            word-break: break-word; /* Handle long content gracefully */
        }

        .crud-buttons {
            display: flex;
            justify-content: center; /* Center the button horizontally */
            align-items: center; /* Center the button vertically if needed */
            margin-top: 20px;
        }

        .crud-buttons button {
            padding: 10px 20px;
            background:rgb(61, 61, 61);
            color: white;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            transition: all 0.3s ease;
        }

        .crud-buttons button:hover {
            background:rgb(141, 142, 145);
        }

        .back-button {
            display: block;
            margin: 20px auto;
            padding: 10px 40px;
            background-color:#007bff;
            color: white;
            text-align: center;
            font-size: 16px;
            border-radius: 5px;
            cursor: pointer;
            text-decoration: none;
            width: auto;
            max-width: 150px;
        }

        .back-button:hover {
            background-color: #0056b3;
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
    </style>
</head>
<body class="dashboard-container">
    <div class="container">
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
                            <li><a href="sub_option2.php">Pendidik</a></li>
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

        <div class="profile-container">
            <h1>Maklumat Pendidik</h1>

        <div class="profile-card">
            <table>
                <tr><th>Nama</th><td><?= htmlspecialchars($row['nama_pendidik']) ?></td></tr>
                <tr><th>IC</th><td><?= htmlspecialchars($row['ic_pendidik']) ?></td></tr>
                <tr><th>Email</th><td><?= htmlspecialchars($row['email_pendidik']) ?></td></tr>
                <tr><th>No Telefon</th><td><?= htmlspecialchars($row['no_pendidik']) ?></td></tr>
                <tr><th>Umur</th><td><?= htmlspecialchars($row['umur']) ?></td></tr>
                <tr><th>Alamat</th><td><?= htmlspecialchars($row['alamat_pendidik']) ?></td></tr>
                <tr><th>Sijil Pengajian</th>
                    <td>
                        <?php if (!empty($row['sijil_pengajian']) && file_exists($row['sijil_pengajian'])): ?>
                            <a class="btn-view" href="<?= htmlspecialchars($row['sijil_pengajian']) ?>" target="_blank">Lihat</a>
                        <?php else: ?>
                            Tiada sijil tersedia
                        <?php endif; ?>
                    </td>
                </tr>
                <tr><th>Kursus Kanak-kanak</th>
                    <td>
                        <?php if (!empty($row['kursus_kanak_kanak']) && file_exists($row['kursus_kanak_kanak'])): ?>
                            <a class="btn-view" href="<?= htmlspecialchars($row['kursus_kanak_kanak']) ?>" target="_blank">Lihat</a>
                        <?php else: ?>
                            Tiada fail tersedia
                        <?php endif; ?>
                    </td>
                </tr>
            </table>
            <a href="javascript:history.back()" class="back-button">Kembali</a>
        </>
    </div>
</body>
</html>
