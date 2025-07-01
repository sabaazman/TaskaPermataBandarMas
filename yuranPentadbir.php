<?php
include 'config.php';
session_start();

// Check if pentadbir is logged in
if (!isset($_SESSION['id_pentadbir'])) {
    header("Location: login.php");
    exit();
}

// Pilih bulan (1-12), default bulan semasa
$selected_month = isset($_GET['month']) ? intval($_GET['month']) : date('n');
// Month names in Malay
$month_names = [
    1 => 'Januari',
    2 => 'Februari',
    3 => 'Mac',
    4 => 'April',
    5 => 'Mei',
    6 => 'Jun',
    7 => 'Julai',
    8 => 'Ogos',
    9 => 'September',
    10 => 'Oktober',
    11 => 'November',
    12 => 'Disember'
];
// Query Yuran Pendaftaran:
// Senaraikan semua pelajar yang approved sahaja (p.pengesahan=1 dan i.pengesahan=1)
// LEFT JOIN ke YURAN untuk bulan & jenis_yuran 'Pendaftaran'
$query_pendaftaran = "
    SELECT 
        p.ic_pelajar,
        p.nama_pelajar,
        (SELECT y1.jumlah FROM yuran y1 
         WHERE y1.ic_pelajar = p.ic_pelajar 
         AND y1.jenis_yuran = 'Pendaftaran'
         AND y1.status = 'success' 
         LIMIT 1) as jumlah,
        (SELECT y2.bulan FROM yuran y2 
         WHERE y2.ic_pelajar = p.ic_pelajar 
         AND y2.jenis_yuran = 'Pendaftaran'
         AND y2.status = 'success'
         LIMIT 1) as bulan,
        (SELECT y3.tarikh FROM yuran y3 
         WHERE y3.ic_pelajar = p.ic_pelajar 
         AND y3.jenis_yuran = 'Pendaftaran'
         AND y3.status = 'success'
         LIMIT 1) as tarikh,
        (SELECT y4.kaedah FROM yuran y4 
         WHERE y4.ic_pelajar = p.ic_pelajar 
         AND y4.jenis_yuran = 'Pendaftaran'
         AND y4.status = 'success'
         LIMIT 1) as kaedah,
        CASE 
            WHEN EXISTS (
                SELECT 1 FROM yuran y5 
                WHERE y5.ic_pelajar = p.ic_pelajar 
                AND y5.jenis_yuran = 'Pendaftaran'
                AND y5.status = 'success'
            ) THEN 'success'
            ELSE 'unpaid'
        END as status
    FROM pelajar p
    JOIN ibubapa i ON p.ibubapa_id = i.id_ibubapa
    WHERE p.pengesahan = 1
      AND i.pengesahan = 1
      AND (NOT EXISTS (
            SELECT 1 FROM yuran y6
            WHERE y6.ic_pelajar = p.ic_pelajar
            AND y6.jenis_yuran = 'Pendaftaran'
            AND y6.status = 'success'
          )
          OR ? >= (
            SELECT MIN(y7.bulan)
            FROM yuran y7
            WHERE y7.ic_pelajar = p.ic_pelajar
            AND y7.jenis_yuran = 'Pendaftaran'
            AND y7.status = 'success'
          )
      )
    ORDER BY p.nama_pelajar ASC
";

$stmt_p = $conn->prepare($query_pendaftaran);
if (!$stmt_p) {
    die("Prepare statement failed for pendaftaran: " . $conn->error);
}
$stmt_p->bind_param("i", $selected_month);
$stmt_p->execute();
$result_pendaftaran = $stmt_p->get_result();

// Query Yuran Bulanan:
// Senaraikan semua pelajar approved sahaja
// LEFT JOIN ke YURAN untuk bulan & jenis_yuran 'Bulanan'
$query_bulanan = "
    SELECT 
        p.ic_pelajar,
        p.nama_pelajar,
        y.jumlah,
        y.bulan,
        y.tarikh,
        y.kaedah,
        y.status
    FROM PELAJAR p
    JOIN ibubapa i ON p.ibubapa_id = i.id_ibubapa
    LEFT JOIN yuran y ON p.ic_pelajar = y.ic_pelajar 
        AND y.bulan = ? 
        AND y.jenis_yuran = 'Bulanan'
    WHERE p.pengesahan = 1
      AND i.pengesahan = 1
    ORDER BY p.nama_pelajar ASC
";

$stmt_b = $conn->prepare($query_bulanan);
if (!$stmt_b) {
    die("Prepare statement failed for bulanan: " . $conn->error);
}
$stmt_b->bind_param("i", $selected_month);
$stmt_b->execute();
$result_bulanan = $stmt_b->get_result();

function status_label($status) {
    if ($status === 'success') {
        return '<span style="color:green;font-weight:bold;">Paid</span>';
    } elseif ($status === 'pending') {
        return '<span style="color:orange;font-weight:bold;">Pending</span>';
    } else {
        return '<span style="color:red;font-weight:bold;">Unpaid</span>';
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pentadbir Dashboard - Yuran Pelajar</title>
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
    .container {
        max-width: 1200px;
        margin: 20px auto;
        padding: 0 20px;
    }
    h2 {
        text-align: center;
        margin-bottom: 10px;
    }
    table {
        width: 100%;
        border-collapse: collapse;
        margin-bottom: 40px;
        box-shadow: 0 2px 10px rgba(0,0,0,0.05);
        border-radius: 8px;
        overflow: hidden;
    }
    th, td {
        padding: 15px;
        text-align: center;
        border: 1px solid #ddd;
        font-size: 16px;
    }
    th {
        background-color: #3a4065;
        color: white;
    }
    tbody tr:nth-child(even) {
        background-color: #f9f9f9;
    }
    tbody tr:hover {
        background-color: #f1f1f1;
    }
    .month-selection {
        text-align: center;
        margin-bottom: 20px;
    }
    .month-selection select {
        padding: 8px 15px;
        font-size: 16px;
        border: 2px solid #3a4065;
        border-radius: 5px;
        cursor: pointer;
        outline: none;
    }
    .month-selection select:hover {
        border-color: #2c3154;
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
        <h1 style="text-align:center;">Maklumat Yuran Pelajar</h1>

        <div class="month-selection">
    <form method="GET" action="yuranPentadbir.php">
        <label for="month">Pilih Bulan: </label>
        <select name="month" id="month" onchange="this.form.submit()">
            <?php foreach ($month_names as $month_num => $month_name): ?>
                <option value="<?php echo $month_num; ?>" <?php if ($month_num == $selected_month) echo 'selected'; ?>>
                    <?php echo $month_name; ?>
                </option>
            <?php endforeach; ?>
        </select>
    </form>
</div>

<h2 style="text-align: center; margin: 20px 0;">Yuran Pendaftaran</h2>
<table>
    <thead>
        <tr>
            <th>No</th>
            <th>Nama Pelajar</th>
            <th>Jumlah (RM)</th>
            <th>Tarikh Bayaran</th>
            <th>Kaedah</th>
            <th>Status</th>
        </tr>
    </thead>
    <tbody>
    <?php
    $no = 1;
    if ($result_pendaftaran->num_rows > 0):
        while ($row = $result_pendaftaran->fetch_assoc()):
            // Jika tiada jumlah, tarikh, kaedah, status maka letak '-'
            $jumlah = $row['jumlah'] !== null ? number_format($row['jumlah'], 2) : '-';
            $tarikh = $row['tarikh'] !== null ? htmlspecialchars($row['tarikh']) : '-';
            $kaedah = $row['kaedah'] !== null ? htmlspecialchars($row['kaedah']) : '-';
            $status = $row['status'] !== null ? $row['status'] : 'unpaid';
    ?>
    <tr>
        <td style="text-align: center;"><?php echo $no++; ?></td>
        <td><?php echo htmlspecialchars($row['nama_pelajar']); ?></td>
        <td style="text-align: center;"><?php echo $jumlah; ?></td>
        <td style="text-align: center;"><?php echo $tarikh; ?></td>
        <td style="text-align: center;"><?php echo $kaedah; ?></td>
        <td style="text-align: center;"><?php echo status_label($status); ?></td>
    </tr>
    <?php
        endwhile;
    else:
    ?>
        <tr><td colspan="7" style="text-align:center;">Tiada rekod pelajar.</td></tr>
    <?php endif; ?>
    </tbody>
</table>

<h2 style="text-align: center; margin: 20px 0;">Yuran Bulanan</h2>
<table>
    <thead>
        <tr>
            <th>No</th>
            <th>Nama Pelajar</th>
            <th>Jumlah (RM)</th>
            <th>Tarikh Bayaran</th>
            <th>Kaedah</th>
            <th>Status</th>
        </tr>
    </thead>
    <tbody>
    <?php
    $no = 1;
    if ($result_bulanan->num_rows > 0):
        while ($row = $result_bulanan->fetch_assoc()):
            $jumlah = $row['jumlah'] !== null ? number_format($row['jumlah'], 2) : '-';
            $tarikh = $row['tarikh'] !== null ? htmlspecialchars($row['tarikh']) : '-';
            $kaedah = $row['kaedah'] !== null ? htmlspecialchars($row['kaedah']) : '-';
            $status = $row['status'] !== null ? $row['status'] : 'unpaid';
    ?>
        <tr>
            <td style="text-align: center;"><?php echo $no++; ?></td>
            <td><?php echo htmlspecialchars($row['nama_pelajar']); ?></td>
            <td style="text-align: center;"><?php echo $jumlah; ?></td>
            <td style="text-align: center;"><?php echo $tarikh; ?></td>
            <td style="text-align: center;"><?php echo $kaedah; ?></td>
            <td style="text-align: center;"><?php echo status_label($status); ?></td>
        </tr>
    <?php
        endwhile;
    else:
    ?>
        <tr><td colspan="7" style="text-align:center;">Tiada rekod pelajar.</td></tr>
    <?php endif; ?>
    </tbody>
</table>
</div>
</body>
</html>

<?php
$stmt_p->close();
$stmt_b->close();
$conn->close();
?>
