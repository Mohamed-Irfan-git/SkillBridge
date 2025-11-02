<?php include './includes/header.php'; ?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SkillBridge - Exchange Skills, Grow Together</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800;900&display=swap" rel="stylesheet">

    <style>
        :root {
            --primary: #0066FF;
            --primary-dark: #0052CC;
            --secondary: #00D9B1;
            --dark: #0A0E27;
            --gray-900: #1A1D3A;
            --gray-800: #2D3149;
            --gray-700: #4A5568;
            --gray-100: #F7FAFC;
            --success: #10B981;
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Inter', sans-serif;
            overflow-x: hidden;
            background: var(--dark);
            color: #fff;
        }

        /* Smooth Scroll */
        html {
            scroll-behavior: smooth;
        }

        /* Hero Section */
        .hero-section {
            min-height: 100vh;
            background: linear-gradient(135deg, var(--dark) 0%, var(--gray-900) 100%);
            position: relative;
            overflow: hidden;
        }

        .hero-gradient {
            position: absolute;
            width: 800px;
            height: 800px;
            background: radial-gradient(circle, rgba(0, 102, 255, 0.15) 0%, transparent 70%);
            border-radius: 50%;
            top: -400px;
            right: -200px;
            animation: pulse 8s ease-in-out infinite;
            filter: blur(80px);
        }

        .hero-gradient-2 {
            position: absolute;
            width: 600px;
            height: 600px;
            background: radial-gradient(circle, rgba(0, 217, 177, 0.1) 0%, transparent 70%);
            border-radius: 50%;
            bottom: -300px;
            left: -150px;
            animation: pulse 10s ease-in-out infinite reverse;
            filter: blur(80px);
        }

        @keyframes pulse {
            0%, 100% { transform: scale(1) translate(0, 0); opacity: 0.8; }
            50% { transform: scale(1.1) translate(20px, -20px); opacity: 1; }
        }

        .hero-content {
            position: relative;
            z-index: 2;
            padding: 140px 0 100px;
        }

        .hero-badge {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            background: rgba(0, 102, 255, 0.1);
            border: 1px solid rgba(0, 102, 255, 0.3);
            padding: 8px 20px;
            border-radius: 50px;
            font-size: 0.85rem;
            font-weight: 500;
            color: var(--secondary);
            margin-bottom: 30px;
            animation: slideDown 1s ease-out;
        }

        @keyframes slideDown {
            from { opacity: 0; transform: translateY(-20px); }
            to { opacity: 1; transform: translateY(0); }
        }

        .hero-title {
            font-size: 5rem;
            font-weight: 900;
            color: #fff;
            margin-bottom: 30px;
            line-height: 1.1;
            letter-spacing: -0.02em;
            animation: fadeInUp 1s ease-out 0.2s backwards;
        }

        .gradient-text {
            background: linear-gradient(135deg, var(--primary) 0%, var(--secondary) 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }

        @keyframes fadeInUp {
            from { opacity: 0; transform: translateY(30px); }
            to { opacity: 1; transform: translateY(0); }
        }

        .hero-subtitle {
            font-size: 1.25rem;
            color: rgba(255, 255, 255, 0.7);
            margin-bottom: 50px;
            line-height: 1.8;
            max-width: 600px;
            animation: fadeInUp 1s ease-out 0.4s backwards;
        }

        .hero-buttons {
            display: flex;
            gap: 16px;
            flex-wrap: wrap;
            animation: fadeInUp 1s ease-out 0.6s backwards;
        }

        .btn-hero {
            padding: 16px 36px;
            font-size: 1rem;
            font-weight: 600;
            border-radius: 12px;
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 10px;
            border: none;
            cursor: pointer;
        }

        .btn-primary-hero {
            background: linear-gradient(135deg, var(--primary) 0%, var(--primary-dark) 100%);
            color: white;
            box-shadow: 0 10px 40px rgba(0, 102, 255, 0.4);
        }

        .btn-primary-hero:hover {
            transform: translateY(-2px);
            box-shadow: 0 15px 50px rgba(0, 102, 255, 0.5);
        }

        .btn-outline-hero {
            background: rgba(255, 255, 255, 0.05);
            color: white;
            border: 1px solid rgba(255, 255, 255, 0.2);
            backdrop-filter: blur(10px);
        }

        .btn-outline-hero:hover {
            background: rgba(255, 255, 255, 0.1);
            border-color: rgba(255, 255, 255, 0.4);
            transform: translateY(-2px);
        }

        .hero-visual {
            position: relative;
            animation: fadeInUp 1s ease-out 0.8s backwards;
        }

        .floating-card {
            background: rgba(255, 255, 255, 0.05);
            backdrop-filter: blur(20px);
            border: 1px solid rgba(255, 255, 255, 0.1);
            border-radius: 20px;
            padding: 24px;
            position: absolute;
            animation: float 6s ease-in-out infinite;
        }

        .floating-card-1 {
            top: 50px;
            right: -20px;
            animation-delay: 0s;
        }

        .floating-card-2 {
            bottom: 100px;
            left: -20px;
            animation-delay: 2s;
        }

        .floating-card-3 {
            top: 200px;
            right: 60px;
            animation-delay: 4s;
        }

        @keyframes float {
            0%, 100% { transform: translateY(0px); }
            50% { transform: translateY(-20px); }
        }

        .card-icon {
            width: 50px;
            height: 50px;
            background: linear-gradient(135deg, var(--primary) 0%, var(--secondary) 100%);
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.5rem;
            margin-bottom: 12px;
        }

        /* Stats Section */
        .stats-section {
            padding: 80px 0;
            background: rgba(255, 255, 255, 0.02);
            border-top: 1px solid rgba(255, 255, 255, 0.05);
            border-bottom: 1px solid rgba(255, 255, 255, 0.05);
        }

        .stat-item {
            text-align: center;
            padding: 20px;
        }

        .stat-number {
            font-size: 3.5rem;
            font-weight: 900;
            margin-bottom: 8px;
            background: linear-gradient(135deg, var(--primary) 0%, var(--secondary) 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }

        .stat-label {
            font-size: 1rem;
            color: rgba(255, 255, 255, 0.6);
            font-weight: 500;
        }

        /* Features Section */
        .features-section {
            padding: 120px 0;
            background: var(--dark);
            position: relative;
        }

        .section-header {
            text-align: center;
            margin-bottom: 80px;
        }

        .section-badge {
            display: inline-block;
            background: rgba(0, 102, 255, 0.1);
            color: var(--primary);
            padding: 6px 16px;
            border-radius: 50px;
            font-size: 0.85rem;
            font-weight: 600;
            margin-bottom: 20px;
            text-transform: uppercase;
            letter-spacing: 0.05em;
        }

        .section-title {
            font-size: 3.5rem;
            font-weight: 900;
            color: #fff;
            margin-bottom: 20px;
            letter-spacing: -0.02em;
        }

        .section-subtitle {
            font-size: 1.25rem;
            color: rgba(255, 255, 255, 0.6);
            max-width: 600px;
            margin: 0 auto;
        }

        .feature-card {
            background: rgba(255, 255, 255, 0.03);
            border: 1px solid rgba(255, 255, 255, 0.1);
            border-radius: 24px;
            padding: 40px;
            height: 100%;
            transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
            position: relative;
            overflow: hidden;
        }

        .feature-card::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 2px;
            background: linear-gradient(90deg, var(--primary) 0%, var(--secondary) 100%);
            transform: scaleX(0);
            transition: transform 0.4s ease;
        }

        .feature-card:hover {
            transform: translateY(-8px);
            background: rgba(255, 255, 255, 0.05);
            border-color: rgba(0, 102, 255, 0.3);
            box-shadow: 0 20px 60px rgba(0, 102, 255, 0.15);
        }

        .feature-card:hover::before {
            transform: scaleX(1);
        }

        .feature-icon {
            width: 70px;
            height: 70px;
            background: linear-gradient(135deg, var(--primary) 0%, var(--secondary) 100%);
            border-radius: 16px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 2rem;
            color: white;
            margin-bottom: 24px;
            transition: transform 0.3s ease;
        }

        .feature-card:hover .feature-icon {
            transform: scale(1.1) rotate(5deg);
        }

        .feature-card h4 {
            color: #fff;
            font-size: 1.5rem;
            margin-bottom: 16px;
            font-weight: 700;
        }

        .feature-card p {
            color: rgba(255, 255, 255, 0.6);
            line-height: 1.8;
            margin: 0;
        }

        /* How It Works */
        .how-it-works {
            padding: 120px 0;
            background: var(--gray-900);
            position: relative;
        }

        .step-card {
            position: relative;
            padding: 40px;
            background: rgba(255, 255, 255, 0.03);
            border: 1px solid rgba(255, 255, 255, 0.1);
            border-radius: 24px;
            margin-bottom: 30px;
            transition: all 0.4s ease;
            overflow: hidden;
        }

        .step-card::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            width: 4px;
            height: 100%;
            background: linear-gradient(180deg, var(--primary) 0%, var(--secondary) 100%);
            transform: scaleY(0);
            transition: transform 0.4s ease;
        }

        .step-card:hover {
            transform: translateX(8px);
            background: rgba(255, 255, 255, 0.05);
            border-color: rgba(0, 102, 255, 0.3);
        }

        .step-card:hover::before {
            transform: scaleY(1);
        }

        .step-number {
            position: absolute;
            top: -15px;
            left: 40px;
            width: 50px;
            height: 50px;
            background: linear-gradient(135deg, var(--primary) 0%, var(--secondary) 100%);
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.5rem;
            font-weight: 800;
            color: white;
            box-shadow: 0 8px 24px rgba(0, 102, 255, 0.4);
        }

        .step-card h4 {
            color: #fff;
            margin-bottom: 16px;
            margin-top: 20px;
            font-weight: 700;
            font-size: 1.5rem;
        }

        .step-card h4 i {
            color: var(--primary);
            margin-right: 12px;
        }

        .step-card p {
            color: rgba(255, 255, 255, 0.6);
            line-height: 1.8;
            margin: 0;
        }

        /* Testimonials */
        .testimonials-section {
            padding: 120px 0;
            background: var(--dark);
        }

        .testimonial-card {
            background: rgba(255, 255, 255, 0.03);
            border: 1px solid rgba(255, 255, 255, 0.1);
            border-radius: 24px;
            padding: 40px;
            height: 100%;
            transition: all 0.4s ease;
            position: relative;
        }

        .testimonial-card::before {
            content: '"';
            position: absolute;
            top: 20px;
            right: 30px;
            font-size: 8rem;
            color: rgba(0, 102, 255, 0.1);
            font-family: Georgia, serif;
            line-height: 1;
        }

        .testimonial-card:hover {
            transform: translateY(-8px);
            background: rgba(255, 255, 255, 0.05);
            border-color: rgba(0, 102, 255, 0.3);
            box-shadow: 0 20px 60px rgba(0, 102, 255, 0.15);
        }

        .testimonial-text {
            color: rgba(255, 255, 255, 0.8);
            font-size: 1.1rem;
            line-height: 1.8;
            margin-bottom: 30px;
            position: relative;
            z-index: 1;
        }

        .testimonial-author {
            display: flex;
            align-items: center;
            gap: 16px;
        }

        .author-avatar {
            width: 60px;
            height: 60px;
            border-radius: 16px;
            background: linear-gradient(135deg, var(--primary) 0%, var(--secondary) 100%);
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.5rem;
            color: white;
            font-weight: 700;
        }

        .author-info h5 {
            color: #fff;
            margin-bottom: 4px;
            font-weight: 700;
        }

        .author-info p {
            color: rgba(255, 255, 255, 0.5);
            margin: 0;
            font-size: 0.9rem;
        }

        /* CTA Section */
        .cta-section {
            padding: 120px 0;
            background: linear-gradient(135deg, var(--primary) 0%, var(--primary-dark) 100%);
            position: relative;
            overflow: hidden;
        }

        .cta-section::before {
            content: '';
            position: absolute;
            width: 600px;
            height: 600px;
            background: radial-gradient(circle, rgba(255, 255, 255, 0.1) 0%, transparent 70%);
            border-radius: 50%;
            top: -300px;
            right: -200px;
            filter: blur(80px);
        }

        .cta-content {
            text-align: center;
            position: relative;
            z-index: 2;
        }

        .cta-section h2 {
            font-size: 3.5rem;
            color: white;
            margin-bottom: 24px;
            font-weight: 900;
            letter-spacing: -0.02em;
        }

        .cta-section p {
            font-size: 1.25rem;
            color: rgba(255, 255, 255, 0.9);
            margin-bottom: 40px;
            max-width: 600px;
            margin-left: auto;
            margin-right: auto;
        }

        .btn-cta {
            background: white;
            color: var(--primary);
            padding: 18px 40px;
            font-size: 1.1rem;
            font-weight: 700;
            border-radius: 12px;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 12px;
            transition: all 0.3s ease;
            box-shadow: 0 10px 40px rgba(0, 0, 0, 0.2);
        }

        .btn-cta:hover {
            transform: translateY(-3px) scale(1.02);
            box-shadow: 0 15px 50px rgba(0, 0, 0, 0.3);
            color: var(--primary);
        }

        /* Footer */
        .footer {
            background: var(--gray-900);
            padding: 80px 0 40px;
            border-top: 1px solid rgba(255, 255, 255, 0.05);
        }

        .footer h5 {
            color: #fff;
            margin-bottom: 24px;
            font-weight: 700;
            font-size: 1.1rem;
        }

        .footer a {
            color: rgba(255, 255, 255, 0.6);
            text-decoration: none;
            transition: 0.3s;
            display: block;
            margin-bottom: 12px;
            font-size: 0.95rem;
        }

        .footer a:hover {
            color: var(--primary);
            transform: translateX(4px);
        }

        .social-links {
            display: flex;
            gap: 12px;
            margin-top: 24px;
        }

        .social-links a {
            width: 44px;
            height: 44px;
            background: rgba(255, 255, 255, 0.05);
            border: 1px solid rgba(255, 255, 255, 0.1);
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            transition: all 0.3s ease;
            margin: 0;
        }

        .social-links a:hover {
            background: var(--primary);
            border-color: var(--primary);
            transform: translateY(-3px);
        }

        .footer-bottom {
            margin-top: 60px;
            padding-top: 30px;
            border-top: 1px solid rgba(255, 255, 255, 0.05);
            text-align: center;
            color: rgba(255, 255, 255, 0.4);
            font-size: 0.9rem;
        }

        /* Responsive */
        @media (max-width: 992px) {
            .hero-title {
                font-size: 3.5rem;
            }

            .section-title {
                font-size: 2.5rem;
            }

            .cta-section h2 {
                font-size: 2.5rem;
            }
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
                justify-content: center;
            }

            .section-title {
                font-size: 2rem;
            }

            .cta-section h2 {
                font-size: 2rem;
            }

            .floating-card {
                display: none;
            }
        }

        /* Scroll Animations */
        .scroll-reveal {
            opacity: 0;
            transform: translateY(30px);
            transition: all 0.8s ease;
        }

        .scroll-reveal.active {
            opacity: 1;
            transform: translateY(0);
        }
    </style>
</head>

<body>
<!-- Hero Section -->
<section class="hero-section">
    <div class="hero-gradient"></div>
    <div class="hero-gradient-2"></div>

    <div class="container">
        <div class="hero-content">
            <div class="row align-items-center">
                <div class="col-lg-6">
                    <div class="hero-badge">
                        <i class="fas fa-sparkles"></i>
                        <span>Join 10,000+ Active Members</span>
                    </div>

                    <h1 class="hero-title">
                        Exchange Skills.<br>
                        <span class="gradient-text">Grow Together.</span>
                    </h1>

                    <p class="hero-subtitle">
                        Connect with a global community of professionals. Share expertise, learn new skills, and build meaningful collaborations that accelerate your growth.
                    </p>

                    <div class="hero-buttons">
                        <a href="auth/register.php" class="btn-hero btn-primary-hero">
                            <i class="fas fa-rocket"></i> Get Started Free
                        </a>
                        <a href="tasks/view_task.php" class="btn-hero btn-outline-hero">
                            <i class="fas fa-compass"></i> Explore Tasks
                        </a>
                    </div>
                </div>

                <div class="col-lg-6">
                    <div class="hero-visual position-relative">
                        <div class="text-center" style="font-size: 18rem; color: rgba(0, 102, 255, 0.08);">
                            <i class="fas fa-handshake"></i>
                        </div>

                        <div class="floating-card floating-card-1">
                            <div class="card-icon"><i class="fas fa-users"></i></div>
                            <div style="color: rgba(255,255,255,0.8); font-weight: 600;">Global Network</div>
                            <div style="color: rgba(255,255,255,0.5); font-size: 0.85rem;">150+ Countries</div>
                        </div>

                        <div class="floating-card floating-card-2">
                            <div class="card-icon"><i class="fas fa-chart-line"></i></div>
                            <div style="color: rgba(255,255,255,0.8); font-weight: 600;">98% Success</div>
                            <div style="color: rgba(255,255,255,0.5); font-size: 0.85rem;">Task Completion</div>
                        </div>

                        <div class="floating-card floating-card-3">
                            <div class="card-icon"><i class="fas fa-bolt"></i></div>
                            <div style="color: rgba(255,255,255,0.8); font-weight: 600;">Real-time</div>
                            <div style="color: rgba(255,255,255,0.5); font-size: 0.85rem;">Collaboration</div>
                        </div>
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
        <div class="section-header scroll-reveal">
            <span class="section-badge">Process</span>
            <h2 class="section-title">How It Works</h2>
            <p class="section-subtitle">Get started in just four simple steps</p>
        </div>

        <div class="row">
            <div class="col-lg-6 scroll-reveal">
                <div class="step-card">
                    <div class="step-number">1</div>
                    <h4><i class="fas fa-user-plus"></i> Create Your Profile</h4>
                    <p>Sign up for free and build your professional profile. Add your skills, experience, portfolio, and what you're looking to learn or accomplish.</p>
                </div>
            </div>

            <div class="col-lg-6 scroll-reveal">
                <div class="step-card">
                    <div class="step-number">2</div>
                    <h4><i class="fas fa-search"></i> Find or Post Tasks</h4>
                    <p>Browse available tasks that match your expertise or post your own task to get help from talented community members.</p>
                </div>
            </div>

            <div class="col-lg-6 scroll-reveal">
                <div class="step-card">
                    <div class="step-number">3</div>
                    <h4><i class="fas fa-handshake"></i> Collaborate & Learn</h4>
                    <p>Work together with professionals worldwide, share knowledge through real-time collaboration, and complete tasks efficiently.</p>
                </div>
            </div>

            <div class="col-lg-6 scroll-reveal">
                <div class="step-card">
                    <div class="step-number">4</div>
                    <h4><i class="fas fa-award"></i> Earn Recognition</h4>
                    <p>Get rated by peers, earn points, unlock exclusive achievements, and build an impressive reputation in the community.</p>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Testimonials -->
<section class="testimonials-section">
    <div class="container">
        <div class="section-header scroll-reveal">
            <span class="section-badge">Testimonials</span>
            <h2 class="section-title">Loved by Professionals Worldwide</h2>
            <p class="section-subtitle">Join thousands of satisfied members who have transformed their careers</p>
        </div>

        <div class="row g-4">
            <div class="col-lg-4 col-md-6 scroll-reveal">
                <div class="testimonial-card">
                    <div class="testimonial-text">
                        "SkillBridge transformed my career. I learned web development from scratch through amazing mentors, and now I'm working on projects I never thought possible. The community support is incredible!"
                    </div>
                    <div class="testimonial-author">
                        <div class="author-avatar">SJ</div>
                        <div class="author-info">
                            <h5>Sarah Johnson</h5>
                            <p>Senior Web Developer</p>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-lg-4 col-md-6 scroll-reveal">
                <div class="testimonial-card">
                    <div class="testimonial-text">
                        "I've connected with talented professionals from 15+ countries and expanded my skill set dramatically. The points system keeps me motivated, and the quality of collaborations is outstanding."
                    </div>
                    <div class="testimonial-author">
                        <div class="author-avatar">MC</div>
                        <div class="author-info">
                            <h5>Michael Chen</h5>
                            <p>Creative Director</p>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-lg-4 col-md-6 scroll-reveal">
                <div class="testimonial-card">
                    <div class="testimonial-text">
                        "The gamification and achievements keep me engaged daily. It's like LinkedIn meets Duolingo, but for professional skills. I've earned 5 certifications and countless connections!"
                    </div>
                    <div class="testimonial-author">
                        <div class="author-avatar">ER</div>
                        <div class="author-info">
                            <h5>Emily Rodriguez</h5>
                            <p>Marketing Strategist</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- CTA Section -->
<section class="cta-section">
    <div class="cta-content">
        <div class="container">
            <h2>Ready to Accelerate Your Growth?</h2>
            <p>Join our thriving community today and unlock endless opportunities for learning, collaboration, and professional development.</p>
            <a href="auth/register.php" class="btn-cta">
                <i class="fas fa-rocket"></i> Start Your Journey Free
            </a>
        </div>
    </div>
</section>

<!-- Footer -->

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js"></script>

<script>
    // Scroll Reveal Animation
    const observerOptions = {
        threshold: 0.1,
        rootMargin: '0px 0px -50px 0px'
    };

    const observer = new IntersectionObserver((entries) => {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                entry.target.classList.add('active');
            }
        });
    }, observerOptions);

    document.querySelectorAll('.scroll-reveal').forEach((element) => {
        observer.observe(element);
    });

    // Counter Animation
    const animateCounter = (element, target) => {
        let current = 0;
        const increment = target / 50;
        const timer = setInterval(() => {
            current += increment;
            if (current >= target) {
                element.textContent = target.toString().includes('+') ? target : target + (target === 98 ? '%' : '+');
                clearInterval(timer);
            } else {
                element.textContent = Math.floor(current) + (target === 98 ? '%' : '+');
            }
        }, 30);
    };

    const statObserver = new IntersectionObserver((entries) => {
        entries.forEach(entry => {
            if (entry.isIntersecting && !entry.target.dataset.animated) {
                const text = entry.target.textContent;
                const target = text === '98%' ? 98 : parseInt(text);
                entry.target.dataset.animated = 'true';
                animateCounter(entry.target, target);
            }
        });
    }, { threshold: 0.5 });

    document.querySelectorAll('.stat-number').forEach(stat => {
        statObserver.observe(stat);
    });
</script>

</body>
<?php include 'includes/footer.php' ?>

<!--</html><span class="section-badge">Features</span>-->
<!--<h2 class="section-title">Everything You Need to Succeed</h2>-->
<!--<p class="section-subtitle">Powerful tools and features designed to help you exchange skills and grow professionally</p>-->
<!--</div>-->

<!--<div class="row g-4">-->
<!--    <div class="col-lg-4 col-md-6 scroll-reveal">-->
<!--        <div class="feature-card">-->
<!--            <div class="feature-icon">-->
<!--                <i class="fas fa-users"></i>-->
<!--            </div>-->
<!--            <h4>Global Community</h4>-->
<!--            <p>Connect with skilled professionals from around the world. Share knowledge, collaborate on projects, and grow together in a supportive environment.</p>-->
<!--        </div>-->
<!--    </div>-->
<!---->
<!--    <div class="col-lg-4 col-md-6 scroll-reveal">-->
<!--        <div class="feature-card">-->
<!--            <div class="feature-icon">-->
<!--                <i class="fas fa-shield-alt"></i>-->
<!--            </div>-->
<!--            <h4>Enterprise Security</h4>-->
<!--            <p>Your data and transactions are protected with bank-level encryption and enterprise-grade security protocols.</p>-->
<!--        </div>-->
<!--    </div>-->
<!---->
<!--    <div class="col-lg-4 col-md-6 scroll-reveal">-->
<!--        <div class="feature-card">-->
<!--            <div class="feature-icon">-->
<!--                <i class="fas fa-star"></i>-->
<!--            </div>-->
<!--            <h4>Reputation System</h4>-->
<!--            <p>Build your credibility with verified ratings, detailed reviews, and achievement badges that showcase your expertise.</p>-->
<!--        </div>-->
<!--    </div>-->
<!---->
<!--    <div class="col-lg-4 col-md-6 scroll-reveal">-->
<!--        <div class="feature-card">-->
<!--            <div class="feature-icon">-->
<!--                <i class="fas fa-coins"></i>-->
<!--            </div>-->
<!--            <h4>Points & Rewards</h4>-->
<!--            <p>Earn points for completing tasks and unlock exclusive benefits, premium features, and special community privileges.</p>-->
<!--        </div>-->
<!--    </div>-->
<!---->
<!--    <div class="col-lg-4 col-md-6 scroll-reveal">-->
<!--        <div class="feature-card">-->
<!--            <div class="feature-icon">-->
<!--                <i class="fas fa-comments"></i>-->
<!--            </div>-->
<!--            <h4>Real-time Chat</h4>-->
<!--            <p>Communicate instantly with collaborators through our integrated messaging system with file sharing and video calls.</p>-->
<!--        </div>-->
<!--    </div>-->
<!---->
<!--    <div class="col-lg-4 col-md-6 scroll-reveal">-->
<!--        <div class="feature-card">-->
<!--            <div class="feature-icon">-->
<!--                <i class="fas fa-trophy"></i>-->
<!--            </div>-->
<!--            <h4>Achievements</h4>-->
<!--            <p>Unlock badges and achievements as you complete milestones, master new skills, and contribute to the community.</p>-->
<!--        </div>-->
<!--    </div>-->
<!--</div>-->
<!--</div>-->
<!--</section>-->

