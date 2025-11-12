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
