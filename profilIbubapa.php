<?php
session_start();
require_once 'config.php'; // Sambungan database

if (!isset($_SESSION['username'])) {
    header("Location: login.php");
    exit();
}

// Ambil data ibu bapa dan pelajar menggunakan username
$username = $_SESSION['username']; 
$query = "
    SELECT 
        IBUBAPA.id_ibubapa,
        IBUBAPA.ic_bapa, IBUBAPA.nama_bapa, IBUBAPA.pekerjaan_bapa, IBUBAPA.pendapatan_bapa, IBUBAPA.email_bapa, IBUBAPA.no_bapa,
        IBUBAPA.ic_ibu, IBUBAPA.nama_ibu, IBUBAPA.pekerjaan_ibu, IBUBAPA.pendapatan_ibu, IBUBAPA.no_ibu, IBUBAPA.EmailIbu,
        PELAJAR.ic_pelajar, PELAJAR.nama_pelajar, PELAJAR.jantina, PELAJAR.alamat_semasa, PELAJAR.umur, PELAJAR.gambar_pelajar
    FROM IBUBAPA
    INNER JOIN PELAJAR ON IBUBAPA.id_ibubapa = PELAJAR.ibubapa_id
    WHERE IBUBAPA.email_bapa = ?
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
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <link rel="stylesheet" href="css/dashboard.css">
    <style>
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

        table {
            width: 100%;
            border-collapse: collapse;
        }

        table th,
        table td {
            padding: 10px;
            text-align: left;
            border-bottom: 1px solid #ddd;
        }

        .student-photo {
            text-align: center;
            margin-bottom: 20px;
        }

        .student-photo img {
            width: 150px;
            height: 150px;
            border-radius: 50%;
            object-fit: cover;
            border: 2px solid #ddd;
        }
        .crud-buttons {
            display: flex;
            justify-content: center;
            gap: 10px;
            margin-top: 20px;
        }

        .crud-buttons button {
            padding: 10px 20px;
            background: #666;
            color: white;
            border: none;
            border-radius: 5px;
            cursor: pointer;
        }

        .crud-buttons button:hover {
            background: #444;
        }

        /* Additional styling for bold section titles and spacing */
        h3 {
            font-weight: bold;
            margin-top: 20px;
        }

        table + h3 {
            margin-top: 40px; /* Add space between student and parent tables */
        }

        .modal-body h5 {
            font-weight: bold;
            margin-top: 20px;
            margin-bottom: 10px;
        }

        .modal-body .mb-3 {
            margin-bottom: 15px;
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
                <li><a href="profilIbubapa.php">Profil</a></li>
                <li><a href="#">Sumbangan Aktiviti</a></li>
                <li><a href="display_jadual.php">Jadual Pelajar</a></li>
                <li><a href="ibubapa_viewLaporan.php">Laporan</a></li>
            </ul>
        </nav>
        <div class="profile-container">
            <h1>Maklumat Profil</h1>
            <div class="student-photo">
                <?php if (!empty($data['gambar_pelajar']) && file_exists("uploads/" . $data['gambar_pelajar'])): ?>
                    <img src="uploads/<?php echo htmlspecialchars($data['gambar_pelajar']); ?>" alt="Gambar Pelajar">
                <?php else: ?>
                    <img src="uploads/default.png" alt="Default Photo">
                <?php endif; ?>
            </div>
            <h3>Maklumat Pelajar</h3>
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
            </table>

            <h3>Maklumat Ibu Bapa</h3>
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
            <button 
            class="btn-edit" 
            data-id-ibubapa="<?php echo htmlspecialchars($data['id_ibubapa']); ?>" 
            data-ic-pelajar="<?php echo htmlspecialchars($data['ic_pelajar']); ?>" 
            data-alamat="<?php echo htmlspecialchars($data['alamat_semasa']); ?>" 
            data-umur="<?php echo htmlspecialchars($data['umur']); ?>" 
            data-pekerjaan-bapa="<?php echo htmlspecialchars($data['pekerjaan_bapa']); ?>" 
            data-pendapatan-bapa="<?php echo htmlspecialchars($data['pendapatan_bapa']); ?>" 
            data-email-bapa="<?php echo htmlspecialchars($data['email_bapa']); ?>" 
            data-telefon-bapa="<?php echo htmlspecialchars($data['no_bapa']); ?>" 
            data-pekerjaan-ibu="<?php echo htmlspecialchars($data['pekerjaan_ibu']); ?>" 
            data-pendapatan-ibu="<?php echo htmlspecialchars($data['pendapatan_ibu']); ?>" 
            data-email-ibu="<?php echo htmlspecialchars($data['EmailIbu']); ?>" 
            data-telefon-ibu="<?php echo htmlspecialchars($data['no_ibu']); ?>">
            Edit
            </button>
            </div>       
        </div>
    </div>
    
        <!-- Modal -->
<div class="modal fade" id="editModal" tabindex="-1" aria-labelledby="editModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="editModalLabel">Edit Profil Ibubapa</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="editForm" method="POST" action="edit_profil_ibubapa.php">
                <div class="modal-body">
                    <!-- Hidden field to pass ic_pelajar -->
                    <input type="hidden" id="ic_pelajar" name="ic_pelajar" value="<?php echo htmlspecialchars($data['ic_pelajar']); ?>">
                    <input type="hidden" id="id_ibubapa" name="id_ibubapa" value="<?php echo htmlspecialchars($data['id_ibubapa']); ?>">
                    <!-- Student Details -->
                    <h5>Maklumat Pelajar</h5>
                    <div class="mb-3">
                        <label for="alamat_semasa" class="form-label">Alamat Semasa</label>
                        <input type="text" class="form-control" id="alamat_semasa" name="alamat_semasa" required>
                    </div>
                    <div class="mb-3">
                        <label for="umur" class="form-label">Umur</label>
                        <input type="number" class="form-control" id="umur" name="umur" required>
                    </div>

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
                    <button type="submit" class="btn btn-primary">Simpan</button>
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
            document.getElementById('ic_pelajar').value = this.getAttribute('data-ic-pelajar');
            document.getElementById('alamat_semasa').value = this.getAttribute('data-alamat');
            document.getElementById('umur').value = this.getAttribute('data-umur');
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
