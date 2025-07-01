<?php
include 'config.php';
session_start();

function formatPhoneNumber($phone) {
    $phone = preg_replace('/[^0-9]/', '', $phone);
    if (substr($phone, 0, 1) === '0') {
        $phone = '+60' . substr($phone, 1);
    }
    else if (substr($phone, 0, 2) !== '60' && substr($phone, 0, 3) !== '+60') {
        $phone = '+60' . $phone;
    }
    else if (substr($phone, 0, 2) === '60') {
        $phone = '+' . $phone;
    }
    return $phone;
}

if (!isset($_SESSION['username'])) {
    header("Location: login.php");
    exit();
}

$id_pendidik = $_SESSION['id_pendidik'];
$selected_month = isset($_GET['month']) ? $_GET['month'] : date('m');

$query = "
    SELECT 
        pelajar.nama_pelajar,
        pelajar.ic_pelajar,
        (SELECT jumlah FROM YURAN 
         WHERE yuran.ic_pelajar = pelajar.ic_pelajar 
         AND jenis_yuran = 'Pendaftaran' 
         AND bulan <= ? 
         AND status = 'success' LIMIT 1) as pendaftaran_jumlah,
        (SELECT status FROM yuran
         WHERE yuran.ic_pelajar = PELAJAR.ic_pelajar 
         AND jenis_yuran = 'Pendaftaran'
         AND bulan <= ?
         AND status = 'success' LIMIT 1) as pendaftaran_status,
        MAX(CASE WHEN yuran.jenis_yuran = 'Bulanan' AND yuran.bulan = ? THEN yuran.jumlah END) as bulanan_jumlah,
        MAX(CASE WHEN yuran.jenis_yuran = 'Bulanan' AND yuran.bulan = ? THEN yuran.status END) as bulanan_status,
        IBUBAPA.no_bapa
    FROM pelajar
    LEFT JOIN yuran ON pelajar.ic_pelajar = yuran.ic_pelajar
    LEFT JOIN ibubapa ON pelajar.ibubapa_id = ibubapa.id_ibubapa
    WHERE pelajar.id_pendidik = ?
    GROUP BY pelajar.nama_pelajar, pelajar.ic_pelajar, ibubapa.no_bapa
    ORDER BY pelajar.nama_pelajar ASC
";

$stmt = $conn->prepare($query);
if (!$stmt) {
    die("Prepare statement failed: " . $conn->error);
}

$stmt->bind_param("iiiii", $selected_month, $selected_month, $selected_month, $selected_month, $id_pendidik);
$stmt->execute();
$result = $stmt->get_result();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $update_status_query = "
        UPDATE yuran
        SET status = 'unpaid'
        WHERE bulan = ? AND status != 'success'
    ";
    $stmt_update = $conn->prepare($update_status_query);
    $stmt_update->bind_param("i", $selected_month);
    $stmt_update->execute();
}
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
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard Pendidik</title>
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
         /* Table Styling */
         table {
            width: 80%;
            margin: 20px auto;
            border-collapse: collapse;
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
            color:rgb(0, 0, 0);
        }

        table td a:hover {
            color:rgb(0, 0, 0);
        }
    </style>
</head>
<body>
    <div class="dashboard-container">
    <div class="container">
        <!-- Header -->
        <header>
            <span>Dashboard Pendidik</span>
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
        <main>
        <h2 style="text-align: center; margin: 20px auto; width: 100%;">Yuran Pelajar - Bulan <?php echo $selected_month; ?></h2>
  

    <!-- Month Selection for Teacher -->
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

        /* Title styling */
        h2 {
            text-align: center;
            margin-bottom: 20px;
            width: 80%;
            margin-left: auto;
            margin-right: auto;
        }

        /* Enhance Table Styling */
        table {
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            border-radius: 8px;
            overflow: hidden;
        }

        table th {
            padding: 15px 20px;
            font-size: 17px;
        }

        table td {
            padding: 12px 20px;
            transition: background-color 0.3s;
        }

        .status-unpaid {
            color: #dc3545;
            font-weight: bold;
        }

        .status-paid {
            color: #28a745;
            font-weight: bold;
        }

        .status-pending {
            color: #ffc107;
            font-weight: bold;
        }
    </style>

    <!-- Update the month selection form with new classes -->
    <div class="month-selection">
    <form method="get" action="yuranPendidik.php">
        <label for="month">Pilih Bulan:</label>
        <select name="month" id="month" onchange="this.form.submit()">
            <?php foreach ($month_names as $month_num => $month_name): ?>
                <option value="<?php echo $month_num; ?>" <?php echo ($month_num == $selected_month) ? 'selected' : ''; ?>>
                    <?php echo $month_name; ?>
                </option>
            <?php endforeach; ?>
        </select>
    </form>
</div>


<table border="1">
    <thead>
        <tr>
            <th>Nama Pelajar</th>
            <th colspan="2">Yuran Pendaftaran</th>
            <th colspan="2">Yuran Bulanan</th>
            <th>Tindakan</th>
        </tr>
        <tr>
            <th></th>
            <th>Jumlah (RM)</th>
            <th>Status</th>
            <th>Jumlah (RM)</th>
            <th>Status</th>
            <th></th>
        </tr>
    </thead>
    <tbody>
    <?php while ($row = $result->fetch_assoc()): ?>
        <tr>
            <td><?php echo htmlspecialchars($row['nama_pelajar']); ?></td>
            <!-- Pendaftaran Payment -->
            <td><?php echo ($row['pendaftaran_status'] === 'success' || $row['pendaftaran_jumlah'] > 0) ? number_format($row['pendaftaran_jumlah'], 2) : '-'; ?></td>
            <td style="color: <?php echo ($row['pendaftaran_status'] === 'success' || $row['pendaftaran_jumlah'] > 0) ? 'green' : 'red'; ?>">
                <?php echo ($row['pendaftaran_status'] === 'success' || $row['pendaftaran_jumlah'] > 0) ? 'Paid' : 'Unpaid'; ?>
            </td>
            <!-- Bulanan Payment -->
            <td><?php echo ($row['bulanan_status'] === 'success' || $row['bulanan_jumlah'] > 0) ? number_format($row['bulanan_jumlah'], 2) : '-'; ?></td>
            <td style="color: <?php echo ($row['bulanan_status'] === 'success' || $row['bulanan_jumlah'] > 0) ? 'green' : 'red'; ?>">
                <?php echo ($row['bulanan_status'] === 'success' || $row['bulanan_jumlah'] > 0) ? 'Paid' : 'Unpaid'; ?>
            </td>
            <td>
                <?php if ($row['bulanan_status'] !== 'success' && $row['bulanan_jumlah'] <= 0): ?>
                    <?php
                    $phone = formatPhoneNumber($row['no_bapa']);
                    $message = rawurlencode("Peringatan: Pembayaran yuran bulanan anak anda, " . 
                        $row['nama_pelajar'] . ", untuk bulan $selected_month masih belum dijelaskan.");
                    $whatsappLink = "https://wa.me/{$phone}?text={$message}";
                    ?>
                    <a href="<?php echo $whatsappLink; ?>" target="_blank" class="whatsapp-button" 
                       style="background-color: #28a745; color: #fff; padding: 8px 16px; border-radius: 6px; text-decoration: none;">
                        Hantar Peringatan
                    </a>
                <?php endif; ?>
            </td>
        </tr>
    <?php endwhile; ?>
    </tbody>
</table>
</div>
</div>

<?php
$stmt->close();
?>
