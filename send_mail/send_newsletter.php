<?php
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $email = $_POST['newsletter_email'];

  require '../php-mailer/Exception.php';
  require '../php-mailer/PHPMailer.php';
  require '../php-mailer/SMTP.php';

  $mail = new PHPMailer(true);
  $response = array(); // Define the response array

  try {
    $mail->SMTPDebug = 0;
    $mail->isSMTP();
    $mail->Host = 'smtp.office365.com';
    $mail->SMTPAuth = true;
    $mail->Username = 'zoi_system@zainomantel.com';
    $mail->Password = 'Zo_i_#971_Lx';
    $mail->SMTPSecure = 'tls';
    $mail->Port = 587;

    $mail->setFrom('zoi_system@zainomantel.com', 'Newsletter Subscriber');
    $mail->addAddress('wholesaleorders@zainomantel.com', 'ZOI User');

    $mail->isHTML(true);
    $mail->Subject = 'Newsletter Subscriber from Website';
    $mail->Body = "Subscriber Email: $email";
    $mail->send();

    $response = array("status" => "Success");
    header('Content-Type: application/json');
    echo json_encode($response);
  } catch (Exception $e) {
    $response = array("status" => "Error", "message" => $mail->ErrorInfo);
    header('Content-Type: application/json');
    echo json_encode($response);
  }
}
?>