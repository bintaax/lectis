 
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






