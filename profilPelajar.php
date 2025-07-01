<?php
session_start();
require_once 'config.php'; // Database connection

// Check if the 'ic' parameter is passed in the URL
if (isset($_GET['ic'])) {
    $ic_pelajar = $_GET['ic'];
    
    // Get the username of the logged-in user (parent's email)
    $username = $_SESSION['username']; 

    // Prepare SQL query to fetch data for this specific student (based on ic_pelajar and parent's email)
    $query = "
        SELECT 
            pelajar.ic_pelajar, 
            pelajar.nama_pelajar, 
            pelajar.jantina, 
             pelajar.alamat_semasa, 
             pelajar.umur, 
             pelajar.gambar_pelajar,
             pelajar.Alahan,
             pelajar.tahun_pengajian,
            pendidik.nama_pendidik
        FROM pelajar
        INNER JOIN ibubapa ON pelajar.ibubapa_id = ibubapa.id_ibubapa
        LEFT JOIN pendidik ON pelajar.id_pendidik = pendidik.id_pendidik
        WHERE pelajar.ic_pelajar = ? AND ibubapa.email_bapa = ?
    ";

    $stmt = $conn->prepare($query);
    if (!$stmt) {
        die("Prepare statement failed: " . $conn->error);
    }

    // Bind parameters: 's' for string (since ic_pelajar and email_bapa are alphanumeric)
    $stmt->bind_param("ss", $ic_pelajar, $username);
    $stmt->execute();
    $result = $stmt->get_result();

    // Fetch student data
    $data = $result->fetch_assoc();

    if (!$data) {
        die("Student not found.");
    }

    $stmt->close();
} else {
    die("Student IC not provided.");
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Profil</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
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

        /* Table styling */
        table {
            width: 100%;
            border-collapse: collapse;
            margin: 1rem 0;
            background: white;
            border-radius: 8px;
            overflow: hidden;
        }

        table th,
        table td {
            padding: 1rem;
            text-align: left;
            border-bottom: 1px solid #eee;
        }

        table th {
            background: rgba(58, 64, 101, 0.05);
            font-weight: 600;
            color: #3a4065;
            text-align: left;
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
            text-decoration: none;
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

        .dropdown li a {
            padding: 0.75rem 1rem;
            display: block;
            color: #3a4065;
            text-decoration: none;
            border-radius: 4px;
            margin: 2px;
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

        /* Student photo styling */
        .student-photo {
            text-align: center;
            margin-bottom: 20px;
        }

        .student-photo img {
            width: 150px;
            height: 150px;
            border-radius: 50%;
            object-fit: cover;
            border: 2px solid #eee;
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

        <div class="profile-container">
            <h1>Maklumat Pelajar</h1>
            <div class="student-photo">
                <?php if (!empty($data['gambar_pelajar']) && file_exists("uploads/" . $data['gambar_pelajar'])): ?>
                    <img src="uploads/<?php echo htmlspecialchars($data['gambar_pelajar']); ?>" alt="Gambar Pelajar">
                <?php else: ?>
                    <img src="uploads/default.png" alt="Default Photo">
                <?php endif; ?>
            </div>
        <table>
            <tr>
                <th>No MyKid Pelajar</th>
                <td><?php echo htmlspecialchars($data['ic_pelajar']); ?></td>
            </tr>
            <tr>
                <th>Nama</th>
                <td><?php echo htmlspecialchars($data['nama_pelajar']); ?></td>
            </tr>
            <tr>
                <th>Tahun Pengajian</th>
                <td><?php echo htmlspecialchars($data['tahun_pengajian']); ?></td>
            </tr>
            <tr>
                <th>Jantina</th>
                <td><?php echo htmlspecialchars($data['jantina']); ?></td>
            </tr>
            <tr>
                <th>Alamat Semasa</th>
                <td><?php echo htmlspecialchars($data['alamat_semasa']); ?></td>
            </tr>
            <tr>
                <th>Umur</th>
                <td><?php echo htmlspecialchars($data['umur']); ?></td>
            </tr>
            <tr>
                <th>Alahan</th>
                <td><?php echo htmlspecialchars($data['Alahan']); ?></td>
            </tr>
            <tr>
                <th>Nama Pendidik</th>
                <td><?php echo htmlspecialchars($data['nama_pendidik'] ?? 'Belum ditetapkan'); ?></td>
            </tr>
        </table>
        <div class="crud-buttons">           
        <button class="btn-edit" 
            data-ic-pelajar="<?php echo htmlspecialchars($data['ic_pelajar']); ?>" 
            data-nama-pelajar="<?php echo htmlspecialchars($data['nama_pelajar']); ?>"
            data-alahan="<?php echo htmlspecialchars($data['Alahan']); ?>"
            data-alamat="<?php echo htmlspecialchars($data['alamat_semasa']); ?>" 
            data-umur="<?php echo htmlspecialchars($data['umur']); ?>"
        >
            Kemaskini
        </button>
        </div>       
        </div>
        </div>

        <!-- Modal for Editing -->
        <div class="modal fade" id="editModal" tabindex="-1" aria-labelledby="editModalLabel" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="editModalLabel">Kemaskini Profil Pelajar</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <form id="editForm" method="POST" action="edit_profilPelajar.php">
                        <div class="modal-body">
                            <!-- Student Details -->
                            <h5>Maklumat Pelajar</h5>
                            <div class="mb-3">
                                <label for="ic_pelajar" class="form-label">No MyKid Pelajar</label>
                                <input type="text" class="form-control" id="ic_pelajar" name="ic_pelajar" required>
                            </div>
                            <div class="mb-3">
                                <label for="nama_pelajar" class="form-label">Nama Pelajar</label>
                                <input type="text" class="form-control" id="nama_pelajar" name="nama_pelajar" required>
                            </div>
                            <div class="mb-3">
                                <label for="alamat_semasa" class="form-label">Alamat Semasa</label>
                                <input type="text" class="form-control" id="alamat_semasa" name="alamat_semasa" required>
                            </div>
                            <div class="mb-3">
                                <label for="umur" class="form-label">Umur</label>
                                <!-- Select dropdown for Umur -->
                                <select class="form-control" id="umur" name="umur" required>
                                    <option value="2">2</option>
                                    <option value="3">3</option>
                                    <option value="4">4</option>
                                </select>
                            </div>
                            <div class="mb-3">
                                <label for="Alahan" class="form-label">Alahan</label>
                                <input type="text" class="form-control" id="Alahan" name="Alahan" required>
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
                    // Populate the modal form fields with data from the button's data attributes
                    document.getElementById('ic_pelajar').value = this.getAttribute('data-ic-pelajar');
                    document.getElementById('nama_pelajar').value = this.getAttribute('data-nama-pelajar');
                    document.getElementById('alamat_semasa').value = this.getAttribute('data-alamat');
                    document.getElementById('umur').value = this.getAttribute('data-umur');
                    document.getElementById('Alahan').value = this.getAttribute('data-alahan');
                    
                    // Open the modal
                    const editModal = new bootstrap.Modal(document.getElementById('editModal'));
                    editModal.show();
                });
            });
        });
        </script>

</body>
</html>
