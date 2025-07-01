<?php
include 'config.php';
session_start();

$ic_pelajar = isset($_GET['id_pelajar']) ? $_GET['id_pelajar'] : null;
if (!$ic_pelajar) {
    die("IC Pelajar tidak ditemukan!");
}

// Dapatkan nama pelajar
$stmt = $conn->prepare("SELECT nama_pelajar FROM pelajar WHERE ic_pelajar = ?");
$stmt->bind_param("s", $ic_pelajar);
$stmt->execute();
$result = $stmt->get_result();
$pelajar = $result->fetch_assoc();
$stmt->close();
if (!$pelajar) {
    die("Pelajar tidak ditemukan!");
}

// Dapatkan max 2 laporan terbaru pelajar ini (order ikut bulan descending)
$stmt = $conn->prepare("
    SELECT bulan, fizikal, deria_persekitaran, sahsiah, kreativiti, komunikasi, matematik_logik
    FROM laporan
    WHERE ic_pelajar = ?
    ORDER BY bulan DESC
    LIMIT 2
");
$stmt->bind_param("s", $ic_pelajar);
$stmt->execute();
$result = $stmt->get_result();

$laporanData = [];
while ($row = $result->fetch_assoc()) {
    $laporanData[] = $row;
}
$stmt->close();
?>

<!DOCTYPE html>
<html lang="ms">
<head>
    <meta charset="UTF-8" />
    <title>Graf Laporan Pelajar</title>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <style>
        body {
            font-family: 'Segoe UI', system-ui, -apple-system, sans-serif;
            background: #f8f9fa;
            color: #333;
            padding: 30px;
        }
        .container {
            max-width: 900px;
            margin: 0 auto;
        }
        h2 {
            text-align: center;
            margin-bottom: 30px;
        }
        a.btn-kembali {
            display: inline-block;
            margin-top: 25px;
            background-color: #007bff;
            color: white;
            padding: 10px 20px;
            border-radius: 6px;
            text-decoration: none;
        }
        a.btn-kembali:hover {
            background-color: #0056b3;
        }
        canvas {
            background: white;
            border-radius: 8px;
            box-shadow: 0 2px 8px rgba(0,0,0,0.1);
            max-width: 100%;
        }
        @media print {
    .no-print {
      display: none !important;
    }
  }
    </style>
</head>
<body>
    <div class="container">
        <h2>Graf Laporan Pelajar: <?php echo htmlspecialchars($pelajar['nama_pelajar']); ?></h2>

        <canvas id="laporanChart" width="800" height="400"></canvas>

    </div>

    <script>
        const ctx = document.getElementById('laporanChart').getContext('2d');

        // Data dari PHP ke JS
        const laporanData = <?php echo json_encode($laporanData, JSON_NUMERIC_CHECK); ?>;

        // Kategori dan warna untuk bar dalam setiap laporan
        const categories = [
            {key: 'fizikal', label: 'Fizikal'},
            {key: 'deria_persekitaran', label: 'Deria Persekitaran'},
            {key: 'sahsiah', label: 'Sahsiah'},
            {key: 'kreativiti', label: 'Kreativiti'},
            {key: 'komunikasi', label: 'Komunikasi'},
            {key: 'matematik_logik', label: 'Matematik & Logik'}
        ];

        // Dua set warna berlainan untuk dua laporan (bulan)
        const colorsPalette = [
            ['rgba(255, 99, 132, 0.7)', 'rgba(54, 162, 235, 0.7)', 'rgba(255, 206, 86, 0.7)', 'rgba(75, 192, 192, 0.7)', 'rgba(153, 102, 255, 0.7)', 'rgba(255, 159, 64, 0.7)'], // Laporan 1
            ['rgba(255, 159, 64, 0.7)', 'rgba(153, 102, 255, 0.7)', 'rgba(75, 192, 192, 0.7)', 'rgba(255, 206, 86, 0.7)', 'rgba(54, 162, 235, 0.7)', 'rgba(255, 99, 132, 0.7)']  // Laporan 2
        ];

        // Sediakan dataset dengan warna berbeza ikut laporan (bulan)
        const datasets = laporanData.map((laporan, idx) => {
            return {
                label: laporan.bulan,
                data: categories.map(cat => laporan[cat.key]),
                backgroundColor: colorsPalette[idx] || colorsPalette[0],
                borderColor: '#fff',
                borderWidth: 1,
                barPercentage: 0.7,
                categoryPercentage: 0.6
            };
        });

        new Chart(ctx, {
            type: 'bar',
            data: {
                labels: categories.map(cat => cat.label),
                datasets: datasets
            },
            options: {
                responsive: true,
                plugins: {
                    title: {
                        display: true,
                        text: 'Graf Skor Laporan Pelajar Mengikut Bulan dan Kategori'
                    },
                    tooltip: {
                        mode: 'index',
                        intersect: false
                    },
                    legend: {
                        position: 'top'
                    }
                },
                scales: {
                    x: {
                        stacked: false,
                        title: {
                            display: true,
                            text: 'Kategori'
                        }
                    },
                    y: {
                        stacked: false,
                        beginAtZero: true,
                        max: 3,
                        title: {
                            display: true,
                            text: 'Skor'
                        },
                        ticks: {
                            stepSize: 1
                        }
                    }
                }
            }
        });
    </script>
   <div style="text-align: center; margin-top: 20px;" class="no-print">
  <a href="pendidikLaporan.php?id_pelajar=<?php echo urlencode($ic_pelajar); ?>" class="btn-kembali" style="display: inline-block; margin-right: 10px; width: auto;">Kembali</a>
  <button onclick="window.print()" style="background-color: #28a745; color: white; border: none; padding: 10px 20px; border-radius: 6px; font-size: 17px; cursor: pointer;">Print</button>
</div>
</body>
</html>
