<?php
session_start();
require_once 'config.php'; // Database connection

if (!isset($_SESSION['username'])) {
    header("Location: login.php");
    exit();
}

$username = $_SESSION['username']; 

// Query to get the student details, fee type, and payment status for the selected month
$query = "
    SELECT 
        pelajar.ic_pelajar, 
        pelajar.nama_pelajar,
        yuran.jenis_yuran,
        yuran.jumlah,
        yuran.status,
        yuran.bulan
    FROM pelajar
    INNER JOIN yuran ON pelajar.ic_pelajar = yuran.ic_pelajar
    INNER JOIN ibubapa ON pelajar.ibubapa_id = ibubapa.id_ibubapa
    WHERE ibubapa.email_bapa = ? AND pelajar.pengesahan = 1
";

$stmt = $conn->prepare($query);
if (!$stmt) {
    die("Prepare statement failed: " . $conn->error);
}

$stmt->bind_param("s", $username);
$stmt->execute();
$result = $stmt->get_result();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pembayaran Yuran</title>
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

        nav ul li {
            position: relative;
        }

        nav ul li a {
            color: #3a4065 !important;
            padding: 1rem 1.5rem;
            text-decoration: none;
            display: flex;
            align-items: center;
            transition: all 0.3s ease;
            border-radius: 6px;
        }

        nav ul li a:hover {
            background: rgba(58, 64, 101, 0.1);
            transform: translateY(-1px);
        }

        .dropdown-parent > a {
            display: flex;
            align-items: center;
            gap: 5px;
            cursor: pointer;
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

        .dropdown li a:hover {
            background: rgba(58, 64, 101, 0.1);
            color: #4e54c8;
        }

        /* Container styling */
        .container {
            width: 80%;
            margin: 0 auto;
            padding: 20px;
            background-color: #fff;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            border-radius: 8px;
        }

        /* Table Styling */
        table {
            width: 100%;
            margin: 20px 0;
            border-collapse: collapse;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.05);
        }

        table th, table td {
            padding: 15px;
            text-align: center;
            border: 1px solid #ddd;
            font-size: 16px;
        }

        table th {
            background-color: #3a4065;
            color: white;
        }

        table tbody tr:nth-child(even) {
            background-color: #f9f9f9;
        }

        table tbody tr:hover {
            background-color: #f1f1f1;
        }

        table td {
            color: #333;
        }

        table td a {
            text-decoration: none;
            color: #333;
        }

        table td a:hover {
            color: #4e54c8;
        }

        /* Responsive design */
        @media (max-width: 768px) {
            nav ul {
                flex-direction: column;
                padding: 1rem !important;
            }

            .dropdown {
                position: static;
                box-shadow: none;
                padding: 0 !important;
            }

            table {
                display: block;
                overflow-x: auto;
            }

            th, td {
                min-width: 120px;
            }
        }
    </style>  
</head>
<body class="dashboard-container">
    <div class="container">
        <!-- Header -->
        <header>
            <a href="ibubapa_dashboard.php"><span>Dashboard Ibu Bapa</span></a>
            <a href="logout.php">Logout</a>
        </header>

        <!-- Navigation -->
        <nav>
            <ul>
            <li class="dropdown-parent">
                    <a href="#">Profil <span class="dropdown-icon">&#x25BC;</span></a>
                    <ul class="dropdown">
                        <li><a href="profilIbubapa1.php">Ibubapa</a></li>
                        <li><a href="pelajar.php">Pelajar</a></li>
                        <li><a href="ibubapa_daftar_pelajarBaru.php">Pendaftaran Pelajar Baru</a></li>
                                    </ul>
                </li>
                <li><a href="yuran.php">Yuran</a></li>
                <li><a href="display_jadual.php">Jadual Pelajar</a></li>
                <li><a href="ibubapaLaporan.php">Laporan</a></li>
            </ul>
        </nav>

<!-- Main Content -->
<main>
   <!-- Add month selection form -->
   <h2 style="text-align: center; margin: 20px 0;">Pembayaran Yuran Pelajar</h2>
   <div class="month-selection">
    <form method="GET" id="monthForm">
        <label for="bulan">Pilih Bulan:</label>
        <select name="bulan" id="bulan" onchange="this.form.submit()">
            <?php
            $selected_month = isset($_GET['bulan']) ? $_GET['bulan'] : date('n');
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
            
            foreach ($month_names as $month_num => $month_name) {
                $selected = ($selected_month == $month_num) ? 'selected' : '';
                echo "<option value=\"$month_num\" $selected>$month_name</option>";
            }
            ?>
        </select>
    </form>
</div>

<!-- Table displaying the students' payment status -->
<table border="1">
    <thead>
        <tr>
            <th>Nama Pelajar</th>
            <th colspan="2">Yuran Pendaftaran</th>
            <th colspan="2">Yuran Bulanan</th>
        </tr>
        <tr>
            <th></th>
            <th>Jumlah</th>
            <th>Status</th>
            <th>Jumlah</th>
            <th>Status</th>
        </tr>
    </thead>
    <tbody>
    <?php
        // Modified query to group payments by student and type
        $query = "
            SELECT 
                p.ic_pelajar,
                p.nama_pelajar,
                MAX(CASE 
                    WHEN y.jenis_yuran = 'Pendaftaran' AND 
                        (y.bulan = ? OR EXISTS (
                            SELECT 1 FROM YURAN y2 
                            WHERE y2.ic_pelajar = p.ic_pelajar 
                            AND y2.jenis_yuran = 'Pendaftaran'
                            AND y2.status = 'success'
                            AND y2.bulan <= ?
                            AND y2.bulan >= 3
                        ))
                    THEN y.jumlah 
                END) as pendaftaran_jumlah,
                MAX(CASE 
                    WHEN y.jenis_yuran = 'Pendaftaran' AND 
                        (y.bulan = ? OR EXISTS (
                            SELECT 1 FROM YURAN y2 
                            WHERE y2.ic_pelajar = p.ic_pelajar 
                            AND y2.jenis_yuran = 'Pendaftaran'
                            AND y2.status = 'success'
                            AND y2.bulan <= ?
                            AND y2.bulan >= 3
                        ))
                    THEN y.status 
                END) as pendaftaran_status,
                MAX(CASE WHEN y.jenis_yuran = 'Bulanan' AND y.bulan = ? THEN y.jumlah END) as bulanan_jumlah,
                MAX(CASE WHEN y.jenis_yuran = 'Bulanan' AND y.bulan = ? THEN y.status END) as bulanan_status
            FROM PELAJAR p
            LEFT JOIN YURAN y ON p.ic_pelajar = y.ic_pelajar
            INNER JOIN IBUBAPA i ON p.ibubapa_id = i.id_ibubapa
            WHERE i.email_bapa = ? AND p.pengesahan = 1
            GROUP BY p.ic_pelajar, p.nama_pelajar
        ";

        $stmt = $conn->prepare($query);
        $stmt->bind_param("iiiiiss", $selected_month, $selected_month, $selected_month, $selected_month, $selected_month, $selected_month, $username);
        $stmt->execute();
        $result = $stmt->get_result();

        while ($data = $result->fetch_assoc()) { ?>
            <tr>
                <td>
                <a href="yuranPelajar.php?ic=<?php echo urlencode($data['ic_pelajar']); ?>&bulan=<?php echo urlencode($selected_month); ?>">
                        <?php echo htmlspecialchars($data['nama_pelajar']); ?>
                    </a>
                </td>
                <!-- Pendaftaran Payment -->
                <td><?php echo $data['pendaftaran_jumlah'] ? number_format($data['pendaftaran_jumlah'], 2) : '-'; ?></td>
                <td style="text-align: center;">
                    <?php
                    $pendaftaran_status = $data['pendaftaran_status'] ?: 'unpaid';
                    if ($pendaftaran_status == 'unpaid') {
                        echo '<span style="color:red;">Unpaid</span>';
                    } elseif ($pendaftaran_status == 'success') {
                        echo '<span style="color:green;">Paid</span>';
                    } else {
                        echo '<span style="color:orange;">Pending</span>';
                    }
                    ?>
                </td>
                <!-- Bulanan Payment -->
                <td><?php echo $data['bulanan_jumlah'] ? number_format($data['bulanan_jumlah'], 2) : '-'; ?></td>
                <td style="text-align: center;">
                    <?php
                    $bulanan_status = $data['bulanan_status'] ?: 'unpaid';
                    if ($bulanan_status == 'unpaid') {
                        echo '<span style="color:red;">Unpaid</span>';
                    } elseif ($bulanan_status == 'success') {
                        echo '<span style="color:green;">Paid</span>';
                    } else {
                        echo '<span style="color:orange;">Pending</span>';
                    }
                    ?>
                </td>
            </tr>
        <?php } ?>
    </tbody>
</table>
</main>

</body>
</html>

<?php
$stmt->close();
?>

<style>
    /* Month Selection Styling */
    .month-selection {
        text-align: center;
        margin: 20px auto;
        padding: 20px;
        background-color: #f8f9fa;
        border-radius: 8px;
        box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        width: 80%;
    }

    .month-selection label {
        font-size: 18px;
        margin-right: 10px;
        color: #333;
    }

    .month-selection select {
        padding: 8px 15px;
        font-size: 16px;
        border: 2px solid #3a4065;
        border-radius: 5px;
        background-color: white;
        cursor: pointer;
        outline: none;
    }

    .month-selection select:hover {
        border-color: #2c3154;
    }

    .month-selection select:focus {
        border-color: #2c3154;
        box-shadow: 0 0 5px rgba(58, 64, 101, 0.3);
    }
</style>