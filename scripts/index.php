<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>CS306 Marketplace Portal</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;600;800&display=swap" rel="stylesheet">
    <style>
        :root {
            --primary: #6366f1;
            --primary-hover: #4f46e5;
            --accent: #a855f7;
            --bg-dark: #0f172a;
            --card-bg: rgba(30, 41, 59, 0.7);
            --border-color: rgba(255, 255, 255, 0.1);
        }
        
        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
        }

        body {
            font-family: 'Outfit', sans-serif;
            background: radial-gradient(circle at 50% 50%, #1e1b4b 0%, var(--bg-dark) 100%);
            color: #f1f5f9;
            min-height: 100vh;
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            overflow-x: hidden;
            padding: 20px;
        }

        /* Decorative glowing blobs */
        .blob {
            position: absolute;
            width: 300px;
            height: 300px;
            background: radial-gradient(circle, rgba(99, 102, 241, 0.15) 0%, rgba(0,0,0,0) 70%);
            border-radius: 50%;
            z-index: 0;
            filter: blur(40px);
        }
        .blob-1 { top: 10%; left: 15%; animation: float 10s ease-in-out infinite; }
        .blob-2 { bottom: 10%; right: 15%; animation: float 12s ease-in-out infinite alternate; }

        @keyframes float {
            0% { transform: translateY(0px) scale(1); }
            50% { transform: translateY(-20px) scale(1.1); }
            100% { transform: translateY(0px) scale(1); }
        }

        .container {
            position: relative;
            z-index: 1;
            max-width: 900px;
            width: 100%;
            text-align: center;
        }

        header {
            margin-bottom: 50px;
        }

        h1 {
            font-size: 3rem;
            font-weight: 800;
            background: linear-gradient(135deg, #fff 0%, #a5b4fc 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            margin-bottom: 12px;
            letter-spacing: -0.02em;
        }

        p.subtitle {
            font-size: 1.15rem;
            color: #94a3b8;
            font-weight: 300;
            max-width: 600px;
            margin: 0 auto;
        }

        .portal-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
            gap: 30px;
            margin-top: 20px;
        }

        .card {
            background: var(--card-bg);
            backdrop-filter: blur(16px);
            -webkit-backdrop-filter: blur(16px);
            border: 1px solid var(--border-color);
            border-radius: 24px;
            padding: 40px 30px;
            text-align: left;
            transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
            position: relative;
            overflow: hidden;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
            height: 320px;
        }

        .card::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: linear-gradient(135deg, rgba(99, 102, 241, 0.1) 0%, rgba(168, 85, 247, 0.1) 100%);
            opacity: 0;
            transition: opacity 0.4s ease;
        }

        .card:hover {
            transform: translateY(-8px);
            border-color: rgba(99, 102, 241, 0.4);
            box-shadow: 0 20px 40px -15px rgba(0, 0, 0, 0.5), 0 0 50px -10px rgba(99, 102, 241, 0.2);
        }

        .card:hover::before {
            opacity: 1;
        }

        .card-icon {
            font-size: 2.5rem;
            margin-bottom: 20px;
            display: inline-block;
            position: relative;
            z-index: 2;
        }

        .card-content {
            position: relative;
            z-index: 2;
        }

        .card-title {
            font-size: 1.5rem;
            font-weight: 600;
            margin-bottom: 12px;
            color: #fff;
        }

        .card-desc {
            font-size: 0.95rem;
            color: #94a3b8;
            line-height: 1.6;
            margin-bottom: 25px;
        }

        .card-action {
            position: relative;
            z-index: 2;
            display: inline-flex;
            align-items: center;
            gap: 8px;
            color: #6366f1;
            text-decoration: none;
            font-weight: 600;
            font-size: 1.05rem;
            transition: gap 0.2s ease, color 0.2s ease;
        }

        .card:hover .card-action {
            color: #818cf8;
            gap: 12px;
        }

        .card-action svg {
            width: 18px;
            height: 18px;
            fill: none;
            stroke: currentColor;
            stroke-width: 2.5;
            stroke-linecap: round;
            stroke-linejoin: round;
            transition: transform 0.2s ease;
        }

        .card:hover .card-action svg {
            transform: translateX(2px);
        }

        footer {
            margin-top: 80px;
            color: #64748b;
            font-size: 0.85rem;
            z-index: 1;
        }

        footer a {
            color: #94a3b8;
            text-decoration: none;
            transition: color 0.2s ease;
        }

        footer a:hover {
            color: #fff;
        }
    </style>
</head>
<body>
    <div class="blob blob-1"></div>
    <div class="blob blob-2"></div>

    <div class="container">
        <header>
            <h1>Marketplace Classifieds Portal</h1>
            <p class="subtitle">Polyglot persistence marketplace application utilizing MySQL for transaction management and MongoDB for support ticketing.</p>
        </header>

        <div class="portal-grid">
            <!-- User Dashboard Card -->
            <div class="card" onclick="window.location.href='user/index.php'" style="cursor: pointer;">
                <div>
                    <div class="card-icon">👤</div>
                    <div class="card-content">
                        <h2 class="card-title">User Dashboard</h2>
                        <p class="card-desc">Browse products, view lists, interact with stored procedures/triggers, and open customer support tickets.</p>
                    </div>
                </div>
                <div>
                    <a href="user/index.php" class="card-action">
                        Enter User Portal 
                        <svg viewBox="0 0 24 24"><path d="M5 12h14M12 5l7 7-7 7"/></svg>
                    </a>
                </div>
            </div>

            <!-- Admin Dashboard Card -->
            <div class="card" onclick="window.location.href='admin/index.php'" style="cursor: pointer;">
                <div>
                    <div class="card-icon">🛡️</div>
                    <div class="card-content">
                        <h2 class="card-title">Admin Dashboard</h2>
                        <p class="card-desc">Review and manage support tickets logged in MongoDB, update status, and response communications.</p>
                    </div>
                </div>
                <div>
                    <a href="admin/index.php" class="card-action">
                        Enter Admin Portal 
                        <svg viewBox="0 0 24 24"><path d="M5 12h14M12 5l7 7-7 7"/></svg>
                    </a>
                </div>
            </div>
        </div>

        <footer>
            <p>CS306 Project Phase 3 | Group 25 | <a href="https://github.com/CemSarp/Mysql-marketplace-webapp.git" target="_blank">GitHub Repository</a></p>
        </footer>
    </div>
</body>
</html>
