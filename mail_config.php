<?php
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;

// Include PHPMailer classes
require 'vendor/PHPMailer-master/src/Exception.php';
require 'vendor/PHPMailer-master/src/PHPMailer.php';
require 'vendor/PHPMailer-master/src/SMTP.php';

function sendMail($to, $subject, $body) {
    $mail = new PHPMailer(true);

    try {
        // Server settings
        $mail->isSMTP();
        $mail->Host = 'smtp.gmail.com';  // Gmail SMTP server
        $mail->SMTPAuth = true;
        $mail->Username = 'sakinahbalqis16@gmail.com';  // Your email address
        $mail->Password = 'sxlbzuesvuadskpz';    // Your app password (Make sure to set up 2-factor authentication and use an App Password)
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
        $mail->Port = 587;

        // Recipients
        $mail->setFrom('sakinahbalqis16@gmail.com', 'Taska Permata Bandar Mas');  // Your name
        $mail->addAddress($to);  // Parent's email address

        // Content
        $mail->isHTML(true);
        $mail->Subject = $subject;
        $mail->Body    = $body;

        // Send email
        $mail->send();
        return true; // If email was sent successfully
    } catch (Exception $e) {
        // Log error if sending failed
        error_log("Email sending failed: " . $e->getMessage());
        return false; // If email was not sent
    }
}
?>
