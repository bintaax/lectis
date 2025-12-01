 

// Menu burger

 const menuButton = document.querySelector("button");
  const mobileMenu = document.getElementById("mobile-menu");

  menuButton.addEventListener("click", () => {
    mobileMenu.classList.toggle("hidden");
    
  });

// Flèches du catalogue
    function scrollLeft(id) {
        document.getElementById(id).scrollBy({left: -300, behavior: 'smooth'});
    }
    function scrollRight(id) {
        document.getElementById(id).scrollBy({left: 300, behavior: 'smooth'});
    }



// Panier 
// --- PANIER ---

function refreshPanier() {
    const container = document.querySelector("#panier-container");
    if (!container) return; // Évite les erreurs sur les pages sans panier

    fetch('/panier')
        .then(r => r.text())
        .then(html => {
            const parser = new DOMParser();
            const doc = parser.parseFromString(html, "text/html");

            const newContent = doc.querySelector("#panier-container");
            if (newContent) {
                container.innerHTML = newContent.innerHTML;
            }
        })
        .catch(err => console.error("Erreur refreshPanier :", err));
}

function addToCart(id) {
    fetch(`/api/panier/add/${id}`, { method: "POST" })
        .then(r => r.json())
        .then(data => {

            // 1️⃣ Mise à jour badge header
            const badge = document.querySelector("#panier-count");
            if (badge && data.count !== undefined) {
                badge.textContent = data.count;
            }

            // 2️⃣ Redirection vers panier
            window.location.href = "/panier";
        })
        .catch(err => {
            console.error("Erreur addToCart :", err);
            alert("Impossible d’ajouter au panier.");
        });
}

function updateQtt(ligneId, quantite) {
    if (quantite <= 0) return deleteLine(ligneId);

    fetch(`/api/panier/update/${ligneId}`, {
        method: "POST",
        headers: { "Content-Type": "application/json" },
        body: JSON.stringify({ quantite })
    })
        .then(r => r.json())
        .then(() => refreshPanier())
        .catch(err => console.error("Erreur updateQtt :", err));
}

function deleteLine(ligneId) {
    fetch(`/api/panier/delete/${ligneId}`, { method: "POST" })
        .then(r => r.json())
        .then(() => refreshPanier())
        .catch(err => console.error("Erreur deleteLine :", err));
}

