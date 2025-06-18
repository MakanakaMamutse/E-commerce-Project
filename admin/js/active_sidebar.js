// Wait for the entire page (DOM) to load before running this script
document.addEventListener("DOMContentLoaded", function () {

    // Get the name of the current file (e.g., 'products.php')
    const currentPage = window.location.pathname.split("/").pop();

    // Loop through all sidebar links and remove the 'active' class from each one
    // This clears any previous highlight so we can apply it fresh
    document.querySelectorAll(".sidebar .nav-link").forEach(link => {
        link.classList.remove("active");
    });

    // Find the sidebar link that matches the current page we're on
    const currentLink = document.querySelector(`.sidebar .nav-link[href="${currentPage}"]`);

    // If we found a match, add the 'active' class to highlight it in the sidebar
    if (currentLink) {
        currentLink.classList.add("active");
    }
});
