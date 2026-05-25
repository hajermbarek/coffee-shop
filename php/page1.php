<?php
include 'cnx.php';
?>

<!doctype html>
<html class="html" lang="fr">
<head>
    <meta charset="UTF-8" />
    <title>Cozy Café</title>
    <link rel="stylesheet" href="style.css" />
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" />
    <link href="https://fonts.googleapis.com/css2?family=Manrope:wght@400;500;600;700&display=swap" rel="stylesheet" />
</head>
<body>

    <?php include 'navbar.php'; ?>

    <section class="headercls">
        <div class="htext">
            <h1 class="welcome">Welcome</h1>
            <h2 class="title">Cozy Café</h2>
            <p class="desccls">
                A modern coffee shop where you can<br>
                Read, Play, and Mingle all while enjoying our best 
                <strong>Brews and Pastries.</strong>
            </p>
            <a href="#services-section" class="explore-btn">Explore</a>
        </div>
    </section>

    <section id="services-section" class="services-section">
      <div class="section-header">
        <h2>Our Zones</h2>
        <p class="section-lead">Choose your atmosphere — focus or fun</p>
      </div>

      <div class="zones-grid">
        <div class="zone-card">
          <h3>Quiet Zone</h3>
          <p class="zone-subtitle">Reading · Studying · Deep Work</p>

          <hr class="zone-divider" />

          <p class="zone-description">
            A calm space designed for concentration. Perfect lighting, no
            distractions, and access to our curated reading collection.
          </p>

          <ul class="zone-features">
            <li>Reserved reading spot</li>
            <li>Guaranteed quiet environment</li>
            <li>Ergonomic seating & warm lighting</li>
            <li>Power outlets & high-speed Wi-Fi</li>
          </ul>

          <a href="reservation/seatingbooks.php" class="zone-btn">Reserve Quiet Zone</a>
        </div>

        <div class="zone-card">
          <h3>Fun Zone</h3>
          <p class="zone-subtitle">Games · Friends · Good Times</p>

          <hr class="zone-divider" />

          <p class="zone-description">
            Where the energy lives. Board games, group tables, tournaments and
            spontaneous hangouts — everyone’s welcome.
          </p>

          <ul class="zone-features">
            <li>Board & team games on reserve</li>
            <li>Group tables booked in advance</li>
            <li>Weekly game nights & tournaments</li>
            <li>Social events calendar</li>
          </ul>

          <a href="reservation/seating_games.php" class="zone-btn">Reserve Fun Zone</a>
        </div>

        <div class="zone-card">
          <h3>Food & Drinks</h3>
          <p class="zone-subtitle">Fresh · Custom · Ready for You</p>

          <hr class="zone-divider" />

          <p class="zone-description">
            No waiting. Browse the menu, personalize your order, and have it
            prepared when you arrive — or right now.
          </p>

          <ul class="zone-features">
            <li>Full customization</li>
            <li>Seasonal & signature specials</li>
            <li>Best local ingredients</li>
          </ul>

          <a href="menu/menu.php" class="zone-btn">See the Menu</a>
        </div>
      </div>
    </section>

    <section class="instagram-feed">
      <div class="feed-header">
        <h2>FOLLOW US ON SOCIAL MEDIA</h2>
        <p>@cozycafe.tn · Real moments, real coffee</p>
      </div>
      <div class="slider-container">
        <button class="slider-btn prev" aria-label="Previous images">←</button>
        <div class="slider-wrapper">
          <div class="slider-track" id="sliderTrack">
            <img src="https://images.unsplash.com/photo-1554118811-1e0d58224f24?ixlib=rb-4.0.3&auto=format&fit=crop&w=800&q=80" alt="Coffee moment" />
            <img src="https://images.unsplash.com/photo-1495474472287-4d71bcdd2085?ixlib=rb-4.0.3&auto=format&fit=crop&w=800&q=80" alt="Cake" />
            <img src="https://i.redd.it/iqdiaavcks1f1.jpeg" alt="book space" />
            <img src="https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9CcRfQxPUhvu2eyo0xuy75rtIIlQKs9Z3Ibfd2A&s" alt="Latte art" />
            <img src="https://jordannews.jo/content/upload/editor/Image1_920212221411675035318.jpg" alt="Pastries" />
            <img src="https://images.unsplash.com/photo-1495474472287-4d71bcdd2085?ixlib=rb-4.0.3&auto=format&fit=crop&w=800&q=80" alt="Brunch" />
            <img src="https://images.unsplash.com/photo-1554118811-1e0d58224f24?ixlib=rb-4.0.3&auto=format&fit=crop&w=800&q=80" alt="Cozy interior" />
            <img src="https://www.upshine.com/files/download/2023collection/Cozy-Coffee-Shop-in-a-Bar-1-.jpg" alt="Latte art" />
            <img src="https://jordannews.jo/content/upload/editor/Image1_920212221411675035318.jpg" alt="Pastries" />
          </div>
        </div>
        <button class="slider-btn next" aria-label="Next images">→</button>
      </div>
    </section>

    <?php include 'footer.php'; ?>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

    <script>
      document.addEventListener("mousemove", (e) => {
        const x = (e.clientX / window.innerWidth) * 100;
        const y = (e.clientY / window.innerHeight) * 100;
        document.querySelector(".services-section").style.backgroundPosition = 
          `${50 + (x - 50) * 0.15}% ${50 + (y - 50) * 0.15}%`;
      });
    </script>

    
    <script>
      const track = document.getElementById('sliderTrack');
      const container = document.querySelector('.slider-container');

      container.addEventListener('mouseenter', () => {
        track.style.animationPlayState = 'paused';
      });

      container.addEventListener('mouseleave', () => {
        track.style.animationPlayState = 'running';
      });

    
      document.querySelector('.prev').addEventListener('click', () => {
        track.style.transition = 'transform 0.4s ease';
        track.style.transform = 'translateX(-300px)';
        setTimeout(() => {
          track.style.transition = 'none';
          track.style.transform = 'translateX(0)';
        }, 400);
      });

      document.querySelector('.next').addEventListener('click', () => {
        track.style.transition = 'transform 0.4s ease';
        track.style.transform = 'translateX(-300px)';
        setTimeout(() => {
          track.style.transition = 'none';
          track.style.transform = 'translateX(0)';
        }, 400);
      });
    </script>
</body>
</html>