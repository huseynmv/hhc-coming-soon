<?php
define('BASEURL', 'https://halalholidaycheck.com/');
require __DIR__ . '/includes/bootstrap.php';   // session + output buffering
require __DIR__ . '/includes/handlers.php';    // handles POST (notify/contact), redirects, flashes

$status = $_SESSION['flash_sent'] ?? null;                 // 'ok' or 'fail'
$show_contact_modal = in_array($status, ['ok','fail'], true);
$contact_success = ($status === 'ok');
unset($_SESSION['flash_sent']);
// Define asset base path INSIDE PHP (unchanged logic for images)
$ASSET = BASEURL . 'assets/public/img/';
?>
<!DOCTYPE html>
<html lang="en">
<head>
<!-- Google Tag Manager -->
<script>(function(w,d,s,l,i){w[l]=w[l]||[];w[l].push({'gtm.start':
new Date().getTime(),event:'gtm.js'});var f=d.getElementsByTagName(s)[0],
j=d.createElement(s),dl=l!='dataLayer'?'&l='+l:'';j.async=true;j.src=
'https://www.googletagmanager.com/gtm.js?id='+i+dl;f.parentNode.insertBefore(j,f);
})(window,document,'script','dataLayer','GTM-TWKD8DRZ');</script>
<!-- End Google Tag Manager -->
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Halal Holiday Check</title>

  <!-- Fonts & Icons -->
  <link href="https://fonts.googleapis.com/css2?family=Onest:wght@400;500;600;700&display=swap" rel="stylesheet">
  <link href="https://fonts.googleapis.com/css2?family=Onest:wght@400;500;600;700&family=Montserrat:wght@400;500;600;700&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

  <!-- Favicon -->
  <link rel="icon" type="image/png" sizes="32x32" href="<?= $ASSET ?>favicon.png">
  <link rel="shortcut icon" href="<?= $ASSET ?>favicon.png" type="image/png">

  <!-- External CSS (moved all <style> blocks here) -->
  <link rel="stylesheet" href="<?= BASEURL ?>assets/public/css/main.css">


</head>
<body>
<!-- Google Tag Manager (noscript) -->
<noscript><iframe src="https://www.googletagmanager.com/ns.html?id=GTM-TWKD8DRZ"
height="0" width="0" style="display:none;visibility:hidden"></iframe></noscript>
<!-- End Google Tag Manager (noscript) -->

  <?php if (!empty($_SESSION['flash_sent'])): ?>
    <div id="flash-message" style="background:#d4edda;color:#155724;padding:12px;text-align:center;font-weight:600;">
      <?= $_SESSION['flash_sent'] === "ok" ? "âœ… Your message has been sent successfully!" : "â?Œ There was a problem sending your message. Please try again." ?>
    </div>
    <?php unset($_SESSION['flash_sent']); ?>
  <?php endif; ?>

  <div class="container"> 
    <!-- HERO -->
    <section class="hero">
      <img src="<?= $ASSET ?>hero.jpg" class="hero_img" alt="">
      <div class="overlay"></div>

      <div class="hero-top-right notranslate">
        <div class="lang-pill notranslate" style="font-weight: 500; font-size: 12px; font-family: Onest; color: #fff;" aria-label="Language">
          <span class="flag notranslate">ðŸ‡¬ðŸ‡§</span>
          <span class="code notranslate" style="font-weight: 500; font-size: 12px; font-family: Onest; color: #fff;">En</span>
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

      <div class="hero_content" style="">
        <img src="<?= $ASSET ?>logo(1).png" alt="Logo">
        <h1 class="hero-title">HalalHolidayCheck &#8212 Global Booking Platform for Hotels, Tours, Transfers, Cars, Stays & Medical Travel</h1>

        <p style="font-weight: 500; font-size: 14px; line-height: 147%;">Hotels, tour operators, rent-a-car companies, property owners, and medical providers  &#8212 all in one place.
Add your business and reach millions of users.&#127775 HalalHolidayCheck  &#8212 Coming Soon!</p>



        <?php if (!empty($_SESSION['flash_notify'])): ?>
          <div id="flash-notify"
               style="margin:12px 0; font-size:12px; font-weight:600;
                      padding:10px 12px; border-radius:8px;
                      <?= $_SESSION['flash_notify']==='ok'
                          ? 'background:#E8F6F2; color:#0b6b61; border:1px solid #c9e5e1;'
                          : 'background:#FFF3F3; color:#a33; border:1px solid #f3d0d0;' ?>">
            <?= $_SESSION['flash_notify']==='ok'
                ? 'âœ… Thanks! We\'ll let you know when we launch.'
                : 'â?Œ Please enter a valid email and try again.' ?>
          </div>
          <?php unset($_SESSION['flash_notify']); ?>
        <?php endif; ?>



        <!-- <div class="contact-options">
          <a href="tel:4441075" class="call-btn">Call us</a>
          <a href="https://wa.me/905395988066?text=Hello%20I%20need%20help" target="_blank" class="whatsapp-btn">Write to WhatsApp</a>
          <a href="mailto:info@halalholidaycheck.com?subject=Support%20Request&body=Hello%2C%20I%20need%20help" class="mail-btn">Contact with mail</a>
        </div> -->
      </div>
    </section>

    <!-- Hidden Google Translate hook -->
    <div id="google_translate_element" style="position:fixed;left:-9999px;top:-9999px;"></div>

    <!-- Language Modal -->
    <div id="lang-modal" class="lang-modal" aria-hidden="true">
      <div class="lang-backdrop" data-close></div>
      <div class="lang-dialog" role="dialog" aria-modal="true" aria-labelledby="lang-title">
        <button class="lang-close" type="button" data-close aria-label="Close">X</button>
        <h3 id="lang-title">Choose your language</h3>

        <div class="lang-grid">
          <button class="lang-item notranslate" data-lang="af">Afrikaans — 🇿🇦 Suid-Afrika</button>
<button class="lang-item notranslate" data-lang="sq">Albanian — 🇦🇱 Shqipëri</button>
<button class="lang-item notranslate" data-lang="ar">Arabic — 🇸🇦 مصر</button>
<button class="lang-item notranslate" data-lang="az">Azərbaycan dili — 🇦🇿 Azərbaycan</button>
<button class="lang-item notranslate" data-lang="eu">Basque — 🇪🇸 Euskal Herria</button>
<button class="lang-item notranslate" data-lang="bn">Bengali — 🇧🇩 বাংলাদেশ</button>
<button class="lang-item notranslate" data-lang="bg">Bulgarian — 🇧🇬 България</button>
<button class="lang-item notranslate" data-lang="ca">Catalan — 🇪🇸 Catalunya</button>
<button class="lang-item notranslate" data-lang="zh-CN">Chinese — 🇨🇳 中国</button>
<button class="lang-item notranslate" data-lang="hr">Croatian — 🇭🇷 Hrvatska</button>
<button class="lang-item notranslate" data-lang="cs">Czech — 🇨🇿 Česká republika</button>
<button class="lang-item notranslate" data-lang="da">Danish — 🇩🇰 Danmark</button>
<button class="lang-item notranslate" data-lang="nl">Dutch — 🇳🇱 Nederland</button>
<button class="lang-item notranslate" data-lang="et">Estonian — 🇪🇪 Eesti</button>
<button class="lang-item notranslate" data-lang="fi">Finnish — 🇫🇮 Suomi</button>
<button class="lang-item notranslate" data-lang="fr">French — 🇫🇷 France</button>
<button class="lang-item notranslate" data-lang="gl">Galician — 🇪🇸 Galicia</button>
<button class="lang-item notranslate" data-lang="ka">Georgian — 🇬🇪 საქართველო</button>
<button class="lang-item notranslate" data-lang="de">German — 🇩🇪 Deutschland</button>
<button class="lang-item notranslate" data-lang="el">Greek — 🇬🇷 Ελλάδα</button>
<button class="lang-item notranslate" data-lang="iw">Hebrew — 🇮🇱 ישראל</button>
<button class="lang-item notranslate" data-lang="hi">Hindi — 🇮🇳 भारत</button>
<button class="lang-item notranslate" data-lang="hu">Hungarian — 🇭🇺 Magyarország</button>
<button class="lang-item notranslate" data-lang="is">Icelandic — 🇮🇸 Ísland</button>
<button class="lang-item notranslate" data-lang="it">Italian — 🇮🇹 Italia</button>
<button class="lang-item notranslate" data-lang="ja">Japanese — 🇯🇵 日本</button>
<button class="lang-item notranslate" data-lang="ko">Korean — 🇰🇷 한국</button>
<button class="lang-item notranslate" data-lang="lv">Latvian — 🇱🇻 Latvija</button>
<button class="lang-item notranslate" data-lang="mk">Macedonian — 🇲🇰 Македонија</button>
<button class="lang-item notranslate" data-lang="ms">Malay — 🇲🇾 Malaysia</button>
<button class="lang-item notranslate" data-lang="fa">Persian — 🇮🇷 ایران</button>
<button class="lang-item notranslate" data-lang="pl">Polish — 🇵🇱 Polska</button>
<button class="lang-item notranslate" data-lang="pt">Portuguese — 🇵🇹 Portugal</button>
<button class="lang-item notranslate" data-lang="ro">Romanian — 🇷🇴 România</button>
<button class="lang-item notranslate" data-lang="ru">Russian — 🇷🇺 Россия</button>
<button class="lang-item notranslate" data-lang="sk">Slovak — 🇸🇰 Slovensko</button>
<button class="lang-item notranslate" data-lang="en">English — 🇬🇧 England</button>
<button class="lang-item notranslate" data-lang="tr">Türkçe — 🇹🇷 Türkiye</button>

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
      <p style="font-size: 12px; font-weight: 500;">Halalholidaycheck is not only a reservation system; with its digital infrastructure, multi-service management, and <br>user experienceâ€“focused approach, it is a tourism technologies platform that connects business partners and <br>end-users in the travel industry.</p>
    </section>

    <!-- CTA -->
    <section class="contact-cta">
      <div class="cta-left">
        <h2 class="cta-title">
          <span class="cta-line">Need help? We&#39;re here for you.</span>
          <span class="cta-line">Let&#39;s keep in touch.</span>
        </h2>
      </div>

      <div class="cta-right">
        <form action="" method="POST">
          <input type="hidden" name="form_id" value="contact_cta">
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
<div>
  <div class="cta-label" style="margin-top: 4px;">Message</div>
  <textarea
    class="cta-textarea cta-input"
    name="message"
    rows="3"
    placeholder="Write a short message (optional)"></textarea>
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
        <div class="footer-lang"><span></span> En</div>
        <div class="footer-currency"><span></span> USD</div>
        <div class="footer-social">
          <a href="https://www.instagram.com/halalholidaycheck/?igsh=MWhzaDE3Z3JseHlqbg%3D%3D#"><i class="fa-brands fa-instagram"></i></a>
          <a href="https://www.facebook.com/share/16vHEvEckk/?mibextid=wwXIfr"><i class="fa-brands fa-facebook-f"></i></a>
        </div>
      </div>
      <div class="footer-phone">
  📞 <a style="text-decoration: none; color:inherit" href="tel:+4915734068958">+4915734068958</a>
</div>
    </footer>
  </div>
<div id="contact-success-modal" class="hhc-modal"
     aria-hidden="<?= $show_contact_modal ? 'false' : 'true' ?>">
  <div class="hhc-backdrop" data-close></div>

  <div class="hhc-dialog" role="dialog" aria-modal="true" aria-labelledby="hhc-success-title">
    <img class="hhc-confetti" src="<?= $ASSET ?>confetti.png" alt="Success">

    <h3 id="hhc-success-title" class="hhc-title">
      <?= $contact_success
            ? 'You Have Been Successfully Submitted!'
            : 'We couldn’t send your message' ?>
    </h3>

    <p class="hhc-subtitle">
      <?= $contact_success
            ? 'Your details were sent successfully. A confirmation has been emailed to you.'
            : 'Please try again in a moment or contact us via WhatsApp/email.' ?>
    </p>

    <button type="button" class="hhc-cta-btn" data-close>
      <span><?= $contact_success ? 'Write new one' : 'Close' ?></span> <span aria-hidden="true">&rarr;</span>
    </button>
  </div>
</div>
  <!-- External JS (all your inline scripts moved here, GTM left inline) -->
  <script defer src="<?= BASEURL ?>assets/public/js/main.js"></script>
</body>
</html>
