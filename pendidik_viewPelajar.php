<?php
// Start the session
session_start();
include 'config.php';  // Include the configuration file for the database connection

// Check if the educator is logged in
if (!isset($_SESSION['username'])) {
    header('Location: login.php');
    exit;
}

// Check if the required parameters are provided in the URL
if (isset($_GET['ic_pelajar']) && isset($_GET['ibubapa_id'])) {
    $ic_pelajar = $_GET['ic_pelajar'];
    $ibubapa_id = $_GET['ibubapa_id'];

    // Query to get the student details from PELAJAR table
    $query_student = "SELECT * FROM pelajar WHERE ic_pelajar = ?";
    $stmt_student = $conn->prepare($query_student);
    $stmt_student->bind_param("s", $ic_pelajar);
    $stmt_student->execute();
    $student_result = $stmt_student->get_result();

    // If no student is found, show an error
    if ($student_result->num_rows == 0) {
        die('Student not found.');
    }

    // Fetch student data
    $student_data = $student_result->fetch_assoc();

    // Query to get the parent details from IBUBAPA table
    $query_parent = "SELECT * FROM ibubapa WHERE id_ibubapa = ?";
    $stmt_parent = $conn->prepare($query_parent);
    $stmt_parent->bind_param("i", $ibubapa_id);
    $stmt_parent->execute();
    $parent_result = $stmt_parent->get_result();

    // If no parent is found, show an error
    if ($parent_result->num_rows == 0) {
        die('Parent not found.');
    }

    // Fetch parent data
    $parent_data = $parent_result->fetch_assoc();
} else {
    die('Missing parameters.');
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Maklumat pelajar dan ibu bapa</title>
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

        .dropdown li a:hover {
            background: rgba(58, 64, 101, 0.1);
            color: #4e54c8;
        }
        .profile-container {
        width: 70%;
        margin: 20px auto;
        background: #fff;
        padding: 20px;
        border-radius: 8px;
        box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
    }

    .profile-container h1 {
        font-weight: bold;
        text-align: center;
        margin-bottom: 20px;
    }

    /* Standardized Table Styling */
    .profile-container table {
        width: 100%;
        border-collapse: collapse;
        table-layout: fixed; /* Ensures consistent column widths */
    }

    .profile-container table th,
    .profile-container table td {
        padding: 12px 15px;
        border-bottom: 1px solid #ddd;
        word-wrap: break-word; /* Handles long content */
    }

    .profile-container table th {
            width: 30%;
            text-align: left;
            background-color: #3a4065; /* Warna biru tua yang Anda mau */
            color: white; /* Warna teks putih untuk kontras */
            font-weight: 600;
            padding: 12px 15px;
        }

    .profile-container table td {
        width: 70%; /* Fixed width for all data cells */
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

    h3 {
        font-weight: bold;
        margin-top: 30px;
        color: #3a4065;
        padding-bottom: 8px;
        border-bottom: 2px solid #eee;
    }

    /* Responsive adjustments */
    @media (max-width: 768px) {
        .profile-container {
            width: 95%;
            padding: 15px;
        }
        
        .profile-container table th,
        .profile-container table td {
            display: block;
            width: 100%;
            box-sizing: border-box;
        }
        
        .profile-container table th {
            width: 30%;
            text-align: left;
            background-color: #3a4065; /* Warna biru tua yang Anda mau */
            color: white; /* Warna teks putih untuk kontras */
            font-weight: 600;
            padding: 12px 15px;
        }
    }

        .modal-body h5 {
            font-weight: bold;
            margin-top: 20px;
            margin-bottom: 10px;
        }

        .modal-body .mb-3 {
            margin-bottom: 15px;
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
<main>
    <div class="profile-container">
        <h1>Maklumat Profil</h1>

        <!-- Student Photo -->
        <div class="student-photo">
            <?php if (!empty($student_data['gambar_pelajar']) && file_exists("uploads/" . $student_data['gambar_pelajar'])): ?>
                <img src="uploads/<?php echo htmlspecialchars($student_data['gambar_pelajar']); ?>" alt="Gambar Pelajar">
            <?php else: ?>
                <img src="uploads/default.png" alt="Default Photo">
            <?php endif; ?>
        </div>

        <!-- Student Information -->
        <h3>Maklumat Pelajar</h3>
        <table class="table">
            <tr>
                <th>No MyKid Pelajar</th>
                <td><?php echo htmlspecialchars($student_data['ic_pelajar']); ?></td>
            </tr>
            <tr>
                <th>Nama</th>
                <td><?php echo htmlspecialchars($student_data['nama_pelajar']); ?></td>
            </tr>
            <tr>
                <th>Jantina</th>
                <td><?php echo htmlspecialchars($student_data['jantina']); ?></td>
            </tr>
            <tr>
                <th>Alamat Semasa</th>
                <td><?php echo htmlspecialchars($student_data['alamat_semasa']); ?></td>
            </tr>
            <tr>
                <th>Umur</th>
                <td><?php echo htmlspecialchars($student_data['umur']); ?></td>
            </tr>
        </table>

        <!-- Parent Information -->
        <h3>Maklumat Ibu Bapa</h3>
        <table class="table">
            <tr>
                <th>No MyKad Bapa</th>
                <td><?php echo htmlspecialchars($parent_data['ic_bapa']); ?></td>
            </tr>
            <tr>
                <th>Nama Bapa</th>
                <td><?php echo htmlspecialchars($parent_data['nama_bapa']); ?></td>
            </tr>
            <tr>
                <th>Pekerjaan Bapa</th>
                <td><?php echo htmlspecialchars($parent_data['pekerjaan_bapa']); ?></td>
            </tr>
            <tr>
                <th>Pendapatan Bapa</th>
                <td><?php echo htmlspecialchars($parent_data['pendapatan_bapa']); ?></td>
            </tr>
            <tr>
                <th>Email Bapa</th>
                <td><?php echo htmlspecialchars($parent_data['email_bapa']); ?></td>
            </tr>
            <tr>
                <th>No Telefon Bapa</th>
                <td><?php echo htmlspecialchars($parent_data['no_bapa']); ?></td>
            </tr>
            <tr>
                <th>No MyKad Ibu</th>
                <td><?php echo htmlspecialchars($parent_data['ic_ibu']); ?></td>
            </tr>
            <tr>
                <th>Nama Ibu</th>
                <td><?php echo htmlspecialchars($parent_data['nama_ibu']); ?></td>
            </tr>
            <tr>
                <th>Pekerjaan Ibu</th>
                <td><?php echo htmlspecialchars($parent_data['pekerjaan_ibu']); ?></td>
            </tr>
            <tr>
                <th>Pendapatan Ibu</th>
                <td><?php echo htmlspecialchars($parent_data['pendapatan_ibu']); ?></td>
            </tr>
            <tr>
                <th>Email Ibu</th>
                <td><?php echo htmlspecialchars($parent_data['EmailIbu']); ?></td>
            </tr>
            <tr>
                <th>No Telefon Ibu</th>
                <td><?php echo htmlspecialchars($parent_data['no_ibu']); ?></td>
            </tr>
        </table>
        <div class="crud-buttons">
            <a href="pendidikPelajar.php">
                <button style="background-color: #28a745; color: white;">Kembali</button>
            </a>
        </div>

    </div>
    </div>
</main>
</html>

<?php
// Close the statement and connection
$stmt_student->close();
$stmt_parent->close();
$conn->close();
?>
