<?php
// --- keep your existing ob_start(), session_start(), and CTA handler above this if needed ---

// Handle "Notify me" form submit
if ($_SERVER['REQUEST_METHOD'] === 'POST' && ($_POST['form_id'] ?? '') === 'notify') {
    $email = filter_var($_POST['email'] ?? '', FILTER_SANITIZE_EMAIL);

    if (filter_var($email, FILTER_VALIDATE_EMAIL)) {
        // Save to a CSV file OUTSIDE the web root if possible
        // Fallback: a protected subfolder under your site
        $dir  = __DIR__ . '/storage';               // make sure this folder is writable
        $file = $dir . '/notify_list.csv';

        if (!is_dir($dir)) {
            @mkdir($dir, 0755, true);
        }

        // Optional: deduplicate (simple linear scan, ok for small lists)
        $exists = false;
        if (file_exists($file)) {
            $rows = file($file, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
            foreach ($rows as $row) {
                $parts = str_getcsv($row);
                if (isset($parts[0]) && strtolower($parts[0]) === strtolower($email)) {
                    $exists = true;
                    break;
                }
            }
        }

        if (!$exists) {
            $fp = fopen($file, 'a');
            if ($fp) {
                // Lock file while writing
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
            // Already subscribed (treat as success UX-wise)
            $_SESSION['flash_notify'] = 'ok';
        }
    } else {
        $_SESSION['flash_notify'] = 'fail';
    }

    // Redirect to clean URL (prevents resubmission on refresh)
    header('Location: ' . strtok($_SERVER['REQUEST_URI'], '?'));
    exit;
}
?>

<?php
// Start buffering + session before any output
ob_start();
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Handle CTA form submit inline (no separate file)
if ($_SERVER['REQUEST_METHOD'] === 'POST' && ($_POST['form_id'] ?? '') === 'contact_cta') {
    // CHANGE THIS to your inbox:
    $to = 'info@halalholidaycheck.com';

    // Collect & sanitize
    $email = filter_var($_POST['email'] ?? '', FILTER_SANITIZE_EMAIL);
    $name  = trim($_POST['name'] ?? '');
    $phone = trim(($_POST['country_code'] ?? '') . ' ' . ($_POST['phone'] ?? ''));

    // Build message
    $subject = 'New Contact Form Submission - HalalHolidayCheck';
    $message = "You received a new message from the Contact CTA form:\n\n"
             . "Name: $name\n"
             . "Email: $email\n"
             . "Phone: $phone\n";

    // Headers
    $domain  = $_SERVER['SERVER_NAME'] ?? 'yourdomain.com';
    $headers = "From: noreply@$domain\r\n";
    if ($email) { $headers .= "Reply-To: $email\r\n"; }

    // Send + flash + redirect to clean URL (no query)
    $ok = @mail($to, $subject, $message, $headers);
    $_SESSION['flash_sent'] = $ok ? 'ok' : 'fail';
    header('Location: ' . strtok($_SERVER['REQUEST_URI'], '?'));
    exit;
}

// Define asset base path INSIDE PHP
$ASSET = BASEURL . 'assets/public/img/';
?>
<!DOCTYPE html>
<html lang="en">


<head>

<script>(function(w,d,s,l,i){w[l]=w[l]||[];w[l].push({'gtm.start':
new Date().getTime(),event:'gtm.js'});var f=d.getElementsByTagName(s)[0],
j=d.createElement(s),dl=l!='dataLayer'?'&l='+l:'';j.async=true;j.src=
'https://www.googletagmanager.com/gtm.js?id='+i+dl;f.parentNode.insertBefore(j,f);
})(window,document,'script','dataLayer','GTM-TWKD8DRZ');</script>

    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Halal Holiday Check</title>
    <link href="https://fonts.googleapis.com/css2?family=Onest:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link
        href="https://fonts.googleapis.com/css2?family=Onest:wght@400;500;600;700&family=Montserrat:wght@400;500;600;700&display=swap"
        rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
<link rel="icon" type="image/png" sizes="32x32" href="<?= $ASSET ?>favicon.png">
<link rel="shortcut icon" href="<?= $ASSET ?>favicon.png" type="image/png">

    
    <style>
        /* RESET CSS */
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
.contact-options a {
  display: inline-block;
  text-align: center;
  padding: 12px 20px;
  border-radius: 12px;
  border: none;
  cursor: pointer;
  transition: all 0.3s;
  width: 216px;
  font-size: 14px;
  font-weight: 600;
  line-height: 147%;
  font-family: Montserrat, sans-serif;
  color: #222222;
  text-decoration: none; /* remove underline */
}

.contact-options a:hover {
  opacity: 0.85;
}

/* specific variations */
.call-btn,
.mail-btn {
  background: #fff;
}

.whatsapp-btn {
  background: #fff;
  border: 2px solid #8a2be2;
}
        body {
            font-family: 'Onest', sans-serif;
            background: #fff;
            color: #000;
        }

        .container {
            width: 100%;
            min-height: 100vh;
            overflow: hidden;
        }

        /* === Hero Section === */
        section.hero {
            position: relative;
            width: 100%;
            height: 661px;
            overflow: hidden;
        }

        .hero_img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            object-position: center;
            display: block;
        }

        .overlay {
            position: absolute;
            inset: 0;
            background-color: #22222299;
        }

        .cards-section .design-img {
            position: absolute;
            top: 0;
            left: 70px;
            /* align with cards start */
            z-index: 0;
            margin-top: 400px;
            /* your vertical offset */
            max-width: none;
            /* allow it to expand naturally */
        }

        .hero_content {
            position: absolute;
            inset: 0;
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            text-align: center;
            color: #fff;
            z-index: 1;
            padding: 0 20px;
        }

        .hero_content img {
            max-width: 200px;
        }

        /* === Hero notify form === */
        .notify-form {
            display: flex;
            gap: 8px;
            margin-top: 50px;
        }

        .notify-form input {
            padding: 12px 16px;
            border: none;
            border-radius: 30px;
            flex: 1;
            min-width: 250px;
            font-family: inherit;
        }

        .notify-form button {
            padding: 12px 24px;
            border: none;
            border-radius: 30px;
            background: #007d6e;
            color: #fff;
            font-weight: 600;
            cursor: pointer;
            transition: background 0.3s;
        }

        .notify-form button:hover {
            background: #005e52;
        }

        /* === Contact Buttons (hero) === */
        .contact-options {
            margin-top: 20px;
            display: flex;
            gap: 12px;
            flex-wrap: wrap;
            justify-content: center;
        }

        .contact-options button {
            padding: 12px 20px;
            border-radius: 12px;
            border: none;
            cursor: pointer;
            transition: all 0.3s;
            width: 216px;
        }

        .contact-options button:hover {
            opacity: 0.85;
        }

        .call-btn,
        .mail-btn {
            background: #fff;
            color: #222222;
        }

        .whatsapp-btn {
            background: #fff;
            border: 2px solid #8a2be2;
        }

        /* === Cards Section === */
        .cards-section {
            position: relative;
            padding: 40px 70px 80px;
            /* left/right inset */
        }

        .cards-section {
            position: relative;
            width: 100%;
            /* full width section */
            padding: 32px 70px 32px 70px;
            /* only left padding */
        }

        .card-grid {
            position: relative;
            z-index: 1;
            display: grid;
            grid-template-columns: repeat(3, minmax(0, 1fr));
            /* 3 columns that expand */
            gap: 8px;
            justify-content: stretch;
            /* fill available width */
        }

        .card {
            position: relative;
            width: 100%;
            aspect-ratio: 425 / 270;
            /* keeps your original ratio while scaling */
            overflow: hidden;
            border-radius: 12px;
        }

        .card img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            display: block;
            cursor: ;
        }

.card::after {
  content: "";
  position: absolute;
  left: 0;
  bottom: 0;
  width: 100%;
  height: 50%;
  background: linear-gradient(to top, #7373731A 0%, #E4E4E400 100%);
  backdrop-filter: blur(2px);
  pointer-events: none;
  z-index: 1;
  transition: backdrop-filter 0.3s ease, background 0.3s ease;
}

.card:hover::after {
  backdrop-filter: blur(0); /* remove blur */
  background: linear-gradient(to top, rgba(115,115,115,0) 0%, rgba(228,228,228,0) 100%);
}


        .card .name {
            position: absolute;
            top: 12px;
            left: 12px;
            z-index: 2;
        }


        .card .title {
            position: absolute;
            left: 12px;
            bottom: 12px;
            font-size: 20px;
            font-weight: 600;
            font-family: Montserrat, sans-serif;
            color: #fff;
            line-height: 147%;
            text-shadow: 0 1px 2px rgba(0, 0, 0, .6);
            z-index: 2;
        }

        /* === Stars / Copy Section === */
        .form-section {
            text-align: center;
            margin-top: 40px;
            padding: 0 70px;
        }

        .form-section img {
            max-width: 100%;
            height: auto;
        }

        .form-section p {
            margin-top: 16px;
            font-size: 20px;
            font-weight: 600;
            font-family: Montserrat, sans-serif;
        }

        /* === Help / Contact CTA === */
        .contact-cta {
            display: grid;
            grid-template-columns: 1fr 1.2fr;
            gap: 16px;
            align-items: stretch;
            margin: 70px 0 32px;
            padding: 0 70px;
            /* align with cards */
        }

        .cta-left {
            background: #1D4B4A;
            color: #fff;
            border-radius: 12px;
            padding: 103px 32px;
            position: relative;
            overflow: hidden;
        }

        .cta-left::after {
            content: "";
            position: absolute;
            inset: 0;
            background: radial-gradient(100% 100% at 0% 0%, rgba(255, 255, 255, 0.06) 0%, rgba(255, 255, 255, 0) 60%);
            pointer-events: none;
        }

        .cta-left h2 {
            font-family: 'Onest', sans-serif;
            font-weight: 700;
            font-size: 42px;
            line-height: 120%;
            letter-spacing: 0.2px;
        }

        .cta-right {
            background: #f8f8f8;
            border-radius: 12px;
            padding: 82px 32px;
        }

        .cta-form-row {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 16px;
            margin-bottom: 16px;
        }

        .cta-label {
            font-size: 12px;
            font-weight: 600;
            font-family: 'Onest', sans-serif;
            color: #222;
            margin-bottom: 6px;
        }

        .cta-input,
        .cta-select,
        .cta-phone {
            width: 100%;
            height: 44px;
            background: #fff;
            border: 1px solid #EFEFEF;
            border-radius: 12px;
            padding: 0 14px;
            font-size: 14px;
            font-family: 'Onest', sans-serif;
            outline: none;
        }

        .cta-input:focus,
        .cta-select:focus,
        .cta-phone:focus {
            border-color: #c9e5e1;
            box-shadow: 0 0 0 3px rgba(0, 109, 98, 0.08);
        }

        .phone-row {
            display: grid;
            grid-template-columns: 220px 1fr;
            gap: 12px;
        }

        .code-block {
            display: grid;
            grid-template-columns: 60px 1fr;
            gap: 8px;
        }

        .flag-box {
            display: flex;
            align-items: center;
            justify-content: center;
            border: 1px solid #EFEFEF;
            background: #fff;
            border-radius: 12px;
            height: 44px;
            font-size: 18px;
        }

        .send-wrap {
            display: flex;
            justify-content: flex-end;
            margin-top: 16px;
        }

        .cta-btn {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            border: none;
            border-radius: 12px;
            background: #FFBF3F;
            color: #222;
            font-weight: 700;
            font-family: 'Montserrat', sans-serif;
            padding: 12px 20px;
            cursor: pointer;
            transition: transform .08s ease, filter .2s ease;
        }

        .cta-btn:hover {
            filter: brightness(0.96);
        }

        .cta-btn:active {
            transform: translateY(1px);
        }

        /* responsive */
        @media (max-width: 1024px) {
            .contact-cta {
                grid-template-columns: 1fr;
            }

            .cta-left {
                padding: 32px;
            }

            .cta-left h2 {
                font-size: 36px;
            }

            .phone-row {
                grid-template-columns: 1fr;
            }
        }

        @media (max-width: 640px) {
            .cta-left h2 {
                font-size: 30px;
            }
        }

        /* === Footer === */
        .footer {
            background: #f8f8f8;
            padding: 20px 70px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            font-size: 12px;
            font-weight: 500;
            font-family: 'Onest', sans-serif;
            color: #222;
        }

        .footer-right {
            display: flex;
            align-items: center;
            gap: 20px;
        }

        .footer-lang,
        .footer-currency {
            display: flex;
            align-items: center;
            gap: 6px;
            cursor: pointer;
            font-size: 12px;
        }

        .footer-social {
            display: flex;
            gap: 14px;
            align-items: center;
        }

        .footer-social a {
            color: #222;
            text-decoration: none;
            font-size: 16px;
            transition: opacity 0.2s;
        }

        .footer-social a:hover {
            opacity: 0.7;
        }
        /* --- Large tablets / small laptops --- */
@media (max-width: 1200px) {
  /* Cards & CTA share the same left start */
  .cards-section { padding: 32px 24px 64px 24px; }
  .cards-section .design-img { left: 24px; }
  .card-grid {
    grid-template-columns: repeat(2, minmax(0, 1fr)); /* 2 per row */
    gap: 12px;
  }

  .contact-cta {
    grid-template-columns: 1fr;    /* stack */
    gap: 20px;
    padding: 0 24px;
    margin: 40px 0 64px;
  }

  .cta-left { padding: 32px; }
  .cta-left h2 { font-size: 36px; }

  .phone-row { grid-template-columns: 1fr; }
}

/* --- Tablets / large phones --- */
@media (max-width: 768px) {
  /* Hero */
  section.hero { height: auto; min-height: 60vh; }
  .hero_content { padding: 24px 16px; }
  .hero_content img { max-width: 150px; }
  .hero_content h1 { font-size: 48px !important; line-height: 120% !important; }

  /* Hero notify form → stacked */
  .notify-form {
    flex-direction: column;
    align-items: stretch;
    gap: 12px;
    margin-top: 24px;
  }
  .notify-form input { width: 100% !important; }
  .notify-form button { width: 100%; }

  /* Cards */
  .cards-section { padding: 24px 16px 56px 16px; }
  .cards-section .design-img { left: 16px; margin-top: 280px; } /* tweak if needed */
  .card-grid {
    grid-template-columns: 1fr;    /* 1 per row */
    gap: 12px;
  }
  .card { border-radius: 10px; }   /* softer corners on small */

  /* Stars / copy */
  .form-section { padding: 0 16px; }
  .form-section p { font-size: 16px; }

  /* CTA form */
  .contact-cta { padding: 0 16px; }
  .cta-form-row { grid-template-columns: 1fr; }
  .code-block { grid-template-columns: 56px 1fr; }
  .send-wrap { justify-content: stretch; }
  .cta-btn { width: 100%; justify-content: center; }

  /* Footer */
  .footer { padding: 16px; flex-direction: column; gap: 12px; align-items: flex-start; }
}

/* --- Small phones --- */
@media (max-width: 480px) {
  .hero_content h1 { font-size: 36px !important; }
  .contact-options { gap: 8px; }
  .contact-options button { width: 100%; }

  /* Tighten the name pill position on cards */
  .card .name { top: 8px; left: 8px; }
  .card .title { left: 10px; bottom: 10px; font-size: 16px; }

  .cta-left h2 { font-size: 28px; }
}
    </style>
                              <style>
  /* one input only: dropdown shows "🇬🇧 +44" etc. */
  .code-block { grid-template-columns: 1fr !important; }
  .flag-box { display: none !important; }
</style>
    <style>
      /* Hero: top-right language + socials */
.hero-top-right{
  position:absolute;
  top:35px;            /* 35px from top */
  right:30px;          /* 30px from right */
  display:flex;
  align-items:center;
  gap:16px;            /* 16px between lang and socials */
  z-index:2;           /* above overlay & content */
} 

.lang-pill{
  display:inline-flex;
  align-items:center;
  gap:4px;             /* 4px between flag and "En" */
  padding:6px 10px;
  /* background:#fff; */
  border-radius:999px;
  font-size:12px;
  font-weight:600;
  color:"#fff";
  font-family:'Onest', sans-serif;
  line-height:1;
  /* box-shadow:0 1px 2px rgba(0,0,0,.08); */
}

.hero-social{
  display:inline-flex;
  align-items:center;
  gap:10px;            /* 10px between instagram and facebook */
}

.hero-social a{
  display:inline-flex;
  align-items:center;
  justify-content:center;
  width:32px;
  height:32px;
  /* border-radius:50%; */
  /* background:#fff; */
  color:#222;
  text-decoration:none;
  font-size:16px;
  box-shadow:0 1px 2px rgba(0,0,0,.08);
  transition:opacity .2s, transform .08s;
}

.hero-social a:hover{ opacity:.9; }
.hero-social a:active{ transform:translateY(1px); }

/* Responsiveness: keep offsets, scale down badges on smaller screens */
@media (max-width: 768px){
  .lang-pill{ padding:5px 8px; font-size:11px; }
  .hero-social a{ width:28px; height:28px; font-size:14px; }
}

@media (max-width: 480px){
  .lang-pill{ padding:4px 8px; font-size:10.5px; }
  .hero-social a{ width:26px; height:26px; font-size:13px; }
}
                              .hero-social a{
  color:#fff;                 /* icon color */
  background:transparent;     /* no white fill */
  box-shadow:none;
}

.hero-social a:hover{ 
  opacity:.95; 
  background:rgba(255,255,255,.08);      /* gentle hover */
}

@media (max-width: 768px){
  .hero-social a{ border-width:1px; }
}
                              /* Make the language pill clickable */
.lang-pill{ cursor:pointer; user-select:none; }

/* Centered modal */
.lang-modal{ position:fixed; inset:0; display:none; z-index:9999; }
.lang-modal[aria-hidden="false"]{ display:block; }
.lang-backdrop{
  position:absolute; inset:0; background:rgba(0,0,0,.45);
  backdrop-filter:saturate(120%) blur(2px);
}
.lang-dialog{
  position:relative; width:min(560px, calc(100% - 32px));
  margin:0 auto; top:50%; transform:translateY(-50%);
  background:#fff; border-radius:16px; padding:20px 20px 16px;
  box-shadow:0 10px 40px rgba(0,0,0,.25);
  font-family:'Onest',sans-serif;
}
.lang-close{
  position:absolute; top:8px; right:10px; width:32px; height:32px;
  border:none; background:transparent; font-size:22px; line-height:1; cursor:pointer;
}
#lang-title{ font-size:18px; font-weight:700; margin-bottom:12px; }

.lang-grid{
  display:grid; grid-template-columns:repeat(3,minmax(0,1fr)); gap:10px;
}
@media (max-width:640px){
  .lang-grid{ grid-template-columns:repeat(2,minmax(0,1fr)); }
}

.lang-item{
  display:inline-flex; align-items:center; justify-content:center;
  gap:8px; height:42px; padding:0 12px; border-radius:12px;
  border:1px solid #EFEFEF; background:#fff; cursor:pointer;
  font-weight:600; color:#222; transition:box-shadow .15s, transform .05s, background .15s;
}
.lang-item:hover{ box-shadow:0 2px 10px rgba(0,0,0,.08); background:#fafafa; }
.lang-item:active{ transform:translateY(1px); }

.lang-note{ margin-top:12px; font-size:12px; color:#666; }

/* Hide Google’s banner + gadget chrome since we use a custom UI */
.goog-te-banner-frame.skiptranslate{ display:none !important; }
body{ top:0 !important; }
.goog-logo-link, .goog-te-gadget span{ display:none !important; }
.goog-te-gadget{ font-size:0 !important; }
           /* Hide google translate elements */
.skiptranslate{
  display: none;
}
body{
  top: 0px !important;
  
}
.goog-te-spinner-pos {
  display: none !important;
}
#goog-gt-tt, .goog-te-balloon-frame{display: none !important;} 
.goog-text-highlight { background: none !important; box-shadow: none !important;}
.goog-logo-link{display: none !important;}
.goog-te-gadget{height: 28px !important;  overflow: hidden;}
.goog-te-banner-frame{display: none !important;}


.goog-text-highlight {
  background: none !important;
  box-shadow: none !important;
  border: none !important;
  padding: 0 !important;
  margin: 0 !important;
  display: none !important;
  font: inherit !important;
  color: inherit !important;
}
#google_translate_element,
.goog-te-banner-frame,
.goog-te-gadget {
  display: none !important;
  visibility: hidden !important;
  height: 0 !important;
  overflow: hidden !important;
}
[class^="VIpgJd-"],
[class*=" VIpgJd-"] {
  background: none !important;
  box-shadow: none !important;
  color: inherit !important;
  font: inherit !important;
  border: none !important;
  text-decoration: none !important;
}

[class^="VIpgJd-"]:hover,
[class*=" VIpgJd-"]:hover {
  background: none !important;
  box-shadow: none !important;
}
/* Hide google translate elements */
           .lang-dialog{
  width: min(720px, calc(100% - 24px));  /* a bit wider than before */
  max-height: 82vh;                      /* scroll if long list */
  overflow: auto;
  padding: 20px;
}

.lang-grid{
  display: grid;
  grid-template-columns: repeat(5, minmax(0,1fr)); /* ✅ five columns */
  gap: 10px;
}

/* breakpoints for graceful stacking */
@media (max-width: 1024px){
  .lang-grid{ grid-template-columns: repeat(4, minmax(0,1fr)); }
}
@media (max-width: 768px){
  .lang-dialog{ width: calc(100% - 24px); }
  .lang-grid{ grid-template-columns: repeat(3, minmax(0,1fr)); }
}
@media (max-width: 520px){
  .lang-grid{ grid-template-columns: repeat(2, minmax(0,1fr)); }
}
@media (max-width: 360px){
  .lang-grid{ grid-template-columns: 1fr; }
}
    </style>
</head>

<body>
<noscript><iframe src="https://www.googletagmanager.com/ns.html?id=GTM-TWKD8DRZ"
height="0" width="0" style="display:none;visibility:hidden"></iframe></noscript>
<?php if (!empty($_SESSION['flash_sent'])): ?>
  <div id="flash-message" style="background:#d4edda;color:#155724;padding:12px;text-align:center;font-weight:600;">
    <?= $_SESSION['flash_sent'] === "ok" ? "✅ Your message has been sent successfully!" : "❌ There was a problem sending your message. Please try again." ?>
  </div>
  <?php unset($_SESSION['flash_sent']); ?>
<?php endif; ?>
<div class="container">
    <!-- HERO -->
    <section class="hero">
        <img src="<?= $ASSET ?>hero.jpg" class="hero_img" alt="">
        <div class="overlay"></div>
                              <div class="hero-top-right notranslate">
  <div class="lang-pill notranslate" style="font-weight: 500;
    font-size: 12px;
    font-family: Onest; color: #fff;" aria-label="Language">
    <span class="flag notranslate">🇬🇧</span><span style="font-weight: 500;
    font-size: 12px;
    font-family: Onest; color: #fff;" class="code notranslate">En</span>
  </div>

  <div class="hero-social" aria-label="Social links">
    <a href="https://www.instagram.com/halalholidaycheck/?igsh=MWhzaDE3Z3JseHlqbg%3D%3D#" target="_blank" rel="noopener" aria-label="Instagram">
      <i class="fa-brands fa-instagram"></i>
    </a>
    <a href="https://www.facebook.com/share/16vHEvEckk/?mibextid=wwXIfr" target="_blank" rel="noopener" aria-label="Facebook">
      <i class="fa-brands fa-facebook-f"></i>
    </a>
  </div>
</div>
        <div class="hero_content">
            <img src="<?= $ASSET ?>logo(1).png" alt="Logo">
            <h1 style="font-size: 80px; font-weight: 600; line-height: 129%;">We’re Coming Soon</h1>
            <p style="font-weight: 500; font-size: 12px; margin-top: 16px; line-height: 147%;">We're working hard
                behind the scenes to bring you a new experience. Stay tuned and <br>be the first to explore what’s next!</p>
<form class="notify-form" action="" method="POST">
  <input type="hidden" name="form_id" value="notify">
  <input type="email" name="email" placeholder="Please enter your email address" required
         style="height: 44px; width: 552px; border: 1px solid #F2F2F2; border-radius: 12px;">
  <button type="submit"
          style="border-radius: 12px; background-color: #266462; font-size: 14px; font-weight: 600; font-family: Montserrat; padding-left: 16px; padding-right: 16px;">
    Notify me
  </button>
</form>
<?php if (!empty($_SESSION['flash_notify'])): ?>
  <div id="flash-notify"
       style="margin:12px 0; font-size:12px; font-weight:600;
              padding:10px 12px; border-radius:8px;
              <?= $_SESSION['flash_notify']==='ok'
                  ? 'background:#E8F6F2; color:#0b6b61; border:1px solid #c9e5e1;'
                  : 'background:#FFF3F3; color:#a33; border:1px solid #f3d0d0;' ?>">
    <?= $_SESSION['flash_notify']==='ok'
        ? '✅ Thanks! We\'ll let you know when we launch.'
        : '❌ Please enter a valid email and try again.' ?>
  </div>
  <?php unset($_SESSION['flash_notify']); ?>
<?php endif; ?>
            <p style="font-size: 12px; font-weight: 500; font-family: Onest; margin-top: 16px;">*Notify me when website is launched <br> or</p>
            
            <div class="contact-options">
  <!-- Call -->
  <a href="tel:4441075" class="call-btn"
     style="font-size: 14px; font-weight: 600; line-height: 147%; font-family: Montserrat; color: #222222; text-decoration:none; display:inline-block; text-align:center;">
    Call us
  </a>

  <!-- WhatsApp -->
  <a href="https://wa.me/905395988066?text=Hello%20I%20need%20help" target="_blank" class="whatsapp-btn"
     style="font-size: 14px; font-weight: 600; line-height: 147%; font-family: Montserrat; color: #222222; text-decoration:none; display:inline-block; text-align:center;">
    Write to WhatsApp
  </a>

  <!-- Email -->
  <a href="mailto:info@halalholidaycheck.com?subject=Support%20Request&body=Hello%2C%20I%20need%20help" class="mail-btn"
     style="font-size: 14px; font-weight: 600; line-height: 147%; font-family: Montserrat; color: #222222; text-decoration:none; display:inline-block; text-align:center;">
    Contact with mail
  </a>
</div>
        </div>
    </section>
                              <div id="google_translate_element" style="position:fixed;left:-9999px;top:-9999px;"></div>

<!-- Language Modal -->
<div id="lang-modal" class="lang-modal" aria-hidden="true">
  <div class="lang-backdrop" data-close></div>
  <div class="lang-dialog" role="dialog" aria-modal="true" aria-labelledby="lang-title">
    <button class="lang-close" type="button" data-close aria-label="Close">×</button>
    <h3 id="lang-title">Choose your language</h3>

    <div class="lang-grid">
      <!-- Add/remove items as you wish. data-lang uses Google codes -->
  <button class="lang-item notranslate" data-lang="af">Afrikaans — Suid-Afrika</button>
  <button class="lang-item notranslate" data-lang="sq">Albanian — Shqipëri</button>
  <button class="lang-item notranslate" data-lang="ar">Arabic — مصر</button>
  <button class="lang-item notranslate" data-lang="az">Azərbaycan dili — Azərbaycan</button>
  <button class="lang-item notranslate" data-lang="eu">Basque — Euskal Herria</button>
  <button class="lang-item notranslate" data-lang="bn">Bengali — বাংলাদেশ</button>
  <button class="lang-item notranslate" data-lang="bg">Bulgarian — България</button>
  <button class="lang-item notranslate" data-lang="ca">Catalan — Catalunya</button>
  <button class="lang-item notranslate" data-lang="zh-CN">Chinese — 中国</button>
  <button class="lang-item notranslate" data-lang="hr">Croatian — Hrvatska</button>
  <button class="lang-item notranslate" data-lang="cs">Czech — Česká republika</button>
  <button class="lang-item notranslate" data-lang="da">Danish — Danmark</button>
  <button class="lang-item notranslate" data-lang="nl">Dutch — Nederland</button>
  <button class="lang-item notranslate" data-lang="et">Estonian — Eesti</button>
  <button class="lang-item notranslate" data-lang="fi">Finnish — Suomi</button>
  <button class="lang-item notranslate" data-lang="fr">French — France</button>
  <button class="lang-item notranslate" data-lang="gl">Galician — Galicia</button>
  <button class="lang-item notranslate" data-lang="ka">Georgian — საქართველო</button>
  <button class="lang-item notranslate" data-lang="de">German — Deutschland</button>
  <button class="lang-item notranslate" data-lang="el">Greek — Ελλάδα</button>
  <button class="lang-item notranslate" data-lang="iw">Hebrew — ישראל</button>
  <button class="lang-item notranslate" data-lang="hi">Hindi — भारत</button>
  <button class="lang-item notranslate" data-lang="hu">Hungarian — Magyarország</button>
  <button class="lang-item notranslate" data-lang="is">Icelandic — Ísland</button>
  <button class="lang-item notranslate" data-lang="it">Italian — Italia</button>
  <button class="lang-item notranslate" data-lang="ja">Japanese — 日本</button>
  <button class="lang-item notranslate" data-lang="ko">Korean — 대한민국</button>
  <button class="lang-item notranslate" data-lang="lv">Latvian — Latvija</button>
  <button class="lang-item notranslate" data-lang="mk">Macedonian — Македонија</button>
  <button class="lang-item notranslate" data-lang="ms">Malay — Malaysia</button>
  <button class="lang-item notranslate" data-lang="fa">Persian — ایران</button>
  <button class="lang-item notranslate" data-lang="pl">Polish — Polska</button>
  <button class="lang-item notranslate" data-lang="pt">Portuguese — Portugal</button>
  <button class="lang-item notranslate" data-lang="ro">Romanian — România</button>
  <button class="lang-item notranslate" data-lang="ru">Russian — Россия</button>
  <button class="lang-item notranslate" data-lang="sk">Slovak — Slovensko</button>
  <button class="lang-item notranslate" data-lang="en">English — England</button>

  <!-- keep Turkish too (was already supported previously) -->
  <button class="lang-item notranslate" data-lang="tr">Türkçe — Türkiye</button>
    </div>

  </div>
</div>

    <!-- CARDS SECTION -->
    <section class="cards-section">
        <img src="<?= $ASSET ?>design.png" alt="Decorative arrows" class="design-img">
        <div class="card-grid">
            <div class="card"><img src="<?= $ASSET ?>card-01.jpg" alt=""><div class="name"><div style="padding:8px 12px;background:#fff;border-radius:8px;">Hotel</div></div><div class="title">Find Your Perfect Stay</div></div>
            <div class="card"><img src="<?= $ASSET ?>card-02.jpg" alt=""><div class="name"><div style="padding:8px 12px;background:#fff;border-radius:8px;">Flights</div></div><div class="title">Book Your Next Flight Effortlessly</div></div>
            <div class="card"><img src="<?= $ASSET ?>card-03.jpg" alt=""><div class="name"><div style="padding:8px 12px;background:#fff;border-radius:8px;">Tour</div></div><div class="title">Explore Stunning Destinations</div></div>
            <div class="card"><img src="<?= $ASSET ?>card-04.jpg" alt=""><div class="name"><div style="padding:8px 12px;background:#fff;border-radius:8px;">Rent a car</div></div><div class="title">Rent the Perfect Ride</div></div>
            <div class="card"><img src="<?= $ASSET ?>card-05.jpg" alt=""><div class="name"><div style="padding:8px 12px;background:#fff;border-radius:8px;">Transfer</div></div><div class="title">Enjoy Hassle-Free Transfers</div></div>
            <div class="card"><img src="<?= $ASSET ?>card-06.jpg" alt=""><div class="name"><div style="padding:8px 12px;background:#fff;border-radius:8px;">Activities</div></div><div class="title">Adventure Awaits</div></div>
            <div class="card"><img src="<?= $ASSET ?>card-07.jpg" alt=""><div class="name"><div style="padding:8px 12px;background:#fff;border-radius:8px;">Event</div></div><div class="title">Discover Memorable Event Spaces</div></div>
            <div class="card"><img src="<?= $ASSET ?>card-08.jpg" alt=""><div class="name"><div style="padding:8px 12px;background:#fff;border-radius:8px;">Room</div></div><div class="title">Find Your Perfect Room</div></div>
            <div class="card"><img src="<?= $ASSET ?>card-09.jpg" alt=""><div class="name"><div style="padding:8px 12px;background:#fff;border-radius:8px;">Hospital</div></div><div class="title">Find the Right Hospital for Your Needs</div></div>
        </div>
    </section>

    <!-- STARS / COPY -->
    <section class="form-section">
        <img src="<?= $ASSET ?>stars.png" alt="Stars">
        <p style="font-size: 32px; font-weight: 600;">More Than Just a Booking System</p>
        <p style="font-size: 12px; font-weight: 500;">Halalholidaycheck is not only a reservation system; with its digital infrastructure, multi-service management, and <br>user experience–focused approach, it is a tourism technologies platform that connects business partners and <br>end-users in the travel industry.</p>
    </section>

    <!-- CTA -->
    <section class="contact-cta">
  <!-- Left green message -->
  <div class="cta-left">
<h2 class="cta-title">
  <span class="cta-line">Need help? We&#39;re here for you.</span>
  <span class="cta-line">Let&#39;s keep in touch.</span>
</h2>


  </div>

  <!-- Right light form -->
  <div class="cta-right">
    <form action="" method="POST">
  <input type="hidden" name="form_id" value="contact_cta">
  <!-- Row: Mail + Full name -->
  <div class="cta-form-row">
    <div>
      <div class="cta-label">Mail</div>
      <input class="cta-input" type="email" name="email" placeholder="you@example.com" required>
    </div>
    <div>
      <div class="cta-label">Full name</div>
      <input class="cta-input" type="text" name="name" placeholder="John Doe">
    </div>
  </div>

  <!-- Row: Phone number -->
  <div>
    <div class="cta-label">Phone number</div>
    <div class="phone-row">
      <div class="code-block">
        <div class="flag-box">&#127468;&#127463;</div>
        <select class="cta-select" name="country_code">
          <option value="+44">+44</option>
          <option value="+1">+1</option>
          <option value="+994">+994</option>
          <option value="+90">+90</option>
        </select>
      </div>
      <input class="cta-phone" type="tel" name="phone" placeholder="1712812819">
    </div>
  </div>

  <div class="send-wrap">
    <button class="cta-btn" type="submit">
      Send message
      <span aria-hidden="true">&rarr;</span>
    </button>
  </div>
</form>

  </div>
</section>

    <!-- FOOTER -->
    <footer class="footer">
        <p>© 2025 HalalHolidayCheck. All rights reserved.</p>
        <div class="footer-right">
            <div class="footer-lang"><span>🇬🇧</span> En</div>
            <div class="footer-currency"><span>💱</span> USD</div>
            <div class="footer-social">
                <a href="https://www.instagram.com/halalholidaycheck/?igsh=MWhzaDE3Z3JseHlqbg%3D%3D#"><i class="fa-brands fa-instagram"></i></a>
                <a href="https://www.facebook.com/share/16vHEvEckk/?mibextid=wwXIfr"><i class="fa-brands fa-facebook-f"></i></a>
            </div>
        </div>
    </footer>
</div>
</body>
<script>
document.addEventListener("DOMContentLoaded", function () {
  /* keep your flash fade */
  ["flash-message","flash-notify"].forEach(id=>{
    const el=document.getElementById(id);
    if(el){
      setTimeout(()=>{ el.style.transition="opacity .5s"; el.style.opacity="0";
        setTimeout(()=>el.remove(),500);
      },3000);
    }
  });

  /* countries (extend as you wish) */
  const countries = [
    { name:"Afghanistan", dial:"+93", flag:"🇦🇫" },
    { name:"Albania", dial:"+355", flag:"🇦🇱" },
    { name:"Algeria", dial:"+213", flag:"🇩🇿" },
    { name:"Andorra", dial:"+376", flag:"🇦🇩" },
    { name:"Angola", dial:"+244", flag:"🇦🇴" },
    { name:"Argentina", dial:"+54", flag:"🇦🇷" },
    { name:"Armenia", dial:"+374", flag:"🇦🇲" },
    { name:"Australia", dial:"+61", flag:"🇦🇺" },
    { name:"Austria", dial:"+43", flag:"🇦🇹" },
    { name:"Azerbaijan", dial:"+994", flag:"🇦🇿" },
    { name:"Bahrain", dial:"+973", flag:"🇧🇭" },
    { name:"Bangladesh", dial:"+880", flag:"🇧🇩" },
    { name:"Belarus", dial:"+375", flag:"🇧🇾" },
    { name:"Belgium", dial:"+32", flag:"🇧🇪" },
    { name:"Bosnia & Herzegovina", dial:"+387", flag:"🇧🇦" },
    { name:"Brazil", dial:"+55", flag:"🇧🇷" },
    { name:"Bulgaria", dial:"+359", flag:"🇧🇬" },
    { name:"Canada", dial:"+1", flag:"🇨🇦" },
    { name:"China", dial:"+86", flag:"🇨🇳" },
    { name:"Croatia", dial:"+385", flag:"🇭🇷" },
    { name:"Cyprus", dial:"+357", flag:"🇨🇾" },
    { name:"Czechia", dial:"+420", flag:"🇨🇿" },
    { name:"Denmark", dial:"+45", flag:"🇩🇰" },
    { name:"Egypt", dial:"+20", flag:"🇪🇬" },
    { name:"Estonia", dial:"+372", flag:"🇪🇪" },
    { name:"Finland", dial:"+358", flag:"🇫🇮" },
    { name:"France", dial:"+33", flag:"🇫🇷" },
    { name:"Georgia", dial:"+995", flag:"🇬🇪" },
    { name:"Germany", dial:"+49", flag:"🇩🇪" },
    { name:"Greece", dial:"+30", flag:"🇬🇷" },
    { name:"Hungary", dial:"+36", flag:"🇭🇺" },
    { name:"Iceland", dial:"+354", flag:"🇮🇸" },
    { name:"India", dial:"+91", flag:"🇮🇳" },
    { name:"Indonesia", dial:"+62", flag:"🇮🇩" },
    { name:"Iran", dial:"+98", flag:"🇮🇷" },
    { name:"Iraq", dial:"+964", flag:"🇮🇶" },
    { name:"Ireland", dial:"+353", flag:"🇮🇪" },
    { name:"Israel", dial:"+972", flag:"🇮🇱" },
    { name:"Italy", dial:"+39", flag:"🇮🇹" },
    { name:"Japan", dial:"+81", flag:"🇯🇵" },
    { name:"Jordan", dial:"+962", flag:"🇯🇴" },
    { name:"Kazakhstan", dial:"+7", flag:"🇰🇿" },
    { name:"Kuwait", dial:"+965", flag:"🇰🇼" },
    { name:"Kyrgyzstan", dial:"+996", flag:"🇰🇬" },
    { name:"Lebanon", dial:"+961", flag:"🇱🇧" },
    { name:"Libya", dial:"+218", flag:"🇱🇾" },
    { name:"Lithuania", dial:"+370", flag:"🇱🇹" },
    { name:"Luxembourg", dial:"+352", flag:"🇱🇺" },
    { name:"Malaysia", dial:"+60", flag:"🇲🇾" },
    { name:"Malta", dial:"+356", flag:"🇲🇹" },
    { name:"Mexico", dial:"+52", flag:"🇲🇽" },
    { name:"Moldova", dial:"+373", flag:"🇲🇩" },
    { name:"Monaco", dial:"+377", flag:"🇲🇨" },
    { name:"Montenegro", dial:"+382", flag:"🇲🇪" },
    { name:"Morocco", dial:"+212", flag:"🇲🇦" },
    { name:"Netherlands", dial:"+31", flag:"🇳🇱" },
    { name:"New Zealand", dial:"+64", flag:"🇳🇿" },
    { name:"North Macedonia", dial:"+389", flag:"🇲🇰" },
    { name:"Norway", dial:"+47", flag:"🇳🇴" },
    { name:"Oman", dial:"+968", flag:"🇴🇲" },
    { name:"Pakistan", dial:"+92", flag:"🇵🇰" },
    { name:"Poland", dial:"+48", flag:"🇵🇱" },
    { name:"Portugal", dial:"+351", flag:"🇵🇹" },
    { name:"Qatar", dial:"+974", flag:"🇶🇦" },
    { name:"Romania", dial:"+40", flag:"🇷🇴" },
    { name:"Russia", dial:"+7", flag:"🇷🇺" },
    { name:"Saudi Arabia", dial:"+966", flag:"🇸🇦" },
    { name:"Serbia", dial:"+381", flag:"🇷🇸" },
    { name:"Singapore", dial:"+65", flag:"🇸🇬" },
    { name:"Slovakia", dial:"+421", flag:"🇸🇰" },
    { name:"Slovenia", dial:"+386", flag:"🇸🇮" },
    { name:"South Africa", dial:"+27", flag:"🇿🇦" },
    { name:"South Korea", dial:"+82", flag:"🇰🇷" },
    { name:"Spain", dial:"+34", flag:"🇪🇸" },
    { name:"Sri Lanka", dial:"+94", flag:"🇱🇰" },
    { name:"Sweden", dial:"+46", flag:"🇸🇪" },
    { name:"Switzerland", dial:"+41", flag:"🇨🇭" },
    { name:"Syria", dial:"+963", flag:"🇸🇾" },
    { name:"Tajikistan", dial:"+992", flag:"🇹🇯" },
    { name:"Tunisia", dial:"+216", flag:"🇹🇳" },
    { name:"Turkmenistan", dial:"+993", flag:"🇹🇲" },
    { name:"Türkiye", dial:"+90", flag:"🇹🇷" },
    { name:"Ukraine", dial:"+380", flag:"🇺🇦" },
    { name:"United Arab Emirates", dial:"+971", flag:"🇦🇪" },
    { name:"United Kingdom", dial:"+44", flag:"🇬🇧" },
    { name:"United States", dial:"+1", flag:"🇺🇸" },
    { name:"Uzbekistan", dial:"+998", flag:"🇺🇿" }
  ];

  const select = document.querySelector('select[name="country_code"]');
  if (!select) return;

  // sort A→Z by name (ignore accents/case)
  const sorted = countries.slice().sort((a,b) =>
    a.name.localeCompare(b.name, 'en', { sensitivity: 'base' })
  );

  // rebuild options
  select.innerHTML = "";
  for (const c of sorted) {
    const opt = document.createElement("option");
    opt.value = c.dial;                         // submitted value
    opt.textContent = `${c.flag} ${c.dial} — ${c.name}`;
    // make Türkiye default
    if (c.name === "Türkiye") opt.selected = true;
    select.appendChild(opt);
  }

  // safety fallback if “Türkiye” wasn’t found for any reason
  if (!select.value) select.value = "+90";
});
</script>
<script>
(function(){
  // 1) Load Google Translate element script
  function loadTranslate(cb){
    if (window.google && window.google.translate) return cb();
    var s=document.createElement('script');
    s.src='https://translate.google.com/translate_a/element.js?cb=googleTranslateElementInit';
    window.googleTranslateElementInit=function(){
      new google.translate.TranslateElement({
        pageLanguage:'en',              // your source language
        includedLanguages:'af,sq,ar,az,eu,bn,bg,ca,zh-CN,hr,cs,da,nl,et,fi,fr,gl,ka,de,el,iw,hi,hu,is,it,ja,ko,lv,mk,ms,fa,pl,pt,ro,ru,sk,en,tr',
        autoDisplay:false
      }, 'google_translate_element');
      cb();
    };
    document.head.appendChild(s);
  }

  // 2) Wait for the hidden select to exist
  function whenComboReady(fn, timeoutMs=10000){
    const start=Date.now();
    (function tick(){
      const sel=document.querySelector('#google_translate_element select.goog-te-combo');
      if (sel) return fn(sel);
      if (Date.now()-start>timeoutMs) return console.warn('Translate combo not found.');
      setTimeout(tick,150);
    })();
  }

  // 3) Open/close modal
  const modal = document.getElementById('lang-modal');
  function openModal(){
    modal.setAttribute('aria-hidden','false');
    // Focus first option for accessibility
    setTimeout(()=>{
      const first=modal.querySelector('.lang-item'); if(first) first.focus();
    },0);
  }
  function closeModal(){ modal.setAttribute('aria-hidden','true'); }

  // 4) Hook the pill to open modal
document.addEventListener('click', (e)=>{
  const pill = e.target.closest('.lang-pill');
  if (pill) { e.preventDefault(); openModal(); return; }

  const closer = e.target.closest('[data-close]');
  if (closer) { e.preventDefault(); closeModal(); return; }
});

  // 5) Apply language via the hidden Google select
  function applyLanguage(lang){
    whenComboReady((sel)=>{
      // 'en' means reset (no translation)
      sel.value = lang;
      sel.dispatchEvent(new Event('change'));
      // Save + update pill label
      try{ localStorage.setItem('site_lang', lang); }catch(_){}
      updatePill(lang);
      closeModal();
    });
  }

  // 6) Update pill (flag + short code)
  const pillEl = document.querySelector('.lang-pill');
  function updatePill(lang){
    if(!pillEl) return;
const map = {
  af:{flag:'🇿🇦',code:'Af'},           // Afrikaans — South Africa
  sq:{flag:'🇦🇱',code:'Sq'},           // Albanian
  ar:{flag:'🇪🇬',code:'Ar'},           // Arabic — Egypt
  az:{flag:'🇦🇿',code:'Az'},           // Azerbaijani
  eu:{flag:'🇪🇸',code:'Eu'},           // Basque (Spain)
  bn:{flag:'🇧🇩',code:'Bn'},           // Bengali — Bangladesh
  bg:{flag:'🇧🇬',code:'Bg'},           // Bulgarian
  ca:{flag:'🇦🇩',code:'Ca'},           // Catalan (Andorra)
  'zh-CN':{flag:'🇨🇳',code:'Zh'},      // Chinese (Simplified)
  hr:{flag:'🇭🇷',code:'Hr'},           // Croatian
  cs:{flag:'🇨🇿',code:'Cs'},           // Czech
  da:{flag:'🇩🇰',code:'Da'},           // Danish
  nl:{flag:'🇳🇱',code:'Nl'},           // Dutch
  et:{flag:'🇪🇪',code:'Et'},           // Estonian
  fi:{flag:'🇫🇮',code:'Fi'},           // Finnish
  fr:{flag:'🇫🇷',code:'Fr'},           // French
  gl:{flag:'🇪🇸',code:'Gl'},           // Galician (Spain)
  ka:{flag:'🇬🇪',code:'Ka'},           // Georgian
  de:{flag:'🇩🇪',code:'De'},           // German
  el:{flag:'🇬🇷',code:'El'},           // Greek
  iw:{flag:'🇮🇱',code:'He'},           // Hebrew (legacy code 'iw')
  hi:{flag:'🇮🇳',code:'Hi'},           // Hindi
  hu:{flag:'🇭🇺',code:'Hu'},           // Hungarian
  is:{flag:'🇮🇸',code:'Is'},           // Icelandic
  it:{flag:'🇮🇹',code:'It'},           // Italian
  ja:{flag:'🇯🇵',code:'Ja'},           // Japanese
  ko:{flag:'🇰🇷',code:'Ko'},           // Korean
  lv:{flag:'🇱🇻',code:'Lv'},           // Latvian
  mk:{flag:'🇲🇰',code:'Mk'},           // Macedonian
  ms:{flag:'🇲🇾',code:'Ms'},           // Malay — Malaysia
  fa:{flag:'🇮🇷',code:'Fa'},           // Persian
  pl:{flag:'🇵🇱',code:'Pl'},           // Polish
  pt:{flag:'🇵🇹',code:'Pt'},           // Portuguese (Portugal)
  ro:{flag:'🇷🇴',code:'Ro'},           // Romanian
  ru:{flag:'🇷🇺',code:'Ru'},           // Russian
  sk:{flag:'🇸🇰',code:'Sk'},           // Slovak
  en:{flag:'🇬🇧',code:'En'},           // English
  tr:{flag:'🇹🇷',code:'Tr'}            // Turkish
};
    const m = map[lang] || map.en;
    pillEl.innerHTML = '<span class="flag">'+m.flag+'</span><span class="code">'+m.code+'</span>';
  }

  // 7) Language item clicks
  document.addEventListener('click', (e)=>{
    const btn = e.target.closest('.lang-item');
    if(!btn) return;
    e.preventDefault();
    const lang = btn.getAttribute('data-lang') || 'en';
    applyLanguage(lang);
  });

  // 8) Initialize: load widget, then apply saved language if any
  loadTranslate(function(){
    const saved = (localStorage.getItem('site_lang') || 'en').toLowerCase();
    updatePill(saved);
    if(saved !== 'en'){
      // Apply after widget is ready
      applyLanguage(saved);
    }
  });
})();
</script>
</html>
