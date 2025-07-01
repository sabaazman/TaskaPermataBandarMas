<?php
include 'config.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Parent Details
    $ic_bapa = $_POST['ic_bapa'];
    $nama_bapa = $_POST['nama_bapa'];
    $pekerjaan_bapa = $_POST['pekerjaan_bapa'];
    $pendapatan_bapa = $_POST['pendapatan_bapa'];
    $email_bapa = $_POST['email_bapa']; // Used as username
    $no_bapa = $_POST['no_bapa'];
    $ic_ibu = $_POST['ic_ibu'];
    $nama_ibu = $_POST['nama_ibu'];
    $pekerjaan_ibu = $_POST['pekerjaan_ibu'];
    $pendapatan_ibu = $_POST['pendapatan_ibu'];
    $no_ibu = $_POST['no_ibu'];
    $EmailIbu = $_POST['EmailIbu'];

    // Define password as father's IC
    $password_bapa = $ic_bapa;

    // Insert Parent Details along with login credentials
    $sql_ibubapa = "INSERT INTO ibubapa (ic_bapa, nama_bapa, pekerjaan_bapa, pendapatan_bapa, email_bapa, no_bapa, ic_ibu, nama_ibu, pekerjaan_ibu, pendapatan_ibu, no_ibu, EmailIbu, username, password, peranan_id)
                    VALUES ('$ic_bapa', '$nama_bapa', '$pekerjaan_bapa', $pendapatan_bapa, '$email_bapa', '$no_bapa', '$ic_ibu', '$nama_ibu', '$pekerjaan_ibu', $pendapatan_ibu, '$no_ibu', '$EmailIbu', '$email_bapa', '$password_bapa', 2)";
    
    if ($conn->query($sql_ibubapa) === TRUE) {
        $ibubapa_id = $conn->insert_id;

        // Insert Child Details for multiple children
        for ($i = 0; $i < count($_POST['nama_pelajar']); $i++) {
            $ic_pelajar = $_POST['ic_pelajar'][$i];
            $nama_pelajar = $_POST['nama_pelajar'][$i];
            $tahun_pengajian = $_POST['tahun_pengajian'][$i];
            $jantina = $_POST['jantina'][$i];
            $alamat_semasa = $_POST['alamat_semasa'][$i];
            $umur = $_POST['umur'][$i];
            $Alahan = $_POST['Alahan'][$i];

            // Handle Image Upload for Child
            $gambar_pelajar = ''; // Initialize the variable to store the file name
            $sijilLahir_pelajar = ''; // Initialize the variable for birth certificate

            if (isset($_FILES['gambar_pelajar']['name'][$i]) && $_FILES['gambar_pelajar']['error'][$i] === UPLOAD_ERR_OK) {
                $target_dir = "uploads/";

                // Create the uploads directory if it doesn't exist
                if (!file_exists($target_dir)) {
                    mkdir($target_dir, 0777, true); // Set directory permissions to writable
                }

                // Generate a unique file name
                $unique_id = uniqid(); // Unique identifier
                $original_name = basename($_FILES["gambar_pelajar"]["name"][$i]);
                $target_file = $target_dir . $unique_id . "_" . $original_name;
                $gambar_pelajar = $unique_id . "_" . $original_name;

                // Check the file type
                $imageFileType = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));
                $allowed_types = ['jpg', 'jpeg', 'png'];

                if (!in_array($imageFileType, $allowed_types)) {
                    die("Only JPG, JPEG, PNG are allowed for images.");
                }

                // Move the uploaded file to the uploads directory
                if (move_uploaded_file($_FILES["gambar_pelajar"]["tmp_name"][$i], $target_file)) {
                    // Successfully uploaded
                } else {
                    die("There was an error uploading the image.");
                }
            } else {
                die("Please upload a valid image file.");
            }

            // Upload Sijil Lahir Pelajar (Birth Certificate)
            if (isset($_FILES['sijilLahir_pelajar']['name'][$i]) && $_FILES['sijilLahir_pelajar']['error'][$i] === UPLOAD_ERR_OK) {
                $target_dir = "uploads/";

                // Create the uploads directory if it doesn't exist
                if (!file_exists($target_dir)) {
                    mkdir($target_dir, 0777, true); // Set directory permissions to writable
                }

                // Generate a unique file name for sijilLahir_pelajar
                $unique_id = uniqid(); // Unique identifier
                $original_name = basename($_FILES["sijilLahir_pelajar"]["name"][$i]);
                $target_file = $target_dir . $unique_id . "_" . $original_name;
                $sijilLahir_pelajar = $unique_id . "_" . $original_name;

                // Check the file type
                $documentFileType = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));
                $allowed_types = ['jpg', 'jpeg', 'png', 'pdf']; // Added pdf to the allowed types

                if (!in_array($documentFileType, $allowed_types)) {
                    die("Only JPG, JPEG, PNG, and PDF files are allowed for birth certificate files.");
                }

                // Move the uploaded file to the uploads directory
                if (move_uploaded_file($_FILES["sijilLahir_pelajar"]["tmp_name"][$i], $target_file)) {
                    // Successfully uploaded birth certificate
                } else {
                    die("There was an error uploading the birth certificate file.");
                }
            }

            // Insert Child Details with allergy field, image file path, and birth certificate
            $sql_pelajar = "INSERT INTO pelajar (ic_pelajar, nama_pelajar, tahun_pengajian, jantina, alamat_semasa, umur, gambar_pelajar, sijilLahir_pelajar, Alahan, ibubapa_id)
                            VALUES ('$ic_pelajar', '$nama_pelajar', '$tahun_pengajian', '$jantina', '$alamat_semasa', $umur, '$gambar_pelajar', '$sijilLahir_pelajar', '$Alahan', $ibubapa_id)";
            
            if ($conn->query($sql_pelajar) === TRUE) {
                // Successful registration message for each child
            } else {
                echo "Error inserting child details: " . $conn->error;
            }
        }

        // Redirect after successful registration
        echo "<script>
                alert('Pendaftaran berjaya. Status pengesahan akan dihantar melalui email bapa.');
                window.location.href = 'index.php';;  // Redirect to index.html after OK
              </script>";
    } else {
        echo "Error inserting parent details: " . $conn->error;
    }
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
        <h3>Butiran Ibu Bapa</h3>
        <div class="container">
            <div class="column">
                <label>Nama Bapa:</label>
                <input type="text" name="nama_bapa" required>
                <label>No Kad Pengenalan Bapa:</label>
                <input type="text" name="ic_bapa" class="form-control" required pattern="^[0-9]{12}$" title="Sila masukkan kad pengenalan yang sah dengan 12 digit sahaja">
                <label>Pekerjaan Bapa:</label>
                <input type="text" name="pekerjaan_bapa">
                <label>Pendapatan Bapa:</label>
                <input type="number" step="0.01" name="pendapatan_bapa">
                <label>Email Bapa:</label>
                <input type="email" name="email_bapa" required>
                <label>No Telefon Bapa:</label>
                <input type="text" name="no_bapa" class="form-control" required pattern="^[0-9]{10}$" title="Sila masukkan nombor telefon yang sah dengan 10 hingga 15 digit sahaja">
            </div>
            <div class="column">
                <label>Nama Ibu:</label>
                <input type="text" name="nama_ibu">
                <label>No Kad Pengenalan Ibu:</label>
                <input type="text" name="ic_ibu" class="form-control" required pattern="^[0-9]{12}$" title="Sila masukkan kad pengenalan yang sah dengan 12 digit sahaja">
                <label>Pekerjaan Ibu:</label>
                <input type="text" name="pekerjaan_ibu">
                <label>Pendapatan Ibu:</label>
                <input type="number" step="0.01" name="pendapatan_ibu">
                <label>Email Ibu:</label>
                <input type="email" name="EmailIbu" required>
                <label>No Telefon Ibu:</label>
                <input type="text" name="no_ibu" class="form-control" required pattern="^[0-9]{10}$" title="Sila masukkan nombor telefon yang sah dengan 10 hingga 15 digit sahaja">
            </div>
        </div>

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
            <button type="button" class="back" onclick="window.location.href='index.php'">Kembali</button>
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
