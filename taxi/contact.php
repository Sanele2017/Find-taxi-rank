<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Contact Us | Onice Tech</title>
    <link rel="stylesheet" href="css/styles.css">
    <style>
        body {
            font-family: Arial, sans-serif;
            line-height: 1.6;
            margin: 0;
            padding: 0;
            background-color: #f0f5f5;
            color: #333;
        }

        .navbar {
            background-color: #000;
            padding: 20px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            color: #fff;
        }

        .navbar h2 {
            font-size: 24px;
            color: #fff;
        }

        .navbar ul {
            list-style: none;
            display: flex;
            gap: 20px;
        }

        .navbar ul li a {
            text-decoration: none;
            color: #fff;
            font-size: 16px;
        }

        .navbar ul li a:hover {
            color: #6CBF4F;
        }

        .contact-section {
            background-color: #fff;
            padding: 60px 20px;
            text-align: center;
        }

        .contact-section h1 {
            font-size: 36px;
            margin-bottom: 20px;
            color: #000;
        }

        .contact-section p {
            font-size: 18px;
            color: #555;
            margin-bottom: 30px;
        }

        .contact-cards {
            display: flex;
            justify-content: center;
            flex-wrap: wrap;
            gap: 30px;
        }

        .contact-card {
            background-color: #f9f9f9;
            padding: 20px;
            border-radius: 12px;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
            text-align: left;
            width: 300px;
            transition: transform 0.3s ease, box-shadow 0.3s ease;
        }

        .contact-card:hover {
            transform: translateY(-10px);
            box-shadow: 0 15px 30px rgba(0, 0, 0, 0.2);
        }

        .contact-card h3 {
            font-size: 20px;
            margin-bottom: 10px;
            color: #000;
        }

        .contact-card p {
            font-size: 16px;
            color: #555;
            margin-bottom: 10px;
        }

        .contact-card a {
            text-decoration: none;
            color: #6CBF4F;
            font-weight: bold;
            transition: color 0.3s ease;
        }

        .contact-card a:hover {
            color: #4E8F35;
        }

        .map-container {
            margin-top: 40px;
            text-align: center;
        }

        .map-container iframe {
            width: 100%;
            max-width: 800px;
            height: 400px;
            border: 0;
            border-radius: 12px;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
        }

        .footer {
            background-color: #000;
            color: #fff;
            text-align: center;
            padding: 20px;
            margin-top: 30px;
        }
    </style>
</head>
<body>

<nav class="navbar">
    <div class="logo">
        <h2>Onice Tech</h2>
    </div>
    <ul class="menu">
        <li><a href="index.php">Home</a></li>
        <li><a href="rank-details.php">Taxi Ranks</a></li>
        <li><a href="contact.php">Contact Us</a></li>
    </ul>
</nav>

<section class="contact-section">
    <h1>Get in Touch with Us</h1>
    <p>We’re here to answer your questions and help you connect with the best solutions.</p>

    <div class="contact-cards">
        <div class="contact-card">
            <h3>Telephone</h3>
            <p>+27 12 345 6789</p>
        </div>

        <div class="contact-card">
            <h3>Email</h3>
            <p><a href="mailto:info@onicetech.co.za">info@onicetech.co.za</a></p>
        </div>

        <div class="contact-card">
            <h3>Physical Address</h3>
            <p>123 Innovation Drive,<br>Midrand, Johannesburg,<br>South Africa</p>
        </div>

        <div class="contact-card">
            <h3>Follow Us</h3>
            <p>
                <a href="#" target="_blank">Facebook</a> | 
                <a href="#" target="_blank">Twitter</a> | 
                <a href="#" target="_blank">LinkedIn</a>
            </p>
        </div>
    </div>

    <div class="map-container">
        <iframe 
            src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3564.317616479271!2d28.058071815078135!3d-25.9990742658354!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x1e96b9b5e6c9c7f9%3A0x92e92b2267e0c5d!2sMidrand!5e0!3m2!1sen!2sza!4v1692304603452" 
            allowfullscreen="" 
            loading="lazy" 
            referrerpolicy="no-referrer-when-downgrade"></iframe>
    </div>
</section>

<div class="footer">
    <p>&copy; 2025 Onice Tech. All rights reserved.</p>
</div>

</body>
</html>
