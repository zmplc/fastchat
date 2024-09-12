// seleziono il form, ovvero l'area di input della chat e prendo incoming_id come valore e gli altri elementi
const form = document.querySelector(".typing-area"),
      incoming_id = form.querySelector(".incoming_id").value,
      inputField = form.querySelector(".input-field"),
      sendBtn = form.querySelector("button"),
      chatBox = document.querySelector(".chat-box");

// per evitare il ricaricamento della pagina
form.onsubmit = (e) => {
    e.preventDefault();
}

// focus sull'input appena viene aperta la pagina chat.php
inputField.focus();

// se c'è testo attivo/disattivo il pulsante per inviare
inputField.onkeyup = () => {
    if (inputField.value != "") {
        sendBtn.classList.add("active"); // attivo
    } else {
        sendBtn.classList.remove("active"); // disattivo
    }
}

// gestisco invio del messaggio dopo l'invio dell'utente
sendBtn.onclick = () => {
    // richiesta AJAX
    let xhr = new XMLHttpRequest();
    // configuro la richiesta con metodo POST e file insert-chat.php
    xhr.open("POST", "php/insert-chat.php", true);
    // se richiesta completata e andata a buon fine allora svuoto il campo di testo e scrollo all'ultimo messaggio
    xhr.onload = () => {
        if (xhr.readyState === XMLHttpRequest.DONE) {
            if (xhr.status === 200) {
                inputField.value = ""; // svuoto campo input
                scrollToBottom(); // scrollo automaticamente in fondo alla chat
            }
        }
    }
    // preparo i dati del form (campo di input) per l'invio del messaggio
    let formData = new FormData(form);
    // invio i dati al server
    xhr.send(formData);
}

// quando il mouse è sopra alla chat attivo la classe active
chatBox.onmouseenter = () => {
    chatBox.classList.add("active");
}

// quando il muose non è sopra la chat rimuovo la classe active
chatBox.onmouseleave = () => {
    chatBox.classList.remove("active");
}
// questo mi serve per far in modo che quando l'utente ha il mouse sulla chat la possa scorrere in alto e in basso altrimenti mi rimaneva bloccata


// ogni 500 millisecondi aggiorno la chat
setInterval(() => {
    // richiesta AJAX
    let xhr = new XMLHttpRequest();
    // configuro la richiesta con POST e file get-chat.php
    xhr.open("POST", "php/get-chat.php", true);
    // richiesta completata
    xhr.onload = () => {
        if (xhr.readyState === XMLHttpRequest.DONE) {
            if (xhr.status === 200) {
                let data = xhr.response; // ottengo i nuovi messaggi da mostrare
                chatBox.innerHTML = data; // aggiorno la chat e inserisco i messaggi
                // scrollo la chat in fondo alla chat
                if (!chatBox.classList.contains("active")) {
                    scrollToBottom();
                }
            }
        }
    }
    // tipo di contenuto della richiesta AJAX
    xhr.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
    // invio incoming_id al server per ottenere i messaggi corretti inviati
    xhr.send("incoming_id=" + incoming_id);
}, 500); // 500 millisecondi di intervallo

// funzione per scrollare automaticamente la chat
function scrollToBottom() {
    chatBox.scrollTop = chatBox.scrollHeight; // scroll impostato in fondo alla chat
}
