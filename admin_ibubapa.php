<?php
include 'config.php';

// Handle delete request
if (isset($_GET['delete'])) {
    $ic = $_GET['delete'];

    // Validate IC before proceeding
    if (!empty($ic)) {
        $sql = "DELETE FROM ibubapa WHERE ic_bapa = '$ic'";
        if ($conn->query($sql) === TRUE) {
            echo "Record deleted successfully.";
        } else {
            echo "Error deleting record: " . $conn->error;
        }
    } else {
        echo "Invalid IC.";
    }
}

// Fetch parent data
$sql = "SELECT * FROM ibubapa";
$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="css/dashboard.css">
    <title>Pentadbir - Ibu Bapa</title>
    <style>
        table {
            width: 100%;
            border-collapse: collapse;
        }
        th, td {
            padding: 8px;
            border: 1px solid #ddd;
            text-align: center;
        }
        th {
            background-color: #f4f4f4;
        }
        button {
            padding: 5px 10px;
            color: white;
            background-color: red;
            border: none;
            cursor: pointer;
        }
        button:hover {
            opacity: 0.8;
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
                        <li><a href="admin_pelajar.php">Pelajar</a></li>
                        <li><a href="admin_pendidik.php">Pendidik</a></li>
                    </ul>
                </li>
                <li><a href="#">Yuran</a></li>
                <li><a href="admin_jadual.php">Jadual Pelajar</a></li>
                <li><a href="pentadbirRPA.php">RPA</a></li>
                <li><a href="pentadbirLaporan.php">Laporan</a></li>
            </ul>
        </nav>
    <h2>Senarai Ibu Bapa</h2>
    <table>
        <thead>
            <tr>
                <th>No</th>
                <th>No Kad Pengenalan Bapa</th>
                <th>Nama Bapa</th>
                <th>Pekerjaan Bapa</th>
                <th>Pendapatan Bapa</th>
                <th>Email Bapa</th>
                <th>No Telefon Bapa</th>
                <th>No Kad Pengenalan Ibu</th>
                <th>Nama Ibu</th>
                <th>Pekerjaan Ibu</th>
                <th>Pendapatan Ibu</th>
                <th>Email Ibu</th>
                <th>No Telefon Ibu</th>
                <th>Action</th>
            </tr>
        </thead>
        <tbody>
            <?php
            if ($result->num_rows > 0) {
                $no = 1;
                while ($row = $result->fetch_assoc()) {
                    echo "<tr>
                        <td>$no</td>
                        <td>{$row['ic_bapa']}</td>
                        <td>{$row['nama_bapa']}</td>
                        <td>{$row['pekerjaan_bapa']}</td>
                        <td>{$row['pendapatan_bapa']}</td>
                        <td>{$row['email_bapa']}</td>
                        <td>{$row['no_bapa']}</td>
                        <td>{$row['ic_ibu']}</td>
                        <td>{$row['nama_ibu']}</td>
                        <td>{$row['pekerjaan_ibu']}</td>
                        <td>{$row['pendapatan_ibu']}</td>
                        <td>{$row['no_ibu']}</td>
                        <td>{$row['EmailIbu']}</td>
                        <td><a href='?delete={$row['ic_bapa']}'><button>Delete</button></a></td>
                    </tr>";
                    $no++;
                }
            } else {
                echo "<tr><td colspan='8'>No records found.</td></tr>";
            }
            ?>
        </tbody>
    </table>
    <style>
        /* Dropdown styling */
        nav ul {
            list-style-type: none;
            padding: 0;
            margin: 0;
        }

        nav ul li {
            position: relative;
        }

        nav ul li a {
            text-decoration: none;
            color: #000;
            display: flex;
            align-items: center;
        }

        nav ul li .dropdown-icon {
            margin-left: 5px;
            font-size: 6px;
        }

        nav ul .dropdown {
            display: none;
            position: absolute;
            left: 0;
            top: 100%;
            background-color: #fff;
            box-shadow: 0px 4px 6px rgba(0, 0, 0, 0.1);
            padding: 0;
            margin: 0;
            list-style-type: none;
            border-radius: 5px;
            overflow: hidden;
        }

        nav ul .dropdown li {
            padding: 10px;
            display: flex;
            align-items: center;
        }

        nav ul li:hover .dropdown {
            display: block;
        }

        nav ul .dropdown li a {
            text-decoration: none;
            color: #000;
            display: flex;
            align-items: center;
        }

        nav ul .dropdown li a:hover {
            background-color: #f0f0f0;
        }

        .dropdown-parent > a {
            cursor: pointer;
        }
    </style>
</body>
</html>
