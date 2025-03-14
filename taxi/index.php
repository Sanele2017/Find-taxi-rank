<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Onice Tech</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@500&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="css/styles.css">
</head>

<body>

    <nav class="navbar">
        <div class="menu_left">
            <div class="logo">
                <h2>Onice Tech</h2>
            </div>
            <div class="menu-li">
                <ul class="menu">
                    <li class="menu_list">
                        <a href="index.php">Home</a>
                    </li>
                    <li class="menu_list">
                        <a href="rank-details.php">Taxi Ranks</a>
                    </li>
                    <li class="menu_list lg-hidden">
                        <a href="contact.php">Contact Us</a>
                    </li>
                </ul>
            </div>

        </div>
        <div class="menu_right">
            <a href="contact.php" class="btn-contact sm-hidden">Contact Us</a>
            <button onclick="toggleMenu()" class="btn-menubar lg-hidden">
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 448 512">
                    <path
                        d="M0 96C0 78.3 14.3 64 32 64H416c17.7 0 32 14.3 32 32s-14.3 32-32 32H32C14.3 128 0 113.7 0 96zM0 256c0-17.7 14.3-32 32-32H416c17.7 0 32 14.3 32 32s-14.3 32-32 32H32c-17.7 0-32-14.3-32-32zM448 416c0 17.7-14.3 32-32 32H32c-17.7 0-32-14.3-32-32s14.3-32 32-32H416c17.7 0 32 14.3 32 32z" />
                </svg>
            </button>
        </div>
    </nav>

    <div class="card">
        <div class="card-img">
            <img src="images/taxi2.webp" class="active" alt="Image 1">
            <img src="images/taxi3.jpg" class="inactive" alt="Image 2">
            <img src="images/taxi1.jpg" class="inactive" alt="Image 3">
            <!-- Add more images as necessary -->
            <div class="carousel-nav">
                <button class="prev"></button>
                <button class="next"></button>
            </div>
        </div>
        <div class="text-content">
            <div class="text-data active">
                <h1>Find a Taxi Rank</h1>
                <p>Locate taxi ranks near you, explore destinations, and check fares with ease.</p>
                <p>Your guide to hassle-free travel and quick taxi connections.</p>
            </div>
        </div>
        <div class="bottom">
            <button onclick="previousImage(); previousText();"><span>&#8592;</span></button>
            <button onclick="nextImage(); nextText();"><span>&#8594;</span></button>
        </div>
    </div>

    <script src="js/app.js"></script>
</body>
</html>