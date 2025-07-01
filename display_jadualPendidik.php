<?php
include 'config.php';

// Function to convert 24-hour to AM/PM for display
function convertToAmPm($time) {
    return date("g:ia", strtotime($time));
}

// Ambil minggu dari GET, default ke 1 jika tiada
$selected_minggu = isset($_GET['minggu']) ? intval($_GET['minggu']) : 1;
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Jadual Mingguan Pendidik</title>
    <link rel="stylesheet" href="css/dashboard.css" />
    <style>
        /* Table styling */
        table {
            width: 100%;
            border-collapse: collapse;
            margin: 1.5rem 0;
            background: white;
            border-radius: 10px;
            overflow: hidden;
            box-shadow: 0 2px 15px rgba(0, 0, 0, 0.05);
        }

        th, td {
            padding: 1rem;
            text-align: left;
            border-bottom: 1px solid #eee;
        }

        th {
            background: rgba(58, 64, 101, 0.05);
            font-weight: 600;
            color: #3a4065;
            text-transform: uppercase;
        }

        tr:hover {
            background: rgba(58, 64, 101, 0.02);
        }

        /* Week selection styling */
        #minggu-select {
            padding: 0.5rem 1rem;
            border: 1px solid rgba(58, 64, 101, 0.2);
            border-radius: 6px;
            font-size: 1rem;
            color: #3a4065;
            background: white;
            cursor: pointer;
            transition: all 0.3s ease;
        }

        #minggu-select:hover {
            border-color: #4e54c8;
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

        <h2 style="text-align: center; margin: 20px 0;">Jadual Mingguan <?php echo $selected_minggu; ?></h2>
        <!-- Dropdown Pilih Minggu -->
        <form method="GET" action="display_jadualPendidik.php" style="text-align:center; margin-bottom: 20px;">
            <label for="minggu-select" style="margin-right: 10px; font-weight: bold;">Pilih Minggu:</label>
            <select name="minggu" id="minggu-select" onchange="this.form.submit()" 
                    style="padding: 6px 12px; font-size: 1rem; border-radius: 5px; border: 1px solid #ccc; cursor: pointer;">
                <?php
                for ($i = 1; $i <= 4; $i++) {
                    $selected = ($i == $selected_minggu) ? "selected" : "";
                    echo "<option value='$i' $selected>Minggu $i</option>";
                }
                ?>
            </select>
        </form>

        <table>
            <thead>
                <tr>
                    <th>Masa</th>
                    <th>Isnin</th>
                    <th>Selasa</th>
                    <th>Rabu</th>
                    <th>Khamis</th>
                    <th>Jumaat</th>
                </tr>
            </thead>
            <tbody>
                <?php
                // Paparkan data jadual ikut minggu yang dipilih sahaja
                $stmt = $conn->prepare("SELECT Masa, Isnin, Selasa, Rabu, Khamis, Jumaat FROM jadual WHERE minggu = ? ORDER BY STR_TO_DATE(Masa, '%H:%i')");
                $stmt->bind_param("i", $selected_minggu);
                $stmt->execute();
                $result = $stmt->get_result();

                if ($result->num_rows === 0) {
                    echo "<tr><td colspan='6'>Tiada data untuk Minggu $selected_minggu.</td></tr>";
                } else {
                    while ($row = $result->fetch_assoc()) {
                        // Convert time from 24-hour to AM/PM format
                        $formattedTime = convertToAmPm($row['Masa']);
                        echo "<tr>";
                        echo "<td>{$formattedTime}</td>"; // Display formatted time
                        echo "<td>{$row['Isnin']}</td>";
                        echo "<td>{$row['Selasa']}</td>";
                        echo "<td>{$row['Rabu']}</td>";
                        echo "<td>{$row['Khamis']}</td>";
                        echo "<td>{$row['Jumaat']}</td>";
                        echo "</tr>";
                    }
                }
                $stmt->close();
                ?>
            </tbody>
        </table>
        </main>
    </div>
    
</body>
</html>
