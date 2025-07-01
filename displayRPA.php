<?php
include 'config.php';
session_start();

if (!isset($_GET['id'])) {
    echo "Invalid RPA ID.";
    exit();
}

$id_RPA = $_GET['id'];

// Fetch RPA data from database
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
        textarea, input {
            width: 98%;
            padding: 10px;
            margin-bottom: 15px;
            border: 1px solid #ccc;
            border-radius: 4px;
            font-size: 14px;
            background-color: #f5f5f5;
        }
        textarea:read-only, input:read-only {
            background-color: #f5f5f5;
            color: #333;
        }
        .button-group {
            display: flex;
            justify-content: space-between;
            margin-top: 20px;
        }
        .btn-kembali, .btn-edit {
            text-decoration: none;
            padding: 10px 20px;
            border-radius: 4px;
            font-size: 16px;
            display: inline-block;
            text-align: center;
            color: white;
        }
        .btn-kembali {
            background-color: #007bff;
        }
        .btn-kembali:hover {
            background-color: #0056b3;
        }
        .btn-edit {
            background-color: #28a745;
        }
        .btn-edit:hover {
            background-color: #218838;
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

            <label>Pengetahuan Sedia Ada Kanak-Kanak:</label>
            <textarea readonly><?php echo $rpa['pengetahuan']; ?></textarea>

            <label>Objektif Pembelajaran:</label>
            <textarea readonly><?php echo $rpa['objektif']; ?></textarea>

            <label>Bidang Perkembangan:</label>
            <textarea readonly><?php echo $rpa['bidang']; ?></textarea>

            <label>Bahan Belajar:</label>
            <textarea readonly><?php echo $rpa['bahan']; ?></textarea>

            <label>Tempat/Ruang:</label>
            <textarea readonly><?php echo $rpa['tempat']; ?></textarea>

            <label>Rancangan Pelaksanaan:</label>
            <textarea readonly><?php echo $rpa['rancangan']; ?></textarea>

            <label>Hasil Pembelajaran/Refleksi:</label>
            <textarea readonly><?php echo $rpa['hasil']; ?></textarea>

            <div class="button-group">
                <a href="pendidikRPA.php?id=<?php echo $rpa['id_RPA']; ?>" class="btn-edit">Kemaskini</a>
                <a href="rpa.php" class="btn-kembali">Kembali</a>
            </div>
        </form>

</body>
</html>