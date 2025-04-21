<?php
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;
use PHPMailer\PHPMailer\OAuth;
use League\OAuth2\Client\Provider\Google;

require 'vendor/autoload.php';

// Replace with your actual credentials
$clientId = '87338492874-ktl9s2871de6adkhvdk0tv8knifi1jv3.apps.googleusercontent.com';
$clientSecret = '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';
$refreshToken = '1//09d8H6otwKliaCgYIARAAGAkSNgF-L9Ir2zxc9GkpcSru9rID64fX1crdtnott3k1uscm_GMYbBDsmBUS99zuHf_kJFRPX-xeeQ';
$yourGmail = 'iurieonbusiness@gmail.com'; // Gmail you're sending from
$recipientEmail = 'iurieonbusiness@gmail.com'; // Where the form should be sent to (your email)

$mail = new PHPMailer(true);

try {
    // Debugging — show all SMTP details in browser
    $mail->SMTPDebug = 2;
    $mail->Debugoutput = 'html';

    // Get form data safely
    $firstname = htmlspecialchars($_POST["firstname"] ?? '');
    $lastname  = htmlspecialchars($_POST["lastname"] ?? '');
    $phone     = htmlspecialchars($_POST["phone"] ?? '');
    $email     = filter_var($_POST["email"] ?? '', FILTER_VALIDATE_EMAIL);
    $address   = htmlspecialchars($_POST["address"] ?? '');

    if (!$email) {
        throw new Exception("Ungültige E-Mail-Adresse.");
    }

    // Configure OAuth2 provider (Google)
    $provider = new Google([
        'clientId'     => $clientId,
        'clientSecret' => $clientSecret,
    ]);

    // Get a fresh access token
    $accessToken = $provider->getAccessToken('refresh_token', [
        'refresh_token' => $refreshToken,
    ]);

   
    // Configure PHPMailer for Gmail OAuth2
    $mail->isSMTP();
    $mail->Host = 'smtp.gmail.com';
    $mail->Port = 587;
    $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
    $mail->SMTPAuth = true;
    $mail->AuthType = 'XOAUTH2';

    $mail->setOAuth(new OAuth([
        'provider'      => $provider,
        'clientId'      => $clientId,
        'clientSecret'  => $clientSecret,
        'refreshToken'  => $refreshToken,
        'userName'      => $yourGmail,
    ]));

    // Sender + recipient
    $mail->setFrom($yourGmail, 'Grüne Systeme');
    $mail->addAddress($recipientEmail, 'Grüne Systeme');  // Your email as recipient
    $mail->addReplyTo($email, "$firstname $lastname");

    // Email content
    $mail->isHTML(true);
    $mail->Subject = "Neue Terminbuchung von $firstname $lastname";
    $mail->Body = "
        <h2>Neue Terminbuchung</h2>
        <p><strong>Vorname:</strong> $firstname</p>
        <p><strong>Nachname:</strong> $lastname</p>
        <p><strong>Mobilnummer:</strong> $phone</p>
        <p><strong>E-Mail:</strong> $email</p>
        <p><strong>Adresse:</strong> $address</p>
    ";
    $mail->AltBody = "Vorname: $firstname\nNachname: $lastname\nMobilnummer: $phone\nE-Mail: $email\nAdresse: $address";

    // Send the email
    $mail->send();
    echo "Vielen Dank! Ihre Anfrage wurde erfolgreich angenommen.";
} catch (Exception $e) {
    echo "<h3>Fehler beim Senden:</h3>";
    echo "<p><strong>PHPMailer Error:</strong> " . $mail->ErrorInfo . "</p>";
    echo "<p><strong>Exception Message:</strong> " . $e->getMessage() . "</p>";
}

?>
