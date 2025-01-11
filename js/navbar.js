
document.addEventListener("DOMContentLoaded", () => {
    const mobileMenu = document.querySelector('.menu-toggle'); 
    const navLinks = document.querySelector('.nav-links');

    if (mobileMenu && navLinks) {
        mobileMenu.addEventListener('click', () => {
            console.log("Toggle button clicked"); 

            navLinks.classList.toggle('show');
        });
    }
});

