<?php
include 'config.php'; // Include the database configuration
session_start(); // Start the session to store user data

// Ensure the user is logged in and has a valid ID
if (!isset($_SESSION['id_pendidik'])) {
    // Redirect to login if not logged in
    header("Location: login.php");
    exit();
}

$id_pendidik = $_SESSION['id_pendidik']; // Get the logged-in educator's ID

// Fetch data from the database
$search_query = "";
if (isset($_POST['search'])) {
    $search_query = $_POST['search'];
}

// Modify SQL query to filter by educator's ID (id_pendidik) and include search functionality
$sql = "SELECT * FROM rpa WHERE (tajuk LIKE '%$search_query%' OR tarikh LIKE '%$search_query%') AND id_pendidik = '$id_pendidik' ORDER BY tarikh DESC";
$result = $conn->query($sql);
?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>RPA  Pendidik</title>
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
/* Enhanced Search and Button Styling */
.top-bar {
    width: 80%;
    margin: 0 auto 25px;
    display: flex;
    justify-content: space-between;
    align-items: center;
    gap: 15px;
}

.search-wrapper {
    display: flex;
    flex-grow: 1;
    max-width: 400px;
    position: relative;
}

.search-wrapper input {
    padding: 8px 15px 8px 35px;
    font-size: 14px;
    border: 2px solid #e0e0e0;
    border-radius: 25px;
    outline: none;
    flex-grow: 1;
    height: 40px;
    box-sizing: border-box;
    transition: all 0.3s ease;
    background-color: #f8f9fa;
}

.search-wrapper input:focus {
    border-color: #3a4065;
    background-color: white;
    box-shadow: 0 0 0 2px rgba(106, 13, 173, 0.2);
}

.search-wrapper::before {
    content: "\f002";
    font-family: "Font Awesome 5 Free";
    font-weight: 900;
    position: absolute;
    left: 12px;
    top: 50%;
    transform: translateY(-50%);
    color: #3a4065;
    font-size: 14px;
}

.search-button {
    display: none; /* Hide the button since we're using the icon */
}

.add-button {
    background-color: #3a4065;
    color: white;
    font-size: 20px;
    width: 36px;
    height: 36px;
    border-radius: 50%;
    text-decoration: none;
    display: flex; /* Changed to flex */
    align-items: center;
    justify-content: center; /* Added this */
    transition: all 0.3s ease;
    border: none;
    cursor: pointer;
    box-shadow: 0 2px 5px rgba(0,0,0,0.1);
    line-height: 1; /* Ensures proper vertical alignment */
    padding: 0; /* Remove any padding */
}

.add-button:hover {
    background-color: #2c2f4d;
    transform: scale(1.05);
    box-shadow: 0 3px 8px rgba(0,0,0,0.15);
}
/* Table Styling */
table {
    width: 80%;
    margin: 20px auto;
    border-collapse: collapse;
    font-size: 16px;
}

th, td {
    padding: 12px 15px;
    text-align: center;
    border: 1px solid #ddd;
}

th {
    background-color: #3a4065;
    color: white;
    font-weight: 600;
}

td {
    vertical-align: middle;
}

/* Center the first column (No) content */
td:first-child {
    text-align: center;
}

/* Make RPA title links look better */
td a {
    color: #3a4065;
    text-decoration: none;
    transition: color 0.3s;
    display: block;
}

td a:hover {
    color: #2c2f4d;
    text-decoration: underline;
}

/* Responsive adjustments */
@media (max-width: 768px) {
    .top-bar {
        flex-direction: column;
        width: 95%;
        gap: 10px;
    }
    
    .search-wrapper {
        width: 100%;
        max-width: none;
    }
    
    table {
        width: 100%;
        font-size: 14px;
    }
    
    th, td {
        padding: 10px 8px;
    }
    
    .add-button {
        width: 100%;
        justify-content: center;
    }
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

    <!-- Main Content -->
    <main>
            <h2 style="text-align: center; margin: 20px 0;">Rancangan Pelaksanaan Aktiviti (RPA)</h2>

                <div class="top-bar">
                    <form method="POST" class="search-wrapper">
                        <input type="text" name="search" placeholder="Search by Tajuk or Tarikh" value="<?php echo $search_query; ?>">
                        <button type="submit" class="search-button">Search</button>
                    </form>

                    <a href="pendidikRPA.php" class="add-button">+</a>
                </div>

                <!-- Table displaying RPA records -->
                <table>
                    <thead>
                        <tr>
                            <th>No</th>
                            <th>RPA</th>
                            <th>Tarikh</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        if ($result->num_rows > 0) {
                            $no = 1;
                            while ($row = $result->fetch_assoc()) {
                                echo "<tr>";
                                echo "<td>" . $no++ . "</td>";
                                // Make the RPA title clickable
                                echo "<td style='text-align: center;'>
                                <a href='displayRPA.php?id=" . $row['id_RPA'] . "' style='color: #333; text-decoration: none;'>
                                    " . htmlspecialchars($row['tajuk']) . "
                                </a>
                              </td>";
                        echo "<td style='text-align: center;'>" . $row['tarikh'] . "</td>";
                        echo "</tr>";
                            }
                        } else {
                            echo "<tr><td colspan='3'>No records found</td></tr>";
                        }
                        ?>
                    </tbody>
                </table>
        </main>
    </div>
</body>
</html>