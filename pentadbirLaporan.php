<?php
include 'config.php';
session_start();

// Check if pentadbir is logged in
if (!isset($_SESSION['id_pentadbir'])) {
    header("Location: login.php");
    exit();
}

// Fetch the list of educators
$sql = "SELECT id_pendidik, nama_pendidik FROM pendidik WHERE pengesahan = 1";
$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pentadbir Laporan</title>
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
            background-color: #f9f9f9;
            margin: 20px;
        }
        h2 {
            font-size: 24px;
            text-align: center;
            margin-bottom: 20px;
            color: #333;
        }
        table {
            width: 80%;
            margin: 20px auto;
            border-collapse: collapse;
            font-size: 16px;
            border: 1px solid #ccc;
            background-color: white;
        }
        th, td {
            padding: 12px;
            text-align: center;
            border: 1px solid #ddd;
        }
        th {
            background-color: #3a4065;
            color: white;
        }
        tr:nth-child(even) {
            background-color: #f2f2f2;
        }
        tr:hover {
            background-color: #f1f1f1;
        }
        a {
            text-decoration: none;
            color:rgb(0, 0, 0);
        }
        a:hover {
            text-decoration: underline;
        }
                /* Pastikan link tajuk RPA tiada underline walau hover */
td a {
    text-decoration: none !important;
    color: rgb(0, 0, 0);
}

td a:hover {
    text-decoration: none !important;
    color: rgb(0, 0, 0);
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

        <h2 style="text-align: center; margin: 20px 0;">Senarai Pendidik</h2>

    <table>
        <thead>
            <tr>
                <th>No</th>
                <th>Nama Pendidik</th>
            </tr>
        </thead>
        <tbody>
            <?php
            if ($result->num_rows > 0) {
                $no = 1;
                while ($row = $result->fetch_assoc()) {
                    echo "<tr>";
                    echo "<td style='text-align: center;'>" . $no++ . "</td>";
                    echo "<td style='text-align: center;'><a href='pentadbir_laporan.php?id_pendidik=" . $row['id_pendidik'] . "'>" . htmlspecialchars($row['nama_pendidik']) . "</a></td>";
                    echo "</tr>";
                }
            } else {
                echo "<tr><td colspan='2'>Tiada pendidik yang dijumpai.</td></tr>";
            }
            ?>
        </tbody>
    </table>
    </main>
</html>

<?php
$conn->close();
?>