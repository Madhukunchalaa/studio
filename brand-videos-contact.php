<?php
ob_start();

/*
|--------------------------------------------------------------------------
| Error Reporting (log only, no display to user)
|--------------------------------------------------------------------------

/*
|--------------------------------------------------------------------------
| Allow POST requests only
|--------------------------------------------------------------------------
*/
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header("Location: brand-videos.html");
    exit;
}

/*
|--------------------------------------------------------------------------
| Sanitize Inputs
|--------------------------------------------------------------------------
*/
$name = trim(strip_tags($_POST['name'] ?? ''));
$email = filter_var(trim($_POST['email'] ?? ''), FILTER_SANITIZE_EMAIL);
$mobile = trim(strip_tags($_POST['mobile'] ?? ''));
$message = trim(strip_tags($_POST['message'] ?? ''));

/*
|--------------------------------------------------------------------------
| Validate Required Fields
|--------------------------------------------------------------------------
*/
if (empty($name) || empty($email) || empty($mobile) || empty($message)) {
    error_log(date('[Y-m-d H:i:s]') . " ERROR: Empty required fields\n", 3, __DIR__ . '/email.log');
    header("Location: brand-videos.html?status=empty");
    exit;
}

if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    error_log(date('[Y-m-d H:i:s]') . " ERROR: Invalid email - $email\n", 3, __DIR__ . '/email.log');
    header("Location: brand-videos.html?status=invalid_email");
    exit;
}

/*
|--------------------------------------------------------------------------
| Email Configuration
|--------------------------------------------------------------------------
*/
$to = "contact@studioxai.global,madhkunchala@gmail.com";
$subject = "New Brand Video Inquiry from {$name}";
$logo_url = "https://pub-d8add5c3ed1e4923aa87c457caea356d.r2.dev/Studio%20X%20AI_Logo.png";

/*
|--------------------------------------------------------------------------
| Email Body (HTML)
|--------------------------------------------------------------------------
*/
$email_content = "
<!DOCTYPE html>
<html>
<head>
<meta charset='UTF-8'>
<style>
body { background:#000; color:#fff; font-family:Arial,sans-serif; margin:0; padding:0; }
.container { max-width:600px; margin:auto; background:#111; padding:40px; border-radius:8px; }
.header { text-align:center; border-bottom:1px solid #333; padding-bottom:20px; }
.logo { max-width:140px; }
.title { color:#00d2ff; font-size:22px; margin-top:15px; }
.label { color:#888; font-size:12px; text-transform:uppercase; margin-top:20px; }
.value { font-size:16px; margin-top:4px; color: white;}
.message-box { background:#000; padding:20px; border-left:3px solid #00d2ff; margin-top:10px; white-space:pre-wrap; color:white; }
.footer { text-align:center; margin-top:40px; font-size:12px; color:#666; }
</style>
</head>
<body>
<div class='container'>
    <div class='header'>
        <img src='{$logo_url}' class='logo' alt='Studio X AI'>
        <div class='title'>New Brand Video Inquiry</div>
    </div>

    <div class='label'>Name</div>
    <div class='value'>{$name}</div>

    <div class='label'>Email</div>
    <div class='value'>{$email}</div>

    <div class='label'>Mobile</div>
    <div class='value'>{$mobile}</div>

    <div class='label'>Project Details</div>
    <div class='message-box'>" . nl2br($message) . "</div>

    <div class='footer'>
        Sent from Studio X AI – Brand Videos Page
    </div>
</div>
</body>
</html>
";

/*
|--------------------------------------------------------------------------
| Email Headers (SAFE)
|--------------------------------------------------------------------------
*/
$headers = "MIME-Version: 1.0\r\n";
$headers .= "Content-Type: text/html; charset=UTF-8\r\n";
$headers .= "From: Studio X AI <contact@studioxai.global>\r\n";
$headers .= "Reply-To: contact@studioxai.global\r\n";
$headers .= "X-Mailer: PHP/" . phpversion();

/*
|--------------------------------------------------------------------------
| Save to Database
|--------------------------------------------------------------------------
*/
require_once __DIR__ . '/dashboard/db.php';
$save_result = save_lead([
    'name' => $name,
    'email' => $email,
    'phone' => $mobile,
    'message' => $message,
    'source' => 'Brand Videos'
]);

if (!$save_result) {
    error_log(date('[Y-m-d H:i:s]') . " DB ERROR: Failed to save lead $email\n", 3, __DIR__ . '/email.log');
}

/*
|--------------------------------------------------------------------------
| Send Email
|--------------------------------------------------------------------------
*/
$sent = mail($to, $subject, $email_content, $headers);

if ($sent) {
    error_log(date('[Y-m-d H:i:s]') . " SUCCESS: Mail sent from {$email}\n", 3, __DIR__ . '/email.log');
    header("Location: thank-you.html?source=brand", true, 302);
    ob_end_flush();
    exit;
} else {
    error_log(date('[Y-m-d H:i:s]') . " ERROR: mail() failed for {$email}\n", 3, __DIR__ . '/email.log');
    header("Location: brand-videos.html?status=error");
    ob_end_flush();
    exit;
}
