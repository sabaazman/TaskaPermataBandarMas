<?php

include 'config.php'; // Use config.php for database connection

// Fetch student and report details
$ic_pelajar = isset($_GET['id_pelajar']) ? $_GET['id_pelajar'] : null;
if (!$ic_pelajar) {
    die("IC Pelajar tidak ditemukan!");
}

$query = "SELECT pelajar.nama_pelajar, pelajar.ic_pelajar, laporan.fizikal, laporan.deria_persekitaran, laporan.sahsiah, laporan.kreativiti, laporan.komunikasi, laporan.matematik_logik, laporan.ulasan, laporan.id_pendidik
          FROM laporan
          INNER JOIN pelajar ON laporan.ic_pelajar = pelajar.ic_pelajar
          WHERE pelajar.ic_pelajar = ?";
$stmt = $conn->prepare($query);
if (!$stmt) {
    die("Kesalahan pada pernyataan SQL: " . $conn->error);
}
$stmt->bind_param("s", $ic_pelajar);
$stmt->execute();
$result = $stmt->get_result();
$data = $result->fetch_assoc();
if (!$data) {
    die("Laporan untuk pelajar ini tidak ditemukan.");
}

// Retrieve educator's name based on the id_pendidik
$id_pendidik = $data['id_pendidik'];
$query_pendidik = "SELECT nama_pendidik FROM pendidik WHERE id_pendidik = ?";
$stmt_pendidik = $conn->prepare($query_pendidik);
if (!$stmt_pendidik) {
    die("Kesalahan pada pernyataan SQL: " . $conn->error);
}
$stmt_pendidik->bind_param("i", $id_pendidik);
$stmt_pendidik->execute();
$result_pendidik = $stmt_pendidik->get_result();
$pendidik_data = $result_pendidik->fetch_assoc();
$nama_pendidik = $pendidik_data ? $pendidik_data['nama_pendidik'] : 'Tidak Ditemukan';
$stmt->close();
$stmt_pendidik->close();

// Get student info
$stmt = $conn->prepare("SELECT nama_pelajar FROM pelajar WHERE ic_pelajar = ?");
$stmt->bind_param("s", $ic_pelajar);
$stmt->execute();
$result = $stmt->get_result();
$pelajar = $result->fetch_assoc();
$stmt->close();
if (!$pelajar) {
    die("Pelajar tidak ditemukan!");
}

// Get all laporan for this student
$stmt = $conn->prepare("
    SELECT laporan.*, pendidik.nama_pendidik 
    FROM laporan 
    INNER JOIN pendidik ON laporan.id_pendidik = pendidik.id_pendidik
    WHERE laporan.ic_pelajar = ?
    ORDER BY bulan DESC
");
$stmt->bind_param("s", $ic_pelajar);
$stmt->execute();
$result = $stmt->get_result();

$laporanData = [];
while ($row = $result->fetch_assoc()) {
    $laporanData[] = $row;
}
$stmt->close();

// Add the report display structure similar to ibubapa_viewLaporan.php
?>

<!DOCTYPE html>
<html lang="ms">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pentadbir - Laporan</title>
    <link rel="stylesheet" href="css/dashboard.css">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <style>
/* Centering the title */
h2 {
            color: #3a4065;
            text-align: center;
            margin: 0.5rem 0 1rem;
            font-size: 1.5rem;
        }

        /* Student info styling */
        .student-info {
            background: rgba(58, 64, 101, 0.05);
            padding: 0.75rem;
            border-radius: 6px;
            margin-bottom: 1rem;
            display: flex;
            flex-direction: column;
           
        }

        .student-info p {
            margin: 0;
            font-size: 0.9rem;
            color: #333;
        }

        /* Fieldset styling for categories */
        fieldset {
            border: 1px solid #ddd;
            border-radius: 6px;
            margin-bottom: 1rem;
            padding: 0.75rem;
            background: #fff;
            page-break-inside: avoid;
            display: flex;
            flex-direction: column;
            align-items: center; /* center secara horizontal */
        }

        legend {
            font-weight: 600;
            font-size: 0.9rem;
            color: #3a4065;
            padding: 0 0.5rem;
        }

        /* Labels for categories */
        label {
            display: block;
            color: #3a4065;
            font-weight: 500;
            margin: 0.5rem 0 0.25rem;
            font-size: 0.85rem;
            text-align: left; /* agar teks label juga di tengah */
            width: 90%; /* supaya label sesuai lebar textarea */
        }

        /* Compact text areas for categories */
        textarea {
            width: 90%;
            min-height: 20px;
            max-height: 60px;
            border-radius: 4px;
            border: 1px solid rgba(58, 64, 101, 0.2);
            padding: 6px;
            font-size: 0.85rem;
            line-height: 1.3;
            background-color: #f8f9fa;
            resize: none;
            overflow-y: auto;
            width: 90%; /* pastikan textarea tetap 90% lebar */
            margin: 0 auto 0.5rem auto; /* margin bawah dan center horizontal */
        }

        #chart-container {
            margin: 1.5rem 0;
            padding: 0.75rem;
            background: #fff;
            border-radius: 6px;
            border: 1px solid #ddd;
            page-break-inside: avoid;
        }

        .button-group {
            text-align: center;
            margin: 1.5rem 0;
        }

        .btn-kembali, .btn-print {
            display: inline-block;
            padding: 8px 16px;
            margin: 0 5px;
            border-radius: 6px;
            font-weight: 600;
            cursor: pointer;
            text-decoration: none;
            border: none;
            color: white;
            font-size: 0.85rem;
        }

        .btn-kembali {
            background-color: #3a4065;
        }

        .btn-print {
            background-color: #28a745;
        }

        /* Print specific styles */
@media print {
    body {
        margin: 0;
        padding: 0;
        font-size: 12pt;
        line-height: 1.2;
    }

    .container {
        width: 100%;
        max-width: none;
        padding: 0;
        margin: 0;
        box-shadow: none;
    }

    .button-group, nav, header {
        display: none !important;
    }

    h2 {
        font-size: 16pt;
        margin: 10pt 0;
    }

    .student-info {
        padding: 5pt;
        margin-bottom: 10pt;
    }

    .student-info p {
        font-size: 11pt;
    }

    fieldset {
        border: 1pt solid #ddd;
        margin-bottom: 15pt;
        padding: 8pt;
    }

    legend {
        font-size: 12pt;
    }

    label {
        font-size: 11pt;
        margin: 3pt 0;
    }

    textarea {
        font-size: 11pt;
        padding: 3pt;
        min-height: auto;
        height: auto;
        border: none;
        background: none;
        margin-bottom: 5pt;
    }

    #chart-container {
        margin: 20pt 0;
        padding: 10pt;
        height: 400pt;
        page-break-after: always; /* Add this to force next content to new page */
    }

    /* Style for score reference table in print */
    .score-reference {
        page-break-before: always; /* Ensure it starts on new page */
        margin-top: 20pt;
        padding: 15pt;
        border: 1pt solid #ddd;
        background: #f8f9fa;
    }

    .score-reference h4 {
        text-align: center;
        margin-bottom: 10pt;
        color: #3a4065;
        font-size: 14pt;
    }

    .score-reference table {
        width: 100%;
        border-collapse: collapse;
    }

    .score-reference th {
        background: #3a4065;
        color: white;
        padding: 8pt;
        text-align: center;
    }

    .score-reference td {
        padding: 8pt;
        border-bottom: 1pt solid #ddd;
    }

    @page {
        size: A4;
        margin: 1cm;
    }
}
        @media (max-width: 768px) {
            .container {
                padding: 0.5rem;
            }

            nav ul {
                flex-direction: column;
                gap: 0.1rem;
            }

            nav ul li a {
                padding: 0.5rem;
            }

            .dropdown {
                position: static;
                box-shadow: none;
                border: none;
                padding: 0 0.5rem;
            }

            .button-group {
                display: flex;
                flex-direction: column;
                gap: 0.5rem;
            }

            .btn-kembali, .btn-print {
                width: 100%;
                margin: 2px 0;
            }
        }
        @media print {
    .no-print {
      display: none !important;
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
                    <li><a href="#">Yuran</a></li>
                    <li><a href="admin_jadual.php">Jadual Pelajar</a></li>
                    <li><a href="pentadbirRPA.php">RPA</a></li>
                    <li><a href="pentadbirLaporan.php">Laporan</a></li>
                </ul>
            </nav>
        <main>

    <div class="container">
        <h2 style="text-align: center; margin: 20px 0;">Laporan & Graf Pencapaian dan Perkembangan Pelajar</h2>

<!-- Student info with reduced vertical gap -->
<div style="display: flex; gap: 1rem; font-size: 0.9rem; margin-bottom: 1rem;">
  <p style="margin: 0;"><strong>Nama Pelajar:</strong> <?php echo htmlspecialchars($pelajar['nama_pelajar']); ?></p>
  <p style="margin: 0;"><strong>No MyKid Pelajar:</strong> <?php echo htmlspecialchars($ic_pelajar); ?></p>
  <p style="margin: 0;">
    <strong>Nama Pendidik:</strong> 
    <?php 
      echo isset($laporanData[0]) ? htmlspecialchars($laporanData[0]['nama_pendidik']) : '-'; 
    ?>
  </p>
</div>

        <!-- Loop through each report (laporan) -->
        <?php foreach ($laporanData as $laporan): ?>
            <fieldset>
                <legend>Laporan Bulan: <?php echo htmlspecialchars($laporan['bulan']); ?></legend>

                <label>Ulasan Perkembangan Fizikal:</label>
                <textarea readonly><?php echo htmlspecialchars($laporan['fizikal_ulasan']); ?></textarea>

                <label>Ulasan Perkembangan Deria & Pemahaman Dunia Persekitaran:</label>
                <textarea readonly><?php echo htmlspecialchars($laporan['deria_persekitaran_ulasan']); ?></textarea>

                <label>Ulasan Perkembangan Sahsiah, Sosio-Emosi Dan Kerohanian:</label>
                <textarea readonly><?php echo htmlspecialchars($laporan['sahsiah_ulasan']); ?></textarea>

                <label>Ulasan Kreativiti & Perkembangan Estetika:</label>
                <textarea readonly><?php echo htmlspecialchars($laporan['kreativiti_ulasan']); ?></textarea>

                <label>Ulasan Perkembangan Bahasa Komunikasi & Literasi Awal:</label>
                <textarea readonly><?php echo htmlspecialchars($laporan['komunikasi_ulasan']); ?></textarea>

                <label>Ulasan Perkembangan Awal Matematik & Pemikiran Logik:</label>
                <textarea readonly><?php echo htmlspecialchars($laporan['matematik_logik_ulasan']); ?></textarea>

                <label>Ulasan Keseluruhan:</label>
                <textarea readonly><?php echo htmlspecialchars($laporan['ulasan']); ?></textarea>
            </fieldset>
        <?php endforeach; ?>

         <!-- Add this right before or after your chart container -->
         <div class="score-reference" style="margin: 20px auto; max-width: 600px; background: #f8f9fa; padding: 15px; border-radius: 8px; border: 1px solid #eee;">

<h4 style="text-align: center; margin-bottom: 10px; color: #3a4065;">Rujukan Skor</h4>
<table style="width: 100%; border-collapse: collapse; margin: 0 auto;">
    <tr style="background: #3a4065; color: white;">
        <th style="padding: 8px; text-align: center;">Skor</th>
        <th style="padding: 8px; text-align: left;">Penerangan</th>
    </tr>
    <tr style="border-bottom: 1px solid #ddd;">
        <td style="padding: 8px; text-align: center; font-weight: bold; color: #3a4065;">1</td>
        <td style="padding: 8px;">Dengan bimbingan</td>
    </tr>
    <tr style="border-bottom: 1px solid #ddd;">
        <td style="padding: 8px; text-align: center; font-weight: bold; color: #3a4065;">2</td>
        <td style="padding: 8px;">Sedikit bimbingan</td>
    </tr>
    <tr>
        <td style="padding: 8px; text-align: center; font-weight: bold; color: #3a4065;">3</td>
        <td style="padding: 8px;">Tanpa bimbingan</td>
    </tr>
</table>
</div>
<!-- Chart Container -->
<div id="chart-container">
            <canvas id="laporanChart" width="800" height="400"></canvas>
        </div>

<!-- Button group centered with inline styles -->
<div style="text-align: center; margin-top: 20px;" class="no-print">
<a href="pentadbir_laporan.php?id_pendidik=<?php echo $id_pendidik; ?>" 
   style="display: inline-block; margin-right: 10px; width: auto; background-color: #007bff; color: white; padding: 10px 20px; border-radius: 6px; text-decoration: none; font-weight: 600; font-size: 17px; text-align: center;">
    Kembali
</a>

  <button 
    onclick="printReport()" 
    style="background-color: #28a745; color: white; border: none; padding: 10px 20px; border-radius: 6px; font-size: 17px; cursor: pointer;">
    Print
  </button>
</div>
    </div>
    <script>
        const laporanData = <?php echo json_encode($laporanData, JSON_NUMERIC_CHECK); ?>;
        const categories = [
            {key: 'fizikal', label: 'Fizikal', color: 'rgba(255, 99, 132, 0.7)'},
            {key: 'deria_persekitaran', label: 'Deria Persekitaran', color: 'rgba(54, 162, 235, 0.7)'},
            {key: 'sahsiah', label: 'Sahsiah', color: 'rgba(255, 206, 86, 0.7)'},
            {key: 'kreativiti', label: 'Kreativiti', color: 'rgba(75, 192, 192, 0.7)'},
            {key: 'komunikasi', label: 'Komunikasi', color: 'rgba(153, 102, 255, 0.7)'},
            {key: 'matematik_logik', label: 'Matematik & Logik', color: 'rgba(255, 159, 64, 0.7)'}
        ];

        const ctx = document.getElementById('laporanChart').getContext('2d');
        const labels = categories.map(c => c.label);

        const datasets = laporanData.map((laporan, idx) => {
            return {
                label: laporan.bulan,
                data: categories.map(cat => laporan[cat.key]),
                backgroundColor: idx % 2 === 0 ? 'rgba(75, 192, 192, 0.7)' : 'rgba(153, 102, 255, 0.7)', // Different color for two reports
                borderColor: '#fff',
                borderWidth: 1
            };
        });

        new Chart(ctx, {
            type: 'bar',
            data: {
                labels: labels,
                datasets: datasets
            },
            options: {
                responsive: true,
                plugins: {
                    title: {
                        display: true,
                        text: 'Graf Skor Laporan Pelajar Mengikut Bulan dan Kategori',
                        font: {
                            size: 14
                        }
                    },
                    tooltip: {
                        mode: 'index',
                        intersect: false
                    },
                    legend: {
                        position: 'top',
                        labels: {
                            font: {
                                size: 12
                            }
                        }
                    }
                },
                scales: {
                    x: { 
                        stacked: false, 
                        title: { 
                            display: true, 
                            text: 'Kategori',
                            font: {
                                size: 12
                            }
                        },
                        ticks: {
                            font: {
                                size: 11
                            }
                        }
                    },
                    y: { 
                        stacked: false,
                        beginAtZero: true,
                        max: 3,
                        title: { 
                            display: true, 
                            text: 'Skor',
                            font: {
                                size: 12
                            }
                        },
                        ticks: { 
                            stepSize: 1,
                            font: {
                                size: 11
                            }
                        }
                    }
                }
            }
        });

        function printReport() {
            window.print();
        }
    </script>
</body>
</html>

<?php
$conn->close();
?>