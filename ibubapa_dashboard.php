<?php
session_start(); // Start the session

// Include the database configuration file to access the $conn variable
include 'config.php'; // Ensure this is the correct path to your database connection file

// Check if the parent is logged in by verifying the session variable
if (!isset($_SESSION['username'])) {
    // If not logged in, redirect to login page
    header('Location: login.php');
    exit();
}

// Retrieve the logged-in parent's username
$username = $_SESSION['username'];

// Get the parent's details from the IBUBAPA table
$sql_ibubapa = "SELECT * FROM ibubapa WHERE username = ? AND peranan_id = 2";
$stmt_ibubapa = $conn->prepare($sql_ibubapa);
$stmt_ibubapa->bind_param("s", $username);
$stmt_ibubapa->execute();
$result_ibubapa = $stmt_ibubapa->get_result();

if ($result_ibubapa->num_rows > 0) {
    // Parent's details found
    $row_ibubapa = $result_ibubapa->fetch_assoc();
    $ibubapa_id = $row_ibubapa['id_ibubapa'];
    // Use the parent's ID for further operations
} else {
    // If no parent details found, log them out and redirect to login
    session_unset();
    session_destroy();
    header('Location: login.php');
    exit();
}
$stmt_ibubapa->close();
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

        /* Card Grid Container */
.stats-container {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
    gap: 25px;
    margin-bottom: 40px;
}

/* Stats Card Styling */
.stats-card {
    background: linear-gradient(135deg, #ffffff 0%, #f8f9fa 100%);
    border-radius: 18px;
    padding: 25px;
    box-shadow: 0 6px 20px rgba(0, 0, 0, 0.08);
    transition: all 0.4s cubic-bezier(0.25, 0.8, 0.25, 1);
    cursor: pointer;
    border: 1px solid rgba(0, 0, 0, 0.03);
    position: relative;
    overflow: hidden;
}

.stats-card:hover {
    transform: translateY(-8px);
    box-shadow: 0 12px 25px rgba(0, 0, 0, 0.15);
}

.stats-card::before {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    width: 100%;
    height: 5px;
    background: linear-gradient(90deg, #4e54c8 0%, #8f94fb 100%);
}

.stats-card h3 {
    margin: 0;
    color: #3a4065;
    font-size: 1.3rem;
    margin-bottom: 15px;
    font-weight: 600;
}

.stats-number {
    font-size: 3rem;
    font-weight: 700;
    color: #4e54c8;
    margin: 15px 0;
    text-shadow: 0 2px 4px rgba(0,0,0,0.05);
}

.stats-card p {
    color: #6c757d;
    font-size: 0.95rem;
    margin: 0;
    padding-top: 10px;
    border-top: 1px solid rgba(0,0,0,0.05);
}

/* Responsive adjustments */
@media (max-width: 768px) {
    .stats-container {
        grid-template-columns: 1fr;
    }
    
    .stats-card {
        padding: 20px;
    }
    
    .stats-number {
        font-size: 2.5rem;
    }
}

        /* Action buttons */
        .action-button {
            display: inline-block;
            padding: 0.5rem 1rem;
            background: #4e54c8;
            color: white;
            text-decoration: none;
            border-radius: 6px;
            font-size: 0.9rem;
            transition: background 0.3s ease;
        }

        .action-button:hover {
            background: #3a4065;
        }

       /* Add this to your existing CSS */
.activity-grid {
    display: grid;
    grid-template-columns: repeat(2, 1fr);
    gap: 25px;
    margin-top: 2rem;
}

.recent-activity {
    margin-top: 0; /* Override the existing margin */
}

/* Responsive adjustment */
@media (max-width: 768px) {
    .activity-grid {
        grid-template-columns: 1fr;
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
          <!-- Main Content -->
        <main>
        <h2 style="text-align: center; margin: 20px 0;">Selamat Datang Ibu Bapa!</h2>
            
        <div class="container">
        <main>
        <div class="stats-container">
        <div class="stats-card">
                    <h3>Pendaftaran Pelajar</h3>
                    <div class="stats-number"><?php
                        $sql = "SELECT COUNT(*) as total FROM pelajar WHERE ibubapa_id = ? AND pengesahan = 1";
                        $stmt = $conn->prepare($sql);
                        $stmt->bind_param("i", $ibubapa_id);
                        $stmt->execute();
                        $result = $stmt->get_result();
                        $row = $result->fetch_assoc();
                        echo $row['total'];
                    ?></div>
                    <div class="stat-label">Jumlah Pelajar Berdaftar</div>
                    <a href="pelajar.php" class="action-button">Senarai Pelajar</a>
                </div>

                <!-- Fee Status Card -->
                <div class="stats-card">
                    <h3>Status Yuran</h3>
                    <div class="stats-number"><?php
                        $current_month = date('n');
                        $sql = "SELECT COUNT(*) as paid FROM yuran y 
                               JOIN pelajar p ON y.ic_pelajar = p.ic_pelajar 
                               WHERE p.ibubapa_id = ? AND y.bulan = ? AND y.status = 'success'";
                        $stmt = $conn->prepare($sql);
                        $stmt->bind_param("ii", $ibubapa_id, $current_month);
                        $stmt->execute();
                        $result = $stmt->get_result();
                        $row = $result->fetch_assoc();
                        echo $row['paid'];
                    ?></div>
                    <div class="stat-label">Yuran Dibayar Bulan Ini</div>
                    <a href="yuran.php" class="action-button">Lihat Yuran</a>
                </div>

                <!-- Reports Card -->
                <div class="stats-card">
                    <h3>Laporan Pelajar</h3>
                    <div class="stats-number"><?php
                        $sql = "SELECT COUNT(*) as total FROM laporan l 
                               JOIN pelajar p ON l.ic_pelajar = p.ic_pelajar 
                               WHERE p.ibubapa_id = ?";
                        $stmt = $conn->prepare($sql);
                        $stmt->bind_param("i", $ibubapa_id);
                        $stmt->execute();
                        $result = $stmt->get_result();
                        $row = $result->fetch_assoc();
                        echo $row['total'];
                    ?></div>
                    <div class="stat-label">Jumlah Laporan</div>
                    <a href="ibubapaLaporan.php" class="action-button">Lihat Laporan</a>
                </div>
            </div>

            <!-- Recent Activity Sections Grid -->
            <div class="activity-grid">
                <!-- Recent Activity Section -->
                <div class="recent-activity">
                    <h3 style="text-align: center; margin: 20px 0;">Aktiviti Terkini</h3>
                    <ul class="activity-list">
                        <?php
                        // Get recent payments
                        $sql = "SELECT y.tarikh, y.status, y.jenis_yuran, p.nama_pelajar 
                               FROM yuran y 
                               JOIN pelajar p ON y.ic_pelajar = p.ic_pelajar 
                               WHERE p.ibubapa_id = ? 
                               ORDER BY y.tarikh DESC LIMIT 5";
                        $stmt = $conn->prepare($sql);
                        $stmt->bind_param("i", $ibubapa_id);
                        $stmt->execute();
                        $result = $stmt->get_result();
                        while ($row = $result->fetch_assoc()) {
                            echo "<li class='activity-item'>";
                            echo "Yuran " . htmlspecialchars($row['jenis_yuran']) . " untuk ";
                            echo htmlspecialchars($row['nama_pelajar']) . " - ";
                            echo "<strong>" . htmlspecialchars($row['status']) . "</strong>";
                            echo " pada " . htmlspecialchars($row['tarikh']);
                            echo "</li>";
                        }
                        ?>
                    </ul>
                </div>

                <!-- Student List Section -->
                <div class="recent-activity">
                    <?php
                    $sql_anak = "
                        SELECT pelajar.nama_pelajar, pelajar.pengesahan
                        FROM pelajar
                        INNER JOIN ibubapa ON pelajar.ibubapa_id = ibubapa.id_ibubapa
                        WHERE ibubapa.id_ibubapa = ?
                        AND (pelajar.pengesahan IS NULL OR pelajar.pengesahan = 0)
                    ";
                    $stmt_anak = $conn->prepare($sql_anak);
                    $stmt_anak->bind_param("i", $ibubapa_id);
                    $stmt_anak->execute();
                    $result_anak = $stmt_anak->get_result();
                    ?>
                   
                    <?php $stmt_anak->close(); ?>
                    <h3 style="text-align: center; margin: 20px 0;">Senarai Anak & Status Pengesahan</h3>
                    <table>
                        <thead>
                            <tr>
                                <th>Nama Anak</th>
                                <th>Status Pengesahan</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if ($result_anak->num_rows > 0): ?>
                                <?php while ($row = $result_anak->fetch_assoc()): ?>
                                    <tr>
                                        <td><?php echo htmlspecialchars($row['nama_pelajar']); ?></td>
                                        <td>
                                            <?php
                                            if (is_null($row['pengesahan'])) {
                                                echo '<span class="status-pending">Pending</span>';
                                            } elseif ($row['pengesahan'] == 1) {
                                                echo '<span class="status-approved">Approved</span>';
                                            } elseif ($row['pengesahan'] == 0) {
                                                echo '<span class="status-rejected">Rejected</span>';
                                            }
                                            ?>
                                        </td>
                                    </tr>
                                <?php endwhile; ?>
                            <?php else: ?>
                                <tr><td colspan="2">Tiada rekod anak.</td></tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </main>
    </div>
</body>
</html>