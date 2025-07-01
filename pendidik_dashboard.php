<?php
// Start the session
session_start();

require_once 'config.php';

// Check if the educator is logged in
if (!isset($_SESSION['id_pendidik'])) {
    header('Location: login.php'); // Redirect to login if not logged in
    exit;
}

// Get the educator's ID (id_pendidik) from session
$id_pendidik = $_SESSION['id_pendidik'];

// Get total students assigned to the educator
$sql_students = "SELECT COUNT(*) as total, 
                        SUM(CASE WHEN jantina = 'Lelaki' THEN 1 ELSE 0 END) as lelaki,
                        SUM(CASE WHEN jantina = 'Perempuan' THEN 1 ELSE 0 END) as perempuan 
                FROM pelajar WHERE id_pendidik = ?";

$stmt = $conn->prepare($sql_students);
$stmt->bind_param("i", $id_pendidik);
$stmt->execute();
$result_students = $stmt->get_result();
$student_stats = $result_students->fetch_assoc();

// Close the statement
$stmt->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard Pendidik</title>
    <link rel="stylesheet" href="css/dashboard.css">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
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

        h2 {
            color: #3a4065;
            margin-top: 0;
            font-size: 1.8rem;
        }

        h3 {
            color: #4a4a4a;
            margin: 1.5rem 0 1rem;
        }
        /* Stats Card Styling */
        .stats-container {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
            gap: 25px;
            margin-bottom: 25px; /* Diubah dari 40px ke 25px */
        }
        
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

            flex-direction: column;      /* Ditambah */
            align-items: center;         /* Ditambah */
            text-align: center;          /* Ditambah */
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
        
        /* Chart Container - Updated */
        .chart-container {
            background: linear-gradient(135deg, #ffffff 0%, #f8f9fa 100%);
            border-radius: 18px;
            padding: 30px;
            box-shadow: 0 6px 20px rgba(0, 0, 0, 0.08);
            margin-top: 0; /* Diubah dari 30px ke 0 */
            margin-bottom: 30px; /* Ditambahkan */
            display: none;
            border: 1px solid rgba(0, 0, 0, 0.03);
            width: 94%;
            grid-column: 1 / -1; /* Baru: Memastikan chart mengambil lebar penuh */
        }
        
        #genderChart {
            max-width: 600px;
            margin: 0 auto;
        }
    </style>
</head>
<body>
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

         <!-- Main Content -->
        <main>
            <h2 style="text-align: center; margin: 20px 0;">Selamat Datang, Pendidik!</h2>
            <!-- Statistics Cards -->
            <div class="stats-container">
                <div class="stats-card" id="pelajarCard">
                    <h3>Jumlah Pelajar</h3>
                    <div class="stats-number"><?php echo $student_stats['total']; ?></div>
                    <p>Pelajar yang di bawah pendidik ini</p>
                </div>
            </div>
            
            <!-- Chart Container (dipindahkan di bawah stats-container) -->
            <div class="chart-container" id="chartContainer">
                <canvas id="genderChart"></canvas>
            </div>
        </main>
    </div>

    <script>
        // Gender distribution chart data
        const genderData = {
            labels: ['Lelaki', 'Perempuan'],
            datasets: [{
                data: [<?php echo $student_stats['lelaki']; ?>, <?php echo $student_stats['perempuan']; ?>],
                backgroundColor: ['#4e54c8', '#ff6b6b'],
            }]
        };

        let chart = null;

        // Event listener to show chart on card click
        document.getElementById('pelajarCard').addEventListener('click', function() {
            const chartContainer = document.getElementById('chartContainer');

            // Toggle chart visibility
            if (chartContainer.style.display === 'none' || chartContainer.style.display === '') {
                chartContainer.style.display = 'block'; // Show chart

                if (!chart) {
                    // Create the chart if not already created
                    const ctx = document.getElementById('genderChart').getContext('2d');
                    chart = new Chart(ctx, {
                        type: 'pie',
                        data: genderData,
                        options: {
                            responsive: true,
                            plugins: {
                                title: {
                                    display: true,
                                    text: 'Taburan Jantina Pelajar',
                                    font: {
                                        size: 16
                                    }
                                },
                                legend: {
                                    position: 'bottom'
                                }
                            }
                        }
                    });
                }
            } else {
                chartContainer.style.display = 'none'; // Hide chart
            }
        });
    </script>
</body>
</html>