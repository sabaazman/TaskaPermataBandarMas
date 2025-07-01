<?php
session_start();
require_once 'config.php'; // Sambungan database

if (!isset($_SESSION['username'])) {
    header("Location: login.php");
    exit();
}

// Ambil data ibu bapa menggunakan username sahaja
$username = $_SESSION['username']; 
$query = "
    SELECT 
        ibubapa.id_ibubapa,
        ibubapa.ic_bapa, ibubapa.nama_bapa, ibubapa.pekerjaan_bapa, ibubapa.pendapatan_bapa, ibubapa.email_bapa, ibubapa.no_bapa,
        ibubapa.ic_ibu, ibubapa.nama_ibu, ibubapa.pekerjaan_ibu, ibubapa.pendapatan_ibu, ibubapa.no_ibu, ibubapa.EmailIbu
    FROM ibubapa
    WHERE ibubapa.email_bapa = ?
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
    <title>Profil</title>
    
    <!-- Bootstrap CSS -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    
    <!-- Your custom styles should be loaded after Bootstrap -->
    <link rel="stylesheet" href="css/dashboard.css">

    <!-- External JS libraries -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    
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

        <!-- Profile Content -->
        <div class="profile-container">
            <h1>Maklumat Ibu Bapa</h1>
            <table>
                <tr>
                    <th>No MyKad Bapa</th>
                    <td><?php echo htmlspecialchars($data['ic_bapa']); ?></td>
                </tr>
                <tr>
                    <th>Nama Bapa</th>
                    <td><?php echo htmlspecialchars($data['nama_bapa']); ?></td>
                </tr>
                <tr>
                    <th>Pekerjaan Bapa</th>
                    <td><?php echo htmlspecialchars($data['pekerjaan_bapa']); ?></td>
                </tr>
                <tr>
                    <th>Pendapatan Bapa</th>
                    <td><?php echo htmlspecialchars($data['pendapatan_bapa']); ?></td>
                </tr>
                <tr>
                    <th>Email Bapa</th>
                    <td><?php echo htmlspecialchars($data['email_bapa']); ?></td>
                </tr>
                <tr>
                    <th>No Telefon Bapa</th>
                    <td><?php echo htmlspecialchars($data['no_bapa']); ?></td>
                </tr>
                <tr>
                    <th>No MyKad Ibu</th>
                    <td><?php echo htmlspecialchars($data['ic_ibu']); ?></td>
                </tr>
                <tr>
                    <th>Nama Ibu</th>
                    <td><?php echo htmlspecialchars($data['nama_ibu']); ?></td>
                </tr>
                <tr>
                    <th>Pekerjaan Ibu</th>
                    <td><?php echo htmlspecialchars($data['pekerjaan_ibu']); ?></td>
                </tr>
                <tr>
                    <th>Pendapatan Ibu</th>
                    <td><?php echo htmlspecialchars($data['pendapatan_ibu']); ?></td>
                </tr>
                <tr>
                    <th>Email Ibu</th>
                    <td><?php echo htmlspecialchars($data['EmailIbu']); ?></td>
                </tr>
                <tr>
                    <th>No Telefon Ibu</th>
                    <td><?php echo htmlspecialchars($data['no_ibu']); ?></td>
                </tr>
            </table>

            <div class="crud-buttons">
                <button class="btn-edit" 
                        data-id-ibubapa="<?php echo htmlspecialchars($data['id_ibubapa']); ?>" 
                        data-pekerjaan-bapa="<?php echo htmlspecialchars($data['pekerjaan_bapa']); ?>" 
                        data-pendapatan-bapa="<?php echo htmlspecialchars($data['pendapatan_bapa']); ?>" 
                        data-email-bapa="<?php echo htmlspecialchars($data['email_bapa']); ?>" 
                        data-telefon-bapa="<?php echo htmlspecialchars($data['no_bapa']); ?>" 
                        data-pekerjaan-ibu="<?php echo htmlspecialchars($data['pekerjaan_ibu']); ?>" 
                        data-pendapatan-ibu="<?php echo htmlspecialchars($data['pendapatan_ibu']); ?>" 
                        data-email-ibu="<?php echo htmlspecialchars($data['EmailIbu']); ?>" 
                        data-telefon-ibu="<?php echo htmlspecialchars($data['no_ibu']); ?>">
                    Kemaskini
                </button>
            </div>       
        </div>
    </div>

    <!-- Modal for editing -->
<div class="modal fade" id="editModal" tabindex="-1" aria-labelledby="editModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="editModalLabel">Kemaskini Profil Ibubapa</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="editForm" method="POST" action="edit_profil_ibubapa.php">
                <div class="modal-body">
                    <input type="hidden" id="id_ibubapa" name="id_ibubapa" value="<?php echo htmlspecialchars($data['id_ibubapa']); ?>">

                    <!-- Father Details -->
                    <h5>Maklumat Bapa</h5>
                    <div class="mb-3">
                        <label for="pekerjaan_bapa" class="form-label">Pekerjaan Bapa</label>
                        <input type="text" class="form-control" id="pekerjaan_bapa" name="pekerjaan_bapa">
                    </div>
                    <div class="mb-3">
                        <label for="pendapatan_bapa" class="form-label">Pendapatan Bapa</label>
                        <input type="number" class="form-control" id="pendapatan_bapa" name="pendapatan_bapa">
                    </div>
                    <div class="mb-3">
                        <label for="email_bapa" class="form-label">Email Bapa</label>
                        <input type="email" class="form-control" id="email_bapa" name="email_bapa">
                    </div>
                    <div class="mb-3">
                        <label for="no_bapa" class="form-label">No Telefon Bapa</label>
                        <input type="text" class="form-control" id="no_bapa" name="no_bapa">
                    </div>

                    <!-- Mother Details -->
                    <h5>Maklumat Ibu</h5>
                    <div class="mb-3">
                        <label for="pekerjaan_ibu" class="form-label">Pekerjaan Ibu</label>
                        <input type="text" class="form-control" id="pekerjaan_ibu" name="pekerjaan_ibu">
                    </div>
                    <div class="mb-3">
                        <label for="pendapatan_ibu" class="form-label">Pendapatan Ibu</label>
                        <input type="number" class="form-control" id="pendapatan_ibu" name="pendapatan_ibu">
                    </div>
                    <div class="mb-3">
                        <label for="email_ibu" class="form-label">Email Ibu</label>
                        <input type="email" class="form-control" id="email_ibu" name="email_ibu">
                    </div>
                    <div class="mb-3">
                        <label for="no_ibu" class="form-label">No Telefon Ibu</label>
                        <input type="text" class="form-control" id="no_ibu" name="no_ibu">
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
            const editButtons = document.querySelectorAll('.btn-edit');
            editButtons.forEach(button => {
                button.addEventListener('click', function () {
                    document.getElementById('id_ibubapa').value = this.getAttribute('data-id-ibubapa');
                    document.getElementById('pekerjaan_bapa').value = this.getAttribute('data-pekerjaan-bapa');
                    document.getElementById('pendapatan_bapa').value = this.getAttribute('data-pendapatan-bapa');
                    document.getElementById('email_bapa').value = this.getAttribute('data-email-bapa');
                    document.getElementById('no_bapa').value = this.getAttribute('data-telefon-bapa');
                    document.getElementById('pekerjaan_ibu').value = this.getAttribute('data-pekerjaan-ibu');
                    document.getElementById('pendapatan_ibu').value = this.getAttribute('data-pendapatan-ibu');
                    document.getElementById('email_ibu').value = this.getAttribute('data-email-ibu');
                    document.getElementById('no_ibu').value = this.getAttribute('data-telefon-ibu');

                    const editModal = new bootstrap.Modal(document.getElementById('editModal'));
                    editModal.show();
                });
            });
        });
    </script>
</body>
</html>