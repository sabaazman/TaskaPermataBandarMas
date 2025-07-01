<?php
session_start();
require_once 'config.php'; // Database connection

if (!isset($_SESSION['username'])) {
    header("Location: login.php");
    exit();
}

$username = $_SESSION['username']; 
$query = "
    SELECT 
        pelajar.ic_pelajar, 
        pelajar.nama_pelajar
    FROM pelajar
    INNER JOIN ibubapa ON pelajar.ibubapa_id = ibubapa.id_ibubapa
    WHERE ibubapa.email_bapa = ?
      AND pelajar.pengesahan = 1
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
    <title>Dashboard Ibu Bapa</title>
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
        }

        nav ul li a {
            color: #3a4065 !important;
            padding: 1rem 1.5rem;
            transition: all 0.3s ease;
            border-radius: 6px;
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

        /* Main content styling */
        main {
            background: white;
            padding: 2rem;
            border-radius: 8px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.05);
            margin-top: 2rem;
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
        /* Remove underline and set color to black for anchor tags inside the table */
        table a {
            color: #333; /* Set text color to black */
            text-decoration: none; /* Remove underline */
        }

        table a:hover {
            color: #333; /* Keep color black on hover as well */
        }
        table {
            width: 80% !important;
            margin: 20px auto !important; /* Center the table */
            border-collapse: collapse !important;
        }

        table th:first-child,
        table td:first-child {
            width: 80px !important;  /* Fixed width for No column */
            text-align: center !important;
        }

        table th:nth-child(2),
        table td:nth-child(2) {
            width: calc(100% - 80px) !important; /* Remaining width for Nama Pelajar */
            text-align: center !important;
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
    <h2 style="text-align: center; margin: 20px 0;">Senarai Nama Pelajar</h2>

    <table>
        <thead>
            <tr>
                <th>No</th>
                <th>Nama Pelajar</th>
            </tr>
        </thead>
        <tbody>
            <?php 
            $no = 1; // Initialize counter
            while ($data = $result->fetch_assoc()) { 
            ?>
                <tr>
                <td style="text-align: center;"><?php echo $no++; ?></td> <!-- Numbering centered -->
                <td style="text-align: center;">
                        <!-- Clickable link to student profile -->
                        <a href="profilPelajar.php?ic=<?php echo $data['ic_pelajar']; ?>">
                            <?php echo htmlspecialchars($data['nama_pelajar']); ?>
                        </a>
                    </td>
                </tr>
            <?php } ?>
        </tbody>
    </table>
</main>
    </div>
</body>
</html>

<?php
$stmt->close();
?>