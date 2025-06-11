function toggleDropdown() {
    const menu = document.getElementById('dropdownMenu');
    menu.style.display = menu.style.display === 'block' ? 'none' : 'block';
}

// Fermer le menu si l'utilisateur clique en dehors
window.onclick = function(event) {
    const menu = document.getElementById('dropdownMenu');
    if (!event.target.matches('.dropbtn')) {
        menu.style.display = 'none';
    }
};
