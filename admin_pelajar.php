<?php
include 'config.php';

// Handle delete request
if (isset($_GET['delete'])) {
    $ic = $_GET['delete'];

    if (!empty($ic)) {
        $stmt = $conn->prepare("DELETE FROM pelajar WHERE ic_pelajar = ?");
        $stmt->bind_param("s", $ic);
        if ($stmt->execute()) {
            echo "Record deleted successfully.";
        } else {
            echo "Error deleting record: " . $conn->error;
        }
        $stmt->close();
    } else {
        echo "Invalid IC.";
    }
}

// SQL: fetch students whose parents AND themselves are approved (pengesahan = 1)
$sql = "SELECT pelajar.*, ibubapa.pengesahan AS pengesahan_ibubapa 
        FROM pelajar
        JOIN ibubapa ON pelajar.ibubapa_id = ibubapa.id_ibubapa
        WHERE ibubapa.pengesahan = 1
          AND pelajar.pengesahan = 1";

$result = $conn->query($sql);

// Fetch details when view button is clicked
if (isset($_GET['view'])) {
    $ic_pelajar = $_GET['view'];

    $stmt = $conn->prepare("SELECT * FROM pelajar WHERE ic_pelajar = ?");
    $stmt->bind_param("s", $ic_pelajar);
    $stmt->execute();
    $student_result = $stmt->get_result();
    $student = $student_result->fetch_assoc();
    $stmt->close();

    if ($student) {
        $id_pendidik = $student['id_pendidik'] ?? null;
        if (!empty($id_pendidik)) {
            $stmt = $conn->prepare("SELECT nama_pendidik FROM pendidik WHERE id_pendidik = ?");
            $stmt->bind_param("i", $id_pendidik);
            $stmt->execute();
            $pendidik_result = $stmt->get_result();
            $pendidik = $pendidik_result->fetch_assoc();
            $stmt->close();
            $student['nama_pendidik'] = $pendidik['nama_pendidik'] ?? 'Tidak Ditentukan';
        } else {
            $student['nama_pendidik'] = 'Tidak Ditentukan';
        }

        $ibubapa_id = $student['ibubapa_id'] ?? null;
        if (!empty($ibubapa_id)) {
            $stmt = $conn->prepare("SELECT * FROM ibubapa WHERE id_ibubapa = ?");
            $stmt->bind_param("i", $ibubapa_id);
            $stmt->execute();
            $ibubapa_result = $stmt->get_result();
            $ibubapa = $ibubapa_result->fetch_assoc();
            $stmt->close();
        } else {
            $ibubapa = [];
        }

        echo json_encode(['student' => $student, 'ibubapa' => $ibubapa]);
    } else {
        echo json_encode(['error' => 'Student not found']);
    }
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="css/dashboard.css">
    <title>Pentadbir - Pelajar</title>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <!-- Bootstrap CSS -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <style>
        table {
            width: 80%;  /* Reduced table width for a more compact view */
            border-collapse: collapse;
            margin: 20px auto;  /* Center the table on the page */
            font-size: 18px;
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
            text-align: center; /* Center text in both columns */
        }

        table tbody tr:nth-child(even) {
            background-color: #f3f3f3;
        }

        table tbody tr:hover {
            background-color: #f1f1f1;
        }
/* Combined Modal Styling - Refined and Enhanced */
.modal-content {
    font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
    color: #2c3e50;
    border-radius: 8px;
    border: none;
    box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
}

.modal-header {
    background: #3a4065;
    color: white;
    border-radius: 8px 8px 0 0;
    padding: 1rem 1.5rem;
    border-bottom: none;
}

.modal-title {
    font-weight: 700;
    font-size: 1.5rem;
    color: white;
    margin: 0;
}

.btn-close {
    color: white;
    opacity: 1;
    filter: brightness(150%);
}

.modal-body {
    padding: 20px 30px;
    background-color: #f9f9fb;
    font-size: 1rem;
    line-height: 1.5;
}

/* Student Information Section */
.student-info {
    background-color: white;
    border-radius: 6px;
    padding: 20px;
    margin-bottom: 20px;
    box-shadow: 0 2px 4px rgba(0, 0, 0, 0.05);
}

.info-row {
    display: flex;
    margin-bottom: 12px;
    align-items: flex-start;
}

.info-label {
    font-weight: 600;
    min-width: 150px;
    color: #3a4065;
}

.info-value {
    flex: 1;
    color: #2c3e50;
}

/* Parent Information Section */
.parent-info {
    background-color: white;
    border-radius: 6px;
    padding: 20px;
    margin-bottom: 15px;
    box-shadow: 0 2px 4px rgba(0, 0, 0, 0.05);
}

.section-title {
    color: #3a4065;
    font-size: 1.25rem;
    font-weight: 600;
    margin: 0 0 15px 0;
    padding-bottom: 8px;
    border-bottom: 1px solid #eee;
}

.parent-section {
    margin-top: 20px;
}

.parent-title {
    font-weight: 600;
    color: #e74c3c;
    margin: 0 0 10px 0;
    font-size: 1.1rem;
}

/* Student Photo */
.student-photo-modal img {
    max-width: 150px;
    height: 150px;
    object-fit: cover;
    border: 3px solid #3a4065;
    border-radius: 50%;
    margin: 0 auto 1rem;
    display: block;
}

/* Modal Footer */
.modal-footer {
    border-top: 1px solid #eee;
    padding: 1rem 1.5rem;
    background: #f9f9fb;
    border-radius: 0 0 8px 8px;
}

/* Close Button */
.modal-footer .btn-secondary {
    background-color: #007bff !important;
    border-color: #007bff !important;
    color: white !important;
    font-weight: 600;
    padding: 8px 20px;
    border-radius: 6px;
    transition: all 0.3s ease;
    cursor: pointer;
    border: none;
}

.modal-footer .btn-secondary:hover,
.modal-footer .btn-secondary:focus {
    background-color: #0056b3 !important;
    border-color: #004085 !important;
    color: white !important;
    transform: translateY(-1px);
    box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
}

/* Responsive Adjustments */
@media (max-width: 768px) {
    .modal-dialog {
        margin: 10px;
    }
    
    .modal-body {
        padding: 15px;
    }
    
    .info-row {
        flex-direction: column;
    }
    
    .info-label {
        min-width: 100%;
        margin-bottom: 5px;
    }
    
    .student-photo-modal {
        margin-bottom: 15px;
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
                            <li><a href="pengesahan_pendidik.php">Pendidik</a></li>
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
        <!-- Table structure with View button -->
 <main>
 <h2 style="text-align: center; margin: 20px 0;">Senarai Pelajar</h2>
    <table>
        <thead>
            <tr>
                <th>No</th>
                <th>Nama Pelajar</th>
                <th>Tindakan</th>
            </tr>
        </thead>
        <tbody>
            <?php if ($result && $result->num_rows > 0): ?>
                <?php $no = 1; ?>
                <?php while ($row = $result->fetch_assoc()): ?>
                    <tr>
                    <td style="text-align: center;"><?php echo $no++; ?></td>
                    <td><?php echo htmlspecialchars($row['nama_pelajar']); ?></td>

                    <td style="text-align: center; white-space: nowrap;">
                        <!-- View Button -->
                        <button 
                            onclick="viewStudent('<?php echo $row['ic_pelajar']; ?>')"
                            style="background-color: #007bff; color: white; padding: 0; border: none; border-radius: 5px; cursor: pointer; margin: 0 3px; width: 40px; height: 40px; display: inline-flex; align-items: center; justify-content: center;"
                            title="View">
                            <i class="fas fa-eye" style="font-size: 16px;"></i>
                        </button>
                        
                        <!-- Edit Button -->
                        <a href="admin_edit_pelajar.php?edit=<?php echo $row['ic_pelajar']; ?>" style="text-decoration: none;">
                            <button 
                                style="background-color: #28a745; color: white; padding: 0; border: none; border-radius: 5px; cursor: pointer; margin: 0 3px; width: 40px; height: 40px; display: inline-flex; align-items: center; justify-content: center;"
                                title="Edit">
                                <i class="fas fa-edit" style="font-size: 16px;"></i>
                            </button>
                        </a>
                        
                        <!-- Delete Button -->
                        <a href="?delete=<?php echo $row['ic_pelajar']; ?>" onclick="return confirmDelete();" style="text-decoration: none;">
                            <button 
                                style="background-color: #dc3545; color: white; padding: 0; border: none; border-radius: 5px; cursor: pointer; margin: 0 3px; width: 40px; height: 40px; display: inline-flex; align-items: center; justify-content: center;"
                                title="Delete">
                                <i class="fas fa-trash-alt" style="font-size: 16px;"></i>
                            </button>
                        </a>
                    </td>
                    </tr>
                <?php endwhile; ?>
            <?php else: ?>
                <tr>
                    <td colspan="3">Tiada pelajar yang telah didaftarkan.</td>
                </tr>
            <?php endif; ?>
        </tbody>
    </table>
</main>

<!-- Updated Student Information Modal -->
<div class="modal fade" id="studentModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Maklumat Pelajar</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-md-4 text-center">
                        <div class="student-photo-modal mb-3">
                            <img src="uploads/default.png" alt="Student Photo" class="img-fluid rounded-circle" style="width: 150px; height: 150px; object-fit: cover; border: 3px solid #3a4065;">
                        </div>
                    </div>
                    <div class="col-md-8">
                        <div class="row">
                            <div class="col-md-6">
                                <p><strong>Nama:</strong> <span id="studentName"></span></p>
                                <p><strong>MyKid Pelajar:</strong> <span id="studentIC"></span></p>
                                <p><strong>Tahun Pengajian:</strong> <span id="tahunPengajian"></span></p>
                                <p><strong>Umur:</strong> <span id="umur"></span></p>
                                <p><strong>Alahan:</strong> <span id="alahan"></span></p>
                            </div>
                            <div class="col-md-6">
                                <p><strong>Jantina:</strong> <span id="jantina"></span></p>
                                <p><strong>Alamat Semasa:</strong> <span id="alamat"></span></p>
                                <p><strong>Nama Pendidik:</strong> <span id="namaPendidik"></span></p>
                            </div>
                        </div>
                    </div>
                </div>

                <hr>

                <h3 class="mt-3">Maklumat Ibu Bapa</h3>
                <div class="row">
                    <div class="col-md-6">
                        <h6>Bapa</h6>
                        <p><strong>Nama:</strong> <span id="nama_bapa"></span></p>
                        <p><strong>Kad Pengenalan:</strong> <span id="ic_bapa"></span></p>
                        <p><strong>Pekerjaan:</strong> <span id="pekerjaan_bapa"></span></p>
                        <p><strong>Pendapatan:</strong> <span id="pendapatan_bapa"></span></p>
                        <p><strong>Email:</strong> <span id="emailBapa"></span></p>
                        <p><strong>No Telefon:</strong> <span id="no_bapa"></span></p>
                    </div>
                    <div class="col-md-6">
                        <h6>Ibu</h6>
                        <p><strong>Nama:</strong> <span id="namaIbu"></span></p>
                        <p><strong>Kad Pengenalan:</strong> <span id="ic_ibu"></span></p>
                        <p><strong>Pekerjaan:</strong> <span id="pekerjaanIbu"></span></p>
                        <p><strong>Pendapatan:</strong> <span id="pendapatan_ibu"></span></p>
                        <p><strong>Email:</strong> <span id="emailIbu"></span></p>
                        <p><strong>No Telefon:</strong> <span id="no_ibu"></span></p>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
            </div>
        </div>
    </div>
</div>

<script>
function viewStudent(ic_pelajar) {
    $.ajax({
        url: "admin_pelajar.php?view=" + ic_pelajar,
        method: "GET",
        dataType: "json",
        success: function(response) {
            if (response.student) {
                $("#studentName").text(response.student.nama_pelajar || '-');
                $("#studentIC").text(response.student.ic_pelajar || '-');
                $("#tahunPengajian").text(response.student.tahun_pengajian || '-');
                $("#umur").text(response.student.umur || '-');
                $("#alahan").text(response.student.Alahan || '-');
                $("#jantina").text(response.student.jantina || '-');
                $("#alamat").text(response.student.alamat_semasa || '-');
                $("#namaPendidik").text(response.student.nama_pendidik || '-');
                
                if (response.student.gambar_pelajar) {
                    $(".student-photo-modal img").attr("src", "uploads/" + response.student.gambar_pelajar);
                } else {
                    $(".student-photo-modal img").attr("src", "uploads/default.png");
                }

                if (response.ibubapa) {
                    $("#nama_bapa").text(response.ibubapa.nama_bapa || '-');
                    $("#ic_bapa").text(response.ibubapa.ic_bapa || '-');
                    $("#pekerjaan_bapa").text(response.ibubapa.pekerjaan_bapa || '-');
                    $("#pendapatan_bapa").text(response.ibubapa.pendapatan_bapa || '-');
                    $("#emailBapa").text(response.ibubapa.email_bapa || '-');
                    $("#no_bapa").text(response.ibubapa.no_bapa || '-');
                    $("#namaIbu").text(response.ibubapa.nama_ibu || '-');
                    $("#ic_ibu").text(response.ibubapa.ic_ibu || '-');
                    $("#pekerjaanIbu").text(response.ibubapa.pekerjaan_ibu || '-');
                    $("#pendapatan_ibu").text(response.ibubapa.pendapatan_ibu || '-');
                    $("#emailIbu").text(response.ibubapa.EmailIbu || '-');
                    $("#no_ibu").text(response.ibubapa.no_ibu || '-');
                }
                
                var modal = new bootstrap.Modal(document.getElementById('studentModal'));
                modal.show();
            }
        }
    });
}

function confirmDelete() {
    return confirm("Are you sure you want to delete this student?");
}
</script>