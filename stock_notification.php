<?php
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require __DIR__ . '/PHPMailer-master/PHPMailer-master/src/Exception.php';
require __DIR__ . '/PHPMailer-master/PHPMailer-master/src/PHPMailer.php';
require __DIR__ . '/PHPMailer-master/PHPMailer-master/src/SMTP.php';

function sendEmailNotification($productName, $threshold, $currentStock) {
    $mail = new PHPMailer(true);

    try {
        // Server settings
        $mail->SMTPDebug = 2; // Enable verbose debug output
        $mail->isSMTP();
        $mail->Host = 'smtp.gmail.com'; // Set the SMTP server to send through
        $mail->SMTPAuth = true;
        $mail->Username = 'presslogic36@gmail.com'; // Your new Gmail address
        $mail->Password = 'rxbm nqcd mcfd bpxh'; // App Password generated
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
        $mail->Port = 587;

        // Recipients
        $mail->setFrom('no-reply@yourstore.com', 'PressLogic');
        $mail->addAddress('altinejup@gmail.com'); // Add a recipient
        $mail->addAddress('aa30340@seeu.edu.mk'); // Add a recipient
        $mail->addAddress('aysegulaksoy1925@gmail.com'); // Add a recipient
        $mail->addAddress('odaaid9@gmail.com'); // Add a recipient
     


        // Content
        $mail->isHTML(true);
        $mail->Subject = "Stock Alert: $productName";
        $mail->Body    = "The stock for $productName has dropped below the threshold of $threshold units. <br><h2>Current stock level:<b> $currentStock </b>units. Please reorder soon.</h2>";

        $mail->send();
        error_log("Email sent successfully to altinejup@gmail.com for $productName stock alert.");
    } catch (Exception $e) {
        error_log("Failed to send email: {$mail->ErrorInfo}");
    }
}
?>
