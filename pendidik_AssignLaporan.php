<?php
include 'config.php';

// Handle delete request
if (isset($_GET['delete'])) {
    $ic = $_GET['delete'];

    // Validate IC before proceeding
    if (!empty($ic)) {
        $sql = "DELETE FROM pelajar WHERE ic_pelajar = '$ic'";
        if ($conn->query($sql) === TRUE) {
            echo "Record deleted successfully.";
        } else {
            echo "Error deleting record: " . $conn->error;
        }
    } else {
        echo "Invalid IC.";
    }
}

// Fetch student data
$sql = "SELECT * FROM pelajar";
$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pendidik - Laporan</title>
    <style>
        table {
            width: 100%;
            border-collapse: collapse;
        }
        th, td {
            padding: 8px;
            border: 1px solid #ddd;
            text-align: center;
        }
        th {
            background-color: #f4f4f4;
        }
        button {
            padding: 5px 10px;
            color: white;
            background-color: red;
            border: none;
            cursor: pointer;
        }
        button:hover {
            opacity: 0.8;
        }
    </style>
</head>
<body>
    <h2>Senarai Pelajar</h2>
    <table>
        <thead>
            <tr>
                <th>No</th>
                <th>IC Pelajar</th>
                <th>Nama Pelajar</th>
                <th>Tindakan</th>
            </tr>
        </thead>
        <tbody>
            <?php
            if ($result->num_rows > 0) {
                $no = 1;
                while ($row = $result->fetch_assoc()) {
                    echo "<tr>
                        <td>$no</td>
                        <td>{$row['ic_pelajar']}</td>
                        <td>{$row['nama_pelajar']}</td>
                        <td><a href='?delete={$row['ic_pelajar']}'><button>Delete</button></a></td>
                    </tr>";
                    $no++;
                }
            } else {
                echo "<tr><td colspan='9'>No records found.</td></tr>";
            }
            ?>
        </tbody>
    </table>
</body>
</html>
