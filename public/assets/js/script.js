 console.log("SCRIPT.JS CHARGÉ"); 
 
// Menu burger: ouverture/fermeture du menu mobile et bascule d'icône
document.addEventListener("DOMContentLoaded", () => {
    const menuToggle = document.getElementById("menu-toggle");
    const mobileMenu = document.getElementById("mobile-menu");

    if (menuToggle && mobileMenu) {
        const icon = menuToggle.querySelector('i');

        function setMobileMenuOpen(isOpen) {
            mobileMenu.classList.toggle("hidden", !isOpen);
            mobileMenu.style.display = isOpen ? "block" : "none";

            if (icon) {
                icon.classList.toggle('fa-bars', !isOpen);
                icon.classList.toggle('fa-xmark', isOpen);
            }
        }

        setMobileMenuOpen(false);

        menuToggle.addEventListener("click", (e) => {
            e.stopPropagation();
            const isOpen = mobileMenu.classList.contains("hidden");
            setMobileMenuOpen(isOpen);
        });

        // Fermer le menu si on clique ailleurs sur la page
        document.addEventListener("click", (e) => {
            if (!mobileMenu.contains(e.target) && !menuToggle.contains(e.target)) {
                setMobileMenuOpen(false);
            }
        });
    }
});

// Champs mot de passe: affiche/masque la valeur via l'icône oeil
document.addEventListener("DOMContentLoaded", () => {
    document.querySelectorAll("[data-password-toggle]").forEach((wrapper) => {
        const input = wrapper.querySelector("input");
        const button = wrapper.querySelector("[data-password-toggle-button]");
        const showIcon = wrapper.querySelector("[data-password-icon-show]");
        const hideIcon = wrapper.querySelector("[data-password-icon-hide]");

        if (!input || !button) return;

        const syncState = () => {
            const isHidden = input.type === "password";
            button.setAttribute("aria-label", isHidden ? "Afficher le mot de passe" : "Masquer le mot de passe");
            button.setAttribute("title", isHidden ? "Afficher le mot de passe" : "Masquer le mot de passe");

            if (showIcon) showIcon.classList.toggle("hidden", !isHidden);
            if (hideIcon) hideIcon.classList.toggle("hidden", isHidden);
        };

        button.addEventListener("click", () => {
            input.type = input.type === "password" ? "text" : "password";
            syncState();
        });

        syncState();
    });
});

// Newsletter: affiche une confirmation visuelle sans backend
document.addEventListener("DOMContentLoaded", () => {
    document.querySelectorAll("[data-newsletter-form]").forEach((form) => {
        if (form.dataset.newsletterBound === "true") return;

        const input = form.querySelector("[data-newsletter-input]");
        const successMessage = form.parentElement?.querySelector("[data-newsletter-success]");

        if (!input || !successMessage) return;

        form.addEventListener("submit", (event) => {
            event.preventDefault();

            if (!input.reportValidity()) return;

            form.reset();
            successMessage.classList.remove("hidden");
        });

        form.dataset.newsletterBound = "true";
    });
});

// Home: prépare les animations si des blocs déclarent data-reveal
document.addEventListener("DOMContentLoaded", () => {
    const revealTargets = document.querySelectorAll("[data-reveal]");
    if (!revealTargets.length) return;

    const observer = new IntersectionObserver((entries) => {
        entries.forEach((entry) => {
            if (!entry.isIntersecting) return;

            entry.target.classList.add("opacity-100", "translate-y-0");
            entry.target.classList.remove("opacity-0", "translate-y-10");
            observer.unobserve(entry.target);
        });
    });

    revealTargets.forEach((target) => observer.observe(target));
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
  const container = document.querySelector("[data-panier-root]");
  if (!container) {
    window.location.reload();
    return;
  }

  fetch(`/panier?t=${Date.now()}`, { headers: { "X-Requested-With": "XMLHttpRequest" } })
    .then(r => r.text())
    .then(html => {
      const doc = new DOMParser().parseFromString(html, "text/html");
      const newContent = doc.querySelector("[data-panier-root]");

      if (!newContent) {
        console.warn("refreshPanier: [data-panier-root] introuvable dans la réponse.");
        window.location.reload();
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
    const resetButtonState = () => {
        btn.innerHTML = originalText;
        btn.disabled = false;
        btn.classList.remove('opacity-70', 'cursor-not-allowed', 'bg-emerald-500', 'hover:bg-emerald-600', 'text-white', 'scale-105');
        btn.classList.add('bg-white', 'text-[--bluegray-dark]');
    };

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

        btn.classList.remove('bg-white', 'text-[--bluegray-dark]', 'opacity-70');
        btn.classList.add('bg-emerald-500', 'hover:bg-emerald-600', 'text-white', 'scale-105');
        btn.innerHTML = 'Livre ajouté ! <i class="fa-solid fa-check"></i>';
        setTimeout(() => {
            resetButtonState();
        }, 1200);
    })
    .catch(err => {
        console.error("Erreur addToCart :", err);
        resetButtonState();
    });
}

function updateCartBadge(count) {
    const badge = document.querySelector("[data-cart-count-badge]");
    if (!badge) return;

    badge.textContent = String(count);
    badge.classList.toggle("hidden", count <= 0);
    badge.classList.toggle("flex", count > 0);
}

window.addToCart = addToCart;
window.updateCartBadge = updateCartBadge;
window.updateQtt = updateQtt;
window.scrollLeft = scrollLeft;
window.scrollRight = scrollRight;

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
document.addEventListener('click', function (e) {

    const button = e.target.closest('[data-password-toggle-button]');
    if (!button) return;

    const container = button.closest('[data-password-toggle]');
    const input = container.querySelector('input');
    const iconShow = button.querySelector('[data-password-icon-show]');
    const iconHide = button.querySelector('[data-password-icon-hide]');

    if (!input) return;

    if (input.type === "password") {
        input.type = "text";
        iconShow.classList.add('hidden');
        iconHide.classList.remove('hidden');
    } else {
        input.type = "password";
        iconShow.classList.remove('hidden');
        iconHide.classList.add('hidden');
    }
});

 document.addEventListener('click', function (e) {

    const modal = document.getElementById('annulationModal');
    const confirmBtn = document.getElementById('confirmCancelBtn');

    if (e.target.closest('.open-modal-btn')) {
        const btn = e.target.closest('.open-modal-btn');
        const id = btn.dataset.id;

        console.log("OPEN MODAL ID:", id);

        confirmBtn.href = "/commande/annuler/" + id;

        modal.classList.remove('hidden');
    }

    if (e.target.closest('#closeModalBtn')) {
        modal.classList.add('hidden');
    }

    if (e.target === modal) {
        modal.classList.add('hidden');
    }

});

window.addToCart = addToCart;
window.updateQtt = updateQtt;
window.scrollLeft = scrollLeft;
window.scrollRight = scrollRight;
window.togglePassword = togglePassword;
