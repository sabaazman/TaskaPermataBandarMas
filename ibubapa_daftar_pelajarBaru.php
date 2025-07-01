<?php
session_start();
include 'config.php'; // Include the database configuration

// Check if the parent is logged in
if (!isset($_SESSION['username'])) {
    header('Location: login.php'); // Redirect to login if not logged in
    exit();
}

// Get the parent ID from the session
$ibubapa_id = $_SESSION['id_ibubapa']; // Assuming 'id_ibubapa' is stored in the session after login

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Loop through each child to register them
    for ($i = 0; $i < count($_POST['nama_pelajar']); $i++) {
        $ic_pelajar = $_POST['ic_pelajar'][$i];
        $nama_pelajar = $_POST['nama_pelajar'][$i];
        $tahun_pengajian = $_POST['tahun_pengajian'][$i];
        $jantina = $_POST['jantina'][$i];
        $alamat_semasa = $_POST['alamat_semasa'][$i];
        $umur = $_POST['umur'][$i];
        $Alahan = $_POST['Alahan'][$i];

        // Handle image upload for the child
        $gambar_pelajar = ''; // Initialize to empty string
        if (isset($_FILES['gambar_pelajar']['name'][$i]) && $_FILES['gambar_pelajar']['error'][$i] === UPLOAD_ERR_OK) {
            $target_dir = "uploads/";

            // Create the directory if it doesn't exist
            if (!file_exists($target_dir)) {
                mkdir($target_dir, 0777, true); // Set permissions
            }

            // Generate a unique filename for the image
            $unique_id = uniqid();
            $original_name = basename($_FILES["gambar_pelajar"]["name"][$i]);
            $target_file = $target_dir . $unique_id . "_" . $original_name;
            $gambar_pelajar = $unique_id . "_" . $original_name;

            // Validate file type (only allow jpg, jpeg, png)
            $imageFileType = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));
            $allowed_types = ['jpg', 'jpeg', 'png'];

            if (!in_array($imageFileType, $allowed_types)) {
                die("Only JPG, JPEG, PNG images are allowed.");
            }

            // Move the uploaded image to the target directory
            if (!move_uploaded_file($_FILES["gambar_pelajar"]["tmp_name"][$i], $target_file)) {
                die("Error uploading the file.");
            }
        }

        // **Handle Sijil Lahir Pelajar upload (baru ditambah)**
        $sijilLahir_pelajar = ''; // Initialize to empty string
        if (isset($_FILES['sijilLahir_pelajar']['name'][$i]) && $_FILES['sijilLahir_pelajar']['error'][$i] === UPLOAD_ERR_OK) {
            $target_dir = "uploads/";

            // Create directory if doesn't exist
            if (!file_exists($target_dir)) {
                mkdir($target_dir, 0777, true);
            }

            $unique_id_cert = uniqid();
            $original_name_cert = basename($_FILES["sijilLahir_pelajar"]["name"][$i]);
            $target_file_cert = $target_dir . $unique_id_cert . "_" . $original_name_cert;
            $sijilLahir_pelajar = $unique_id_cert . "_" . $original_name_cert;

            // Allowed file types for sijil lahir
            $allowed_cert_types = ['jpg', 'jpeg', 'png', 'pdf'];

            $certFileType = strtolower(pathinfo($target_file_cert, PATHINFO_EXTENSION));
            if (!in_array($certFileType, $allowed_cert_types)) {
                die("Only JPG, JPEG, PNG, and PDF files are allowed for birth certificate files.");
            }

            if (!move_uploaded_file($_FILES["sijilLahir_pelajar"]["tmp_name"][$i], $target_file_cert)) {
                die("Error uploading the birth certificate file.");
            }
        }

        // Insert the child's details into the pelajar table with sijilLahir_pelajar field
        $sql_pelajar = "INSERT INTO pelajar (ic_pelajar, nama_pelajar, tahun_pengajian, jantina, alamat_semasa, umur, alahan, gambar_pelajar, sijilLahir_pelajar, ibubapa_id) 
                        VALUES ('$ic_pelajar', '$nama_pelajar', '$tahun_pengajian', '$jantina', '$alamat_semasa', '$umur', '$Alahan', '$gambar_pelajar', '$sijilLahir_pelajar', '$ibubapa_id')";

        if ($conn->query($sql_pelajar) === TRUE) {
            // You can add success message or leave it empty
        } else {
            echo "Error: " . $sql_pelajar . "<br>" . $conn->error;
        }
    }

    // Redirect after successful registration
    echo "<script>
            alert('Pendaftaran berjaya. Status pengesahan akan dihantar melalui email bapa.');
            window.location.href = 'ibubapa_dashboard.php';  // Redirect to dashboard after successful registration
          </script>";
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pendaftaran Pelajar Baharu</title>
    <style>
        /* CSS Gaya */
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
            background-color: #f9f9f9;
        }

        nav {
            background-color: #333;
            color: white;
            padding: 1rem;
            text-align: center;
        }

        h2, h3 {
            text-align: center;
            margin: 1rem 0;
        }

        form {
            max-width: 800px;
            margin: 2rem auto;
            padding: 1.5rem;
            background: white;
            border-radius: 8px;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
        }

        .container {
            display: flex;
            justify-content: space-between;
            gap: 2rem;
        }

        .column {
            flex: 1;
        }

        label {
            display: block;
            margin: 0.5rem 0 0.2rem;
        }

        input, select, textarea {
            width: 100%;
            padding: 0.5rem;
            margin-bottom: 1rem;
            border: 1px solid #ccc;
            border-radius: 4px;
        }

        .buttons {
            display: flex;
            justify-content: center;
            gap: 1rem;
        }

        button {
            padding: 0.5rem 1rem;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            background-color: #4caf50;
            color: white;
        }

        button.back {
            background-color: #f44336; /* Red for back button */
        }

        button:hover {
            opacity: 0.9;
        }

        .add-child {
            display: flex;
            justify-content: flex-end;
            align-items: center;
            margin-top: 1rem;
        }

        .add-child button {
            background-color: #2196F3; /* Blue for Add button */
            font-size: 30px;
            color: white;
            border: none;
            padding: 10px 15px;
            cursor: pointer;
            border-radius: 5px;
            width: 50px;
            height: 50px;
            text-align: center;
            display: inline-flex;
            justify-content: center;
            align-items: center;
        }

        .child-container {
            margin-top: 20px;
            padding: 20px;
            border: 2px solid #ddd;
            border-radius: 8px;
            background-color: #f8f8f8;
        }

        .child-container h4 {
            text-align: center;
            font-size: 20px;
            margin-bottom: 15px;
            color: #333;
        }

    </style>
</head>
<body>
    <nav>
        <h2>Pendaftaran Pelajar Baharu</h2>
    </nav>

    <form method="post" enctype="multipart/form-data"> <!-- Added enctype for file upload -->

        <div id="children-container">
            <h3>Butiran Pelajar</h3>
            <div class="container child-container" id="child1">
                <h4>Anak 1</h4>
                <div class="column">
                    <label>Nama Pelajar:</label>
                    <input type="text" name="nama_pelajar[]" required>
                    <label>Kad MyKid Pelajar:</label>
                    <input type="text" name="ic_pelajar[]" required>
                    <label>Tahun Pengajian:</label>
                    <input type="text" name="tahun_pengajian[]">
                    <label>Jantina:</label>
                    <select name="jantina[]" required>
                        <option value="Lelaki">Lelaki</option>
                        <option value="Perempuan">Perempuan</option>
                    </select>
                    <label>Alamat Semasa:</label>
                    <textarea name="alamat_semasa[]"></textarea>
                    <label>Umur:</label>
                    <select name="umur[]" required>
                        <option value="2">2</option>
                        <option value="3">3</option>
                        <option value="4">4</option>
                    </select>
                    <label>Alahan/Penyakit:</label>
                    <input type="text" name="Alahan[]">
                    <label>Gambar Pelajar:</label>
                    <input type="file" name="gambar_pelajar[]" accept="image/*">
                    <label>Sijil Kelahiran Pelajar:</label>
                    <input type="file" name="sijilLahir_pelajar[]" accept="image/*,.pdf">
                </div>
            </div>
        </div>

        <div class="add-child">
            <button type="button" id="add-child-button">+</button>
        </div>

        <div class="buttons">
            <button type="button" class="back" onclick="window.location.href='ibubapa_dashboard.php'">Kembali</button>
            <button type="submit">Daftar</button>
        </div>
    </form>

    <script>
        let childCount = 1;

        document.getElementById('add-child-button').addEventListener('click', function () {
            childCount++;
            let container = document.getElementById('children-container');
            let newChildContainer = document.createElement('div');
            newChildContainer.classList.add('container', 'child-container');
            newChildContainer.innerHTML = `
                <h4>Anak ${childCount}</h4>
                <div class="column">
                    <label>Nama Pelajar:</label>
                    <input type="text" name="nama_pelajar[]" required>
                    <label>Kad MyKid Pelajar:</label>
                    <input type="text" name="ic_pelajar[]" required>
                    <label>Tahun Pengajian:</label>
                    <input type="text" name="tahun_pengajian[]">
                    <label>Jantina:</label>
                    <select name="jantina[]" required>
                        <option value="Lelaki">Lelaki</option>
                        <option value="Perempuan">Perempuan</option>
                    </select>
                    <label>Alamat Semasa:</label>
                    <textarea name="alamat_semasa[]"></textarea>
                    <label>Umur:</label>
                    <select name="umur[]" required>
                        <option value="2">2</option>
                        <option value="3">3</option>
                        <option value="4">4</option>
                    </select>
                    <label>Alahan/Penyakit:</label>
                    <input type="text" name="Alahan[]">
                    <label>Gambar Pelajar:</label>
                    <input type="file" name="gambar_pelajar[]" accept="image/*">
                    <label>Sijil Kelahiran Pelajar:</label>
                    <input type="file" name="sijilLahir_pelajar[]" accept="image/*,.pdf">
                </div>
            `;
            container.appendChild(newChildContainer);
        });
    </script>
</body>
</html>
