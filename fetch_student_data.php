<?php
include 'config.php';

if (isset($_GET['ic_pelajar'])) {
    $ic_pelajar = $_GET['ic_pelajar'];

    // Fetch student data
    $sql_student = "SELECT * FROM PELAJAR WHERE ic_pelajar = '$ic_pelajar'";
    $student_result = $conn->query($sql_student);
    $student = $student_result->fetch_assoc();

    // Fetch guardian data
    $ibubapa_id = $student['ibubapa_id'];
    $sql_ibubapa = "SELECT * FROM IBUBAPA WHERE id_ibubapa = '$ibubapa_id'";
    $ibubapa_result = $conn->query($sql_ibubapa);
    $ibubapa = $ibubapa_result->fetch_assoc();

    // Combine both data
    $data = array_merge($student, $ibubapa);
    echo json_encode($data);
}
?>
