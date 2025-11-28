 
// Menu burger

 const menuButton = document.querySelector("button");
  const mobileMenu = document.getElementById("mobile-menu");

  menuButton.addEventListener("click", () => {
    mobileMenu.classList.toggle("hidden");
    
  });

  // Si l'utilisateur n'a pas 16 ou 18 ans
    const ageWarning = document.getElementById("age-warning");                          
    const ageRequired18 = 18; 
    const ageRequired16 = 16;

    if (ageWarning) {
      ageWarning.textContent = `Ce livre est destiné à un public adulte et averti ${ageRequired18} ans et plus.`;
    }

// Scroll du catalogue
    function scrollLeft(id) {
        document.getElementById(id).scrollBy({left: -300, behavior: 'smooth'});
    }
    function scrollRight(id) {
        document.getElementById(id).scrollBy({left: 300, behavior: 'smooth'});
    }



document.addEventListener('DOMContentLoaded', () => {

    // --- AJOUT AU PANIER ---
    document.querySelectorAll('.add-to-cart').forEach(button => {
        button.addEventListener('click', async () => {
            const id = button.dataset.id;

            const response = await fetch(`/panier/add/${id}`, {
                method: 'POST'
            });

            const data = await response.json();

            if (data.success) {
                document.getElementById('panier-count').textContent = data.total;
            }
        });
    });

    // --- SUPPRESSION DU PANIER ---
    document.querySelectorAll('.remove-from-cart').forEach(button => {
        button.addEventListener('click', async () => {
            const id = button.dataset.id;

            const response = await fetch(`/panier/remove/${id}`, {
                method: 'POST'
            });

            const data = await response.json();

            if (data.success) {
                document.getElementById('panier-count').textContent = data.total;
            }
        });
    });

});




