<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>LogiTrans - Modern Logistics Solutions</title>
    <link rel="stylesheet" href="assets/css/style.css">
    <style>
        .hero {
            height: 90vh;
            display: flex;
            align-items: center;
            justify-content: center;
            text-align: center;
            background: linear-gradient(rgba(0, 0, 0, 0.6), rgba(0, 0, 0, 0.6)), url('https://images.unsplash.com/photo-1519003722824-194d4455a60c?ixlib=rb-4.0.3&auto=format&fit=crop&w=1920&q=80');
            background-size: cover;
            background-position: center;
            color: white;
            padding: 0 1rem;
        }

        .hero-content h1 {
            font-size: 3.5rem;
            margin-bottom: 1.5rem;
            text-shadow: 2px 2px 4px rgba(0, 0, 0, 0.3);
        }

        .hero-content p {
            font-size: 1.25rem;
            max-width: 700px;
            margin: 0 auto 2.5rem;
            opacity: 0.9;
        }

        .nav-home {
            background: rgba(255, 255, 255, 0.1);
            backdrop-filter: blur(10px);
            padding: 1rem 5%;
            display: flex;
            justify-content: space-between;
            align-items: center;
            position: fixed;
            width: 100%;
            top: 0;
            z-index: 1000;
        }

        .nav-logo {
            font-size: 1.5rem;
            font-weight: 800;
            color: #38bdf8;
            text-decoration: none;
        }

        .nav-btns {
            display: flex;
            gap: 1rem;
        }

        .feature-section {
            padding: 5rem 10%;
            background: white;
            text-align: center;
        }

        .feature-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
            gap: 3rem;
            margin-top: 3rem;
        }

        .feature-item h3 {
            margin-bottom: 1rem;
            color: var(--primary-color);
        }
    </style>
</head>

<body>
    <nav class="nav-home">
        <a href="index.php" class="nav-logo">LogiTrans</a>
        <div class="nav-btns">
            <a href="login.php" class="btn btn-primary" style="width: auto;">Login</a>
        </div>
    </nav>

    <header class="hero">
        <div class="hero-content">
            <h1>Reliable Logistics for Every Move</h1>
            <p>Connect with the best transporters or manage your fleet with ease. Real-time load tracking and secure
                payments.</p>
            <div class="nav-btns" style="justify-content: center;">
                <a href="register.php" class="btn btn-primary" style="width: 200px; padding: 1rem;">Get Started</a>
            </div>
        </div>
    </header>

    <section class="feature-section">
        <h2>Our Core Services</h2>
        <div class="feature-grid">
            <div class="feature-item">
                <h3>For Dispatchers</h3>
                <p>Post loads easily, assign trusted drivers, and track your shipments from city to city with full
                    transparency.</p>
            </div>
            <div class="feature-item">
                <h3>For Transporters</h3>
                <p>Find work instantly. Browse our marketplace, claim loads that match your route, and complete tasks
                    for fast payouts.</p>
            </div>
            <div class="feature-item">
                <h3>Nationwide Network</h3>
                <p>Covering all major cities in Pakistan. From Karachi to Peshawar, we keep the wheels turning.</p>
            </div>
        </div>
    </section>

    <footer style="padding: 2rem; background: #1e293b; color: #94a3b8; text-align: center;">
        <p>&copy; 2026 LogiTrans Systems. All rights reserved.</p>
    </footer>
</body>

</html>