<?php

include 'config.php'; // Use config.php for database connection
session_start(); // Start the session to store user data

// Ensure the user is logged in and has a valid ID
if (!isset($_SESSION['username'])) {
    // Redirect to login if not logged in
    header("Location: login.php");
    exit();
}

$id_pendidik = $_SESSION['id_pendidik']; // Get the logged-in educator's ID

// Query to get the list of students assigned to this educator directly from pelajar
$query = "
    SELECT ic_pelajar, nama_pelajar
    FROM pelajar
    WHERE id_pendidik = ?"; // Use ? for parameterized query

// Prepare and execute the query
$stmt = $conn->prepare($query);

// Check if the prepare statement failed
if ($stmt === false) {
    // Output the error message if the query fails
    die('Error preparing query: ' . $conn->error);
}

$stmt->bind_param("i", $id_pendidik); // Bind the educator's ID to the query
$stmt->execute();
$result = $stmt->get_result();

?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pendidik Laporan</title>
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
        h2 {
            font-size: 24px;
            text-align: center;
            margin-bottom: 10px;
        }
       /* Override dashboard.css table styles for this specific table */
       .dashboard-container table.custom-table {
            width: 800px !important;
            max-width: 900px !important;
            margin: 20px auto !important;
            border-collapse: collapse;
            font-size: 16px;
            table-layout: auto !important;
            text-align: center;
            box-shadow: 0 2px 15px rgba(0, 0, 0, 0.449);
            border-radius: 8px;
            overflow: hidden;
            background-color: white;
        }
        .dashboard-container table.custom-table thead tr {
            background-color: #3a4065;
            color: #ffffff;
            font-weight: bold;
            text-align: center;
        }
        .dashboard-container table.custom-table th,
        .dashboard-container table.custom-table td {
            padding: 12px 15px;
            border: 1px solid #ddd;
            text-align: center;
        }
        .dashboard-container table.custom-table tbody tr:nth-child(even) {
            background-color: #f3f3f3;
        }
        .dashboard-container table.custom-table tbody tr:hover {
            background-color: #f1f1f1;
        }
        .col-no {
            width: 50px;
            text-align: center;
        }
        td a {
            display: inline-block;
            margin: 0;
            text-decoration: none;
            color: white;
            padding: 10px 20px;
            border-radius: 5px;
            border: none;
            cursor: pointer;
        }
       /* Button Styles */
    a.laporan-btn, a.view-btn {
        padding: 10px;
        border-radius: 5px;
        text-decoration: none;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        width: 25px; /* Fixed width */
        height: 25px; /* Fixed height */
        transition: all 0.3s ease;
    }

    a.laporan-btn {
        background-color: #28a745; /* Green */
        color: white;
        margin-right: 5px; /* Space between buttons */
    }

    a.view-btn {
        background-color: #007bff; /* Blue */
        color: white;
    }

    a.laporan-btn:hover {
        background-color: #218838; /* Darker green */
    }

    a.view-btn:hover {
        background-color: #0056b3; /* Darker blue */
    }

    /* Icon Styles */
    .fas {
        font-size: 16px;
    }

    .col-no {
        width: 50px;
        text-align: center;
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
            <h2 style="text-align: center; margin: 20px 0;">Senarai Pelajar</h2>
            <table class="custom-table">
                <thead>
                    <tr>
                        <th class="col-no">No</th>
                        <th>Nama Pelajar</th>
                        <th>Tindakan</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if ($result && $result->num_rows > 0): ?>
                        <?php 
                        $no = 1;
                        while ($row = $result->fetch_assoc()): ?>
                            <tr>
                                <td class="col-no"><?php echo $no++; ?></td>
                                <td><?php echo htmlspecialchars($row['nama_pelajar']); ?></td>
                                <td style="text-align: center;">
                                    <a href="pendidik_laporan.php?id_pelajar=<?php echo $row['ic_pelajar']; ?>" 
                                    class="laporan-btn" 
                                    title="Laporan">
                                        <i class="fas fa-file-alt"></i>
                                    </a>
                                    <a href="pendidikLaporan.php?id_pelajar=<?php echo $row['ic_pelajar']; ?>" 
                                    class="view-btn" 
                                    title="View">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                </td>
                            </tr>
                        <?php endwhile; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="3" style="text-align:center;">Tiada pelajar yang telah didaftarkan.</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </main>
    </div>
</body>
</html>

<?php
$conn->close();
?>