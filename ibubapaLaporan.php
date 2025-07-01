<?php
include 'config.php';
session_start();

// Check if the parent is logged in
if (!isset($_SESSION['id_ibubapa'])) {
    header("Location: login.php");
    exit();
}

$id_ibubapa = $_SESSION['id_ibubapa']; // Get the parent's ID from session

// Fetch only approved students associated with the parent
$sql = "SELECT pelajar.ic_pelajar, pelajar.nama_pelajar
        FROM pelajar
        INNER JOIN ibubapa ON pelajar.ibubapa_id = ibubapa.id_ibubapa
        WHERE ibubapa.id_ibubapa = ?
        AND pelajar.pengesahan = 1";
$stmt = $conn->prepare($sql);
if (!$stmt) {
    die("Kesalahan pada pernyataan SQL: " . $conn->error);
}
$stmt->bind_param("i", $id_ibubapa);
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
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
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

        
          /* Main content styling */
    main {
        background: white;
        padding: 2rem;
        border-radius: 8px;
        box-shadow: 0 2px 10px rgba(0, 0, 0, 0.05);
        margin-top: 2rem;
    }

    /* Table styling */
    table {
        width: 100%;
        border-collapse: collapse;
        margin: 1.5rem 0;
        background: white;
        border-radius: 10px;
        overflow: hidden;
        box-shadow: 0 2px 15px rgba(0, 0, 0, 0.1);
    }

    th, td {
        padding: 1rem;
        text-align: left;
        border-bottom: 1px solid #eee;
    }

    th {
        background: #3a4065;
        color: white;
        font-weight: 600;
    }

    /* Center No column */
    .col-no {
        text-align: center;
        width: 60px;
    }

    /* View button styling */
    .btn-view {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        padding: 0.5rem;
        background-color: #28a745;
        color: white;
        border: none;
        border-radius: 50%;
        cursor: pointer;
        transition: all 0.3s ease;
        width: 36px;
        height: 36px;
    }

    .btn-view:hover {
        background-color: #218838;
        transform: translateY(-2px);
        box-shadow: 0 4px 8px rgba(0,0,0,0.1);
    }

    .btn-view::after {
        content: "View";
        position: absolute;
        top: -30px;
        left: 50%;
        transform: translateX(-50%);
        background: #333;
        color: white;
        padding: 4px 8px;
        border-radius: 4px;
        font-size: 12px;
        opacity: 0;
        transition: opacity 0.3s;
        pointer-events: none;
        white-space: nowrap;
    }

    .btn-view:hover::after {
        opacity: 1;
    }

    /* Centered action column */
    td:last-child {
        text-align: center;
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

        <main>
    <h2 style="text-align: center; margin: 20px 0;">Laporan Pelajar</h2>

    <table>
        <thead>
            <tr>
                <th class="col-no">No</th>
                <th>Nama Pelajar</th>
                <th>Tindakan</th>
            </tr>
        </thead>
        <tbody>
            <?php
            if ($result->num_rows > 0) {
                $no = 1;
                while ($row = $result->fetch_assoc()) {
                    echo "<tr>";
                    echo "<td style='text-align: center;'>" . $no++ . "</td>";
                    echo "<td>" . htmlspecialchars($row['nama_pelajar']) . "</td>";
                    echo "<td style='text-align: center;'>
                            <a href='ibubapa_viewLaporan.php?id_pelajar=" . urlencode($row['ic_pelajar']) . "'>
                                <button class='btn-view' title='View' style='background-color: #007bff; color: white; border: none; padding: 10px 20px; border-radius: 5px; font-size: 16px; cursor: pointer;'>
                                    <i class='fas fa-eye'></i>
                                </button>
                            </a>
                          </td>";
                    echo "</tr>";
                }
            } else {
                echo "<tr><td colspan='3' style='text-align: center;'>Tiada pelajar yang dijumpai untuk ibubapa ini.</td></tr>";
            }
            ?>
        </tbody>
    </table>
</main>

    </div>
</body>
</html>

<?php
$stmt->close();
$conn->close();
?>
