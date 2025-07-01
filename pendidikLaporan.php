<?php
include 'config.php';
session_start();

if (!isset($_SESSION['username'])) {
    header("Location: login.php");
    exit();
}

$ic_pelajar = isset($_GET['id_pelajar']) ? $_GET['id_pelajar'] : null;
if (!$ic_pelajar) {
    die("IC Pelajar tidak ditemukan!");
}

// Fetch student name for heading
$stmt = $conn->prepare("SELECT nama_pelajar FROM pelajar WHERE ic_pelajar = ?");
if (!$stmt) {
    die("Kesalahan pada pernyataan SQL: " . $conn->error);
}
$stmt->bind_param("s", $ic_pelajar);
$stmt->execute();
$result = $stmt->get_result();
$pelajar = $result->fetch_assoc();
$stmt->close();
if (!$pelajar) {
    die("Pelajar tidak ditemukan.");
}

// Fetch all laporan bulan for this pelajar
$stmt = $conn->prepare("SELECT bulan, tarikh_laporan FROM laporan WHERE ic_pelajar = ? ORDER BY tarikh_laporan DESC");
if (!$stmt) {
    die("Kesalahan pada pernyataan SQL: " . $conn->error);
}
$stmt->bind_param("s", $ic_pelajar);
$stmt->execute();
$result = $stmt->get_result();

?>
<!DOCTYPE html>
<html lang="ms">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Senarai Laporan Pelajar</title>
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
            color: white;
            text-decoration: none;
            font-weight: 600;
            transition: all 0.3s ease;
        }

        header a:hover {
            opacity: 0.9;
            transform: translateY(-1px);
        }

        /* Navigation styling */
        nav {
            background: #ffffff;
            border-radius: 8px;
            margin: 1rem 0;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.05);
        }

        nav ul {
            background: transparent !important;
            padding: 0 !important;
            margin: 0;
            display: flex;
            gap: 0.5rem;
            list-style-type: none;
        }

        nav ul li a {
            color: #3a4065 !important;
            padding: 1rem 1.5rem;
            transition: all 0.3s ease;
            border-radius: 6px;
            text-decoration: none;
            display: flex;
            align-items: center;
        }

        nav ul li a:hover {
            background: rgba(58, 64, 101, 0.1);
            transform: translateY(-1px);
        }

        nav ul li {
            position: relative;
        }

        .dropdown-parent > a {
            display: flex;
            align-items: center;
            gap: 5px;
        }

        .dropdown {
            position: absolute;
            top: 100%;
            left: 0;
            background-color: #ffffff !important;
            min-width: 200px;
            border-radius: 8px;
            box-shadow: 0 8px 24px rgba(0, 0, 0, 0.15);
            border: 1px solid rgba(58, 64, 101, 0.1);
            padding: 0.5rem;
            display: none;
            z-index: 1000;
        }

        .dropdown-parent:hover .dropdown {
            display: block;
        }

        .dropdown li {
            width: 100%;
            margin: 0;
        }

        .dropdown li a {
            padding: 0.75rem 1rem;
            display: block;
            color: #3a4065;
            text-decoration: none;
            border-radius: 4px;
            margin: 2px;
        }

        h2 {
            font-size: 24px;
            text-align: center;
            margin-bottom: 10px;
        }

        /* Override dashboard.css table styles for this specific table */
        table.custom-table {
            width: 800px !important;
            max-width: 900px !important;
            margin: 20px auto !important;
            border-collapse: collapse;
            font-size: 16px;
            table-layout: auto !important;
            text-align: center;
            box-shadow: 0 2px 8px rgba(0,0,0,0.1);
            border-radius: 8px;
            overflow: hidden;
            background-color: white;
        }
        table.custom-table thead tr {
            background-color: #3a4065;
            color: #ffffff;
            font-weight: bold;
            text-align: center;
        }
        table.custom-table th,
        table.custom-table td {
            padding: 12px 15px;
            border: 1px solid #ddd;
            text-align: center;
        }
        table.custom-table tbody tr:nth-child(even) {
            background-color: #f3f3f3;
        }
        table.custom-table tbody tr:hover {
            background-color: #f1f1f1;
        }
        .col-no {
            width: 50px;
            text-align: center;
        }
        a.bulan-link {
            color: #007bff;
            text-decoration: none;
            font-weight: 600;
        }
        a.bulan-link:hover {
            text-decoration: underline;
        }
    </style>
</head>
<body class="dashboard-container">
    <div class="container">
        <!-- Header -->
        <header>
            <a href="pendidik_dashboard.php"><span>Dashboard Pendidik</span></a>
            <a href="logout.php">Logout</a>
        </header>

        <!-- Navigation -->
        <nav>
            <ul>
                <li class="dropdown-parent">
                    <a href="#">Profil <span class="dropdown-icon">&#x25BC;</span></a>
                    <ul class="dropdown">
                        <li><a href="profilPendidik.php">Pendidik</a></li>
                        <li><a href="pendidikPelajar.php">Pelajar</a></li>
                    </ul>
                </li>
                <li><a href="yuranPendidik.php">Yuran</a></li>
                <li><a href="display_jadualPendidik.php">Jadual Pelajar</a></li>
                <li><a href="rpa.php">RPA</a></li>
                <li><a href="laporan.php">Laporan</a></li>
            </ul>
        </nav>

        <!-- Main content -->
        <main>
        <h2 style="text-align: center; margin: 20px 0;">Senarai Laporan Pelajar</h2>

            <?php if ($result->num_rows > 0): ?>
                <table class="custom-table">
                    <thead>
                        <tr>
                            <th class="col-no">No</th>
                            <th>Bulan</th>
                            <th>Tarikh Penilaian</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        $no = 1;
                        while ($row = $result->fetch_assoc()):
                            $bulan = htmlspecialchars($row['bulan']);
                            $tarikh_laporan = htmlspecialchars($row['tarikh_laporan']);
                            $link = "pendidik_viewLaporan.php?id_pelajar=" . urlencode($ic_pelajar) . "&bulan=" . urlencode($row['bulan']);
                        ?>
                        <tr>
                            <td><?php echo $no++; ?></td>
                            <td><a href="<?php echo $link; ?>" style="color: black; text-decoration: none;"><?php echo $bulan; ?></a></td>
                            <td><?php echo $tarikh_laporan; ?></td>
                        </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
                <div style="text-align: center; margin-top: 20px;">
                <a href="pendidikGraph.php?id_pelajar=<?php echo urlencode($ic_pelajar); ?>"
                    style="background-color: #28a745; color: white; padding: 10px 20px; border-radius: 6px; text-decoration: none; font-weight: 600;">
                    Graf
                </a>
                </div>
            <?php else: ?>
                <p>Tiada laporan ditemukan untuk pelajar ini.</p>
            <?php endif; ?>
        </main>
    </div>
</body>
</html>

<?php $conn->close(); ?>