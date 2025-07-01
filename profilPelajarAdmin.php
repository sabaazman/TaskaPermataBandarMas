<?php
session_start();
require_once 'config.php'; // Sambungan ke database

// Semak jika pentadbir sedang log masuk
if (!isset($_SESSION['id_pentadbir'])) {
    header("Location: login.php");
    exit();
}

// Semak jika parameter 'ic' ada dalam URL
if (isset($_GET['ic'])) {
    $ic_pelajar = $_GET['ic'];

    // Query untuk mendapatkan maklumat pelajar berdasarkan ic_pelajar
    $query = "
    SELECT 
        pelajar.ic_pelajar, 
        pelajar.nama_pelajar, 
        pelajar.jantina, 
        pelajar.alamat_semasa, 
        pelajar.umur, 
        pelajar.gambar_pelajar,
        pelajar.Alahan,
        pelajar.tahun_pengajian,
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
        ibubapa.no_ibu, 
       ibubapa.EmailIbu, 
        ibubapa.pengesahan,
        pelajar.sijilLahir_pelajar
    FROM pelajar
    INNER JOIN ibubapa ON pelajar.ibubapa_id = ibubapa.id_ibubapa
    WHERE pelajar.ic_pelajar = ?";

    $stmt = $conn->prepare($query);
    if (!$stmt) {
        die("Prepare statement failed: " . $conn->error);
    }

    // Bind parameter: 's' for string (ic_pelajar)
    $stmt->bind_param("s", $ic_pelajar);
    $stmt->execute();
    $result = $stmt->get_result();

    // Ambil data pelajar
    $data = $result->fetch_assoc();

    if (!$data) {
        die("Pelajar tidak dijumpai.");
    }

    $stmt->close();
} else {
    die("IC pelajar tidak disediakan.");
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
            background-color:  #0056b3; /* Darker blue on hover */
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
            <h1>Maklumat Pelajar</h1>
            <div class="student-photo">
                <?php if (!empty($data['gambar_pelajar']) && file_exists("uploads/" . $data['gambar_pelajar'])): ?>
                    <img src="uploads/<?php echo htmlspecialchars($data['gambar_pelajar']); ?>" alt="Gambar Pelajar">
                <?php else: ?>
                    <img src="uploads/default.png" alt="Default Photo">
                <?php endif; ?>
            </div>

            <h3>Maklumat Pelajar</h3>
            <table>
                <tr>
                    <th>No MyKid Pelajar</th>
                    <td><?php echo htmlspecialchars($data['ic_pelajar']); ?></td>
                </tr>
                <tr>
                    <th>Nama Pelajar</th>
                    <td><?php echo htmlspecialchars($data['nama_pelajar']); ?></td>
                </tr>
                <tr>
                    <th>Tahun Pengajian</th>
                    <td><?php echo htmlspecialchars($data['tahun_pengajian']); ?></td>
                </tr>
                <tr>
                    <th>Jantina</th>
                    <td><?php echo htmlspecialchars($data['jantina']); ?></td>
                </tr>
                <tr>
                    <th>Alamat Semasa</th>
                    <td><?php echo htmlspecialchars($data['alamat_semasa']); ?></td>
                </tr>
                <tr>
                    <th>Umur</th>
                    <td><?php echo htmlspecialchars($data['umur']); ?></td>
                </tr>
                <tr>
                    <th>Alahan</th>
                    <td><?php echo htmlspecialchars($data['Alahan']); ?></td>
                </tr>
                <tr>
                    <th>Sijil Lahir</th>
                    <td>
                        <?php if (!empty($data['sijilLahir_pelajar']) && file_exists("uploads/" . $data['sijilLahir_pelajar'])): ?>
                            <a href="uploads/<?php echo htmlspecialchars($data['sijilLahir_pelajar']); ?>" target="_blank">Lihat Sijil Lahir</a>
                        <?php else: ?>
                            Tiada sijil lahir tersedia
                        <?php endif; ?>
                    </td>
                </tr>
            </table>

            <h3>Maklumat Ibu Bapa</h3>
            <table>
                <tr>
                    <th>No MyKad Bapa</th>
                    <td><?php echo htmlspecialchars($data['ic_bapa']); ?></td>
                </tr>
                <tr>
                    <th>Nama Bapa</th>
                    <td><?php echo htmlspecialchars($data['nama_bapa']); ?></td>
                </tr>
                <tr>
                    <th>Pekerjaan Bapa</th>
                    <td><?php echo htmlspecialchars($data['pekerjaan_bapa']); ?></td>
                </tr>
                <tr>
                    <th>Pendapatan Bapa</th>
                    <td><?php echo htmlspecialchars($data['pendapatan_bapa']); ?></td>
                </tr>
                <tr>
                    <th>Email Bapa</th>
                    <td><?php echo htmlspecialchars($data['email_bapa']); ?></td>
                </tr>
                <tr>
                    <th>No Telefon Bapa</th>
                    <td><?php echo htmlspecialchars($data['no_bapa']); ?></td>
                </tr>
                <tr>
                    <th>No MyKad Ibu</th>
                    <td><?php echo htmlspecialchars($data['ic_ibu']); ?></td>
                </tr>
                <tr>
                    <th>Nama Ibu</th>
                    <td><?php echo htmlspecialchars($data['nama_ibu']); ?></td>
                </tr>
                <tr>
                    <th>Pekerjaan Ibu</th>
                    <td><?php echo htmlspecialchars($data['pekerjaan_ibu']); ?></td>
                </tr>
                <tr>
                    <th>Pendapatan Ibu</th>
                    <td><?php echo htmlspecialchars($data['pendapatan_ibu']); ?></td>
                </tr>
                <tr>
                    <th>Email Ibu</th>
                    <td><?php echo htmlspecialchars($data['EmailIbu']); ?></td>
                </tr>
                <tr>
                    <th>No Telefon Ibu</th>
                    <td><?php echo htmlspecialchars($data['no_ibu']); ?></td>
                </tr>
            </table>
            <a href="javascript:history.back()" class="back-button">Kembali</a>
        </div>
        </main>
    </div>
    
</body>
</html>
