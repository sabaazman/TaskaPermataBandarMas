<?php
session_start();
require_once 'config.php'; // Database connection

if (!isset($_SESSION['username'])) {
    header("Location: login.php");
    exit();
}

// Fetch educator data using the email_pendidik as the username
$username = $_SESSION['username']; 
$query = "
    SELECT 
        pendidik.ic_pendidik, 
        pendidik.nama_pendidik, 
        pendidik.email_pendidik, 
        pendidik.no_pendidik,
        pendidik.umur,
        pendidik.alamat_pendidik,
        pendidik.sijil_pengajian,
        pendidik.kursus_kanak_kanak
    FROM pendidik
    WHERE pendidik.email_pendidik = ?
";

$stmt = $conn->prepare($query);
if (!$stmt) {
    die("Prepare statement failed: " . $conn->error);
}

$stmt->bind_param("s", $username);
$stmt->execute();
$result = $stmt->get_result();
$data = $result->fetch_assoc();

if (!$data) {
    die("No data found for the logged-in user.");
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <title>Profil Pendidik</title>
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

        /* Profile container styling */
        .profile-container {
            width: 80%;
            margin: 20px auto;
            background: #fff;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.05);
        }

        .profile-container h1 {
            font-weight: bold;
            text-align: center;
            margin-bottom: 20px;
            color: #3a4065;
        }

       /* Table styling adjustments */
        table {
            width: 50%;
            border-collapse: collapse;
            margin: 1rem 0;
            background: white;
            border-radius: 8px;
            overflow: hidden;
            table-layout: fixed; /* Add this for consistent column widths */
        }

        table th,
        table td {
            padding: 0.75rem 1rem; /* Reduced padding */
            text-align: left;
            border-bottom: 1px solid #eee;
            vertical-align: top; /* Better alignment for multi-line content */
        }

        table th {
            background: rgba(58, 64, 101, 0.05);
            font-weight: 600;
            color: #3a4065;
            width: 30%; /* Set width for headers */
            min-width: 150px; /* Minimum width to prevent squeezing */
        }

        table td {
            width: 70%; /* Set width for data cells */
            word-break: break-word; /* Handle long content gracefully */
        }

        .crud-buttons {
            display: flex;
            justify-content: center; /* Center the button horizontally */
            align-items: center; /* Center the button vertically if needed */
            margin-top: 20px;
        }

        .crud-buttons button {
            padding: 10px 20px;
            background:rgb(61, 61, 61);
            color: white;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            transition: all 0.3s ease;
        }

        .crud-buttons button:hover {
            background:rgb(141, 142, 145);
        }

        /* Modal styling */
        .modal-content {
            border-radius: 8px;
        }

        .modal-header {
            background: #3a4065;
            color: white;
            border-radius: 8px 8px 0 0;
        }

        .modal-title {
            color: white;
        }

        .btn-close {
            color: white;
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

            .profile-container {
                width: 95%;
                padding: 15px;
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
        <div class="profile-container">
        <h1>Maklumat Profil Pendidik</h1>

<div class="profile-card">

    <table>
        <tr>
            <th>No MyKad Pendidik</th>
            <td><?php echo htmlspecialchars($data['ic_pendidik']); ?></td>
        </tr>
        <tr>
            <th>Nama Pendidik</th>
            <td><?php echo htmlspecialchars($data['nama_pendidik']); ?></td>
        </tr>
        <tr>
            <th>Email Pendidik</th>
            <td><?php echo htmlspecialchars($data['email_pendidik']); ?></td>
        </tr>
        <tr>
            <th>No Telefon Pendidik</th>
            <td><?php echo htmlspecialchars($data['no_pendidik']); ?></td>
        </tr>
        <tr>
            <th>Umur</th>
            <td><?php echo htmlspecialchars($data['umur']); ?></td>
        </tr>
        <tr>
            <th>Alamat Pendidik</th>
            <td><?php echo htmlspecialchars($data['alamat_pendidik']); ?></td>
        </tr>
    </table>
</div>

<div class="crud-buttons">
    <button class="btn-edit"
            data-ic-pendidik="<?php echo htmlspecialchars($data['ic_pendidik']); ?>"
            data-email-pendidik="<?php echo htmlspecialchars($data['email_pendidik']); ?>"
            data-no-pendidik="<?php echo htmlspecialchars($data['no_pendidik']); ?>"
            data-umur="<?php echo htmlspecialchars($data['umur']); ?>"
            data-alamat-pendidik="<?php echo htmlspecialchars($data['alamat_pendidik']); ?>">
        Kemaskini
    </button>
</div>

<!-- Modal -->
<div class="modal fade" id="editModal" tabindex="-1" aria-labelledby="editModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="editModalLabel">Kemaskini Profil Pendidik</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="editForm" method="POST" action="edit_profil_pendidik.php" enctype="multipart/form-data">
                <div class="modal-body">
                    <input type="hidden" id="ic_pendidik" name="ic_pendidik">
                    <div class="mb-3">
                        <label for="email_pendidik" class="form-label">Email</label>
                        <input type="email" class="form-control" id="email_pendidik" name="email_pendidik" required>
                    </div>
                    <div class="mb-3">
                        <label for="no_pendidik" class="form-label">No Telefon</label>
                        <input type="text" class="form-control" id="no_pendidik" name="no_pendidik" required>
                    </div>
                    <div class="mb-3">
                        <label for="umur" class="form-label">Umur</label>
                        <input type="number" class="form-control" id="umur" name="umur" required>
                    </div>
                    <div class="mb-3">
                        <label for="alamat_pendidik" class="form-label">Alamat</label>
                        <input type="text" class="form-control" id="alamat_pendidik" name="alamat_pendidik" required>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="submit" class="btn btn-primary" style="background-color: #28a745; color: white; border: none; padding: 10px 20px; border-radius: 5px; font-size: 16px; cursor: pointer;">Simpan</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        const editButton = document.querySelector('.btn-edit');
        editButton.addEventListener('click', function () {
            document.getElementById('ic_pendidik').value = this.getAttribute('data-ic-pendidik');
            document.getElementById('email_pendidik').value = this.getAttribute('data-email-pendidik');
            document.getElementById('no_pendidik').value = this.getAttribute('data-no-pendidik');
            document.getElementById('umur').value = this.getAttribute('data-umur');
            document.getElementById('alamat_pendidik').value = this.getAttribute('data-alamat-pendidik');
            const editModal = new bootstrap.Modal(document.getElementById('editModal'));
            editModal.show();
        });
    });
</script>
    </div>
</body>
</html>
