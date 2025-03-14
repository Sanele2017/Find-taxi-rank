<?php
// Database credentials
$host = 'localhost';
$dbname = 'taxi-rank';
$username = 'root';
$password = '';

try {
    // Create a PDO connection to the database
    $pdo = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Query to fetch all ranks
    $query = "SELECT ranks_id, city, rank_name, location, association, operating_hours FROM ranks";
    $stmt = $pdo->query($query);

    // Fetch all rows as an associative array
    $ranks = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    echo 'Database connection failed: ' . $e->getMessage();
    exit;
}
// Fetch distinct cities from the database
try {
    $cityQuery = "SELECT DISTINCT city FROM ranks";
    $cityStmt = $pdo->query($cityQuery);
    $cities = $cityStmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    echo 'Database query failed: ' . $e->getMessage();
}

// Update handling (this block can be handled in the same script if needed)
// Ensure the 'POST' request is being handled correctly
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $id = $_POST['ranks_id'];
    $city = $_POST['city'];
    $rank_name = $_POST['rank_name'];
    $location = $_POST['location'];
    $association = $_POST['association'];
    $operating_hours = $_POST['operating_hours'];

    $sql = "UPDATE ranks SET city = ?, rank_name = ?, location = ?, association = ?, operating_hours = ? WHERE ranks_id = ?";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([$city, $rank_name, $location, $association, $operating_hours, $id]);

    echo json_encode([
        'success' => true,
        'ranks_id' => $id,
        'city' => $city,
        'rank_name' => $rank_name,
        'location' => $location,
        'association' => $association,
        'operating_hours' => $operating_hours,
    ]);
    exit;
}

if (isset($_GET['ranks_id'])) {
    $id = $_GET['ranks_id'];

    $sql = "DELETE FROM ranks WHERE ranks_id = ?";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([$id]);

    echo json_encode(['success' => true]);
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard - View Ranks</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="admin-dashboard.css">
    <style>
        /* Header */
        h1 {
            font-size: 2.5rem;
            font-weight: 600;
            color: #4b2d94;
            margin-bottom: 20px;
        }

        /* Dropdown Styling */
        .form-select {
            border-radius: 30px;
            padding: 12px 20px;
            background-color: #ffffff;
            border: 2px solid #dedede;
            font-size: 1rem;
            transition: all 0.3s ease;
        }

        .form-select:focus {
            border-color: #4b2d94;
            box-shadow: 0 0 5px rgba(75, 45, 148, 0.5);
        }

        .form-label {
            font-weight: 500;
            color: #4b2d94;
            font-size: 1.2rem;
        }

        /* Table Styling */
        table {
            width: 100%;
            background-color: #fff;
            border-radius: 8px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            margin-top: 20px;
            overflow: hidden;
        }

        thead {
            background-color: #4b2d94;
            color: #fff;
            text-align: center;
            font-size: 1.1rem;
        }

        th, td {
            padding: 12px 20px;
            text-align: center;
        }

        tbody tr {
            border-bottom: 1px solid #f1f1f1;
        }

        tbody tr:hover {
            background-color: #f0f0f0;
            cursor: pointer;
        }

        /* Button Styling */
        .btn {
            border-radius: 30px;
            font-weight: 500;
            padding: 10px 20px;
            background-color: #4b2d94;
            color: white;
            transition: background-color 0.3s ease;
        }

        .btn:hover {
            background-color: #7c4dff;
        }

        .action-buttons {
            display: flex;
            justify-content: center;
            gap: 10px;
        }

        .btn-edit, .btn-delete {
            padding: 8px 16px;
            font-size: 1rem;
        }

        .btn-edit {
            background-color: #ff9900;
        }

        .btn-delete {
            background-color: #ff3333;
        }

        /* Modern and Cute Colors */
        .btn, .form-select {
            border-color: #4b2d94;
        }

        .form-select:focus {
            border-color: #7c4dff;
        }
         /* Modal for editing ranks */
         .modal-content {
            padding: 20px;
        }
    </style>
</head>
<body>
    <div class="container-fluid">
        <div class="row">
            <!-- Sidebar -->
            <nav class="col-md-3 col-lg-2 d-md-block sidebar">
                <h3 class="mb-4">Admin Panel</h3>
                <a href="admin-dashboard.php" class="active">Dashboard</a>
                <a href="#" data-bs-toggle="collapse" data-bs-target="#manageRanksSubmenu" aria-expanded="false" aria-controls="manageRanksSubmenu">Manage Ranks</a>
                <div class="collapse" id="manageRanksSubmenu">
                    <div class="submenu">
                        <a href="view-rank.php">View Ranks</a>
                        <a href="add-ranks.php">Add Rank</a>
                        <a href="route.php">Add Routes</a>
                    </div>
                </div>
                <a href="settings.php">Settings</a>
                <a href="login.php">Logout</a>
            </nav>

            <!-- Main Content -->
<main class="col-md-9 col-lg-10 main-content">
    <h1 class="mb-4">View Ranks</h1>

    <!-- City Dropdown -->
<div class="mb-4">
    <label for="cityFilter" class="form-label">Filter by City:</label>
    <select id="cityFilter" class="form-select" onchange="filterRanksByCity()">
        <option value="all">All Cities</option>
        <?php foreach ($cities as $city): ?>
            <option value="<?php echo htmlspecialchars($city['city']); ?>">
                <?php echo htmlspecialchars($city['city']); ?>
            </option>
        <?php endforeach; ?>
    </select>
</div>


    <div class="row mt-4">
        <div class="col-md-12">
            <!-- Ranks Table -->
            <table class="table table-bordered">
                <thead>
                    <tr>
                        <th>City</th>
                        <th>Rank Name</th>
                        <th>Location</th>
                        <th>Association</th>
                        <th>Operating Hours</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody id="ranksTableBody">
                    <!-- Table rows will be dynamically inserted here -->
                    <?php foreach ($ranks as $rank): ?>
                    <tr data-id="<?php echo $rank['ranks_id']; ?>">
                        <td class="cell-city"><?php echo htmlspecialchars($rank['city']); ?></td>
                        <td class="cell-rank-name"><?php echo htmlspecialchars($rank['rank_name']); ?></td>
                        <td class="cell-location"><?php echo htmlspecialchars($rank['location']); ?></td>
                        <td class="cell-association"><?php echo htmlspecialchars($rank['association']); ?></td>
                        <td class="cell-operating-hours"><?php echo htmlspecialchars($rank['operating_hours']); ?></td>
                        <td>
                            <button class="btn btn-edit" onclick="openEditModal(<?php echo $rank['ranks_id']; ?>)">Edit</button>
                            <button class="btn btn-delete" onclick="deleteRank(<?php echo $rank['ranks_id']; ?>)">Delete</button>
                        </td>
                    </tr>
                <?php endforeach; ?>

                </tbody>
            </table>
        </div>
    </div>
</main>
        </div>
    </div>

     <!-- Edit Rank Modal -->
<div class="modal fade" id="editRankModal" tabindex="-1" aria-labelledby="editRankModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="editRankModalLabel">Edit Rank</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="editRankForm" method="POST" action="">
                <input type="hidden" id="editRankId" name="ranks_id">
                <div class="mb-3">
                    <label for="editCity" class="form-label">City</label>
                    <input type="text" class="form-control" id="editCity" name="city" required>
                </div>
                <div class="mb-3">
                    <label for="editRankName" class="form-label">Rank Name</label>
                    <input type="text" class="form-control" id="editRankName" name="rank_name" required>
                </div>
                <div class="mb-3">
                    <label for="editLocation" class="form-label">Location</label>
                    <input type="text" class="form-control" id="editLocation" name="location" required>
                </div>
                <div class="mb-3">
                    <label for="editAssociation" class="form-label">Association</label>
                    <input type="text" class="form-control" id="editAssociation" name="association" required>
                </div>
                <div class="mb-3">
                    <label for="editOperatingHours" class="form-label">Operating Hours</label>
                    <input type="text" class="form-control" id="editOperatingHours" name="operating_hours" required>
                </div>
                <button type="submit" class="btn btn-primary">Save Changes</button>
            </form>
            </div>
        </div>
    </div>
</div>

<!-- Add necessary JS libraries before closing </body> -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/js/bootstrap.bundle.min.js"></script>
<script>
    // Open Edit Modal and Prepopulate Data
    function openEditModal(id) {
        // Fetch the row data using the ID
        const row = document.querySelector(`tr[data-id='${id}']`);
        const city = row.querySelector('.cell-city').innerText;
        const rankName = row.querySelector('.cell-rank-name').innerText;
        const location = row.querySelector('.cell-location').innerText;
        const association = row.querySelector('.cell-association').innerText;
        const operatingHours = row.querySelector('.cell-operating-hours').innerText;

        // Populate the modal form
        document.getElementById('editCity').value = city;
        document.getElementById('editRankName').value = rankName;
        document.getElementById('editLocation').value = location;
        document.getElementById('editAssociation').value = association;
        document.getElementById('editOperatingHours').value = operatingHours;
        document.getElementById('editRankId').value = id;

        // Show the modal
        const editModal = new bootstrap.Modal(document.getElementById('editRankModal'));
        editModal.show();
    }

    function filterRanksByCity() {
    const selectedCity = document.getElementById('cityFilter').value.toLowerCase();
    const rows = document.querySelectorAll('#ranksTableBody tr');

    rows.forEach(row => {
        const city = row.querySelector('.cell-city').innerText.toLowerCase();
        row.style.display = selectedCity === 'all' || city === selectedCity ? '' : 'none';
    });
}

    //edit form
    document.getElementById('editRankForm').addEventListener('submit', function (e) {
    e.preventDefault(); // Prevent default form submission

    const formData = new FormData(this);

    fetch('', {
        method: 'POST',
        body: formData,
    })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                // Update the table row without refreshing
                const row = document.querySelector(`tr[data-id='${data.ranks_id}']`);
                row.querySelector('.cell-city').innerText = data.city;
                row.querySelector('.cell-rank-name').innerText = data.rank_name;
                row.querySelector('.cell-location').innerText = data.location;
                row.querySelector('.cell-association').innerText = data.association;
                row.querySelector('.cell-operating-hours').innerText = data.operating_hours;

                alert('Rank updated successfully!');
                const editModal = bootstrap.Modal.getInstance(document.getElementById('editRankModal'));
                editModal.hide();
            } else {
                alert('Failed to update rank. Please try again.');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('An error occurred. Please try again.');
        });
});


    // Delete Rank
    function deleteRank(id) {
    if (confirm('Are you sure you want to delete this rank?')) {
        fetch(`?ranks_id=${id}`, {
            method: 'GET',
        })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    // Remove the row from the table
                    const row = document.querySelector(`tr[data-id='${id}']`);
                    row.remove();

                    alert('Rank deleted successfully!');
                } else {
                    alert('Failed to delete rank. Please try again.');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('An error occurred. Please try again.');
            });
    }
}

</script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
