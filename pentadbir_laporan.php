<?php
include 'config.php';
session_start();

// Check if pentadbir is logged in
if (!isset($_SESSION['id_pentadbir'])) {
    header("Location: login.php");
    exit();
}

// Get the educator's ID from the URL
$id_pendidik = isset($_GET['id_pendidik']) ? $_GET['id_pendidik'] : null;
if (!$id_pendidik) {
    die("ID Pendidik tidak ditemukan!");
}

// Fetch unique students associated with the educator
$sql = "SELECT DISTINCT pelajar.ic_pelajar, pelajar.nama_pelajar
        FROM pelajar
        INNER JOIN laporan ON pelajar.ic_pelajar = laporan.ic_pelajar
        WHERE laporan.id_pendidik = ?";
$stmt = $conn->prepare($sql);
if (!$stmt) {
    die("Kesalahan pada pernyataan SQL: " . $conn->error);
}
$stmt->bind_param("i", $id_pendidik);
$stmt->execute();
$result = $stmt->get_result();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pentadbir - Laporan</title>
    <link rel="stylesheet" href="css/dashboard.css">
    <style>
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
        table {
            width: 80%;
            border-collapse: collapse;
            margin: 20px auto; /* This centers the table horizontally */
            font-size: 18px;
            text-align: center;
        }
        table thead tr {
            background-color: #3a4065;
            color: #ffffff;
            text-align: center;
            font-weight: bold;
        }
        table th, table td {
            padding: 12px 15px;
            border: 1px solid #ddd;
        }
        table tbody tr:nth-child(even) {
            background-color: #f3f3f3;
        }
        table tbody tr:hover {
            background-color: #f1f1f1;
        }
        table td:last-child {
            width: 200px; /* Adjust to fit buttons nicely */
        }
        td a {
            display: inline-block; /* Ensure buttons are inline-block */
            margin: 0; /* Remove default margin */
            text-decoration: none; /* Remove underline from links */
        }
        button {
            padding: 8px 16px;
            font-size: 14px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            color: #fff;
        }
        a button.btn-laporan {
            background-color: #28a745 !important;
        }

        a button.btn-view {
            background-color: #007bff !important;
        }
        .col-no {
            width: 50px; /* or 40px, adjust as needed */
            text-align: center;
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
        <h2 style="text-align: center; margin: 20px 0;">Senarai Pelajar</h2>
        <table>
            <thead>
                <tr>
                    <th class="col-no">No</th>
                    <th>Nama Pelajar</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                <?php if ($result && $result->num_rows > 0): ?>
                    <?php 
                    $no = 1;
                    while ($row = $result->fetch_assoc()): ?>
                        <tr>
                            <td style='text-align: center;' class="col-no"><?php echo $no++; ?></td>
                            <td><?php echo htmlspecialchars($row['nama_pelajar']); ?></td>
                            <td>
                                <div style="text-align: center;">
                                <a href="pentadbir_viewLaporan.php?id_pelajar=<?php echo $row['ic_pelajar']; ?>">
                                    <button style="background-color: #007bff; color: white; padding: 10px 20px; border: none; border-radius: 5px; font-size: 16px; cursor: pointer; display: inline-block; margin: auto;">View</button>
                                </a>

                                </div>
                            </td>
                        </tr>
                    <?php endwhile; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="2">Tiada laporan pelajar disediakan.</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
        </main>
    </body>
    </html>

<?php
$conn->close();
?>