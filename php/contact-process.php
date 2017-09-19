<?php
require_once 'Mail.php';

$host = "ssl://email-smtp.us-west-2.amazonaws.com";
$port = "465";
$username = 'AKIAJFO7ACH5R7GRUHQA';
$password = 'At+hixEplzjygepMYCGQEhuXw8K7c1ikDatX60TFfAxb';

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
        $subject = "Message from $subjectPrefix";
        $body    = '
            <strong>Name: </strong>'.$name.'<br />
            <strong>Email: </strong>'.$email.'<br />
            <strong>Message: </strong>'.nl2br($message).'<br />
        ';

        $headers = array();
        $headers['MIME-Version'] = '1.1';
        $headers['Content-type'] = 'text/html; charset=utf-8';
        $headers['Content-Transfer-Encoding'] = '8bit';
        $headers['Date'] = date('r', $_SERVER['REQUEST_TIME']);
        $headers['Message-ID'] = '<' . $_SERVER['REQUEST_TIME'] . md5($_SERVER['REQUEST_TIME']) . '@' . $_SERVER['SERVER_NAME'] . '>';
        $headers['From'] = $name . ' <' . $email . '> ';
        $headers['Return-Path'] = $emailTo;
        $headers['Reply-To'] = $email;
        $headers['X-Mailer'] = 'PHP/'. phpversion();
        $headers['X-Originating-IP'] = $_SERVER['SERVER_ADDR'];

        $smtp = Mail::factory('smtp',
          array ('host' => $host,
            'port' => $port,
            'auth' => true,
            'username' => $username,
            'password' => $password));

        $mail = $smtp->send($emailTo, $headers, $body);

        if (PEAR::isError($mail)) {
          $data['success'] = false;
          $data['errors']  = $mail->getMessage();
        } else {
          $data['success'] = true;
          $data['confirmation'] = 'Congratulations. Your message has been sent successfully';
        }
    }

    // return all our data to an AJAX call
    echo json_encode($data);
}
