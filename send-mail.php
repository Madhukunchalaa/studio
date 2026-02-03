<?php
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Sanitize and validate input
    $name = strip_tags(trim($_POST["name"] ?? ''));
    $email = filter_var(trim($_POST["email"] ?? ''), FILTER_SANITIZE_EMAIL);
    $phone = strip_tags(trim($_POST["phone"] ?? ''));
    $service = strip_tags(trim($_POST["service"] ?? '')); // Matches the 'name' attribute if you add it, currently ID is video-need

    // NOTICE: In the form HTML provided earlier, the select element had id="video-need" but NO NAME attribute.
    // If the user's HTML doesn't have name="service" or name="video-need", this will be empty.
    // I should probably check the HTML form again to ensure the input names match. 
    // Looking back at the HTML viewing in Step 81:
    // <select id="video-need" class="form-select" required> ... </select>
    // It MISSES the name attribute! 
    // <input type="text" id="name" ... > also misses name attribute?
    // Wait, viewing file Step 81:
    // <input type="text" id="name" class="form-input" placeholder="Your Name Here" required>
    // It DOES NOT have name="name". It only has id="name".
    // PHP requires name attributes to pick up POST data.
    // I MUST fix the HTML form first or concurrently. 

    // Let's assume I will fix the HTML form to have name="name", name="email", name="phone", name="service".

    // Check for required fields
    if (empty($name) || empty($email)) {
        // Redirect with error or handle gracefully
        // For now, let's just exit or redirect
        header("Location: avatar-solutions.html?status=empty");
        exit;
    }

    // Recipient email address
    $to = "contact@studioxai.global";

    $subject = "New Avatar Strategy Inquiry from $name";

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
                <h1 class='title'>New Avatar Strategy Inquiry</h1>
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
                <span class='label'>Phone / WhatsApp</span>
                <div class='value'>$phone</div>
            </div>
            
            <div class='info-group' style='border-bottom: none;'>
                <span class='label'>Video Interest</span>
                <div class='value' style='color:#00d2ff;'>$service</div>
            </div>
            
            <div class='footer'>
                <p>Sent from Studio X AI Avatar Landing Page</p>
            </div>
        </div>
    </body>
    </html>
    ";

    // Email headers
    $headers = "MIME-Version: 1.0" . "\r\n";
    $headers .= "Content-type:text/html;charset=UTF-8" . "\r\n";
    $headers .= "From: Studio X AI <contact@studioxai.global>" . "\r\n";
    $headers .= "Reply-To: $email" . "\r\n";
    $headers .= "X-Mailer: PHP/" . phpversion();

    // Log file path
    $log_file = 'email.log';

    // Save to Database
    require_once __DIR__ . '/dashboard/db.php';
    $save_result = save_lead([
        'name' => $name,
        'email' => $email,
        'phone' => $phone,
        'service' => $service,
        'source' => 'Avatar/Landing'
    ]);

    if (!$save_result) {
        error_log(date('[Y-m-d H:i:s]') . " DB ERROR: Failed to save lead $email\n", 3, $log_file);
    }

    // Send email
    if (mail($to, $subject, $email_content, $headers)) {
        // Log Success
        error_log(date('[Y-m-d H:i:s]') . " SUCCESS: Email sent to $to from $email\n", 3, $log_file);

        // Redirect to thank-you page
        header("Location: thank-you.html");
        exit;
    } else {
        // Log Failure
        error_log(date('[Y-m-d H:i:s]') . " ERROR: Mail function failed for $to from $email\n", 3, $log_file);

        // Show error (or redirect to error page)
        echo "Oops! Something went wrong and we couldn't send your message.";
    }

} else {
    // If accessed directly without POST
    header("Location: avatar-solutions.html");
    exit();
}
?>