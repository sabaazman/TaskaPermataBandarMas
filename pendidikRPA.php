<?php
include 'config.php';
session_start();

if (!isset($_SESSION['id_pendidik'])) {
    header("Location: login.php");
    exit();
}

$id_pendidik = $_SESSION['id_pendidik'];
$editing = isset($_GET['id']);
$rpa = [];

// If editing, fetch existing data
if ($editing) {
    $id_RPA = $_GET['id'];
    $query = "SELECT * FROM rpa WHERE id_RPA = '$id_RPA'";
    $result = $conn->query($query);
    if ($result && $result->num_rows > 0) {
        $rpa = $result->fetch_assoc();
    } else {
        echo "RPA tidak dijumpai.";
        exit();
    }
}

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $minggu = $_POST['minggu'];
    $tarikh = $_POST['tarikh'];
    $masa = $_POST['masa'];
    $hari = $_POST['hari'];
    $tajuk = $_POST['tajuk'];
    $pengetahuan = $_POST['pengetahuan'];
    $objektif = $_POST['objektif'];
    $bidang = $_POST['bidang'];
    $bahan = $_POST['bahan'];
    $tempat = $_POST['tempat'];
    $rancangan = $_POST['rancangan'];
    $hasil = $_POST['hasil'];

    if ($editing) {
        $update = "UPDATE rpa SET minggu='$minggu', tarikh='$tarikh', masa='$masa', hari='$hari',
                   tajuk='$tajuk', pengetahuan='$pengetahuan', objektif='$objektif',
                   bidang='$bidang', bahan='$bahan', tempat='$tempat',
                   rancangan='$rancangan', hasil='$hasil'
                   WHERE id_RPA = '$id_RPA'";
        $conn->query($update);
    } else {
        $insert = "INSERT INTO rpa (minggu, tarikh, masa, hari, tajuk, pengetahuan, objektif,
                   bidang, bahan, tempat, rancangan, hasil, id_pendidik)
                   VALUES ('$minggu', '$tarikh', '$masa', '$hari', '$tajuk', '$pengetahuan', '$objektif',
                   '$bidang', '$bahan', '$tempat', '$rancangan', '$hasil', '$id_pendidik')";
        $conn->query($insert);
    }

    header("Location: rpa.php");
    exit();
}
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
            width: 100%;
            padding: 10px;
            margin-bottom: 15px;
            border: 1px solid #ccc;
            border-radius: 4px;
            font-size: 14px;
            resize: vertical;
        }
        .button-group {
            display: flex;
            justify-content: space-between;
            margin-top: 20px;
        }

        /* Shared base style for both button and link */
        .btn-kembali, .btn-edit {
            text-decoration: none;
            padding: 10px 20px;
            border-radius: 4px;
            font-size: 16px;
            display: inline-block;
            text-align: center;
            color: white;
            cursor: pointer;
            border: none;
        }

        /* Specific for <a> tag */
        .btn-kembali {
            background-color: #007bff;
        }
        .btn-kembali:hover {
            background-color: #0056b3;
        }

        /* Specific for both <a> or <button> using class btn-edit */
        .btn-edit {
            background-color: #28a745;
        }
        .btn-edit:hover {
            background-color: #218838;
        }

        /* Ensure <button> element adopts styling properly */
        button.btn-edit {
            all: unset; /* resets browser default */
            background-color: #28a745;
            color: white;
            padding: 10px 20px;
            font-size: 16px;
            border-radius: 4px;
            cursor: pointer;
            display: inline-block;
            text-align: center;
        }
        button.btn-edit:hover {
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

        <body>
        <h2 style="text-align: center; margin: 20px 0;"><?php echo $editing ? "Kemaskini" : "Tambah"; ?> Rancangan Pelaksanaan Aktiviti (RPA)</h2>


<form method="POST">
    <label for="minggu">Minggu:</label>
    <select name="minggu" id="minggu" required>
        <option value="">-- Pilih Minggu --</option>
        <?php for ($i = 1; $i <= 4; $i++): ?>
            <option value="<?php echo $i; ?>" <?php if (($rpa['minggu'] ?? '') == $i) echo 'selected'; ?>>
                Minggu <?php echo $i; ?>
            </option>
        <?php endfor; ?>
    </select>

    <label for="tarikh">Tarikh:</label>
    <input type="date" name="tarikh" id="tarikh" value="<?php echo $rpa['tarikh'] ?? ''; ?>" required>

    <label for="masa">Masa:</label>
    <select name="masa" id="masa" required>
        <option value="">-- Pilih Masa --</option>
        <option value="10:30 AM" <?php if (($rpa['masa'] ?? '') == "10:30 AM") echo "selected"; ?>>10:30 AM</option>
        <option value="4:00 PM" <?php if (($rpa['masa'] ?? '') == "4:00 PM") echo "selected"; ?>>4:00 PM</option>
    </select>

    <label for="hari">Hari:</label>
    <select name="hari" id="hari" required>
        <option value="">-- Pilih Hari --</option>
        <?php
        $days = ['Isnin', 'Selasa', 'Rabu', 'Khamis', 'Jumaat'];
        foreach ($days as $day):
        ?>
            <option value="<?php echo $day; ?>" <?php if (($rpa['hari'] ?? '') == $day) echo 'selected'; ?>>
                <?php echo $day; ?>
            </option>
        <?php endforeach; ?>
    </select>

    <label for="tajuk">Tajuk:</label>
    <textarea id="tajuk" name="tajuk" required><?php echo $rpa['tajuk'] ?? ''; ?></textarea>

    <label for="pengetahuan">Pengetahuan Sedia Ada Kanak-kanak:</label>
    <textarea id="pengetahuan" name="pengetahuan" required><?php echo $rpa['pengetahuan'] ?? ''; ?></textarea>

    <label for="objektif">Objektif Pembelajaran:</label>
    <textarea id="objektif" name="objektif" required><?php echo $rpa['objektif'] ?? ''; ?></textarea>

    <label for="bidang">Bidang Perkembangan:</label>
    <textarea id="bidang" name="bidang" required><?php echo $rpa['bidang'] ?? ''; ?></textarea>

    <label for="bahan">Bahan Belajar:</label>
    <textarea id="bahan" name="bahan" required><?php echo $rpa['bahan'] ?? ''; ?></textarea>

    <label for="tempat">Tempat/Ruang:</label>
    <textarea id="tempat" name="tempat" required><?php echo $rpa['tempat'] ?? ''; ?></textarea>

    <label for="rancangan">Rancangan Pelaksanaan:</label>
    <textarea id="rancangan" name="rancangan" required><?php echo $rpa['rancangan'] ?? ''; ?></textarea>

    <label for="hasil">Hasil Pembelajaran/Refleksi:</label>
    <textarea id="hasil" name="hasil" required><?php echo $rpa['hasil'] ?? ''; ?></textarea>

    <div class="button-group">
    <button type="submit" class="btn-edit">
        <?php echo $editing ? "Kemaskini" : "Simpan"; ?>
    </button>
    <a href="rpa.php" class="btn-kembali">Kembali</a>
    </div>

</div>
</form>

</body>
</html>