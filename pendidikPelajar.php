<?php
// Start the session
session_start();
include 'config.php';  // Include the configuration file for the database connection

// Check if the educator is logged in
if (!isset($_SESSION['username'])) {
    header('Location: login.php');
    exit;
}

// Get the educator's ID (id_pendidik) from the session
$id_pendidik = $_SESSION['id_pendidik'];

// Query to get the list of students assigned to this educator
$query = "
    SELECT nama_pelajar, ic_pelajar, ibubapa_id
    FROM pelajar
    WHERE id_pendidik = ?
";

// Prepare and execute the query
$stmt = $conn->prepare($query);

// Check if the prepare statement failed
if ($stmt === false) {
    // Output the error message if the query fails
    die('Error preparing query: ' . $conn->error);
}

$stmt->bind_param("i", $id_pendidik);
$stmt->execute();
$result = $stmt->get_result();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <title>Pelajar</title>
    <link rel="stylesheet" href="css/dashboard.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <style>
        /* Table Styling */
        table {
            width: 100%;
            margin: 20px auto;
            border-collapse: collapse;
            font-size: 16px;
        }

        table th, table td {
            padding: 15px;
            text-align: center;
            border: 1px solid #ddd;
        }

        table th {
            background-color: #3a4065;
            color: white;
        }

        table tbody tr:nth-child(even) {
            background-color: #f9f9f9;
        }

        table tbody tr:hover {
            background-color: #f1f1f1;
        }

        table td {
            color: #333;
        }

        table td a {
            text-decoration: none;
            color: #007bff;
        }

        table td a:hover {
            color: #0056b3;
        }

         /* View button hover effects */
            .btn-view {
                transition: all 0.3s ease;
            }
            
            .btn-view:hover {
                background-color: #0056b3 !important;
                transform: translateY(-2px);
                box-shadow: 0 4px 8px rgba(0,0,0,0.1);
            }
            
            .btn-view:active {
                transform: translateY(0);
            }

        /* Profile Container */
        .profile-container {
            width: 80%;
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

        .profile-card {
            margin-bottom: 20px;
            padding: 20px;
            background: #f9f9f9;
            border-radius: 8px;
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

        .dropdown li a:hover {
            background: rgba(58, 64, 101, 0.1);
            color: #4e54c8;
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
    <h2 style="text-align: center; margin: 20px 0;">Senarai Nama Pelajar</h2>

    <?php if ($result->num_rows > 0): ?>
        <table class="table table-bordered" style="width: 80%; margin: 20px auto; border-collapse: collapse; font-size: 16px;">
            <thead>
                <tr>
                    <th style="padding: 15px; text-align: center; background-color: #3a4065; color: white;">No</th>
                    <th style="padding: 15px; text-align: center; background-color: #3a4065; color: white;">Nama Pelajar</th>
                    <th style="padding: 15px; text-align: center; background-color: #3a4065; color: white;">Tindakan</th>
                </tr>
            </thead>
            <tbody>
                <?php 
                $no = 1;
                while ($row = $result->fetch_assoc()): ?>
                    <tr>
                        <td style="padding: 15px; text-align: center; border: 1px solid #ddd;"><?php echo $no++; ?></td>
                        <td style="padding: 15px; text-align: center; border: 1px solid #ddd;"><?php echo $row['nama_pelajar']; ?></td>
                        <td style="padding: 15px; text-align: center; border: 1px solid #ddd;">
                        <a href='pendidik_viewPelajar.php?ic_pelajar=<?php echo $row['ic_pelajar']; ?>&ibubapa_id=<?php echo $row['ibubapa_id']; ?>'>
                            <button class='btn-view' title='View' style='background-color: #007bff; color: white; border: none; padding: 10px 20px; border-radius: 5px; font-size: 16px; cursor: pointer;'>
                                <i class='fas fa-eye'></i>
                            </button>
                        </a>
                        </td>
                    </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    <?php else: ?>
        <p>Tiada pelajar yang di daftarkan.</p>
    <?php endif; ?>
</main>

    </div>
</body>
</html>

<?php
// Close the statement and connection
$stmt->close();
$conn->close();
?>