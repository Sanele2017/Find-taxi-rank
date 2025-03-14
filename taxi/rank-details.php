<?php
// Database connection details
$host = "localhost";
$username = "root";
$password = "";
$database = "taxi-rank";

// Connect to the database
$conn = new mysqli($host, $username, $password, $database);

// Check for connection errors
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Initialize variables for search and dropdown filtering
$searchCity = "";
$selectedCity = "";
$filterQuery = "";

// Handle search and dropdown independently
if (!empty($_GET['search_city'])) {
    $searchCity = $conn->real_escape_string($_GET['search_city']);
    $filterQuery = "WHERE city LIKE '%$searchCity%'";
} elseif (!empty($_GET['dropdown_city']) && $_GET['dropdown_city'] !== "all") {
    $selectedCity = $conn->real_escape_string($_GET['dropdown_city']);
    $filterQuery = "WHERE city = '$selectedCity'";
}

// Fetch data from the 'ranks' table based on the filter
$sql = "SELECT city, COUNT(*) AS number_of_ranks, operating_hours, association 
        FROM ranks 
        $filterQuery
        GROUP BY city, operating_hours, association";
$result = $conn->query($sql);

// Fetch all cities for the dropdown
$citiesQuery = "SELECT DISTINCT city FROM ranks";
$citiesResult = $conn->query($citiesQuery);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Onice Tech</title>
    <link rel="stylesheet" href="css/styles.css">
    <style>
        /* General Reset */
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
            background-color: #f9f9f9;
            color: #333;
        }

        /* Navbar */
        .navbar {
            background-color: #004d40;
            color: white;
            padding: 15px 20px;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .navbar .logo h2 {
            margin: 0;
            font-size: 24px;
            color: white;
        }

        .navbar .menu {
            list-style: none;
            margin: 0;
            padding: 0;
            display: flex;
            gap: 20px;
        }

        .navbar .menu li {
            display: inline;
        }

        .navbar .menu a {
            color: white;
            text-decoration: none;
            font-size: 16px;
            font-weight: 500;
            transition: color 0.3s;
        }

        .navbar .menu a:hover {
            color: #c8e6c9;
        }

        /* Search and Dropdown Section */
        #search-dropdown {
            padding: 50px 20px;
            text-align: center;
            background-color: #fff;
            margin: 40px auto;
            border-radius: 10px;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.2);
            max-width: 800px;
        }

        #search-dropdown input, #search-dropdown select {
            padding: 12px;
            border-radius: 25px;
            border: 2px solid #004d40;
            font-size: 16px;
            margin-bottom: 20px;
            width: 70%;
            transition: transform 0.3s, border-color 0.3s;
        }

        #search-dropdown input:focus, #search-dropdown select:focus {
            border-color: #00796b;
            transform: scale(1.05);
        }

        /* Table Styling */
        .table-responsive {
            margin-top: 30px;
            overflow-x: auto;
            border-radius: 10px;
            padding: 20px;
            background-color: #fff;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
        }

        table {
            width: 100%;
            border-collapse: collapse;
            background-color: #fff;
        }

        table th, table td {
            padding: 15px;
            text-align: left;
            font-size: 16px;
        }

        table th {
            background-color: #004d40;
            color: white;
        }

        table tr:nth-child(even) {
            background-color: #f2f2f2;
        }

        table td a {
            color: #00796b;
            font-weight: bold;
            text-decoration: none;
        }

        table td a:hover {
            text-decoration: underline;
        }

        /* Buttons */
        .btn {
            background-color: #004d40;
            color: white;
            padding: 10px 20px;
            border: none;
            border-radius: 25px;
            font-size: 16px;
            cursor: pointer;
            transition: background-color 0.3s, transform 0.3s;
        }

        .btn:hover {
            background-color: #00796b;
            transform: translateY(-3px);
        }

        /* Responsive Design */
        @media (max-width: 768px) {
            .navbar .menu {
                flex-direction: column;
                align-items: center;
            }

            #search-dropdown input, #search-dropdown select {
                width: 100%;
            }

            table th, table td {
                font-size: 14px;
                padding: 10px;
            }
        }
    </style>
</head>

<body>

<nav class="navbar">
    <div class="menu_left">
        <div class="logo">
            <h2>Onice Tech</h2>
        </div>
        <div class="menu-li">
            <ul class="menu">
                <li><a href="index.php">Home</a></li>
                <li><a href="rank-details.php">Taxi Ranks</a></li>
                <li><a href="contact.php">Contact Us</a></li>
            </ul>
        </div>
    </div>
</nav>

<section id="search-dropdown" class="search-dropdown-section">
    <div class="container">
        <!-- Search and Dropdown Form -->
        <form id="filterForm" method="GET" class="mb-4">
            <!-- Search Box -->
            <input 
                type="text" 
                name="search_city" 
                placeholder="Search by city..." 
                value="<?= htmlspecialchars($searchCity); ?>" 
                class="form-control" 
                oninput="autoFilter()">

            <!-- Dropdown Menu -->
            <select name="dropdown_city" class="form-control" onchange="autoFilter()">
                <option value="all" <?= $selectedCity === "" ? "selected" : "" ?>>Select All Cities</option>
                <?php
                if ($citiesResult->num_rows > 0) {
                    while ($cityRow = $citiesResult->fetch_assoc()) {
                        $selected = ($cityRow['city'] === $selectedCity) ? "selected" : "";
                        echo "<option value='" . htmlspecialchars($cityRow['city']) . "' $selected>" . htmlspecialchars($cityRow['city']) . "</option>";
                    }
                }
                ?>
            </select>
        </form>

        <!-- Table displaying taxi ranks -->
        <div class="table-responsive mt-4">
            <table class="table table-bordered">
                <thead>
                    <tr>
                        <th>City</th>
                        <th>Number of Taxi Ranks</th>
                        <th>Operating Hours</th>
                        <th>Association</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    // Check if the query returned any rows
                    if ($result->num_rows > 0) {
                        while ($row = $result->fetch_assoc()) {
                            // Make city a clickable link
                            echo "<tr>";
                            echo "<td><a href='city-details.php?city=" . urlencode($row['city']) . "'>" . htmlspecialchars($row['city']) . "</a></td>";
                            echo "<td>" . htmlspecialchars($row['number_of_ranks']) . "</td>";
                            echo "<td>" . htmlspecialchars($row['operating_hours']) . "</td>";
                            echo "<td>" . htmlspecialchars($row['association']) . "</td>";
                            echo "</tr>";
                        }
                    } else {
                        echo "<tr><td colspan='4'>No data found</td></tr>";
                    }
                    ?>
                </tbody>
            </table>
        </div>
    </div>
</section>
<script>
        // Automatically submit the form on dropdown or search input change
        function autoFilter() {
            document.getElementById('filterForm').submit();
        }
    </script>

</body>
</html>
