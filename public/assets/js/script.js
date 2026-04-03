 
// Menu burger: ouverture/fermeture du menu mobile et bascule d'icône
document.addEventListener("DOMContentLoaded", () => {
    const menuToggle = document.getElementById("menu-toggle");
    const mobileMenu = document.getElementById("mobile-menu");

    if (menuToggle && mobileMenu) {
        // Gère l'affichage du menu mobile + changement d'icône
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

        // Variante avec display forcé (fallback si besoin)
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

        // Fermer le menu si on clique ailleurs sur la page
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

// Flèches du catalogue: scroll horizontal dans les carrousels
    function scrollLeft(id) {
        document.getElementById(id).scrollBy({left: -300, behavior: 'smooth'});
    }
    function scrollRight(id) {
        document.getElementById(id).scrollBy({left: 300, behavior: 'smooth'});
    }



// Panier: rafraîchit le contenu du panier (utilisé après modif)
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


// Ajout au panier: appelle l'API et met à jour l'UI
function addToCart(id) {
    const btn = document.querySelector('#add-to-cart-btn');
    if (!btn) return;

    const originalText = btn.innerHTML;
    btn.disabled = true;
    btn.classList.add('opacity-70', 'cursor-not-allowed');
    btn.innerHTML = "Ajout en cours...";

    fetch(`/api/panier/add/${id}`, { 
        method: "POST",
        headers: { 'X-Requested-With': 'XMLHttpRequest' }
    })
    .then(r => {
        return r.json();
    })
    .then(data => {
        if (!data || !data.success) {
            throw new Error((data && data.message) || "Erreur lors de l'ajout au panier.");
        }

        updateCartBadge(data.count);

        btn.innerHTML = "Livre ajoute";
        setTimeout(() => {
            btn.innerHTML = originalText;
            btn.disabled = false;
            btn.classList.remove('opacity-70', 'cursor-not-allowed');
        }, 1200);
    })
    .catch(err => {
        console.error("Erreur addToCart :", err);
        btn.innerHTML = originalText;
        btn.disabled = false;
        btn.classList.remove('opacity-70', 'cursor-not-allowed');
    });
}

function updateCartBadge(count) {
    const badge = document.querySelector("[data-cart-count-badge]");
    if (!badge) return;

    badge.textContent = String(count);
    badge.classList.toggle("hidden", count <= 0);
    badge.classList.toggle("flex", count > 0);
}

// Modifie la quantité d'une ligne via l'API
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
            updateCartBadge(data.count);
            refreshPanier();
            
        }
    })
    .catch(err => console.error("Erreur updateQtt :", err));
}

// Suppression d'une ligne du panier: délégation de clic
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
        if (data.count !== undefined) {
          updateCartBadge(data.count);
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




// Onglets de l'espace personnel: navigation et affichage conditionnel
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
    buttons.forEach((btn) => {
      btn.classList.remove('bg-[--bluegray-dark]', 'text-white', 'shadow-lg', 'border-transparent');
      btn.classList.add('bg-white', 'text-[--bluegray-deep]', 'border', 'border-[--bluegray-medium]/20');
    });

    const activeButtons = document.querySelectorAll('[data-tab="' + name + '"]');
    activeButtons.forEach((activeBtn) => {
      activeBtn.classList.remove('bg-white', 'text-[--bluegray-deep]', 'border', 'border-[--bluegray-medium]/20');
      activeBtn.classList.add('bg-[--bluegray-dark]', 'text-white', 'shadow-lg', 'border-transparent');
    });
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

document.addEventListener('DOMContentLoaded', () => {
  const openButtons = document.querySelectorAll('[data-modal-open]');
  const closeButtons = document.querySelectorAll('[data-modal-close]');

  function toggleModal(modalId, shouldOpen) {
    const modal = document.getElementById(modalId);
    if (!modal) return;

    modal.classList.toggle('hidden', !shouldOpen);
    modal.classList.toggle('flex', shouldOpen);
    modal.setAttribute('aria-hidden', shouldOpen ? 'false' : 'true');

    if (shouldOpen) {
      document.body.classList.add('overflow-hidden');
    } else if (!document.querySelector('[data-modal].flex')) {
      document.body.classList.remove('overflow-hidden');
    }
  }

  openButtons.forEach((button) => {
    button.addEventListener('click', () => toggleModal(button.dataset.modalOpen, true));
  });

  closeButtons.forEach((button) => {
    button.addEventListener('click', () => toggleModal(button.dataset.modalClose, false));
  });

  document.querySelectorAll('[data-modal]').forEach((modal) => {
    modal.addEventListener('click', (event) => {
      if (event.target === modal) {
        toggleModal(modal.id, false);
      }
    });
  });

  document.addEventListener('keydown', (event) => {
    if (event.key !== 'Escape') return;

    document.querySelectorAll('[data-modal].flex').forEach((modal) => {
      toggleModal(modal.id, false);
    });
  });
});


// Gestion mot de passe: affiche/masque la saisie
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

window.addToCart = addToCart;
window.updateQtt = updateQtt;
window.scrollLeft = scrollLeft;
window.scrollRight = scrollRight;
window.togglePassword = togglePassword;
