<?php
use PHPMailer\PHPMailer\PHPMailer;

require_once '../vendor/autoload.php';

// send and reply settings
$sendToAddress = 'magdalena.gonzalez@geointegrity.com';
$sendToName = 'Magdalena Gonzalez - GeoIntegrity';
$replyToAddress = 'magdalena.gonzalez@geointegrity.com';
$replyToName = 'Magdalena Gonzalez - GeoIntegrity';

$errors = array(); // array to hold validation errors
$data   = array(); // array to pass back data

if($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name    = stripslashes(trim($_POST['name']));
    $email   = stripslashes(trim($_POST['email']));
    $message = stripslashes(trim($_POST['message']));


    if (empty($name)) {
        $errors['name'] = 'Name is required.';
    }

    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors['email'] = 'Email is invalid.';
    }

    if (empty($message)) {
        $errors['message'] = 'Message is required.';
    }

    if (!empty($errors)) {
        $data['success'] = false;
        $data['errors']  = $errors;
    } else {

        $mail = new PHPMailer;
        $mail->isSMTP();
        $mail->SMTPAuth = true;
        $mail->Username = 'AKIAJFO7ACH5R7GRUHQA';
        $mail->Password = 'At+hixEplzjygepMYCGQEhuXw8K7c1ikDatX60TFfAxb';
        $mail->Host = 'email-smtp.us-west-2.amazonaws.com';
        $mail->Port = 465;
        $mail->SMTPSecure = 'ssl';

        $mail->addAddress($sendToAddress, $sendToName);
        $mail->addReplyTo($replyToAddress, $replyToName);
        $mail->setFrom($email, $name);

        $mail->Subject = "Message from GeoIntegrity - Website - Contact Form";

        $mail->isHTML(true);
        $mail->Body = '
            <strong>Name: </strong>'.$name.'<br />
            <strong>Email: </strong>'.$email.'<br />
            <strong>Message: </strong>'.nl2br($message).'<br />
        ';

        $mail->AltBody = "Name: $name\r\nEmail: $email\r\nMessage: $message";

        if(!$mail->send()) {
          $data['success'] = false;
          $data['errors']  = $mail->ErrorInfo;
        } else {
          $data['success'] = true;
          $data['confirmation'] = 'Congratulations. Your message has been sent successfully';
        }
    }

    echo json_encode($data);
}
