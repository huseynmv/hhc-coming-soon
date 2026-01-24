<?php
// --- Notify form submit ---
if ($_SERVER['REQUEST_METHOD'] === 'POST' && ($_POST['form_id'] ?? '') === 'notify') {
    $email = filter_var($_POST['email'] ?? '', FILTER_SANITIZE_EMAIL);

    if (filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $dir  = __DIR__ . '/../storage';
        $file = $dir . '/notify_list.csv';

        if (!is_dir($dir)) { @mkdir($dir, 0755, true); }

        $exists = false;
        if (file_exists($file)) {
            $rows = file($file, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
            foreach ($rows as $row) {
                $parts = str_getcsv($row);
                if (isset($parts[0]) && strtolower($parts[0]) === strtolower($email)) {
                    $exists = true; break;
                }
            }
        }

        if (!$exists) {
            $fp = fopen($file, 'a');
            if ($fp) {
                if (flock($fp, LOCK_EX)) {
                    fputcsv($fp, [
                        $email,
                        date('Y-m-d H:i:s'),
                        $_SERVER['REMOTE_ADDR'] ?? ''
                    ]);
                    flock($fp, LOCK_UN);
                }
                fclose($fp);
                $_SESSION['flash_notify'] = 'ok';
            } else {
                $_SESSION['flash_notify'] = 'fail';
            }
        } else {
            $_SESSION['flash_notify'] = 'ok';
        }
    } else {
        $_SESSION['flash_notify'] = 'fail';
    }

    header('Location: ' . strtok($_SERVER['REQUEST_URI'], '?'));
    exit;
}

// --- Contact CTA submit ---
require __DIR__ . '/../vendor/autoload.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

// --- Contact CTA submit ---
if ($_SERVER['REQUEST_METHOD'] === 'POST' && ($_POST['form_id'] ?? '') === 'contact_cta') {
    $to = 'info@halalholidaycheck.com';

    $email = filter_var($_POST['email'] ?? '', FILTER_SANITIZE_EMAIL);
    $name  = trim($_POST['name'] ?? '');
    $phone = trim(($_POST['country_code'] ?? '') . ' ' . ($_POST['phone'] ?? ''));
    $message = trim($_POST['message'] ?? '');

    $subject = 'New Contact Form Submission - HalalHolidayCheck';
    $message = "You received a new message from the Contact CTA form:\n\n"
             . "Name: $name\n"
             . "Email: $email\n"
             . "Phone: $phone\n"
             - "Message: $message\n";

    try {
        $mail = new PHPMailer(true);
        $mail->isSMTP();
        $mail->Host       = 'smtp.gmail.com';      
        $mail->SMTPAuth   = true;
        $mail->Username   = 'contact@halalholidaycheck.com';
        $mail->Password   = 'your_email_password';      
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
        $mail->Port       = 587;                          // or 465 if using SSL

        $mail->setFrom('contact@halalholidaycheck.com', 'HalalHolidayCheck');
        $mail->addAddress('info@halalholidaycheck.com');

        if ($email) {
            $mail->addReplyTo($email);
        }

        $mail->Subject = $subject;
        $mail->Body    = $message;

        $mail->send();
        $_SESSION['flash_sent'] = 'ok';
    } catch (Exception $e) {
        $_SESSION['flash_sent'] = 'fail';
    }

    header('Location: ' . strtok($_SERVER['REQUEST_URI'], '?'));
    exit;
}
