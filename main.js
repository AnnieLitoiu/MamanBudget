// les bulles de conversation
const bubbles = [
    document.getElementById('bubble1'),
    document.getElementById('bubble2'),
    document.getElementById('bubble3'),
    document.getElementById('bubble4'),
    document.getElementById('bubble5')

  ];
  
  let index = 0;
  
  function showNextBubble() {
    if (index > 0) bubbles[index - 1].style.opacity = 0; // cache la précédente
    if (index < bubbles.length) {
      bubbles[index].style.opacity = 1;
      index++;
      setTimeout(showNextBubble, 8000); // change toutes les 6 secondes
    }
  }
  
  window.onload = () => {
    showNextBubble();
  };

  bubbles.forEach(bubble => {
    bubble.addEventListener('click', ()=>{
      index++;
      showNextBubble();
    })
  });
  // showNextBubble();
  //fin des bulles de conversation


  // les functions pour la page d'evenements
  const eventText = document.getElementById('eventText');
  const CHOIX = document.getElementById('choix');
  
  let data = {};  // Variable pour stocker les données JSON
  let currentWeek = 1;  // Semaine actuelle
  let currentAge = "bebe";  // Tranche d'âge actuelle
  let currentEventIndex = 0;  // Index de l'événement actuel
  
  // Variables de statut du joueur
  let bienEtre = 0;
  let stress = 0;
  let budget = 100;
  
  // Charger les données JSON
  fetch('data.json')
    .then(response => response.json())
    .then(jsonData => {
      data = jsonData;  // Stocker les données JSON dans la variable 'data'
      updateStatsDisplay();  // Initialiser l'affichage des stats
      showEvent();  // Afficher le premier événement
    })
    .catch(error => {
      console.error('Erreur lors du chargement du fichier JSON :', error);
    });
  
  // Mettre à jour l'affichage des statistiques
  function updateStatsDisplay() {
    document.getElementById("bienEtre").textContent = bienEtre;
    document.getElementById("stress").textContent = stress;
    document.getElementById("budget").textContent = budget;
  }
  
  // Afficher un événement
  function showEvent() {
    const weekData = data.weeks[`week${currentWeek}`]; // Récupère les données de la semaine en cours
    const events = weekData[currentAge]; // Récupère les événements pour l'âge en cours
  
    if (currentEventIndex >= events.length) {
      // Si tous les événements sont terminés, passer au bilan
      showBilan(weekData.bilan);
      return;
    }
  
    const event = events[currentEventIndex];
    eventText.textContent = event.text; // Afficher le texte de l'événement
    CHOIX.innerHTML = "";  // Réinitialiser les choix précédents
  
    // Ajouter les boutons pour chaque choix
    event.choices.forEach((choice) => {
      const btn = document.createElement("button");
      btn.classList.add('btn-choix');
      btn.textContent = choice.text;
      btn.onclick = () => applyImpact(choice.impact);
      CHOIX.appendChild(btn);
    });
  }
  
  // Appliquer les impacts en fonction du choix
  function applyImpact(impact) {
    bienEtre += impact.bienEtre || 0;
    stress += impact.stress || 0;
    budget += impact.budget || 0;
  
    updateStatsDisplay();  // Mettre à jour l'affichage des statistiques
  
    currentEventIndex++;  // Passer à l'événement suivant
    showEvent();  // Afficher le prochain événement
  }
  
  // Afficher le bilan à la fin de la semaine
  function showBilan(bilan) {
    eventText.textContent = "Bilan de la semaine : " + bilan; // Afficher le bilan
    CHOIX.innerHTML = "";  // Réinitialiser les choix
  
    // Remettre le jeu à zéro pour la prochaine semaine ou terminer le jeu
    currentWeek++;
    currentEventIndex = 0;  // Revenir au début des événements pour la nouvelle semaine
  
    // Si il y a encore des semaines à jouer, afficher le prochain événement
    if (data.weeks[`week${currentWeek}`]) {
      setTimeout(() => {
        showEvent(); // Lancer la prochaine semaine après 2 secondes
      }, 2000); // Attendre 2 secondes avant de commencer la semaine suivante
    } else {
      eventText.textContent = "Le jeu est terminé. Félicitations !"; // Fin du jeu
    }
  }
  
// fin des functions pour la page d'evenements
