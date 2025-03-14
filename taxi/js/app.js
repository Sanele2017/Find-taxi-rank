let currentIndex = 0;
const images = document.querySelectorAll('.card img');
const prevButton = document.querySelector('.prev');
const nextButton = document.querySelector('.next');

function showImage(index) {
    images.forEach((img, i) => {
        img.classList.remove('active');
        img.classList.add('inactive');
    });

    images[index].classList.add('active');
    images[index].classList.remove('inactive');
}

function nextImage() {
    currentIndex = (currentIndex + 1) % images.length;
    showImage(currentIndex);
}

function prevImage() {
    currentIndex = (currentIndex - 1 + images.length) % images.length;
    showImage(currentIndex);
}

prevButton.addEventListener('click', prevImage);
nextButton.addEventListener('click', nextImage);

// Automatically cycle through images every 3 seconds
setInterval(nextImage, 3000);

function filterTable() {
    const searchInput = document.getElementById("searchInput").value.toLowerCase();
    const citySelect = document.getElementById("citySelect").value.toLowerCase();
    const table = document.getElementById("taxiTable");
    const rows = table.getElementsByTagName("tr");

    for (let i = 1; i < rows.length; i++) {
        const cityCell = rows[i].getElementsByTagName("td")[0]; // City column
        const cityText = cityCell ? cityCell.textContent.toLowerCase() : "";
        const isSearchMatch = cityText.includes(searchInput);
        const isCityMatch = citySelect === "" || cityText === citySelect;

        rows[i].style.display = isSearchMatch && isCityMatch ? "" : "none";
    }
}
