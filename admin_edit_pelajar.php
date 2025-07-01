<?php
include 'config.php';

if (isset($_GET['edit'])) {
    $ic_pelajar = $_GET['edit'];

    // Fetch student details with assigned teacher
    $sql_student = "SELECT pelajar.*, pendidik.nama_pendidik FROM pelajar 
                    LEFT JOIN pendidik ON pelajar.id_pendidik = pendidik.id_pendidik
                    WHERE pelajar.ic_pelajar = '$ic_pelajar'";
    $student_result = $conn->query($sql_student);
    $student = $student_result->fetch_assoc();

    // Fetch all teachers
    $sql_pendidik = "SELECT id_pendidik, nama_pendidik FROM pendidik WHERE pengesahan = 1";
    $pendidik_result = $conn->query($sql_pendidik);
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $ic_pelajar = $_POST['ic_pelajar'];
    $id_pendidik = $_POST['id_pendidik'];
    $id_pentadbir = 1; // Track that pentadbir (ID 1) made this assignment

    $sql_update = "UPDATE pelajar SET id_pendidik = '$id_pendidik', id_pentadbir = '$id_pentadbir' WHERE ic_pelajar = '$ic_pelajar'";
    if ($conn->query($sql_update) === TRUE) {
        echo "Pendidik berjaya ditetapkan oleh Pentadbir.";
    } else {
        echo "Ralat: " . $conn->error;
    }
}
?>

<!DOCTYPE html>
<html lang="ms">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kemaskini Maklumat Pelajar</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <link rel="stylesheet" href="css/dashboard.css">
    <style>
        /* untuk edit maklumat pelajar */
        .profile-container {
            width: 80%;
            margin: 20px auto;
            background: #fff;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        }

        .profile-container h1 {
            font-size: 26px; /* Adjust as per your preference */
            font-weight: bold;
            text-align: center;
            margin-bottom: 20px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
        }

        table th,
        table td {
            padding: 10px;
            text-align: left;
            border-bottom: 1px solid #ddd;
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

        .form-group {
            margin-bottom: 15px;
        }

        label {
            font-weight: bold;
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

        .crud-buttons .btn-back {
            background: #28a745; /* Green color for the "Kembali" button */
        }

        .crud-buttons .btn-back:hover {
            background: #218838; /* Darker green when hovered */
        }

        h3 {
            font-weight: bold;
            margin-top: 20px;
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
    </style>
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
        <body>
        <div class="profile-container">
            <h1>Kemaskini Maklumat Pelajar</h1>
            <form method="post">
                <input type="hidden" name="ic_pelajar" value="<?php echo $student['ic_pelajar']; ?>">
                <div class="form-group">
                    <label for="nama_pelajar">Nama:</label>
                    <p><?php echo htmlspecialchars($student['nama_pelajar']); ?></p>
                </div>
                <div class="form-group">
                    <label for="tahun_pengajian">Tahun Pengajian:</label>
                    <p><?php echo htmlspecialchars($student['tahun_pengajian']); ?></p>
                </div>
                <div class="form-group">
                    <label for="jantina">Jantina:</label>
                    <p><?php echo htmlspecialchars($student['jantina']); ?></p>
                </div>
                <div class="form-group">
                    <label for="alamat_semasa">Alamat Semasa:</label>
                    <p><?php echo htmlspecialchars($student['alamat_semasa']); ?></p>
                </div>
                <div class="form-group">
                    <label for="umur">Umur:</label>
                    <p><?php echo htmlspecialchars($student['umur']); ?></p>
                </div>
                <div class="form-group">
                    <label for="alergi">Alahan:</label>
                    <p><?php echo htmlspecialchars($student['Alahan']); ?></p>
                </div>
                <div class="form-group">
                    <label for="id_pendidik">Pilih Pendidik:</label>
                    <select name="id_pendidik" class="form-control">
                        <?php while ($row = $pendidik_result->fetch_assoc()): ?>
                            <option value="<?php echo $row['id_pendidik']; ?>" <?php echo ($student['id_pendidik'] == $row['id_pendidik']) ? 'selected' : ''; ?>>
                                <?php echo htmlspecialchars($row['nama_pendidik']); ?>
                            </option>
                        <?php endwhile; ?>
                    </select>
                </div>
                <div style="display: flex; justify-content: center; gap: 10px; margin-top: 20px;">
    <button type="submit" 
            style="padding: 10px 20px; background: #28a745; color: white; border: none; border-radius: 5px; cursor: pointer;"
            onmouseover="this.style.background='#218838'" 
            onmouseout="this.style.background='#28a745'">
        Simpan
    </button>
    <a href="admin_pelajar.php" style="text-decoration: none;">
        <button type="button" 
                style="padding: 10px 20px; background: #666; color: white; border: none; border-radius: 5px; cursor: pointer;"
                onmouseover="this.style.background='#444'" 
                onmouseout="this.style.background='#666'">
            Kembali
        </button>
    </a>
</div>
            </form>
        </div>
    </div>
</body>
</html>