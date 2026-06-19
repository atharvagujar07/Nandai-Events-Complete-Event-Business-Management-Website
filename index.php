<?php
require __DIR__ . '/db.php';

$categories = $pdo->query('SELECT id, name, slug FROM gallery_categories WHERE is_active = 1 ORDER BY name LIMIT 6')->fetchAll();
$gallery = $pdo->query(
    'SELECT gi.file_path, gi.title, gc.name AS category_name
     FROM gallery_images gi
     JOIN gallery_categories gc ON gc.id = gi.category_id
     ORDER BY gi.created_at DESC
     LIMIT 8'
)->fetchAll();
?>
<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Nandai Events</title>
  <link rel="stylesheet" href="style.css">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
</head>
<body>
  <nav class="site-nav">
    <a class="brand" href="index.php"><img src="assets/site/nandai-logo-large.png" alt="Nandai Events"><span>Nandai Events</span></a>
    <div>
      <a href="#services">Services</a>
      <a href="gallery.php">Gallery</a>
      <a href="#enquiry">Enquiry</a>
      <!-- <a href="login.php">Admin</a> -->
    </div>
  </nav>

  <header class="hero-3d">
    <img class="hero-bg" src="assets/site/hero-destination og.png" alt="Destination event">
    <div class="hero-glass">
      <img src="assets/site/nandai-logo-large.png" alt="Nandai Events logo">
      <p class="eyebrow">We turn ideas into action</p>
      <h1>Destinations Made Memorable</h1>
      <p>Extraordinary places. Extraordinary events. Premium planning for weddings, corporate events, birthdays, anniversaries, social events, and destination celebrations.</p>
      <div class="hero-actions">
        <a class="site-btn" href="#enquiry">Plan My Event</a>
        <a class="site-btn ghost" href="gallery.php">View Gallery</a>
      </div>
    </div>
  </header>

  <section class="service-strip" id="services">
    <article><span>01</span><h3>Weddings</h3><p>Elegant decor, rituals, entries, hospitality, and flawless flow.</p></article>
    <article><span>02</span><h3>Corporate Events</h3><p>Stage, production, light, sound, LED, and brand experiences.</p></article>
    <article><span>03</span><h3>Social Events</h3><p>Birthdays, anniversaries, naming ceremonies, and family functions.</p></article>
    <article><span>04</span><h3>Destination Events</h3><p>Venue planning, logistics, and unforgettable guest moments.</p></article>
  </section>

  <section class="experience-panel">
    <img src="assets/site/experiences.png" alt="Unforgettable experiences">
    <div>
      <p class="eyebrow">Creative minds. Precise planning.</p>
      <h2>We create experiences, not just events.</h2>
      <p>From moodboard to final execution, our team builds a refined event experience around your story, guests, venue, and budget.</p>
    </div>
  </section>

  <section class="gallery-preview">
    <div class="section-head">
      <div>
        <p class="eyebrow">Recent work</p>
        <h2>Event Gallery</h2>
      </div>
      <a class="site-btn ghost" href="gallery.php">Open Full Gallery</a>
    </div>
    <div class="site-gallery-grid">
      <?php foreach ($gallery as $image): ?>
        <figure>
          <img src="<?= htmlspecialchars($image['file_path']) ?>" alt="<?= htmlspecialchars($image['title'] ?: $image['category_name']) ?>">
          <figcaption><?= htmlspecialchars($image['category_name']) ?></figcaption>
        </figure>
      <?php endforeach; ?>
      <?php if (!$gallery): ?>
        <?php foreach (['hero-events.png','hero-destination.png','experiences.png'] as $fallback): ?>
          <figure><img src="assets/site/<?= $fallback ?>" alt="Nandai event"><figcaption>Nandai Events</figcaption></figure>
        <?php endforeach; ?>
      <?php endif; ?>
    </div>
  </section>

  <section class="enquiry-section" id="enquiry">
    <div>
      <p class="eyebrow">Let us call you</p>
      <h2>Tell us about your event</h2>
      <p>Share the basic details and our team will contact you with ideas, costing, and planning support.</p>
    </div>
    <form class="enquiry-card" action="submit_enquiry.php" method="post">
      <input name="name" placeholder="Your name" required>
      <input name="phone" placeholder="Phone number" required>
      <input type="email" name="email" placeholder="Email address">
      <select name="event_type">
        <option value="">Select event type</option>
        <?php foreach ($categories as $category): ?>
          <option value="<?= htmlspecialchars($category['name']) ?>"><?= htmlspecialchars($category['name']) ?></option>
        <?php endforeach; ?>
        <option>Wedding</option>
        <option>Birthday Party</option>
        <option>Anniversary</option>
        <option>Corporate Event</option>
      </select>
      <input type="date" name="event_date">
      <input type="number" name="budget" min="0" step="1000" placeholder="Approx budget">
      <textarea name="message" placeholder="Message / venue / theme details"></textarea>
      <button class="site-btn" type="submit">Submit Enquiry</button>
    </form>
  </section>

  <footer class="custom-footer">

    <div class="footer-container">

        <div class="footer-logo">
            <img src="assets/site/nandai-logo-large.png" alt="Nandai Events">
            <h3>Nandai Events</h3>
            <p>We Plan. You Celebrate.</p>
        </div>

        <div class="footer-links">
            <h4>Quick Links</h4>
            <a href="index.php">Home</a>
            <a href="#services">Services</a>
            <a href="gallery.php">Gallery</a>
            <a href="#enquiry">Enquiry</a>
            <a href="./login.php">Admin Login</a>

        </div>

        <div class="footer-contact">
            <h4>Connect With Us</h4>

            <div class="social-icons">
                <a href="https://google.com" target="_blank">
                      <i class="fab fa-google"></i>
                </a>

                <a href="https://www.instagram.com/nandai.events?igsh=cTM3N202ZjkwNm5s" target="_blank">
                    <i class="fab fa-instagram"></i>
                </a>

                <a href="https://linkedin.com/in/your_profile" target="_blank">
                    <i class="fab fa-linkedin"></i>
                </a>

                <a href="https://twitter.com/your_username" target="_blank">
                    <i class="fab fa-x-twitter"></i>
                </a>

                <a href="https://facebook.com/your_page" target="_blank">
                    <i class="fab fa-facebook"></i>
                </a>

                <a href="https://wa.me/917719948722" target="_blank">
                    <i class="fab fa-whatsapp"></i>
                </a>

                <a href="mailto:nandaievents@gmail.com">
                    <i class="fas fa-envelope"></i>
                </a>

            </div>
            <div class="footer-map">
               <h4>Our Location</h4>

           <iframe
        src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d30267.800790311445!2d73.78405983408126!3d18.507420581788153!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x3bc2bfb732af849d%3A0xd4078b48b3fe44f0!2sKothrud%2C%20Pune%2C%20Maharashtra!5e0!3m2!1sen!2sin!4v1781850576568!5m2!1sen!2sin"
        width="100%"
        height="250"
        style="border:0;"
        allowfullscreen=""
        loading="lazy"
        referrerpolicy="no-referrer-when-downgrade">
    </iframe>
</div>

        </div>

    </div>

    <div class="copyright-bar">
        © 2026 Nandai Events | Designed & Developed by <a href="https://atharvagujar07.github.io/Portfolio/" style="color:#f4c06a;">Atharva Gujar</a> 
    </div>

</footer>
  <script src="script.js"></script>
</body>
</html>

