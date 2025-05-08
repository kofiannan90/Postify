<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Postify</title>
    <link rel="stylesheet" href="style.css" />
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: sans-serif;
        }

        body {
            background: #f9f9f9;
            color: #333;
        }

        .navbar {
            background: white;
            padding: 15px 20px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
            position: fixed;
            width: 100%;
            top: 0;
            z-index: 10;
        }

        .logo {
            font-size: 1.5rem;
            font-weight: bold;
            color: #9D00FF;
        }

        .menu-toggle {
            font-size: 1.8rem;
            cursor: pointer;
            display: none;
        }

        .nav-links {
            list-style: none;
            display: flex;
            gap: 20px;
        }

        .nav-links li a {
            text-decoration: none;
            color: #333;
            font-weight: 500;
        }

        .nav-links li a:hover {
            color: #9D00FF;
        }

        /* Responsive */
        @media screen and (max-width: 768px) {
            .menu-toggle {
                display: block;
            }

            .nav-links {
                position: absolute;
                top: 60px;
                left: 0;
                width: 100%;
                background: white;
                flex-direction: column;
                align-items: center;
                display: none;
            }

            .nav-links.active {
                display: flex;
            }

            .nav-links li {
                margin: 10px 0;
            }
        }
    </style>
</head>

<body>

    <!-- Navbar -->
    <nav class="navbar">
        <div class="logo">Postify</div>
        <div class="menu-toggle" id="menu-toggle">&#9776;</div>
        <ul class="nav-links" id="nav-links">
            <li><a href="#">Home</a></li>
            <li><a href="#">Explore</a></li>
            <li><a href="#">Ask</a></li>
            <li><a href="#">Profile</a></li>
        </ul>
    </nav>



    <script>
        const toggleBtn = document.getElementById('menu-toggle');
        const navLinks = document.getElementById('nav-links');

        toggleBtn.addEventListener('click', () => {
            navLinks.classList.toggle('active');
        });
    </script>

</body>

</html>