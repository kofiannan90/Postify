<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Postify</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" />
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }

        body {
            background: #f9f9f9;
            color: #333;
            min-height: 200vh; /* For demonstration */
        }

        .navbar {
            background: rgba(255, 255, 255, 0.8);
            backdrop-filter: blur(10px);
            -webkit-backdrop-filter: blur(10px);
            padding: 15px 30px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            box-shadow: 0 4px 30px rgba(0, 0, 0, 0.1);
            position: fixed;
            width: 100%;
            top: 0;
            z-index: 1000;
            border-bottom: 1px solid rgba(255, 255, 255, 0.3);
            transition: all 0.3s ease;
        }

        .navbar.scrolled {
            background: rgba(0, 0, 0, 0.8);
            backdrop-filter: blur(10px);
            -webkit-backdrop-filter: blur(10px);
            color: white;
            box-shadow: 0 4px 30px rgba(0, 0, 0, 0.3);
            border-bottom: 1px solid rgba(0, 0, 0, 0.3);
        }

        .navbar.scrolled .logo {
            color: #fff;
        }

        .navbar.scrolled .nav-links li a {
            color: #fff;
        }

        .navbar.scrolled .nav-links li a:hover {
            color: #b36bff;
        }

        .navbar.scrolled .logout {
            background: rgba(255, 0, 0, 0.8);
            color: white;
        }

        .logo {
            font-size: 1.8rem;
            font-weight: bold;
            color: #9D00FF;
            display: flex;
            align-items: center;
            gap: 10px;
            transition: all 0.3s ease;
        }

        .logo i {
            font-size: 1.5rem;
        }

        .menu-toggle {
            font-size: 1.8rem;
            cursor: pointer;
            display: none;
            color: #9D00FF;
            transition: all 0.3s ease;
        }

        .menu-toggle:hover {
            transform: scale(1.1);
        }

        .nav-links {
            list-style: none;
            display: flex;
            gap: 30px;
            transition: all 0.3s ease;
        }

        .nav-links li a {
            text-decoration: none;
            color: #333;
            font-weight: 600;
            padding: 8px 12px;
            border-radius: 8px;
            transition: all 0.3s ease;
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .nav-links li a:hover {
            color: #9D00FF;
            background: rgba(157, 0, 255, 0.1);
            transform: translateY(-2px);
        }

        .nav-links li a i {
            font-size: 0.9rem;
        }

        .logout {
            background: rgba(255, 0, 0, 0.7);
            color: white;
            padding: 10px 20px;
            border-radius: 8px;
            text-decoration: none;
            font-weight: bold;
            transition: all 0.3s ease;
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .logout:hover {
            background: rgba(255, 0, 0, 0.9);
            transform: translateY(-2px);
            box-shadow: 0 4px 8px rgba(255, 0, 0, 0.2);
        }

        /* Responsive */
        @media screen and (max-width: 768px) {
            .navbar {
                padding: 15px 20px;
            }
            
            .menu-toggle {
                display: block;
            }

            .nav-links {
                position: fixed;
                top: 70px;
                left: 0;
                width: 100%;
                background: rgba(255, 255, 255, 0.95);
                backdrop-filter: blur(15px);
                -webkit-backdrop-filter: blur(15px);
                flex-direction: column;
                align-items: center;
                padding: 20px 0;
                display: none;
                box-shadow: 0 10px 20px rgba(0, 0, 0, 0.1);
                border-radius: 0 0 15px 15px;
            }

            .navbar.scrolled .nav-links {
                background: rgba(0, 0, 0, 0.95);
            }

            .nav-links.active {
                display: flex;
            }

            .nav-links li {
                margin: 10px 0;
                width: 100%;
                text-align: center;
            }

            .nav-links li a {
                justify-content: center;
                padding: 12px;
            }

            .logout {
                width: 80%;
                justify-content: center;
                margin-top: 10px;
            }
        }
    </style>
</head>

<body>

    <!-- Navbar -->
    <nav class="navbar" id="navbar">
        <div class="logo">
            <i class="fas fa-bolt"></i>
            <span>Postify</span>
        </div>
        <div class="menu-toggle" id="menu-toggle">
            <i class="fas fa-bars"></i>
        </div>
        <ul class="nav-links" id="nav-links">
            <li><a href="../pages/home.php"><i class="fas fa-home"></i> Home</a></li>
            <li><a href="../pages/create_post.php"><i class="fas fa-plus-circle"></i> Create Post</a></li>
            <li><a href="../pages/profile.php"><i class="fas fa-user"></i> Profile</a></li>
            <li><a href="../actions/logout.php" class="logout"><i class="fas fa-sign-out-alt"></i> Logout</a></li>
        </ul>
    </nav>

    <script>
        const toggleBtn = document.getElementById('menu-toggle');
        const navLinks = document.getElementById('nav-links');
        const navbar = document.getElementById('navbar');

        // Toggle mobile menu
        toggleBtn.addEventListener('click', () => {
            navLinks.classList.toggle('active');
            toggleBtn.innerHTML = navLinks.classList.contains('active') 
                ? '<i class="fas fa-times"></i>' 
                : '<i class="fas fa-bars"></i>';
        });

        // Change navbar style on scroll
        window.addEventListener('scroll', () => {
            if (window.scrollY > 50) {
                navbar.classList.add('scrolled');
            } else {
                navbar.classList.remove('scrolled');
            }
        });

        // Close mobile menu when clicking on a link
        document.querySelectorAll('.nav-links a').forEach(link => {
            link.addEventListener('click', () => {
                navLinks.classList.remove('active');
                toggleBtn.innerHTML = '<i class="fas fa-bars"></i>';
            });
        });
    </script>

</body>

</html>