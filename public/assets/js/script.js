 
// Menu burger

document.addEventListener("DOMContentLoaded", () => {
    const menuToggle = document.getElementById("menu-toggle");
    const mobileMenu = document.getElementById("mobile-menu");

    if (!menuToggle) {
        console.error("menu-toggle introuvable");
        return;
    }

    if (!mobileMenu) {
        console.error("mobile-menu introuvable");
        return;
    }

    menuToggle.addEventListener("click", () => {
        mobileMenu.classList.toggle("hidden");
    });
});

// Flèches du catalogue
    function scrollLeft(id) {
        document.getElementById(id).scrollBy({left: -300, behavior: 'smooth'});
    }
    function scrollRight(id) {
        document.getElementById(id).scrollBy({left: 300, behavior: 'smooth'});
    }



// Panier 
/**
 * Met à jour le contenu du panier sans recharger la page
 */
function refreshPanier() {
    const container = document.querySelector("#panier-container");
    if (!container) return;

    fetch('/panier')
        .then(r => r.text())
        .then(html => {
            const parser = new DOMParser();
            const doc = parser.parseFromString(html, "text/html");
            const newContent = doc.querySelector("#panier-container");
            
            if (newContent) {
                container.innerHTML = newContent.innerHTML;
                // Optionnel : mettre à jour le total si tu as un élément dédié hors du container
            }
        })
        .catch(err => console.error("Erreur refreshPanier :", err));
}

/**
 * Ajoute un livre au panier via l'API
 */
function addToCart(id) {
    fetch(`/api/panier/add/${id}`, { 
        method: "POST",
        headers: { 'X-Requested-With': 'XMLHttpRequest' }
    })
    .then(r => {
        if (r.status === 401) {
            window.location.href = "/login"; // Redirection si session expirée
            return;
        }
        return r.json();
    })
    .then(data => {
        if (data.success) {
            // 1️⃣ Mise à jour du badge dans le header
            const badge = document.querySelector("#panier-count");
            if (badge) {
                badge.textContent = data.count;
                badge.classList.remove('hidden'); // Au cas où il était caché
            }

            // 2️⃣ Feedback visuel sur le bouton (si on est sur la fiche produit)
            const btn = document.querySelector('#add-to-cart-btn');
            if (btn) {
                const originalText = btn.innerHTML;
                btn.innerHTML = "Ajouté ! ✓";
                btn.classList.replace('bg-white', 'bg-green-500');
                btn.classList.add('text-white');
                
                setTimeout(() => {
                    btn.innerHTML = originalText;
                    btn.classList.replace('bg-green-500', 'bg-white');
                    btn.classList.remove('text-white');
                }, 2000);
            }

            // 3️⃣ Redirection (optionnelle)
            // window.location.href = "/panier"; 
        }
    })
    .catch(err => {
        console.error("Erreur addToCart :", err);
        alert("Impossible d’ajouter au panier.");
    });
}

/**
 * Modifie la quantité d'une ligne
 */
function updateQtt(ligneId, quantite) {
    if (quantite <= 0) return deleteLine(ligneId);

    fetch(`/api/panier/update/${ligneId}`, {
        method: "POST",
        headers: { "Content-Type": "application/json" },
        body: JSON.stringify({ quantite })
    })
    .then(r => r.json())
    .then(data => {
        if (data.success) {
            refreshPanier();
            // On peut aussi mettre à jour le badge ici si ton API renvoie le nouveau count
        }
    })
    .catch(err => console.error("Erreur updateQtt :", err));
}

/**
 * Supprime une ligne du panier
 */
function deleteLine(ligneId) {
    if (!confirm("Supprimer cet article ?")) return;

    fetch(`/api/panier/delete/${ligneId}`, { method: "POST" })
    .then(r => r.json())
    .then(data => {
        if (data.success) {
            refreshPanier();
        }
    })
    .catch(err => console.error("Erreur deleteLine :", err));
}


// Onglets de l'espace personnel

document.addEventListener('DOMContentLoaded', () => {
    const buttons = document.querySelectorAll('.tab-button');
    const panels = document.querySelectorAll('.tab-panel');

    function showTab(name) {
        panels.forEach(panel => panel.classList.add('hidden'));
        document.getElementById('tab-' + name).classList.remove('hidden');

        buttons.forEach(btn => btn.classList.remove('bg-white', 'border', 'border-b-0'));
        const activeBtn = document.querySelector('[data-tab="' + name + '"]');
        activeBtn.classList.add('bg-white', 'border', 'border-b-0');
    }

    showTab('profil');
    buttons.forEach(btn =>
        btn.addEventListener('click', () => showTab(btn.getAttribute('data-tab')))
    );
});

// GESTION MOT DE PASSE (Version améliorée)
function togglePassword(button) {
    // On cherche l'input qui se trouve dans le même bloc que le bouton
    const container = button.parentElement;
    const input = container.querySelector('input');
    const icon = button.querySelector('i');

    if (input.type === "password") {
        input.type = "text";
        icon.classList.remove('fa-eye');
        icon.classList.add('fa-eye-slash');
    } else {
        input.type = "password";
        icon.classList.remove('fa-eye-slash');
        icon.classList.add('fa-eye');
    }
}