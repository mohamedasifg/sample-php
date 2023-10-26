<?php
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;

require '../php-mailer/Exception.php';
require '../php-mailer/PHPMailer.php';
require '../php-mailer/SMTP.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $response = array();

    $service = $_POST['zoi_service'];
    $org_name = $_POST['org_name'];
    $applicant_name = $_POST['applicant_name'];
    $email = $_POST['email'];
    $phone = $_POST['phone'];
    $message = $_POST['message'];
    $recaptchaResponse = $_POST['g-recaptcha-response'];

    // Verify reCAPTCHA response
    $recaptchaSecret = '6Lfhs6koAAAAAG-w1jtxo7VFV7PkYdoTTeSfgxLr'; // Replace with your reCAPTCHA secret key
    $recaptchaVerifyUrl = 'https://www.google.com/recaptcha/api/siteverify';
        $recaptchaData = [
            'secret' => $recaptchaSecret,
            'response' => $recaptchaResponse,
        ];
        $recaptchaOptions = [
            'http' => [
                'method' => 'POST',
                'content' => http_build_query($recaptchaData),
            ],
        ];
        $recaptchaContext = stream_context_create($recaptchaOptions);
        $recaptchaResult = file_get_contents($recaptchaVerifyUrl, false, $recaptchaContext);
        $recaptchaResult = json_decode($recaptchaResult);
    
        if (!$recaptchaResult->success) {
            $response = array("status" => "Error", "message" => "reCAPTCHA verification failed");
        } else {
            try {
                $mail = new PHPMailer(true);

             $mail->SMTPDebug = false; // Enable verbose debug output
			 $mail->CharSet = 'utf-8';
         $mail->isSMTP(); // Send using SMTP
         $mail->Host = 'smtp.office365.com'; // Set the SMTP server to send through
         $mail->SMTPAuth = true; // Enable SMTP authentication
         $mail->Username = 'zoi_system@zainomantel.com'; // SMTP username
         $mail->Password = 'Zo_i_#971_Lx'; // SMTP password
         $mail->SMTPSecure = 'tls';
         $mail->Port = 587;



       // $mail->SMTPDebug = 2;
      //  $mail->isSMTP(); // Send using SMTP
      //  $mail->Host = 'smtp.gmail.com'; // Set the SMTP server to send through
     //   $mail->SMTPAuth = true; // Enable SMTP authentication
      //  $mail->Username = '@gmail.com'; // SMTP username
     //   $mail->Password = 'vlrw nnnh nbii yjdn'; // SMTP password
      //  $mail->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS; // Enable implicit TLS encryption
      //  $mail->Port = 465; // TCP port to connect to; use 587 if you have set `SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS`

            $mail->setFrom('zoi_system@zainomantel.com', 'ZOI Contact US Inquiry');

            $recipient = '';

            if ($service === 'Infrastructure' || $service === 'Connectivity' || $service === 'Internet') {
                $recipient = 'wholesaleorders@zainomantel.com';
            } elseif ($service === 'Global Carrier Solutions') {
                $recipient = 'carriersales@zainomantel.com';
            } elseif ($service === 'Roaming and Mobile Services') {
                $recipient = 'RoamingWS@zainomantel.com';
            }

            if ($recipient) {
                $mail->addAddress($recipient, $service);
            }

            $mail->isHTML(true);
            $mail->Subject = 'ZOI Contact US Inquiry';
            $mail->Body = "Service: $service <br> Org Name: $org_name <br> Applicant Name: $applicant_name <br> Email: $email <br> Phone: $phone <br> Message: $message <br>";

            $mail->send();

            $response = array("status" => "Success");
            header('Content-Type: application/json');
            echo json_encode($response);
        } catch (Exception $e) {
            // Log the error
            error_log("PHPMailer Error: " . $e->getMessage(), 0);
            // Send a user-friendly error response
            $response = array("status" => "Error", "message" => "An error occurred while processing your request. Please try again later.");
            header('Content-Type: application/json');
            echo json_encode($response);
        }
    }
}
?>


            

