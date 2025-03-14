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

// Retrieve the selected city from the query parameter
if (isset($_GET['city']) && !empty($_GET['city'])) {
    $city = $conn->real_escape_string($_GET['city']);
} else {
    // Redirect to the main page if no city is selected
    header("Location: index.php");
    exit();
}

// Query to fetch ranks and their routes under the selected city
$sql = "
    SELECT 
        ranks.rank_name, 
        routes.route_name, 
        routes.fare 
    FROM 
        ranks 
    INNER JOIN 
        routes 
    ON 
        ranks.ranks_id = routes.ranks_id 
    WHERE 
        ranks.city = '$city'
    ORDER BY 
        ranks.rank_name, routes.route_name";
$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Routes and Fares for <?= htmlspecialchars($city); ?></title>
    <link rel="stylesheet" href="css/styles.css">
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;700&display=swap" rel="stylesheet">
</head>
<style>
    body {
    font-family: 'Roboto', sans-serif;
    line-height: 1.6;
    color: #333333;
}

/* City Details Section */
#city-details {
    padding: 60px 20px;
    text-align: center;
    background-color: #ffffff;
    border-radius: 15px;
    box-shadow: 0 8px 20px rgba(0, 0, 0, 0.1);
    margin-top: 40px;
}

#city-details h1 {
    font-size: 36px;
    color: #004080; /* Professional dark blue */
    margin-bottom: 20px;
    font-weight: 700;
    letter-spacing: 1px;
}

#city-details h1 span {
    color: #008080; /* Teal for accent */
}

.table-responsive {
    margin-top: 30px;
    overflow-x: auto;
    padding: 20px;
    border-radius: 10px;
    background-color: #f7f9fc; /* Light grey-blue */
}

table {
    width: 100%;
    border-collapse: collapse;
    border-radius: 8px;
    overflow: hidden;
    box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
    background-color: #ffffff;
}

table th, table td {
    padding: 15px 20px;
    text-align: left;
    font-size: 16px;
    border-bottom: 1px solid #e6e6e6; /* Subtle border */
}

table th {
    background-color: #004080; /* Professional dark blue */
    color: white;
    text-transform: uppercase;
    letter-spacing: 0.8px;
}

table td {
    color: #333333; /* Neutral text color */
    background-color: #f9f9f9; /* Subtle contrast */
    border-radius: 8px;
    transition: background-color 0.3s ease;
}

table tr:hover td {
    background-color: #eaf3fc; /* Light blue hover effect */
}

table td strong {
    font-weight: 700;
    color: #004080;
}

/* Button Styles */
.btn {
    display: inline-block;
    padding: 12px 28px;
    margin-top: 30px;
    background-color: #004080; /* Professional dark blue */
    color: white;
    font-size: 18px;
    text-decoration: none;
    border-radius: 50px;
    transition: background-color 0.3s ease, transform 0.3s ease;
}

.btn:hover {
    background-color: #00509e; /* Slightly brighter blue */
    transform: translateY(-3px);
}

/* Responsive Styles */
@media (max-width: 768px) {
    #city-details h1 {
        font-size: 28px;
    }

    table th, table td {
        font-size: 14px;
        padding: 12px 15px;
    }

    .btn {
        width: 100%;
        padding: 15px;
    }
}

</style>
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

<section id="city-details" class="city-details-section">
    <div class="container">
        <h1>Taxi Ranks in <?= htmlspecialchars($city); ?></h1>

        <!-- Table to display ranks, routes, and fares -->
        <div class="table-responsive mt-4">
            <table class="table table-bordered">
                <thead>
                    <tr>
                        <th>Rank Name</th>
                        <th>Destination</th>
                        <th>Fare</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    // Check if the query returned any rows
                    if ($result->num_rows > 0) {
                        $currentRank = "";
                        while ($row = $result->fetch_assoc()) {
                            // If the rank changes, display it in a new row
                            if ($currentRank !== $row['rank_name']) {
                                $currentRank = $row['rank_name'];
                                echo "<tr>";
                                echo "<td><strong>" . htmlspecialchars($currentRank) . "</strong></td>";
                                echo "<td>" . htmlspecialchars($row['route_name']) . "</td>";
                                echo "<td>R" . htmlspecialchars($row['fare']) . "</td>";
                                echo "</tr>";
                            } else {
                                // Otherwise, leave the rank cell empty to group rows
                                echo "<tr>";
                                echo "<td></td>";
                                echo "<td>" . htmlspecialchars($row['route_name']) . "</td>";
                                echo "<td>R" . htmlspecialchars($row['fare']) . "</td>";
                                echo "</tr>";
                            }
                        }
                    } else {
                        echo "<tr><td colspan='3'>No routes or fares found for this city.</td></tr>";
                    }
                    ?>
                </tbody>
            </table>
        </div>

        <a href="rank-details.php" class="btn btn-primary">Back to Home</a>
    </div>
</section>

</body>
</html>

<?php
// Close the database connection
$conn->close();
?>
