
<!DOCTYPE html>
<html lang="en">
<<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SkillBridge - Exchange Skills, Grow Together</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Pacifico&family=Poppins:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">

    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Poppins', sans-serif;
            overflow-x: hidden;
        }

        /* Hero Section */
        .hero-section {
            min-height: 100vh;
            background: linear-gradient(135deg, #00b074, #00c896);
            position: relative;
            overflow: hidden;
        }

        .hero-section::before {
            content: '';
            position: absolute;
            width: 500px;
            height: 500px;
            background: radial-gradient(circle, rgba(255,255,255,0.25) 0%, transparent 70%);
            border-radius: 50%;
            top: -100px;
            right: -100px;
            animation: float 20s infinite;
        }

        .hero-section::after {
            content: '';
            position: absolute;
            width: 400px;
            height: 400px;
            background: radial-gradient(circle, rgba(255,255,255,0.2) 0%, transparent 70%);
            border-radius: 50%;
            bottom: -100px;
            left: -100px;
            animation: float 25s infinite reverse;
        }

        @keyframes float {
            0%, 100% { transform: translate(0, 0) rotate(0deg); }
            33% { transform: translate(30px, -30px) rotate(120deg); }
            66% { transform: translate(-20px, 20px) rotate(240deg); }
        }

        .hero-content {
            position: relative;
            z-index: 2;
            padding: 120px 0 80px;
        }

        .hero-title {
            font-size: 4rem;
            font-weight: 800;
            color: #fff;
            margin-bottom: 25px;
            line-height: 1.2;
            text-shadow: 0 4px 20px rgba(0,0,0,0.2);
        }

        .hero-subtitle {
            font-size: 1.4rem;
            color: rgba(255,255,255,0.95);
            margin-bottom: 40px;
            line-height: 1.8;
        }

        .hero-buttons {
            display: flex;
            gap: 20px;
            flex-wrap: wrap;
        }

        .btn-hero {
            padding: 18px 45px;
            font-size: 1.1rem;
            font-weight: 600;
            border-radius: 50px;
            transition: all 0.3s;
            text-decoration: none;
            display: inline-block;
        }

        .btn-primary-hero {
            background: white;
            color: #00b074;
            border: none;
            box-shadow: 0 10px 30px rgba(0,176,116,0.4);
        }

        .btn-primary-hero:hover {
            background: #00b074;
            color: white;
            box-shadow: 0 15px 40px rgba(0,176,116,0.5);
        }

        .btn-outline-hero {
            background: transparent;
            color: white;
            border: 2px solid white;
        }

        .btn-outline-hero:hover {
            background: white;
            color: #00b074;
        }

        /* Features Section */
        .features-section {
            padding: 100px 0;
            background: #f9fafb;
            position: relative;
        }

        .section-title {
            text-align: center;
            margin-bottom: 70px;
        }

        .section-title h2 {
            font-size: 3rem;
            font-weight: 700;
            color: #00b074;
            margin-bottom: 15px;
        }

        .section-title p {
            font-size: 1.2rem;
            color: #4a5568;
        }

        .feature-card {
            background: white;
            border: 1px solid #e2e8f0;
            border-radius: 20px;
            padding: 40px 30px;
            text-align: center;
            transition: all 0.4s;
            height: 100%;
        }

        .feature-card:hover {
            transform: translateY(-10px);
            border-color: #00b074;
            box-shadow: 0 10px 30px rgba(0,176,116,0.15);
        }

        .feature-icon {
            width: 80px;
            height: 80px;
            margin: 0 auto 25px;
            background: linear-gradient(135deg, #00b074, #00c896);
            border-radius: 20px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 2.5rem;
            color: white;
            box-shadow: 0 10px 30px rgba(0,176,116,0.3);
        }

        .feature-card h4 {
            color: #00b074;
            font-size: 1.5rem;
            margin-bottom: 15px;
            font-weight: 600;
        }

        .feature-card p {
            color: #4a5568;
            line-height: 1.8;
        }

        /* Stats Section */
        .stats-section {
            padding: 80px 0;
            background: linear-gradient(135deg, #00b074, #00c896);
            color: white;
        }

        .stat-item {
            text-align: center;
        }

        .stat-number {
            font-size: 3.5rem;
            font-weight: 800;
            margin-bottom: 10px;
            text-shadow: 0 4px 15px rgba(0,0,0,0.2);
        }

        .stat-label {
            font-size: 1.2rem;
            opacity: 0.95;
        }

        /* How It Works */
        .how-it-works {
            padding: 100px 0;
            background: #f9fafb;
        }

        .step-card {
            position: relative;
            padding: 40px;
            background: white;
            border-radius: 20px;
            border: 1px solid #e2e8f0;
            margin-bottom: 30px;
            transition: 0.3s;
        }

        .step-card:hover {
            transform: translateY(-10px);
            border-color: #00b074;
            box-shadow: 0 10px 30px rgba(0,176,116,0.15);
        }

        .step-number {
            position: absolute;
            top: -20px;
            left: 40px;
            width: 50px;
            height: 50px;
            background: linear-gradient(135deg, #00b074, #00c896);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.5rem;
            font-weight: bold;
            color: white;
        }

        .step-card h4 {
            color: #00b074;
            margin-bottom: 15px;
            margin-top: 20px;
        }

        .step-card p {
            color: #4a5568;
        }

        /* Testimonials */
        .testimonials-section {
            padding: 100px 0;
            background: white;
        }

        .testimonial-card {
            background: #f9fafb;
            border-radius: 20px;
            padding: 35px;
            border: 1px solid #e2e8f0;
            height: 100%;
        }

        .testimonial-text {
            color: #2d3748;
            font-size: 1.1rem;
            line-height: 1.8;
            margin-bottom: 25px;
            font-style: italic;
        }

        .author-avatar {
            width: 60px;
            height: 60px;
            border-radius: 50%;
            background: linear-gradient(135deg, #00b074, #00c896);
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.5rem;
            color: white;
            font-weight: bold;
        }

        .author-info h5 {
            color: #00b074;
            margin-bottom: 5px;
        }

        .author-info p {
            color: #4a5568;
            margin: 0;
            font-size: 0.9rem;
        }

        /* CTA Section */
        .cta-section {
            padding: 100px 0;
            background: linear-gradient(135deg, #00b074, #00c896);
            text-align: center;
        }

        .cta-section h2 {
            font-size: 3rem;
            color: white;
            margin-bottom: 25px;
            font-weight: 700;
        }

        .cta-section p {
            font-size: 1.3rem;
            color: rgba(255,255,255,0.95);
            margin-bottom: 40px;
        }

        /* Footer */
        .footer {
            background: #004d26;
            padding: 60px 0 30px;
            color: rgba(255,255,255,0.8);
        }

        .footer h5 {
            color: #00c896;
            margin-bottom: 20px;
            font-weight: 600;
        }

        .footer a {
            color: rgba(255,255,255,0.7);
            text-decoration: none;
            transition: 0.3s;
        }

        .footer a:hover {
            color: #00b074;
        }

        .social-links a {
            display: inline-block;
            width: 40px;
            height: 40px;
            background: rgba(255,255,255,0.1);
            border-radius: 50%;
            text-align: center;
            line-height: 40px;
            margin-right: 10px;
            transition: 0.3s;
        }

        .social-links a:hover {
            background: #00b074;
            color: white;
        }

        @media (max-width: 768px) {
            .hero-title {
                font-size: 2.5rem;
            }

            .hero-subtitle {
                font-size: 1.1rem;
            }

            .hero-buttons {
                flex-direction: column;
            }

            .btn-hero {
                width: 100%;
                text-align: center;
            }
        }
    </style>
</head>

<body>
<!-- Hero Section -->
<section class="hero-section">
    <div class="container">
        <div class="hero-content text-center text-md-start">
            <div class="row align-items-center">
                <div class="col-lg-7">
                    <h1 class="hero-title">
                        Exchange Skills.<br>
                        Grow <span style="color: #00bfff;">Together.</span>
                    </h1>
                    <p class="hero-subtitle">
                        Join a thriving community where knowledge meets opportunity.
                        Learn from experts, share your skills, and build meaningful connections.
                    </p>
                    <div class="hero-buttons">
                        <a href="auth/register.php" class="btn-hero btn-primary-hero">
                            <i class="fas fa-rocket"></i> Get Started Free
                        </a>
                        <a href="tasks/view_task.php" class="btn-hero btn-outline-hero">
                            <i class="fas fa-search"></i> Explore Tasks
                        </a>
                    </div>
                </div>
                <div class="col-lg-5 text-center mt-5 mt-lg-0">
                    <div style="font-size: 20rem; color: rgba(255,255,255,0.1);">
                        <i class="fas fa-handshake"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Stats Section -->
<section class="stats-section">
    <div class="container">
        <div class="row">
            <div class="col-md-3 col-6 mb-4 mb-md-0">
                <div class="stat-item">
                    <div class="stat-number">10K+</div>
                    <div class="stat-label">Active Users</div>
                </div>
            </div>
            <div class="col-md-3 col-6 mb-4 mb-md-0">
                <div class="stat-item">
                    <div class="stat-number">25K+</div>
                    <div class="stat-label">Skills Exchanged</div>
                </div>
            </div>
            <div class="col-md-3 col-6">
                <div class="stat-item">
                    <div class="stat-number">98%</div>
                    <div class="stat-label">Satisfaction Rate</div>
                </div>
            </div>
            <div class="col-md-3 col-6">
                <div class="stat-item">
                    <div class="stat-number">150+</div>
                    <div class="stat-label">Countries</div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Features Section -->
<section class="features-section">
    <div class="container">
        <div class="section-title">
            <h2>Why Choose SkillBridge?</h2>
            <p>Everything you need to exchange skills and grow professionally</p>
        </div>

        <div class="row g-4">
            <div class="col-lg-4 col-md-6">
                <div class="feature-card">
                    <div class="feature-icon">
                        <i class="fas fa-users"></i>
                    </div>
                    <h4>Global Community</h4>
                    <p>Connect with skilled professionals from around the world. Share knowledge, collaborate, and grow together.</p>
                </div>
            </div>

            <div class="col-lg-4 col-md-6">
                <div class="feature-card">
                    <div class="feature-icon">
                        <i class="fas fa-shield-alt"></i>
                    </div>
                    <h4>Secure Platform</h4>
                    <p>Your data and transactions are protected with enterprise-grade security and encryption.</p>
                </div>
            </div>

            <div class="col-lg-4 col-md-6">
                <div class="feature-card">
                    <div class="feature-icon">
                        <i class="fas fa-star"></i>
                    </div>
                    <h4>Reputation System</h4>
                    <p>Build your credibility with ratings, reviews, and verified achievements.</p>
                </div>
            </div>

            <div class="col-lg-4 col-md-6">
                <div class="feature-card">
                    <div class="feature-icon">
                        <i class="fas fa-coins"></i>
                    </div>
                    <h4>Points & Rewards</h4>
                    <p>Earn points for completing tasks and unlock exclusive benefits and features.</p>
                </div>
            </div>

            <div class="col-lg-4 col-md-6">
                <div class="feature-card">
                    <div class="feature-icon">
                        <i class="fas fa-comments"></i>
                    </div>
                    <h4>Real-time Chat</h4>
                    <p>Communicate instantly with collaborators through our integrated messaging system.</p>
                </div>
            </div>

            <div class="col-lg-4 col-md-6">
                <div class="feature-card">
                    <div class="feature-icon">
                        <i class="fas fa-trophy"></i>
                    </div>
                    <h4>Achievements</h4>
                    <p>Unlock badges and achievements as you complete milestones and master new skills.</p>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- How It Works -->
<section class="how-it-works">
    <div class="container">
        <div class="section-title">
            <h2>How It Works</h2>
            <p>Get started in just four simple steps</p>
        </div>

        <div class="row">
            <div class="col-lg-6">
                <div class="step-card">
                    <div class="step-number">1</div>
                    <h4><i class="fas fa-user-plus"></i> Create Your Profile</h4>
                    <p>Sign up for free and build your professional profile. Add your skills, experience, and what you're looking to learn.</p>
                </div>
            </div>

            <div class="col-lg-6">
                <div class="step-card">
                    <div class="step-number">2</div>
                    <h4><i class="fas fa-search"></i> Find or Post Tasks</h4>
                    <p>Browse available tasks that match your skills or post your own task to get help from the community.</p>
                </div>
            </div>

            <div class="col-lg-6">
                <div class="step-card">
                    <div class="step-number">3</div>
                    <h4><i class="fas fa-handshake"></i> Collaborate & Learn</h4>
                    <p>Work together with others, share knowledge, and complete tasks while building valuable connections.</p>
                </div>
            </div>

            <div class="col-lg-6">
                <div class="step-card">
                    <div class="step-number">4</div>
                    <h4><i class="fas fa-award"></i> Earn Recognition</h4>
                    <p>Get rated, earn points, unlock achievements, and build your reputation in the community.</p>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Testimonials -->
<section class="testimonials-section">
    <div class="container">
        <div class="section-title">
            <h2>What Our Users Say</h2>
            <p>Join thousands of satisfied members</p>
        </div>

        <div class="row g-4">
            <div class="col-lg-4 col-md-6">
                <div class="testimonial-card">
                    <div class="testimonial-text">
                        "SkillBridge helped me learn web development from scratch. The community is incredibly supportive!"
                    </div>
                    <div class="testimonial-author">
                        <div class="author-avatar">S</div>
                        <div class="author-info">
                            <h5>Sarah Johnson</h5>
                            <p>Web Developer</p>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-lg-4 col-md-6">
                <div class="testimonial-card">
                    <div class="testimonial-text">
                        "I've connected with amazing professionals and expanded my skill set significantly. Highly recommended!"
                    </div>
                    <div class="testimonial-author">
                        <div class="author-avatar">M</div>
                        <div class="author-info">
                            <h5>Michael Chen</h5>
                            <p>Graphic Designer</p>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-lg-4 col-md-6">
                <div class="testimonial-card">
                    <div class="testimonial-text">
                        "The points system and achievements keep me motivated. It's like LinkedIn meets Duolingo!"
                    </div>
                    <div class="testimonial-author">
                        <div class="author-avatar">E</div>
                        <div class="author-info">
                            <h5>Emily Rodriguez</h5>
                            <p>Marketing Specialist</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- CTA Section -->
<section class="cta-section">
    <div class="container">
        <h2>Ready to Start Your Journey?</h2>
        <p>Join our community today and unlock endless learning opportunities</p>
        <a href="auth/register.php" class="btn-hero btn-outline-hero" style="background: white; color: #0072ff;">
            <i class="fas fa-rocket"></i> Join SkillBridge Now
        </a>
    </div>
</section>

<!-- Footer -->
<?php include 'includes/footer.php' ?>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>