// Dynamically add rows for route and fare
let rowCount = 0;
document.getElementById('addRowBtn').addEventListener('click', function () {
    rowCount++;
    const routesList = document.getElementById('routesList');

    // Create a new row
    const newRow = document.createElement('div');
    newRow.classList.add('row', 'mb-3');
    newRow.innerHTML = `
        <div class="col-md-5">
            <input type="text" class="form-control" name="route_${rowCount}" placeholder="Enter route (e.g., A to B)" required>
        </div>
        <div class="col-md-5">
            <input type="number" class="form-control" name="fare_${rowCount}" placeholder="Enter fare (e.g., 20)" required>
        </div>
        <div class="col-md-2">
            <button type="button" class="btn btn-danger removeRowBtn">Remove</button>
        </div>
    `;
    routesList.appendChild(newRow);
});

// Event delegation for removing rows
document.getElementById('routesList').addEventListener('click', function (e) {
    if (e.target.classList.contains('removeRowBtn')) {
        e.target.closest('.row').remove();
    }
});

// Fetch ranks from the database and populate the dropdown
document.addEventListener('DOMContentLoaded', function () {
    fetch('route.php') // Backend PHP script to fetch ranks
        .then(response => response.json())
        .then(data => {
            const rankSelect = document.getElementById('rankSelect');
            data.forEach(rank => {
                const option = document.createElement('option');
                option.value = rank.id;
                option.textContent = rank.name;
                rankSelect.appendChild(option);
            });
        })
        .catch(error => console.error('Error fetching ranks:', error));
});

// Handle form submission
document.getElementById('addRouteForm').addEventListener('submit', function (e) {
    e.preventDefault();

    // Collect form data
    const formData = new FormData(this);

    // Send data to the backend using AJAX
    fetch('route.php', {
        method: 'POST',
        body: formData,
    })
        .then(response => response.json())
        .then(data => {
            if (data.status === 'success') {
                alert('Routes and fares saved successfully!');
                clearForm(); // Clear the form on success
            } else {
                alert('Error: ' + data.message);
            }
        })
        .catch(error => {
            console.error('Error saving routes:', error);
            alert('An error occurred while saving the data.');
        });
});

// Function to clear the form
function clearForm() {
    document.getElementById('rankSelect').selectedIndex = 0; // Reset rank dropdown
    document.getElementById('routesList').innerHTML = ''; // Clear dynamically added rows
    document.getElementById('addRouteForm').reset(); // Reset other form inputs
}
