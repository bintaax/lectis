 
// Menu burger
document.addEventListener("DOMContentLoaded", () => {
    const menuToggle = document.getElementById("menu-toggle");
    const mobileMenu = document.getElementById("mobile-menu");

    if (menuToggle && mobileMenu) {
        menuToggle.addEventListener("click", (e) => {
            e.stopPropagation(); // Empêche la fermeture immédiate
            mobileMenu.classList.toggle("hidden");
            
            // Bonus : Change l'icône de burger à "X" si tu veux
            const icon = menuToggle.querySelector('i');
            if (icon) {
                icon.classList.toggle('fa-bars');
                icon.classList.toggle('fa-xmark');
            }
        });

        menuToggle.addEventListener("click", (e) => {
    e.stopPropagation();
    
    if (mobileMenu.classList.contains('hidden')) {
        mobileMenu.classList.remove('hidden');
        mobileMenu.style.display = 'block'; // On force l'affichage
    } else {
        mobileMenu.classList.add('hidden');
        mobileMenu.style.display = 'none'; // On force la disparition
    }

    const icon = menuToggle.querySelector('i');
    if (icon) {
        icon.classList.toggle('fa-bars');
        icon.classList.toggle('fa-xmark');
    }
});

        // Fermer le menu si on clique n'importe où ailleurs sur la page
        document.addEventListener("click", (e) => {
            if (!mobileMenu.contains(e.target) && !menuToggle.contains(e.target)) {
                mobileMenu.classList.add("hidden");
                const icon = menuToggle.querySelector('i');
                if (icon) {
                    icon.classList.add('fa-bars');
                    icon.classList.remove('fa-xmark');
                }
            }
        });
    }
});

// Flèches du catalogue
    function scrollLeft(id) {
        document.getElementById(id).scrollBy({left: -300, behavior: 'smooth'});
    }
    function scrollRight(id) {
        document.getElementById(id).scrollBy({left: 300, behavior: 'smooth'});
    }



// Panier 
// Rafraîchit le contenu du panier (utilisé après modif)
function refreshPanier() {
  const container = document.querySelector("#panier-container");
  if (!container) return;

  fetch(`/panier?t=${Date.now()}`, { headers: { "X-Requested-With": "XMLHttpRequest" } })
    .then(r => r.text())
    .then(html => {
      const doc = new DOMParser().parseFromString(html, "text/html");
      const newContent = doc.querySelector("#panier-container");

      if (!newContent) {
        console.warn("refreshPanier: #panier-container introuvable dans la réponse.");
        return;
      }

      container.replaceWith(newContent);
    })
    .catch(err => console.error("Erreur refreshPanier :", err));
}


// Ajout au panier
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

// Suppression d'une ligne du panier
document.addEventListener("click", (e) => {
  const btn = e.target.closest(".btn-delete-line");
  if (!btn) return;

  const ligneId = btn.dataset.ligneId;
  if (!ligneId) return;

  // Optionnel: feedback visuel
  btn.disabled = true;
  btn.classList.add("opacity-50", "cursor-not-allowed");

  fetch(`/api/panier/delete/${ligneId}`, {
    method: "POST",
    headers: { "X-Requested-With": "XMLHttpRequest" }
  })
    .then(r => r.json())
    .then(data => {
      if (data.success) {
        // badge header si ton API renvoie count
        if (data.count !== undefined) {
          const badge = document.querySelector("#panier-count");
          if (badge) {
            badge.textContent = data.count;
            badge.classList.toggle("hidden", data.count <= 0);
          }
        }

        refreshPanier();
        return;
      }

      // si success=false
      btn.disabled = false;
      btn.classList.remove("opacity-50", "cursor-not-allowed");
      refreshPanier();
    })
    .catch(err => {
      console.error("Erreur deleteLine :", err);
      btn.disabled = false;
      btn.classList.remove("opacity-50", "cursor-not-allowed");
    });
});


// Onglets de l'espace personnel

// Onglets de l'espace personnel (SAFE)
document.addEventListener('DOMContentLoaded', () => {
  const buttons = document.querySelectorAll('.tab-button');
  const panels = document.querySelectorAll('.tab-panel');

  // ✅ Si la page n'a pas d'onglets, on ne fait rien
  if (!buttons.length || !panels.length) return;

  function showTab(name) {
    // cacher tous les panels
    panels.forEach(panel => panel.classList.add('hidden'));

    // ✅ panel cible
    const targetPanel = document.getElementById('tab-' + name);
    if (!targetPanel) return; // évite crash si tab inexistante
    targetPanel.classList.remove('hidden');

    // gérer boutons
    buttons.forEach(btn => btn.classList.remove('bg-white', 'border', 'border-b-0'));
    const activeBtn = document.querySelector('[data-tab="' + name + '"]');
    if (activeBtn) {
      activeBtn.classList.add('bg-white', 'border', 'border-b-0');
    }
  }

  // ✅ Choix d'onglet par défaut : le 1er bouton si "profil" n'existe pas
  const defaultName = document.getElementById('tab-profil')
    ? 'profil'
    : (buttons[0].getAttribute('data-tab') || 'profil');

  showTab(defaultName);

  buttons.forEach(btn => {
    btn.addEventListener('click', () => showTab(btn.getAttribute('data-tab')));
  });
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