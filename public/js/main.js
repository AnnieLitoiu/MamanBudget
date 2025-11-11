// public/js/main.js
document.addEventListener("DOMContentLoaded", function () {
  console.log("[MamanSolo] JS global chargé ✅");

  // ==========================
  // 1. Cartes cliquables (avatar, logement, nb enfants)
  // ==========================
  const cards = document.querySelectorAll(".choice-card");

  cards.forEach((card) => {
    card.addEventListener("click", () => {
      const input = card.querySelector("input");
      if (!input) return;

      // nom du groupe (ex: situation_form[logement])
      const groupName = input.name;

      // on enlève l'état actif des autres cartes du même groupe
      document
        .querySelectorAll('.choice-card input[name="' + groupName + '"]')
        .forEach((i) => {
          const parent = i.closest(".choice-card");
          if (parent) parent.classList.remove("is-active");
        });

      // on coche le bon input
      input.checked = true;
      // on ajoute l'état actif à la carte cliquée
      card.classList.add("is-active");
    });
  });

  // ==========================
  // 2. Slider revenu mensuel
  // ==========================
  const slider = document.getElementById("revenu-slider");
  const mensuelSpan = document.getElementById("revenu-mensuel-val");
  const hebdoSpan = document.getElementById("revenu-hebdo-val");
  const hiddenInput = document.getElementById("revenu-hidden");

  if (slider && mensuelSpan && hebdoSpan && hiddenInput) {
    const updateRevenu = (val) => {
      const montant = parseInt(val, 10) || 0;
      mensuelSpan.textContent = montant + " €";
      const hebdo = Math.floor(montant / 4);
      hebdoSpan.textContent = hebdo + " €";
      hiddenInput.value = montant;
    };

    // init avec la valeur actuelle du slider
    updateRevenu(slider.value);

    // quand on bouge le slider
    slider.addEventListener("input", function (e) {
      updateRevenu(e.target.value);
    });

    // pour certains navigateurs (fallback)
    slider.addEventListener("change", function (e) {
      updateRevenu(e.target.value);
    });
  } else {
    console.warn("[MamanSolo] Slider revenu non trouvé dans la page.");
  }
});
