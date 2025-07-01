<?php
include 'config.php';
include 'mail_config.php';

$sql_first_time = "SELECT IBUBAPA.id_ibubapa, IBUBAPA.nama_bapa, IBUBAPA.pengesahan, COUNT(PELAJAR.ic_pelajar) AS anak_count
                   FROM IBUBAPA
                   JOIN PELAJAR ON IBUBAPA.id_ibubapa = PELAJAR.ibubapa_id
                   GROUP BY IBUBAPA.id_ibubapa, IBUBAPA.nama_bapa, IBUBAPA.pengesahan
                   ORDER BY IBUBAPA.pengesahan IS NOT NULL, IBUBAPA.id_ibubapa DESC";

$sql_new_children = "SELECT PELAJAR.ic_pelajar, PELAJAR.nama_pelajar, PELAJAR.pengesahan, IBUBAPA.nama_bapa
                     FROM PELAJAR
                     JOIN IBUBAPA ON PELAJAR.ibubapa_id = IBUBAPA.id_ibubapa
                     WHERE IBUBAPA.pengesahan = 1 
                     ORDER BY PELAJAR.pengesahan IS NOT NULL, PELAJAR.ic_pelajar DESC";

$result_first_time = $conn->query($sql_first_time);
$result_new_children = $conn->query($sql_new_children);
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
            width: 90%;  /* Reduced table width for a more compact view */
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
            justify-content: flex-start;
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
        /* --- Tab container styling --- */
        .tab-container {
        display: flex;
        border-bottom: 2px solid #ddd;
        max-width: 900px;
        margin: 20px auto 10px;
        border-radius: 8px 8px 0 0;
        overflow: hidden;
        }

        .tab {
        flex: 1;
        text-align: center;
        cursor: pointer;
        padding: 12px 0;
        font-weight: 600;
        background: #eee;
        color: #666;
        transition: background-color 0.3s, color 0.3s;
        user-select: none;
        }

        .tab.active {
        background-color: #007bff;
        color: white;
        font-weight: 700;
        }

        .tab:hover:not(.active) {
        background-color: #ddd;
        }

        /* --- Tab content area --- */
        .tab-content {
        max-width: 900px;
        margin: 0 auto 20px;
        background: white;
        padding: 20px;
        border-radius: 0 8px 8px 8px;
        box-shadow: 0 2px 8px rgba(0,0,0,0.1);
        }

        .tab-content > div {
        display: none;
        }

        .tab-content > div.active {
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

        <!-- Main Content -->
        <main>  
           
 <h1 style="text-align:center; margin-top:30px;">Pengesahan Ibu Bapa dan Pelajar</h1>

  <div class="tab-container">
    <div class="tab active" data-tab="first-time">Pengesahan Pendaftaran Pertama</div>
    <div class="tab" data-tab="new-children">Pengesahan Pelajar Baru</div>
  </div>

  <div class="tab-content">
    <div id="first-time" class="active">
      <h2 style="text-align:center; margin-top:30px;"> Pengesahan Pendaftaran Pertama</h2>
      <?php if ($result_first_time && $result_first_time->num_rows > 0): ?>
        <table>
          <thead>
            <tr>
              <th>No</th>
              <th>Nama Bapa</th>
              <th>Status Pengesahan</th>
              <th>Tindakan</th>
            </tr>
          </thead>
          <tbody>
            <?php 
            $no = 1;
            // Reset pointer for the query result if needed, or query again if exhausted
            mysqli_data_seek($result_first_time, 0);
            while($row = $result_first_time->fetch_assoc()): 
                $status = $row['pengesahan'];
            ?>
           <tr style="text-align: center;">
            <td style="text-align: center; vertical-align: middle;"><?= $no ?></td>
            <td style="text-align: center; vertical-align: middle;">
                <a href="profilIbubapaAdmin.php?id_ibubapa=<?= urlencode($row['id_ibubapa']) ?>" style="text-decoration: none;">
                    <?= htmlspecialchars($row['nama_bapa']) ?>
                </a>
            </td>
            <td style="text-align: center; vertical-align: middle;">
                <?php 
                if (is_null($status)) {
                    echo "Pending";
                } elseif ($status == 1) {
                    echo "Approved";
                } elseif ($status == 0) {
                    echo "Rejected";
                }
                ?>
            </td>
            <td style="text-align: center; vertical-align: middle;">
                <?php if (is_null($status)): ?>
                    <a href="approve_reject.php?action=approve_ibubapa&id=<?= urlencode($row['id_ibubapa']) ?>">
                        <button style="background-color: #28a745; color: white; padding: 10px 20px; border: none; border-radius: 5px; font-size: 16px; cursor: pointer;">Approve</button>
                    </a>
                    <a href="approve_reject.php?action=reject_ibubapa&id=<?= urlencode($row['id_ibubapa']) ?>">
                        <button style="background-color: #dc3545; color: white; padding: 10px 20px; border: none; border-radius: 5px; font-size: 16px; cursor: pointer;">Reject</button>
                    </a>
                <?php else: ?>
                    <span>Tidak memerlukan tindakan</span>
                <?php endif; ?>
            </td>
            </tr>
            <?php 
            $no++;
            endwhile; 
            ?>
          </tbody>
        </table>
      <?php else: ?>
        <p>Tiada pendaftaran ibu bapa yang belum disahkan.</p>
      <?php endif; ?>
    </div>

    <div id="new-children">
    <h2 style="text-align:center; margin-top:30px;"> Pengesahan Pendaftaran Pelajar Baru</h2>
      <?php if ($result_new_children && $result_new_children->num_rows > 0): ?>
        <table>
          <thead>
            <tr>
              <th>No</th>
              <th>Nama Pelajar</th>
              <th>Status Pengesahan</th>
              <th>Tindakan</th>
            </tr>
          </thead>
          <tbody>
            <?php 
            $no = 1;
            // Reset pointer for the query result if needed, or query again if exhausted
            mysqli_data_seek($result_new_children, 0);
            while($row = $result_new_children->fetch_assoc()): 
                $status = $row['pengesahan'];
            ?>
           <tr style="text-align: center;">
            <td style="text-align: center;"><?= $no ?></td>
            <td style="text-align: center;"><a href="profilPelajarAdmin.php?ic=<?= urlencode($row['ic_pelajar']) ?>" style="text-decoration: none;"><?= htmlspecialchars($row['nama_pelajar']) ?></a></td>
            <td style="text-align: center;">
                <?php 
                if (is_null($status)) {
                    echo "Pending";
                } elseif ($status == 1) {
                    echo "Approved";
                } elseif ($status == 0) {
                    echo "Rejected";
                }
                ?>
            </td>
            <td style="text-align: center; vertical-align: middle;">
                <?php if (is_null($status)): ?>
                    <a href="approve_reject.php?action=approve_pelajar&id=<?= urlencode($row['ic_pelajar']) ?>">
                        <button style="background-color: #28a745; color: white; padding: 10px 20px; border: none; border-radius: 5px; font-size: 16px; cursor: pointer;">Approve</button>
                    </a>
                    <a href="approve_reject.php?action=reject_pelajar&id=<?= urlencode($row['ic_pelajar']) ?>">
                        <button style="background-color: #dc3545; color: white; padding: 10px 20px; border: none; border-radius: 5px; font-size: 16px; cursor: pointer;">Reject</button>
                    </a>
                <?php else: ?>
                    <span>Tidak memerlukan tindakan</span>
                <?php endif; ?>
            </td>
            </tr>
            <?php 
            $no++;
            endwhile; 
            ?>
          </tbody>
        </table>
      <?php else: ?>
        <p>Tiada pendaftaran anak baru yang belum disahkan.</p>
      <?php endif; ?>
    </div>
  </div>

  <script>
    // JavaScript to switch tabs
    const tabs = document.querySelectorAll('.tab');
    const tabContents = document.querySelectorAll('.tab-content > div');

    tabs.forEach(tab => {
      tab.addEventListener('click', () => {
        // Remove active class from all tabs and contents
        tabs.forEach(t => t.classList.remove('active'));
        tabContents.forEach(tc => tc.classList.remove('active'));

        // Add active class to clicked tab and corresponding content
        tab.classList.add('active');
        const target = tab.getAttribute('data-tab');
        document.getElementById(target).classList.add('active');
      });
    });
  </script>

</body>
</html>

<?php
$conn->close();
?>