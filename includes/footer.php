<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
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
            min-height: 100vh;
            display: flex;
            flex-direction: column;
        }

        main {
            flex: 1;
            padding-bottom: 100px; /* Space for footer */
        }

        .footer {
            background: rgba(255, 255, 255, 0.8);
            backdrop-filter: blur(10px);
            -webkit-backdrop-filter: blur(10px);
            padding: 30px 20px;
            text-align: center;
            margin-top: auto;
            box-shadow: 0 -4px 30px rgba(0, 0, 0, 0.1);
            border-top: 1px solid rgba(255, 255, 255, 0.3);
            transition: all 0.4s ease;
        }

        .footer.scrolled {
            background: rgba(0, 0, 0, 0.8);
            backdrop-filter: blur(10px);
            -webkit-backdrop-filter: blur(10px);
            color: white;
            box-shadow: 0 -4px 30px rgba(0, 0, 0, 0.3);
            border-top: 1px solid rgba(0, 0, 0, 0.3);
        }

        .footer.scrolled p,
        .footer.scrolled a {
            color: white;
        }

        .footer.scrolled a:hover {
            color: #b36bff;
        }

        .footer p {
            font-size: 1rem;
            margin-bottom: 15px;
            color: #333;
            transition: all 0.3s ease;
        }

        .footer ul {
            list-style: none;
            display: flex;
            justify-content: center;
            flex-wrap: wrap;
            gap: 25px;
            margin: 15px 0;
        }

        .footer a {
            text-decoration: none;
            color: #333;
            font-weight: 500;
            display: flex;
            align-items: center;
            gap: 8px;
            padding: 8px 12px;
            border-radius: 8px;
            transition: all 0.3s ease;
        }

        .footer a:hover {
            color: #9D00FF;
            background: rgba(157, 0, 255, 0.1);
            transform: translateY(-2px);
        }

        .footer a i {
            font-size: 0.9rem;
        }

        /* Responsive */
        @media screen and (max-width: 768px) {
            .footer {
                padding: 25px 15px;
            }

            .footer ul {
                gap: 15px;
                flex-direction: column;
                align-items: center;
            }

            .footer a {
                padding: 10px 15px;
                width: 100%;
                justify-content: center;
            }
        }
    </style>
</head>

<body>
    <main>
        <!-- Your page content here -->
    </main>

    <footer class="footer" id="footer">
        <p>&copy; 2025 Postify. All rights reserved.</p>
        <ul>
            <li><a href="#"><i class="fas fa-lock"></i> Privacy</a></li>
            <li><a href="#"><i class="fas fa-file-contract"></i> Terms</a></li>
            <li><a href="#"><i class="fas fa-question-circle"></i> Support</a></li>
            <li><a href="#"><i class="fas fa-envelope"></i> Contact</a></li>
        </ul>
    </footer>

    <script>
        const footer = document.getElementById('footer');

        // Change footer style on scroll
        window.addEventListener('scroll', () => {
            if (window.scrollY > 50) {
                footer.classList.add('scrolled');
            } else {
                footer.classList.remove('scrolled');
            }
        });
    </script>

</body>

</html>