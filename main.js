const bubbles = [
    document.getElementById('bubble1'),
    document.getElementById('bubble2'),
    document.getElementById('bubble3')
  ];
  
  let current = 0;
  
  function showNextBubble() {
    if (current > 0) bubbles[current - 1].style.opacity = 0; // cache la précédente
    if (current < bubbles.length) {
      bubbles[current].style.opacity = 1;
      current++;
      setTimeout(showNextBubble, 6000); // change toutes les 3 secondes
    }
  }
  
  window.onload = () => {
    showNextBubble();
  };