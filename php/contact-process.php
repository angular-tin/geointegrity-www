<?php
// Import PHPMailer classes into the global namespace
// These must be at the top of your script, not inside a function
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

// Modify the path in the require statement below to refer to the
// location of your Composer autoload.php file.
require_once '../vendor/autoload.php';

// Instantiate a new PHPMailer
$mail = new PHPMailer(true);

// Tell PHPMailer to use SMTP
$mail->isSMTP();

// Replace recipient@example.com with a "To" address. If your account
// is still in the sandbox, this address must be verified.
// Also note that you can include several addAddress() lines to send
// email to multiple recipients.
$mail->addAddress('lablancas@gmail.com', 'Lucas Blancas');

// Replace smtp_username with your Amazon SES SMTP user name.
$mail->Username = 'AKIAJFO7ACH5R7GRUHQA';

// Replace smtp_password with your Amazon SES SMTP password.
$mail->Password = 'At+hixEplzjygepMYCGQEhuXw8K7c1ikDatX60TFfAxb';

// Specify a configuration set. If you do not want to use a configuration
// set, comment or remove the next line.
// $mail->addCustomHeader('X-SES-CONFIGURATION-SET', 'ConfigSet');

// If you're using Amazon SES in a region other than US West (Oregon),
// replace email-smtp.us-west-2.amazonaws.com with the Amazon SES SMTP
// endpoint in the appropriate region.
$mail->Host = 'email-smtp.us-west-2.amazonaws.com';

// The port you will connect to on the Amazon SES SMTP endpoint.
$mail->Port = 465;

// Configure your Subject Prefix and Recipient here
$subjectPrefix = 'GeoIntegrity - Website - Contact Form';
$emailTo       = 'magdalena.gonzalez@geointegrity.com';

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

    // if there are any errors in our errors array, return a success boolean or false
    if (!empty($errors)) {
        $data['success'] = false;
        $data['errors']  = $errors;
    } else {
        // Replace sender@example.com with your "From" address.
        // This address must be verified with Amazon SES.
        $mail->setFrom($email, $name);

        // The subject line of the email
        $mail->Subject = "Message from $subjectPrefix";

        // The HTML-formatted body of the email
        $mail->Body = '
            <strong>Name: </strong>'.$name.'<br />
            <strong>Email: </strong>'.$email.'<br />
            <strong>Message: </strong>'.nl2br($message).'<br />
        ';

        // Tells PHPMailer to use SMTP authentication
        $mail->SMTPAuth = true;

        // Enable SSL encryption
        $mail->SMTPSecure = 'ssl';

        // Tells PHPMailer to send HTML-formatted email
        $mail->isHTML(true);

        // The alternative email body; this is only displayed when a recipient
        // opens the email in a non-HTML email client. The \r\n represents a
        // line break.
        $mail->AltBody = "Name: $name\r\nEmail: $email\r\nMessage: $message";

        if(!$mail->send()) {
          $data['success'] = false;
          $data['errors']  = $mail->ErrorInfo;
        } else {
          $data['success'] = true;
          $data['confirmation'] = 'Congratulations. Your message has been sent successfully';
        }
    }

    // return all our data to an AJAX call
    echo json_encode($data);
}
