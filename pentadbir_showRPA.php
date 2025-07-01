<?php
include 'config.php';
session_start();

// Check if pentadbir is logged in
if (!isset($_SESSION['id_pentadbir'])) {
    header("Location: login.php");
    exit();
}

if (!isset($_GET['id'])) {
    echo "Invalid RPA ID.";
    exit();
}

$id_RPA = $_GET['id'];

// Fetch RPA data
$sql = "SELECT * FROM rpa WHERE id_RPA = '$id_RPA'";
$result = $conn->query($sql);

if ($result->num_rows !== 1) {
    echo "RPA not found.";
    exit();
}

$rpa = $result->fetch_assoc();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pentadbir - RPA</title>
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
        body {
            font-family: Arial, sans-serif;
            margin: 20px;
            background-color: #f9f9f9;
        }
        h2 {
            font-size: 24px;
            text-align: center;
            margin-bottom: 10px;
        }
        form {
            background-color: #fff;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
            max-width: 800px;
            margin: 0 auto;
        }
        label {
            font-weight: bold;
            margin-bottom: 5px;
            display: block;
        }
        textarea, input, select {
            width: 98%;
            padding: 10px;
            margin-bottom: 15px;
            border: 1px solid #ccc;
            border-radius: 4px;
            font-size: 14px;
            background-color: #f5f5f5;
            resize: vertical;
        }
        .btn-kembali {
            background-color: #007bff;
            color: #fff;
            text-decoration: none;
            padding: 10px 20px;
            border-radius: 4px;
            font-size: 16px;
            display: inline-block;
            text-align: center;
        }
        .btn-kembali:hover {
            background-color: #0056b3;
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
                    <li><a href="#">Yuran</a></li>
                    <li><a href="admin_jadual.php">Jadual Pelajar</a></li>
                    <li><a href="pentadbirRPA.php">RPA</a></li>
                    <li><a href="pentadbirLaporan.php">Laporan</a></li>
                </ul>
            </nav>
        <h2>Maklumat Lengkap RPA</h2>

        <form>
            <label>Minggu:</label>
            <input type="text" value="<?php echo $rpa['minggu']; ?>" readonly>

            <label>Tarikh:</label>
            <input type="date" value="<?php echo $rpa['tarikh']; ?>" readonly>

            <label>Masa:</label>
            <input type="text" value="<?php echo $rpa['masa']; ?>" readonly>

            <label>Hari:</label>
            <input type="text" value="<?php echo $rpa['hari']; ?>" readonly>

            <label>Tajuk:</label>
            <textarea readonly><?php echo $rpa['tajuk']; ?></textarea>

            <label>Pengetahuan:</label>
            <textarea readonly><?php echo $rpa['pengetahuan']; ?></textarea>

            <label>Objektif:</label>
            <textarea readonly><?php echo $rpa['objektif']; ?></textarea>

            <label>Bidang:</label>
            <textarea readonly><?php echo $rpa['bidang']; ?></textarea>

            <label>Bahan:</label>
            <textarea readonly><?php echo $rpa['bahan']; ?></textarea>

            <label>Tempat:</label>
            <textarea readonly><?php echo $rpa['tempat']; ?></textarea>

            <label>Rancangan:</label>
            <textarea readonly><?php echo $rpa['rancangan']; ?></textarea>

            <label>Hasil:</label>
            <textarea readonly><?php echo $rpa['hasil']; ?></textarea>

            <a href="pentadbir_displayRPA.php?id_pendidik=<?php echo $rpa['id_pendidik']; ?>" class="btn-kembali">Kembali</a>
        </form>

    </div>
</body>
</html>
