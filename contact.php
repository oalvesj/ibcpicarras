<?php
header('Content-Type: application/json');

// Validate receiving email address
$receiving_email_address = 'adrianocei@yahoo.com.br';
if (!filter_var($receiving_email_address, FILTER_VALIDATE_EMAIL)) {
    die(json_encode(['success' => false, 'message' => 'Invalid receiving email']));
}

// Check if library exists
$php_email_form = '../assets/vendor/php-email-form/php-email-form.php';
if (!file_exists($php_email_form)) {
    die(json_encode(['success' => false, 'message' => 'Email form library not found']));
}
require_once($php_email_form);

// Validate form inputs
$required_fields = ['name', 'email', 'subject', 'message'];
foreach ($required_fields as $field) {
    if (empty($_POST[$field])) {
        die(json_encode(['success' => false, 'message' => "Missing $field"]));
    }
}

// Validate email
if (!filter_var($_POST['email'], FILTER_VALIDATE_EMAIL)) {
    die(json_encode(['success' => false, 'message' => 'Invalid email format']));
}

$contact = new PHP_Email_Form;
$contact->ajax = true;
$contact->to = $receiving_email_address;
$contact->from_name = $_POST['name'];
$contact->from_email = $_POST['email'];
$contact->subject = $_POST['subject'];

// Uncomment and configure SMTP if needed
/*
$contact->smtp = array(
    'host' => 'smtp.yourhost.com',
    'username' => 'your_username',
    'password' => 'your_password',
    'port' => '587'
);
*/

$contact->add_message($_POST['name'], 'From');
$contact->add_message($_POST['email'], 'Email');
$contact->add_message($_POST['message'], 'Message', 10);

$result = $contact->send();

echo json_encode([
    'success' => $result,
    'message' => $result ? 'Email sent successfully' : 'Email sending failed'
]);
?>