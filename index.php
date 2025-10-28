<?php
session_start();
require_once 'includes/header.php';
?>

<!-- Hero Section -->
<section class="hero" style="min-height: 100vh; display: flex; flex-direction: column; justify-content: center; align-items: center; text-align: center; padding-top: 70px;padding-bottom: 20px; background: linear-gradient(135deg, #002853, #003f7d, #0059a0); color: #fff;">
    <h1 style="font-size: 3rem; font-weight: 700; margin-bottom: 20px; color: #ffffff; text-shadow: 0 4px 15px rgba(0, 0, 0, 0.4);">
        Grow Together by Exchanging Skills
    </h1>
    <p style="font-size: 1.2rem; max-width: 700px; margin-bottom: 40px; line-height: 1.7; color: #e0e0e0;">
        Welcome to our Skill Exchange Community — a platform built for learners, creators, and dreamers.
        Here, you can teach what you know and learn what you don’t. Share your knowledge, expand your mind, and connect with people who inspire you.
    </p>

    <?php if(!isset($_SESSION['user_id'])): ?>
    <div class="d-flex gap-3 flex-wrap justify-content-center">
        <a href="auth/register.php" class="btn btn-primary btn-lg" style="padding: 15px 40px; border-radius: 50px; font-weight: 600;">Get Started</a>
        <a href="auth/login.php" class="btn btn-outline-light btn-lg" style="padding: 15px 40px; border-radius: 50px; font-weight: 600;">Login</a>
    </div>
    <?php endif; ?>

    <!-- Features Section -->
    <div class="features" style="display: flex; justify-content: center; flex-wrap: wrap; margin-top: 80px; gap: 30px;">
        <div class="feature-box" style="background: rgba(255,255,255,0.1); border-radius: 15px; padding: 30px; width: 300px; text-align: center; backdrop-filter: blur(10px); box-shadow: 0 4px 15px rgba(0,0,0,0.3); transition: transform 0.3s;">
            <h3 style="font-size: 1.5rem; margin-bottom: 15px; color: #fff;">Learn from Others</h3>
            <p style="color: #d0d0d0;">Connect with mentors and peers to gain real-world skills that empower your personal and professional growth.</p>
        </div>

        <div class="feature-box" style="background: rgba(255,255,255,0.1); border-radius: 15px; padding: 30px; width: 300px; text-align: center; backdrop-filter: blur(10px); box-shadow: 0 4px 15px rgba(0,0,0,0.3); transition: transform 0.3s;">
            <h3 style="font-size: 1.5rem; margin-bottom: 15px; color: #fff;">Share Your Expertise</h3>
            <p style="color: #d0d0d0;">Be a teacher, guide, or coach. Help others by sharing what you’ve mastered — every skill matters here.</p>
        </div>

        <div class="feature-box" style="background: rgba(255,255,255,0.1); border-radius: 15px; padding-left:30px; padding-right:30px;padding-top:30px;width: 300px; text-align: center; backdrop-filter: blur(10px); box-shadow: 0 4px 15px rgba(0,0,0,0.3); transition: transform 0.3s;">
            <h3 style="font-size: 1.5rem; margin-bottom: 15px; color: #fff;">Collaborate & Build</h3>
            <p style="color: #d0d0d0;">Join hands with like-minded individuals to create, innovate, and bring new ideas to life through collaboration.</p>
        </div>
    </div>
</section>

<?php
require_once './includes/footer.php';
?>
