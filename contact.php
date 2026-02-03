<?php
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Sanitize and validate input
    $name = strip_tags(trim($_POST["full_name"] ?? ''));
    $email = filter_var(trim($_POST["email"] ?? ''), FILTER_SANITIZE_EMAIL);
    $mobile = strip_tags(trim($_POST["mobile"] ?? ''));
    $company = strip_tags(trim($_POST["company"] ?? ''));
    $goal = strip_tags(trim($_POST["goal"] ?? ''));
    $message = strip_tags(trim($_POST["message"] ?? ''));
    $timeline = strip_tags(trim($_POST["timeline"] ?? ''));

    // Check for required fields
    if (empty($name) || empty($email) || empty($goal) || empty($message)) {
        // Redirect with error
        header("Location: contact.html?status=empty");
        exit;
    }

    // Recipient email address - CHANGE THIS TO YOUR EMAIL
    $to = "contact@studioxai.global";

    $subject = "New Project Inquiry from $name";

    // HTML Email Template
    $logo_url = "https://pub-d8add5c3ed1e4923aa87c457caea356d.r2.dev/Studio%20X%20AI_Logo.png";

    $email_content = "
    <!DOCTYPE html>
    <html>
    <head>
    <style>
        body { background-color: #000000; color: #ffffff; font-family: 'Arial', sans-serif; line-height: 1.6; margin: 0; padding: 0; }
        .container { max-width: 600px; margin: 0 auto; background-color: #111111; padding: 40px; border: 1px solid #333; border-radius: 8px; }
        .header { text-align: center; margin-bottom: 30px; border-bottom: 1px solid #333; padding-bottom: 20px; }
        .logo { max-width: 150px; height: auto; }
        .title { color: #00d2ff; font-size: 24px; font-weight: bold; margin: 10px 0; }
        .info-group { margin-bottom: 20px; border-bottom: 1px solid #222; padding-bottom: 15px; }
        .label { color: #888888; font-size: 12px; text-transform: uppercase; letter-spacing: 1px; margin-bottom: 5px; display: block; }
        .value { color: #ffffff; font-size: 16px; font-weight: 500; }
        .message-box { background-color: #000; padding: 20px; border-left: 3px solid #00d2ff; margin-top: 10px; font-style: italic; color: #ddd; }
        .footer { text-align: center; margin-top: 40px; font-size: 12px; color: #666; }
    </style>
    </head>
    <body>
        <div class='container'>
            <div class='header'>
                <img src='$logo_url' alt='Studio X AI' class='logo'>
                <h1 class='title'>New Project Inquiry</h1>
            </div>
            
            <div class='info-group'>
                <span class='label'>Name</span>
                <div class='value'>$name</div>
            </div>
            
            <div class='info-group'>
                <span class='label'>Email</span>
                <div class='value'><a href='mailto:$email' style='color:#00d2ff; text-decoration:none;'>$email</a></div>
            </div>

            <div class='info-group'>
                <span class='label'>Mobile Number</span>
                <div class='value'>$mobile</div>
            </div>
            
            <div class='info-group'>
                <span class='label'>Company</span>
                <div class='value'>$company</div>
            </div>
            
            <div class='info-group'>
                <span class='label'>Service Interest</span>
                <div class='value' style='color:#00d2ff;'>$goal</div>
            </div>
            
            <div class='info-group'>
                <span class='label'>Ideal Timeline</span>
                <div class='value'>$timeline</div>
            </div>
            
            <div class='info-group' style='border-bottom: none;'>
                <span class='label'>Project Details</span>
                <div class='message-box'>
                    " . nl2br($message) . "
                </div>
            </div>
            
            <div class='footer'>
                <p>Sent from Studio X AI Website Form</p>
            </div>
        </div>
    </body>
    </html>
    ";

    // Email headers
    $headers = "MIME-Version: 1.0" . "\r\n";
    $headers .= "Content-type:text/html;charset=UTF-8" . "\r\n";
    $headers .= "From: $name <contact@studioxai.global>" . "\r\n"; // Use domain email as sender to prevent spam flagging
    $headers .= "Reply-To: $email" . "\r\n";
    $headers .= "X-Mailer: PHP/" . phpversion();

    // Send email
    // Log file path
    $log_file = 'email.log';

    // Save to Database
    require_once __DIR__ . '/dashboard/db.php';
    $save_result = save_lead([
        'name' => $name,
        'email' => $email,
        'phone' => $mobile,
        'company' => $company,
        'service' => $goal,
        'timeline' => $timeline,
        'message' => $message,
        'source' => 'Contact Page'
    ]);

    if (!$save_result) {
        error_log(date('[Y-m-d H:i:s]') . " DB ERROR: Failed to save lead $email\n", 3, $log_file);
    }

    // Send email
    if (mail($to, $subject, $email_content, $headers)) {
        // Log Success
        error_log(date('[Y-m-d H:i:s]') . " SUCCESS: Email sent to $to from $email\n", 3, $log_file);

        // Success Redirect
        header("Location: thank-you.html");
    } else {
        // Log Failure
        error_log(date('[Y-m-d H:i:s]') . " ERROR: Mail function failed for $to from $email\n", 3, $log_file);

        // Failure Redirect
        header("Location: contact.html?status=error");
    }
} else {
    // If accessed directly without POST
    header("Location: contact.html");
}
?>