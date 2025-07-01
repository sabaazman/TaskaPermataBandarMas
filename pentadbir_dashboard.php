<?php
require_once 'config.php';

// Get total approved students
$sql_pelajar = "SELECT COUNT(*) as total, 
                      SUM(CASE WHEN jantina = 'Lelaki' THEN 1 ELSE 0 END) as lelaki,
                      SUM(CASE WHEN jantina = 'Perempuan' THEN 1 ELSE 0 END) as perempuan 
               FROM pelajar WHERE pengesahan = 1";
$result_pelajar = $conn->query($sql_pelajar);
$pelajar_stats = $result_pelajar->fetch_assoc();

// Get total approved parents
$sql_ibubapa = "SELECT COUNT(*) as total FROM ibubapa WHERE pengesahan = 1";
$result_ibubapa = $conn->query($sql_ibubapa);
$total_ibubapa = $result_ibubapa->fetch_assoc()['total'];

// Get total approved teachers
$sql_pendidik = "SELECT COUNT(*) as total FROM pendidik WHERE pengesahan = 1";
$result_pendidik = $conn->query($sql_pendidik);
$total_pendidik = $result_pendidik->fetch_assoc()['total'];
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pentadbir Dashboard</title>
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

    /* Chart Container */
    .chart-container {
        background: linear-gradient(135deg, #ffffff 0%, #f8f9fa 100%);
        border-radius: 18px;
        padding: 30px;
        box-shadow: 0 6px 20px rgba(0, 0, 0, 0.08);
        margin-top: 30px;
        display: none;
        border: 1px solid rgba(0, 0, 0, 0.03);
    }

    #genderChart {
        max-width: 600px;
        margin: 0 auto;
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

    </style>
</head>
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

        <!-- Main Content -->
        <main>
            
            <!-- Statistics Cards -->
            <div class="stats-container">
                <div class="stats-card" id="pelajarCard">
                    <h3>Jumlah Pelajar</h3>
                    <div class="stats-number"><?php echo $pelajar_stats['total']; ?></div>
                    <p>Pelajar yang disahkan</p>
                </div>
                
                <div class="stats-card">
                    <h3>Jumlah Ibu Bapa</h3>
                    <div class="stats-number"><?php echo $total_ibubapa; ?></div>
                    <p>Ibu bapa yang disahkan</p>
                </div>
                
                <div class="stats-card">
                    <h3>Jumlah Pendidik</h3>
                    <div class="stats-number"><?php echo $total_pendidik; ?></div>
                    <p>Pendidik yang disahkan</p>
                </div>
            </div>

            <!-- Gender Distribution Chart -->
            <div class="chart-container" id="chartContainer">
                <canvas id="genderChart"></canvas>
            </div>
        </main>
    </div>

    <script>
    // Initialize chart with student gender data
    const genderData = {
        labels: ['Lelaki', 'Perempuan'],
        datasets: [{
            data: [<?php echo $pelajar_stats['lelaki']; ?>, <?php echo $pelajar_stats['perempuan']; ?>],
            backgroundColor: ['#4e54c8', '#ff6b6b'],
        }]
    };

    let chart = null;

    // Add click event to pelajar card
    document.getElementById('pelajarCard').addEventListener('click', function() {
        const chartContainer = document.getElementById('chartContainer');
        
        if (chartContainer.style.display === 'none' || chartContainer.style.display === '') {
            chartContainer.style.display = 'block';
            
            if (!chart) {
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
            chartContainer.style.display = 'none';
        }
    });
    </script>
    
</body>
</html>


