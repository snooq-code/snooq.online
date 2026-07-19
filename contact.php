<?php
// contact.php - Complete working contact form
// Upload this to your website server!

$botToken = "8803615282:AAFGKwOasWB-Pp1K0pmKsDoFFeSXUzgVHno";
$chatId = "8819455405";

$success = false;
$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Sanitize inputs
    $name = htmlspecialchars(trim($_POST['name'] ?? ''));
    $email = htmlspecialchars(trim($_POST['email'] ?? ''));
    $phone = htmlspecialchars(trim($_POST['phone'] ?? ''));
    $subject = htmlspecialchars(trim($_POST['subject'] ?? ''));
    $message = htmlspecialchars(trim($_POST['message'] ?? ''));
    
    // Validate
    if (empty($name) || empty($email) || empty($subject) || empty($message)) {
        $error = "Please fill in all required fields.";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = "Invalid email address.";
    } else {
        // Build notification
        $notification = "🔔 <b>New Contact Form Submission</b>\n\n";
        $notification .= "<b>Name:</b> $name\n";
        $notification .= "<b>Email:</b> $email\n";
        if ($phone) $notification .= "<b>Phone:</b> $phone\n";
        $notification .= "<b>Subject:</b> $subject\n";
        $notification .= "<b>Time:</b> " . date('Y-m-d H:i:s') . "\n\n";
        $notification .= "<b>Message:</b>\n$message";
        
        // Send to Telegram via SERVER
        $url = "https://api.telegram.org/bot" . $botToken . "/sendMessage";
        $data = [
            'chat_id' => $chatId,
            'text' => $notification,
            'parse_mode' => 'HTML'
        ];
        
        $ch = curl_init();
        curl_setopt_array($ch, [
            CURLOPT_URL => $url,
            CURLOPT_POST => true,
            CURLOPT_POSTFIELDS => http_build_query($data),
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_SSL_VERIFYPEER => true,
            CURLOPT_TIMEOUT => 10
        ]);
        
        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);
        
        if ($httpCode === 200) {
            $success = true;
        } else {
            $error = "Failed to send message. Please try again.";
            error_log("Telegram Error: HTTP $httpCode - $response");
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Contact Us</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            display: flex;
            justify-content: center;
            align-items: center;
            padding: 20px;
        }
        .container {
            background: white;
            border-radius: 20px;
            box-shadow: 0 20px 60px rgba(0,0,0,0.3);
            padding: 40px;
            max-width: 500px;
            width: 100%;
        }
        h1 { color: #333; margin-bottom: 10px; }
        .subtitle { color: #666; margin-bottom: 30px; font-size: 14px; }
        .form-group { margin-bottom: 20px; }
        label { display: block; color: #555; font-weight: 600; margin-bottom: 5px; font-size: 14px; }
        input, textarea, select {
            width: 100%;
            padding: 12px 15px;
            border: 2px solid #e0e0e0;
            border-radius: 10px;
            font-size: 14px;
            transition: all 0.3s ease;
        }
        input:focus, textarea:focus, select:focus {
            outline: none;
            border-color: #667eea;
            box-shadow: 0 0 0 3px rgba(102, 126, 234, 0.1);
        }
        textarea { height: 120px; resize: vertical; }
        .btn-submit {
            width: 100%;
            padding: 14px;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            border: none;
            border-radius: 10px;
            font-size: 16px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s ease;
        }
        .btn-submit:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 20px rgba(102, 126, 234, 0.3);
        }
        .alert {
            padding: 12px 15px;
            border-radius: 10px;
            margin-bottom: 20px;
        }
        .alert-success {
            background: #d4edda;
            color: #155724;
            border: 1px solid #c3e6cb;
            display: <?php echo $success ? 'block' : 'none'; ?>;
        }
        .alert-error {
            background: #f8d7da;
            color: #721c24;
            border: 1px solid #f5c6cb;
            display: <?php echo $error ? 'block' : 'none'; ?>;
        }
        .form-row {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 15px;
        }
        .status-bar {
            text-align: center;
            padding: 10px;
            background: #f8f9fa;
            border-radius: 10px;
            margin-bottom: 20px;
            font-size: 12px;
            color: #666;
        }
        .status-bar span {
            color: #28a745;
            font-weight: bold;
        }
        @media (max-width: 600px) {
            .container { padding: 25px; }
            .form-row { grid-template-columns: 1fr; }
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="status-bar">
            🤖 Bot: @Snooq_bot | Status: <span>● Online</span>
        </div>
        
        <h1>📧 Contact Us</h1>
        <p class="subtitle">We'll get back to you within 24 hours</p>
        
        <div class="alert alert-success">
            ✅ Your message has been sent successfully! We'll contact you soon.
        </div>
        
        <div class="alert alert-error">
            ❌ <?php echo $error; ?>
        </div>
        
        <form method="POST" action="">
            <div class="form-row">
                <div class="form-group">
                    <label for="name">Full Name *</label>
                    <input type="text" id="name" name="name" required 
                           value="<?php echo htmlspecialchars($_POST['name'] ?? ''); ?>">
                </div>
                <div class="form-group">
                    <label for="email">Email Address *</label>
                    <input type="email" id="email" name="email" required
                           value="<?php echo htmlspecialchars($_POST['email'] ?? ''); ?>">
                </div>
            </div>
            
            <div class="form-group">
                <label for="phone">Phone Number</label>
                <input type="tel" id="phone" name="phone"
                       value="<?php echo htmlspecialchars($_POST['phone'] ?? ''); ?>">
            </div>
            
            <div class="form-group">
                <label for="subject">Subject *</label>
                <select id="subject" name="subject" required>
                    <option value="">Select a subject</option>
                    <option value="general" <?php echo (($_POST['subject'] ?? '') === 'general') ? 'selected' : ''; ?>>General Inquiry</option>
                    <option value="support" <?php echo (($_POST['subject'] ?? '') === 'support') ? 'selected' : ''; ?>>Technical Support</option>
                    <option value="sales" <?php echo (($_POST['subject'] ?? '') === 'sales') ? 'selected' : ''; ?>>Sales Question</option>
                    <option value="callback" <?php echo (($_POST['subject'] ?? '') === 'callback') ? 'selected' : ''; ?>>Request Callback</option>
                    <option value="feedback" <?php echo (($_POST['subject'] ?? '') === 'feedback') ? 'selected' : ''; ?>>Feedback</option>
                </select>
            </div>
            
            <div class="form-group">
                <label for="message">Message *</label>
                <textarea id="message" name="message" required><?php echo htmlspecialchars($_POST['message'] ?? ''); ?></textarea>
            </div>
            
            <button type="submit" class="btn-submit">🚀 Send Message</button>
        </form>
    </div>
</body>
</html>
