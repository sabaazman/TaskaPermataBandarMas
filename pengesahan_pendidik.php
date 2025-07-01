<?php
include 'config.php';

// Get the selected filter value
$filter_status = isset($_GET['filter_status']) ? $_GET['filter_status'] : '';

// Build the SQL query based on the filter
$sql = "SELECT id_pendidik, ic_pendidik, nama_pendidik, email_pendidik, no_pendidik, umur, alamat_pendidik, sijil_pengajian, kursus_kanak_kanak, pengesahan 
        FROM pendidik";

// Apply the filter if any
if ($filter_status == 'Approved') {
    $sql .= " WHERE pengesahan = 1"; // Approved
} elseif ($filter_status == 'Rejected') {
    $sql .= " WHERE pengesahan = 0"; // Rejected
} elseif ($filter_status == 'Pending') {
    $sql .= " WHERE pengesahan IS NULL"; // Pending
} elseif ($filter_status == 'All' || $filter_status == '') {
    // No WHERE clause needed for All
}

// Add an ORDER BY clause to sort by name
$sql .= " ORDER BY nama_pendidik ASC";

$result = $conn->query($sql);

// Check query success
if (!$result) {
    die("Error in query: " . $conn->error);
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="css/dashboard.css">
    <title>Pengesahan Pelajar</title>
    <style>
        /* Styling the main table */
        table {
            width: 80%;  /* Reduced table width for a more compact view */
            border-collapse: collapse;
            margin: 20px auto;  /* Center the table on the page */
            font-size: 15px;
            text-align: center; /* Centering the table content */
        }

        table thead tr {
            background-color: #3a4065;
            color: #ffffff;
            font-weight: bold;
        }

        table th, table td {
            padding: 8px 12px;  /* Adjusted padding for smaller table */
            border: 1px solid #ddd;
        }

        table tbody tr:nth-child(even) {
            background-color: #f3f3f3;
        }

        table tbody tr:hover {
            background-color: #f1f1f1;
        }

        td a {
            display: block;  /* Stack buttons vertically */
            margin: 3px 0;    /* Add some space between buttons */
            text-decoration: none;
        }

        button {
            padding: 2px 4px;  /* Smaller padding for the buttons */
            font-size: 15px;   /* Smaller font size */
            border: none;
            border-radius: 3px;
            cursor: pointer;
            color: #fff;
            width: 100px;       /* Adjust button width to fit content */
            margin: 2px 0;     /* Adjust margin between buttons */
        }

        /* Styling for the approve/reject buttons */
        a button.btn-approve {
            background-color: #28a745 !important;
        }

        a button.btn-reject {
            background-color: #dc3545 !important;
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
        /* Styling the filter section */
        .filter-container {
            margin: 20px;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .filter-container select, .filter-container button {
            padding: 10px 15px;
            margin-right: 10px;
            font-size: 14px;
            border-radius: 5px;
        }
        .filter-container button {
            background-color: #007bff;
            color: white;
            border: none;
        }
        .filter-container button:hover {
            background-color: #0056b3;
        }
        td a {
            color: black; /* Set link color to black */
            text-decoration: none; /* Remove underline from the link */
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
                            <li><a href="sub_option2.php">Pendidik</a></li>
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
    <h1 style="text-align:center; margin-top:30px;">Maklumat Pengesahan Pendidik</h1>
    <form method="GET" action="pengesahan_pendidik.php" class="filter-container">
        <label for="filter_status" style="margin-right: 10px;">Filter by Status:</label>
        <select name="filter_status">
            <option value="All" <?php if(!isset($_GET['filter_status']) || $_GET['filter_status'] == 'All') echo 'selected'; ?>>All</option>
            <option value="Pending" <?php if(isset($_GET['filter_status']) && $_GET['filter_status'] == 'Pending') echo 'selected'; ?>>Pending</option>
            <option value="Approved" <?php if(isset($_GET['filter_status']) && $_GET['filter_status'] == 'Approved') echo 'selected'; ?>>Approved</option>
            <option value="Rejected" <?php if(isset($_GET['filter_status']) && $_GET['filter_status'] == 'Rejected') echo 'selected'; ?>>Rejected</option>
        </select>
        <button type="submit">Filter</button>
    </form>
    <table>
        <thead>
            <tr>
                <th>No</th>
                <th>Nama Pendidik</th>
                <th>Status Pengesahan</th>
                <th>Tindakan</th>
            </tr>
        </thead>
        <tbody>
<?php
if ($result->num_rows > 0) {
    $no = 1;
    while ($row = $result->fetch_assoc()) {
        $status = $row['pengesahan'];
        echo "<tr style='text-align: center;'>
                <td style='text-align: center;'>$no</td>
                <td style='text-align: center;'><a href='profilPendidikAdmin.php?ic=" . $row['ic_pendidik'] . "'>" . $row['nama_pendidik'] . "</a></td>
                <td style='text-align: center;'>";
        if (is_null($status)) {
            echo "Pending";
        } elseif ($status == 1) {
            echo "Approved";
        } elseif ($status == 0) {
            echo "Rejected";
        }
        echo "</td>
                <td style='text-align: center;'>";
        
        // Only show buttons if status is NULL (pending)
        if (is_null($status)) {
            echo "<a href='approve_reject.php?action=approve&id=" . $row['id_pendidik'] . "&type=pendidik'>
                    <button style='background-color: #28a745; color: white; padding: 10px 20px; border: none; border-radius: 5px; font-size: 16px; cursor: pointer; margin: 0 5px;'>Approve</button>
                  </a>
                  <a href='approve_reject.php?action=reject&id=" . $row['id_pendidik'] . "&type=pendidik'>
                    <button style='background-color: #dc3545; color: white; padding: 10px 20px; border: none; border-radius: 5px; font-size: 16px; cursor: pointer; margin: 0 5px;'>Reject</button>
                  </a>";
        } else {
            echo "<span>Tidak memerlukan tindakan</span>";
        }
        
        echo "</td>
              </tr>";
        $no++;
    }
} else {
    echo "<tr><td colspan='4' style='text-align: center;'>Tiada rekod ditemui.</td></tr>";
}
?>
        </tbody>
    </table>
</main>