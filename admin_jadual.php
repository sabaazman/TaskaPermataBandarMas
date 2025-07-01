<?php
include 'config.php';
session_start();

// Function to convert AM/PM to 24-hour format for database storage
function convertTo24Hour($time) {
    return date("H:i", strtotime($time));
}

// Function to convert 24-hour to AM/PM for display
function convertToAmPm($time) {
    return date("g:ia", strtotime($time));
}

// Get week and sort parameters
$selected_minggu = 1;
if (isset($_POST['minggu'])) {
    $selected_minggu = intval($_POST['minggu']);
} elseif (isset($_GET['minggu'])) {
    $selected_minggu = intval($_GET['minggu']);
}

$order_by = "STR_TO_DATE(Masa, '%H:%i')"; // Always sort by time properly

// Process Save (Add)
if ($_POST && !empty($_POST['masa'])) {
    $masa = $_POST['masa'];
    $masa_24h = convertTo24Hour($masa);
    $isnin = $_POST['isnin'];
    $selasa = $_POST['selasa'];
    $rabu = $_POST['rabu'];
    $khamis = $_POST['khamis'];
    $jumaat = $_POST['jumaat'];
    $id_pentadbir = 1;
    $minggu = isset($_POST['minggu']) ? intval($_POST['minggu']) : 1;

    // Check duplicate time for same week
    $check_query = "SELECT * FROM jadual WHERE STR_TO_DATE(Masa, '%H:%i') = STR_TO_DATE('$masa_24h', '%H:%i') AND minggu = $minggu";
    $check_result = mysqli_query($conn, $check_query);

    if (mysqli_num_rows($check_result) > 0) {
        echo "<script>
            alert('Ralat: Data untuk masa $masa di Minggu $minggu sudah ada. Sila pilih masa lain.');
            window.location.href = 'admin_jadual.php?minggu=$minggu&sort=" . ($_GET['sort'] ?? '') . "';
        </script>";
        exit();
    } else {
        $insert_query = "INSERT INTO jadual (minggu, Masa, Isnin, Selasa, Rabu, Khamis, Jumaat, id_pentadbir) 
                         VALUES ($minggu, '$masa_24h', '$isnin', '$selasa', '$rabu', '$khamis', '$jumaat', '$id_pentadbir')";
        if (mysqli_query($conn, $insert_query)) {
            echo "<script>alert('Jadual berjaya disimpan!');</script>";
        } else {
            echo "<script>alert('Ralat: " . mysqli_error($conn) . "');</script>";
        }
    }
    header("Location: admin_jadual.php?minggu=$minggu");
    exit();
}

// Process Delete (empty day data)
if (isset($_GET['delete_id'])) {
    $id = intval($_GET['delete_id']);
    $empty_query = "UPDATE jadual SET Isnin='', Selasa='', Rabu='', Khamis='', Jumaat='' WHERE IdJadual = $id";

    if (mysqli_query($conn, $empty_query)) {
        echo "<script>alert('Data jadual telah dikosongkan!');</script>";
    } else {
        echo "<script>alert('Ralat: " . mysqli_error($conn) . "');</script>";
    }
    header("Location: admin_jadual.php?minggu=$selected_minggu");
    exit();
}

// Process Update day data
if ($_POST && isset($_POST['update_id'])) {
    $id = intval($_POST['update_id']);
    $isnin = $_POST['isnin'];
    $selasa = $_POST['selasa'];
    $rabu = $_POST['rabu'];
    $khamis = $_POST['khamis'];
    $jumaat = $_POST['jumaat'];

    $update_query = "UPDATE jadual SET Isnin = '$isnin', Selasa = '$selasa', Rabu = '$rabu', Khamis = '$khamis', Jumaat = '$jumaat' WHERE IdJadual = $id";
    if (mysqli_query($conn, $update_query)) {
        echo "<script>alert('Jadual berjaya dikemaskini!');</script>";
    } else {
        echo "<script>alert('Ralat: " . mysqli_error($conn) . "');</script>";
    }
    header("Location: admin_jadual.php?minggu=$selected_minggu");
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="css/dashboard.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <title>Admin Jadual Mingguan</title>
    <style>
        /* Tajuk */
        h1 {
            text-align: center;
            font-size: 2rem;
            color: #333;
            margin-bottom: 20px;
        }

        /* Button submit umum */
        button[type="submit"], button {
            display: block;
            margin: 20px auto;
            background-color: #3a4065;
            color: white;
            padding: 10px 20px;
            border: none;
            border-radius: 5px;
            font-size: 1rem;
            cursor: pointer;
            transition: background-color 0.3s ease;
        }

        button[type="submit"]:hover, button:hover {
            background-color: #2c2f4f;
            opacity: 0.9;
        }
        /* Styling dropdown dan button filter */
        .filter-form {
            margin: 20px auto;
            max-width: 400px;
            text-align: center;
        }

        .filter-form select, .filter-form button {
            padding: 6px 12px;
            font-size: 1rem;
            border-radius: 5px;
            border: 1px solid #ccc;
            cursor: pointer;
            margin-right: 10px;
        }

        /* Jadual utama */
        table {
            width: 80%;
            max-width: 900px;
            margin: 20px auto;
            border-collapse: collapse;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            background-color: #fff;
            text-align: center;
        }

        th, td {
            padding: 10px 12px;
            border: 1px solid #ddd;
            font-size: 1rem;
        }

        th {
            background-color: #3a4065;
            color: white;
            font-weight: bold;
        }

        tr:nth-child(even) {
            background-color: #f9f9f9;
        }

        tr:hover {
            background-color: #f1f1f1;
        }

        /* Responsive table for smaller screens */
        @media (max-width: 768px) {
            table {
                width: 95%;
            }

            th, td {
                padding: 8px;
                font-size: 0.9rem;
            }
        }

        /* Modal dan overlay */
        #modal {
            display: none;
            position: fixed;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            background-color: white;
            border: 1px solid #ccc;
            padding: 20px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
            z-index: 1000;
            width: 300px;
        }

        #overlay {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0, 0, 0, 0.5);
            z-index: 999;
        }

        #modal.active, #overlay.active {
            display: block;
        }

        /* Label dan input dalam modal */
        label {
            display: block;
            margin: 10px 0 5px;
        }

        input {
            width: 100%;
            padding: 5px;
            box-sizing: border-box;
        }

        /* Button dalam modal */
        button {
            margin-top: 10px;
        }

        /* Button update khusus */
        .button-update {
            background-color: green;
            color: white;
            border: none;
            padding: 5px 10px;
            cursor: pointer;
            border-radius: 5px;
        }

        /* Button close modal */
        .button-close {
            background-color: transparent; /* telus */
            color: black;
            font-size: 1.2rem;
            border: none;
            cursor: pointer;
            float: right;
            padding: 0;
            margin: 0;
            text-decoration: none;
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
        <header>
            <a href="pentadbir_dashboard.php">Dashboard Pentadbir</a>
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
        <main>
    <h1 style="text-align: center; font-size: 2rem; color: #333; margin-bottom: 20px;">
        Kemaskini Mingguan
        <form method="GET" action="admin_jadual.php" style="display: inline-block; margin-left: 10px;">
            <select name="minggu" onchange="this.form.submit()" 
                style="padding: 5px; font-size: 1rem; border-radius: 5px; border: 1px solid #ccc;">
                <?php
                for ($i = 1; $i <= 4; $i++) {
                    $sel = ($i == $selected_minggu) ? "selected" : "";
                    echo "<option value='$i' $sel>Minggu $i</option>";
                }
                ?>
            </select>
        </form>
    </h1>

    <form method="POST" action="admin_jadual.php">
        <input type="hidden" name="minggu" value="<?php echo $selected_minggu; ?>">
        <table>
            <thead>
                <tr>
                    <th>Masa</th>
                    <th>Isnin</th>
                    <th>Selasa</th>
                    <th>Rabu</th>
                    <th>Khamis</th>
                    <th>Jumaat</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td>
                        <select name="masa" required>
                            <option value="">Pilih Masa</option>
                            <?php
                            $masa = ["7:30am", "8:00am", "8:30am", "9:00am", "9:30am", "10:15am", "10:30am", 
                                    "11:20am", "11:45am", "12:00pm", "12:30pm", "1:00pm", "3:00pm", "3:45pm", 
                                    "4:00pm", "4:30pm", "5:00pm"];
                            foreach ($masa as $time) {
                                echo "<option value='$time'>$time</option>";
                            }
                            ?>
                        </select>
                    </td>
                    <td><input type="text" name="isnin" placeholder="Isnin"></td>
                    <td><input type="text" name="selasa" placeholder="Selasa"></td>
                    <td><input type="text" name="rabu" placeholder="Rabu"></td>
                    <td><input type="text" name="khamis" placeholder="Khamis"></td>
                    <td><input type="text" name="jumaat" placeholder="Jumaat"></td>
                </tr>
            </tbody>
        </table>
        <button type="submit" style="display: block; margin: 20px auto; background-color: #3a4065; color: white; padding: 10px 20px; border: none; border-radius: 5px; font-size: 1rem; cursor: pointer;">
            Simpan Jadual
        </button>
    </form>

    <h1 style="text-align: center; font-size: 2rem; color: #333; margin-bottom: 20px;">Jadual Mingguan <?php echo $selected_minggu; ?></h1>
    <table>
        <thead>
            <tr>
                <th>Masa</th>
                <th>Isnin</th>
                <th>Selasa</th>
                <th>Rabu</th>
                <th>Khamis</th>
                <th>Jumaat</th>
                <th>Tindakan</th>
            </tr>
        </thead>
        <tbody>
            <?php
            $query = "SELECT * FROM jadual WHERE minggu = $selected_minggu ORDER BY STR_TO_DATE(Masa, '%H:%i')";
            $result = mysqli_query($conn, $query);
            
            while ($row = mysqli_fetch_assoc($result)) {
                $displayTime = convertToAmPm($row['Masa']);
                echo "<tr>";
                echo "<td>{$displayTime}</td>";
                echo "<td>{$row['Isnin']}</td>";
                echo "<td>{$row['Selasa']}</td>";
                echo "<td>{$row['Rabu']}</td>";
                echo "<td>{$row['Khamis']}</td>";
                echo "<td>{$row['Jumaat']}</td>";
                echo "<td>
                <div style='display: flex; gap: 8px; justify-content: center; align-items: center;'>
                    <button style='background-color: green; color: white; border: none; height: 40px; width: 40px; cursor: pointer; border-radius: 6px; font-size: 15px; display: flex; justify-content: center; align-items: center; padding: 0;' 
                        onclick='openModal({$row['IdJadual']}, \"{$row['Isnin']}\", \"{$row['Selasa']}\", \"{$row['Rabu']}\", \"{$row['Khamis']}\", \"{$row['Jumaat']}\")'>
                        <i class='fas fa-edit' style='margin: 0;'></i>
                    </button>
                    <button style='background-color: red; color: white; border: none; height: 40px; width: 40px; cursor: pointer; border-radius: 6px; font-size: 15px; display: flex; justify-content: center; align-items: center; padding: 0;' 
                        onclick='confirmDelete({$row['IdJadual']})'>
                        <i class='fas fa-trash' style='margin: 0;'></i>
                    </button>
                </div>
            </td>";
                echo "</tr>";
            }
            ?>
        </tbody>
    </table>

    <div id="overlay"></div>
    <div id="modal">
        <button style="background: none; border: none; float: right; cursor: pointer;" onclick="closeModal()">✕</button>
        <h2>Kemaskini Jadual</h2>
        <form method="POST" action="admin_jadual.php">
            <input type="hidden" name="update_id" id="update_id">
            <input type="hidden" name="minggu" value="<?php echo $selected_minggu; ?>">
            
            <div style="display: grid; grid-template-columns: 100px 1fr; gap: 10px; align-items: center; margin-bottom: 15px;">
                <label>Isnin:</label>
                <input type="text" name="isnin" id="modal_isnin" style="padding: 8px;">
                
                <label>Selasa:</label>
                <input type="text" name="selasa" id="modal_selasa" style="padding: 8px;">
                
                <label>Rabu:</label>
                <input type="text" name="rabu" id="modal_rabu" style="padding: 8px;">
                
                <label>Khamis:</label>
                <input type="text" name="khamis" id="modal_khamis" style="padding: 8px;">
                
                <label>Jumaat:</label>
                <input type="text" name="jumaat" id="modal_jumaat" style="padding: 8px;">
            </div>
            
            <button type="submit" style="background-color: #4CAF50; color: white; padding: 10px 15px; border: none; border-radius: 4px; cursor: pointer;">
                Kemaskini
            </button>
        </form>
    </div>

    <script>
        function openModal(id, isnin, selasa, rabu, khamis, jumaat) {
            document.getElementById('update_id').value = id;
            document.getElementById('modal_isnin').value = isnin;
            document.getElementById('modal_selasa').value = selasa;
            document.getElementById('modal_rabu').value = rabu;
            document.getElementById('modal_khamis').value = khamis;
            document.getElementById('modal_jumaat').value = jumaat;
            document.getElementById('modal').classList.add('active');
            document.getElementById('overlay').classList.add('active');
        }

        function closeModal() {
            document.getElementById('modal').classList.remove('active');
            document.getElementById('overlay').classList.remove('active');
        }

        function confirmDelete(id) {
            if (confirm('Adakah anda pasti mahu mengosongkan jadual ini?')) {
                window.location.href = 'admin_jadual.php?delete_id=' + id + '&minggu=<?php echo $selected_minggu; ?>';
            }
        }
    </script>
</main>
</body>
</html>