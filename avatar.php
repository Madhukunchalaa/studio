<?php
/*
|--------------------------------------------------------------------------
| Avatar Strategy Form Handler
|--------------------------------------------------------------------------
*/

// 1. Database Configuration
require_once __DIR__ . '/dashboard/db.php';

// 2. Logging Setup
$log_file = __DIR__ . '/email.log';
ini_set('log_errors', 1);
ini_set('error_log', $log_file);

// 3. Handle POST Request
if ($_SERVER["REQUEST_METHOD"] == "POST") {

    // Sanitize Input
    $name = strip_tags(trim($_POST["name"] ?? ''));
    $email = filter_var(trim($_POST["email"] ?? ''), FILTER_SANITIZE_EMAIL);
    $phone = strip_tags(trim($_POST["phone"] ?? ''));
    $service = strip_tags(trim($_POST["service"] ?? ''));

    // Validate Required Fields
    if (empty($name) || empty($email)) {
        header("Location: avatar-solutions.html?status=empty");
        exit;
    }

    // 4. Save to Database
    $save_result = save_lead([
        'name' => $name,
        'email' => $email,
        'phone' => $phone,
        'service' => $service,
        'source' => 'Avatar Strategy' // Specific source for this form
    ]);

    if (!$save_result) {
        error_log(date('[Y-m-d H:i:s]') . " DB ERROR: Failed to save avatar lead for $email\n", 3, $log_file);
    }

    // 5. Send Email Notification
    $to = "contact@studioxai.global,madhkunchala@gmail.com";
    $subject = "New Avatar Strategy Inquiry from $name";

    // Email Template
    $logo_url = "https://pub-d8add5c3ed1e4923aa87c457caea356d.r2.dev/Studio%20X%20AI_Logo.png";

    $email_content = "
    <!DOCTYPE html>
    <html>
    <head>
    <style>
        body { background-color: #000; color: #fff; font-family: Arial, sans-serif; }
        .container { max-width: 600px; margin: auto; background: #111; padding: 40px; border-radius: 8px; border: 1px solid #333; }
        .header { text-align: center; border-bottom: 1px solid #333; padding-bottom: 20px; }
        .logo { max-width: 150px; }
        .title { color: #00d2ff; font-size: 24px; margin: 20px 0; }
        .label { color: #888; font-size: 12px; text-transform: uppercase; margin-top: 15px; }
        .value { font-size: 16px; margin-bottom: 15px; }
        .highlight { color: #00d2ff; }
    </style>
    </head>
    <body>
        <div class='container'>
            <div class='header'>
                <img src='$logo_url' alt='Studio X AI' class='logo'>
                <h1 class='title'>Avatar Strategy Inquiry</h1>
            </div>
            
            <div class='label'>Name</div>
            <div class='value'>$name</div>
            
            <div class='label'>Email</div>
            <div class='value'>$email</div>

            <div class='label'>Phone</div>
            <div class='value'>$phone</div>
            
            <div class='label'>Video Need</div>
            <div class='value highlight'>$service</div>
            
            <div style='text-align: center; margin-top: 30px; font-size: 12px; color: #666;'>
                Sent from Studio X AI Website
            </div>
        </div>
    </body>
    </html>
    ";

    $headers = "MIME-Version: 1.0" . "\r\n";
    $headers .= "Content-type:text/html;charset=UTF-8" . "\r\n";
    $headers .= "From: Studio X AI <contact@studioxai.global>" . "\r\n";
    $headers .= "Reply-To: $email" . "\r\n";

    if (mail($to, $subject, $email_content, $headers)) {
        error_log(date('[Y-m-d H:i:s]') . " SUCCESS: Avatar email sent for $email\n", 3, $log_file);
        header("Location: thank-you.html?source=avatar");
        exit;
    } else {
        error_log(date('[Y-m-d H:i:s]') . " ERROR: Mail failed for $email\n", 3, $log_file);
        echo "<h3>Unable to process your request. Please try again later.</h3>";
    }

} else {
    // Redirect if accessed directly
    header("Location: avatar-solutions.html");
    exit;
}
?>