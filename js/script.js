let chatOverlay = document.getElementById('anaimoai-chatOverlay');
let chatContainer = document.getElementById('anaimoai-chatContainer_1');
if (chatContainer == null) {
  chatContainer = document.getElementById('anaimoai-chatContainer_2');
}
let chatIframe = document.getElementById('anaimoai-chatIframe');
let button = document.getElementsByClassName('anaimoai-chat-btn-container_1')[0]
if (button == null) {
  button =  document.getElementsByClassName('anaimoai-chat-btn-container_2')[0]
}

function anaimoai_toggleChat() {
  if (chatOverlay.style.display === 'block') {
    anaimoai_minimizeChat();
  } else {
    chatOverlay.style.display = 'block';
    chatContainer.style.display = 'block';
    chatOverlay.style.pointerEvents = 'auto';
    chatContainer.hidden = false;
    console.log(button)
    button.style.display = 'none';
  }
}

function anaimoai_minimizeChat() {
  chatOverlay.style.display = 'none';
  chatContainer.hidden = true;
  button.style.display = 'flex';
}

function anaimoai_closeChat(src) {
  chatOverlay.style.display = 'none';
  chatContainer.style.display = 'none';
  chatIframe.src = src; // Clear the iframe source when closing
}

document.addEventListener('click', function (event) {
  if (
    !chatContainer.contains(event.target) && // Click is outside chatContainer
    event.target == chatOverlay // Click is in chatOverlay
  ) {
    anaimoai_toggleChat(); 
  }
});

function animate() {
  const container = document.querySelector("#anim-container-element");
  container.classList.add("animate");
  setTimeout(() => container.classList.remove("animate"), 1200);
}

onload = (event) => {
  animate();
  const intervalId = setInterval(animate, 60000);
};

