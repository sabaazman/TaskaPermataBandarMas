<?php
include 'config.php';
include 'mail_config.php';
session_start();

// Semak jika pentadbir sudah log masuk
if (!isset($_SESSION['id_pentadbir'])) {
    header("Location: login.php");
    exit();
}

// Semak jika parameter 'action' dan 'id' ada dalam URL
if (isset($_GET['action']) && isset($_GET['id'])) {
    $action = $_GET['action'];
    $id = $_GET['id'];
    $status = null;
    $table = null;
    $id_field = null;
    $redirect = null;

    // Tentukan status berdasarkan tindakan approve/reject
    if (strpos($action, 'approve') === 0) {
        $status = 1;
    } elseif (strpos($action, 'reject') === 0) {
        $status = 0;
    } else {
        echo "Tindakan tidak sah.";
        exit();
    }

    // Tentukan table, id_field dan redirect ikut jenis entiti
    if (strpos($action, 'ibubapa') !== false) {
        $table = "ibubapa";
        $id_field = "id_ibubapa";
        $redirect = 'pengesahan_pelajar.php';

        // Get parent's email and name
        $sql_parent = "SELECT email_bapa, nama_bapa FROM ibubapa WHERE id_ibubapa = ?";
        $stmt_parent = $conn->prepare($sql_parent);
        $stmt_parent->bind_param("i", $id);
        $stmt_parent->execute();
        $result_parent = $stmt_parent->get_result();
        $parent_data = $result_parent->fetch_assoc();
        $stmt_parent->close();

        // Update ibu bapa
        $sql = "UPDATE $table SET pengesahan = ? WHERE $id_field = ?";
        $stmt = $conn->prepare($sql);

        if ($stmt) {
            $stmt->bind_param("ii", $status, $id);
            $stmt->execute();
            $affected = $stmt->affected_rows;
            $stmt->close();

            if ($affected > 0) {
                // Log email sending attempt
                error_log("Attempting to send email to: " . $parent_data['email_bapa']);

                // Send email notification
                $subject = $status == 1 ? "Pendaftaran Anda Telah Diluluskan" : "Pendaftaran Anda Tidak Diluluskan";
                $body = $status == 1 ? 
                    "Salam {$parent_data['nama_bapa']},<br><br>Pendaftaran anda di Taska Permata Bandar Mas telah diluluskan. Anda kini boleh log masuk ke sistem." :
                    "Salam {$parent_data['nama_bapa']},<br><br>Harap maaf, pendaftaran anda di Taska Permata Bandar Mas tidak dapat diluluskan. Sila hubungi pihak pentadbiran untuk maklumat lanjut.";

                // Ensure sendMail is called correctly and verify if it returns true
                $mail_sent = sendMail($parent_data['email_bapa'], $subject, $body);

                if ($mail_sent) {
                    error_log("Email sent successfully to: " . $parent_data['email_bapa']);
                } else {
                    error_log("Failed to send email to: " . $parent_data['email_bapa']);
                }

                if ($status == 1) {
                    // Approve all children if parent is approved
                    $sql_approve_children = "UPDATE pelajar SET pengesahan = 1 WHERE ibubapa_id = ?";
                    $stmt2 = $conn->prepare($sql_approve_children);
                    if ($stmt2) {
                        $stmt2->bind_param("i", $id);
                        $stmt2->execute();
                        $stmt2->close();
                    }
                }

                echo "<script>
                        alert('Pengesahan berjaya.');
                        window.location.href = '$redirect';
                      </script>";
            } else {
                echo "<script>
                        alert('Pengesahan gagal. Sila cuba lagi.');
                        window.location.href = '$redirect';
                      </script>";
            }
        }

    } elseif (strpos($action, 'pelajar') !== false) {
        $table = "pelajar";
        $id_field = "ic_pelajar";
        $redirect = 'pengesahan_pelajar.php';

        // Get student and parent info
        $sql_info = "SELECT p.nama_pelajar, i.email_bapa, i.nama_bapa 
                     FROM pelajar p 
                     JOIN ibubapa i ON p.ibubapa_id = i.id_ibubapa 
                     WHERE p.ic_pelajar = ?";
        $stmt_info = $conn->prepare($sql_info);
        $stmt_info->bind_param("s", $id);
        $stmt_info->execute();
        $result_info = $stmt_info->get_result();
        $info = $result_info->fetch_assoc();
        $stmt_info->close();

        $sql = "UPDATE $table SET pengesahan = ? WHERE $id_field = ?";
        $stmt = $conn->prepare($sql);

        if ($stmt) {
            $stmt->bind_param("is", $status, $id);
            $stmt->execute();
            if ($stmt->affected_rows > 0) {
                // Log email sending attempt
                error_log("Attempting to send email to: " . $info['email_bapa']);

                // Send email notification
                $subject = $status == 1 ? 
                    "Pendaftaran Anak Anda Telah Diluluskan" : 
                    "Pendaftaran Anak Anda Tidak Diluluskan";
                $body = $status == 1 ? 
                    "Salam {$info['nama_bapa']},<br><br>Pendaftaran untuk anak anda {$info['nama_pelajar']} di Taska Permata Bandar Mas telah diluluskan." :
                    "Salam {$info['nama_bapa']},<br><br>Harap maaf, pendaftaran untuk anak anda {$info['nama_pelajar']} di Taska Permata Bandar Mas tidak dapat diluluskan. Sila hubungi pihak pentadbiran untuk maklumat lanjut.";

                // Ensure sendMail is called correctly and verify if it returns true
                $mail_sent = sendMail($info['email_bapa'], $subject, $body);

                if ($mail_sent) {
                    error_log("Email sent successfully to: " . $info['email_bapa']);
                } else {
                    error_log("Failed to send email to: " . $info['email_bapa']);
                }

                echo "<script>
                        alert('Pengesahan berjaya.');
                        window.location.href = '$redirect';
                      </script>";
            } else {
                echo "<script>
                        alert('Pengesahan gagal. Sila cuba lagi.');
                        window.location.href = '$redirect';
                      </script>";
            }
            $stmt->close();
        }
    } elseif (strpos($action, 'pendidik') !== false || (isset($_GET['type']) && $_GET['type'] == 'pendidik')) {
        $table = "pendidik";
        $id_field = "id_pendidik";
        $redirect = 'pengesahan_pendidik.php';

        // Get pendidik's email and name
        $sql_pendidik = "SELECT email_pendidik, nama_pendidik FROM pendidik WHERE id_pendidik = ?";
        $stmt_pendidik = $conn->prepare($sql_pendidik);
        $stmt_pendidik->bind_param("i", $id);
        $stmt_pendidik->execute();
        $result_pendidik = $stmt_pendidik->get_result();
        $pendidik_data = $result_pendidik->fetch_assoc();
        $stmt_pendidik->close();

        // Update pendidik
        $sql = "UPDATE $table SET pengesahan = ? WHERE $id_field = ?";
        $stmt = $conn->prepare($sql);

        if ($stmt) {
            $stmt->bind_param("ii", $status, $id);
            $stmt->execute();
            $affected = $stmt->affected_rows;
            $stmt->close();

            if ($affected > 0) {
                // Log email sending attempt
                error_log("Attempting to send email to: " . $pendidik_data['email_pendidik']);

                // Send email notification
                $subject = $status == 1 ? "Pendaftaran Anda Telah Diluluskan" : "Pendaftaran Anda Tidak Diluluskan";
                $body = $status == 1 ? 
                    "Salam {$pendidik_data['nama_pendidik']},<br><br>Pendaftaran anda sebagai pendidik di Taska Permata Bandar Mas telah diluluskan. Anda kini boleh log masuk ke sistem." :
                    "Salam {$pendidik_data['nama_pendidik']},<br><br>Harap maaf, pendaftaran anda sebagai pendidik di Taska Permata Bandar Mas tidak dapat diluluskan. Sila hubungi pihak pentadbiran untuk maklumat lanjut.";

                $mail_sent = sendMail($pendidik_data['email_pendidik'], $subject, $body);

                if ($mail_sent) {
                    error_log("Email sent successfully to: " . $pendidik_data['email_pendidik']);
                } else {
                    error_log("Failed to send email to: " . $pendidik_data['email_pendidik']);
                }

                echo "<script>
                        alert('Pengesahan berjaya.');
                        window.location.href = '$redirect';
                      </script>";
            } else {
                echo "<script>
                        alert('Pengesahan gagal. Sila cuba lagi.');
                        window.location.href = '$redirect';
                      </script>";
            }
        }
    }
} else {
    echo "Tindakan tidak sah.";
}
?>
